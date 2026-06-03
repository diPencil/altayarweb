<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Page;
use App\Models\Employee as Agent;
// use App\Models\Artwork;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Language;
// use App\Models\Collection as TravelCollection;
use App\Models\ListingType;
use App\Models\MembershipPlan;
use App\Models\PrivilegeCard;
use App\Models\Subscriber;
use App\Models\Listing;
use App\Models\ServiceBooking;
use App\Models\Reel;
use App\Models\TourPackage;
use App\Models\TourBooking;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function index()
    {

        $pageTitle = 'Home';
        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();

        // check all tour package running or expired
        TourPackage::where('status', '!=', 3)
            ->where('tour_end', '<', now())
            ->update(['status' => 3]);

        TourPackage::where('status', '!=', 2)
            ->where('tour_start', '<=', now())
            ->where('tour_end', '>', now())
            ->update(['status' => 2]);

        $homeReels = Reel::active()
            ->select(['id', 'title', 'title_ar', 'source_name', 'source_name_ar', 'video_path', 'thumbnail_path'])
            ->ordered()
            ->take(8)
            ->get();

        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections', 'homeReels'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = __($page->name);
        $sections = $page->secs;

        if ($slug === 'about') {
            $aboutMeContent = getContent('about_me.content', true);
            $aboutMeElement = getContent('about_me.element', false, 4);

            return view($this->activeTemplate . 'about', compact(
                'pageTitle',
                'page',
                'aboutMeContent',
                'aboutMeElement'
            ));
        }

        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact', compact('pageTitle'));
    }

    public function listings()
    {
        $pageTitle = 'Listing Offers';
        $listings = Listing::with('listingType')->active()->latest()->paginate(8);

        return view($this->activeTemplate . 'listings.index', compact('pageTitle', 'listings'));
    }

    public function listingDetails($slug, $id)
    {
        $pageTitle = 'Listing Offer Details';
        $listing = Listing::with('listingType')->active()->findOrFail($id);
        $relatedListings = Listing::with('listingType')
            ->active()
            ->where('id', '!=', $listing->id)
            ->latest()
            ->limit(3)
            ->get();

        return view($this->activeTemplate . 'listings.details', compact('pageTitle', 'listing', 'relatedListings'));
    }

    public function clientFeedback()
    {
        $pageTitle = 'Client Feedback';
        return view($this->activeTemplate . 'client_feedback', compact('pageTitle'));
    }

    public function listingBooking($slug, $id)
    {
        $pageTitle = 'Listing Offer Booking';
        $listing = Listing::with('listingType')->active()->findOrFail($id);

        return view($this->activeTemplate . 'listings.booking', compact('pageTitle', 'listing'));
    }

    public function listingBookingStore(Request $request, $slug, $id)
    {
        if (!auth()->check()) {
            return redirect()->route('user.login');
        }

        $listing = Listing::active()->findOrFail($id);

        if (!$listing->start_date || !$listing->end_date) {
            $notify[] = ['error', 'This listing does not have trip dates yet'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'service_date' => 'required|date',
            'service_time' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:1000',
        ]);

        $serviceDate = Carbon::parse($request->service_date)->startOfDay();
        $listingStartDate = Carbon::parse($listing->start_date)->startOfDay();
        $listingEndDate = Carbon::parse($listing->end_date)->startOfDay();

        if (! $serviceDate->betweenIncluded($listingStartDate, $listingEndDate)) {
            $notify[] = ['error', 'Please select a booking date within the listing schedule'];
            return back()->withInput()->withNotify($notify);
        }

        $availableTimes = $listing->availableTimes();
        if (count($availableTimes) && (! $request->service_time || ! in_array($request->service_time, $availableTimes, true))) {
            $notify[] = ['error', 'Please select a valid travel time'];
            return back()->withNotify($notify);
        }

        $serviceBooking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'created_by_admin_id' => null,
            'booking_type' => 'stay',
            'title' => $listing->title,
            'booking_date' => now()->toDateString(),
            'service_date' => $serviceDate->toDateString(),
            'service_end_date' => $listing->end_date,
            'service_time' => $request->service_time,
            'amount' => $listing->finalPrice(),
            'currency' => $listing->currency ?? 'USD',
            'status' => 0,
            'notes' => trim(($request->notes ?? '') . "\nListing: {$listing->title}")
        ]);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = 'New booking request for ' . $listing->title;
        $adminNotification->click_url = '#';
        $adminNotification->save();

        if ($serviceBooking->amount <= 0) {
            $notify[] = ['success', 'Your booking request has been submitted successfully'];
            return to_route('user.service.booking.my.list', 'stay')->withNotify($notify);
        }

        $baseCurrency = strtoupper($listing->currency ?? gs()->cur_text ?? 'USD');

        $gate = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('currency', $baseCurrency)->first();

        if (!$gate) {
            $notify[] = ['success', 'Your booking request has been submitted successfully'];
            return to_route('user.service.booking.my.list', 'stay')->withNotify($notify);
        }

        $amountToPay = $serviceBooking->amount;
        $charge = $gate->fixed_charge + ($amountToPay * $gate->percent_charge / 100);
        $payable = $amountToPay + $charge;
        $final_amo = $payable * $gate->rate;

        $data = new \App\Models\Deposit();
        $data->user_id = auth()->id();
        $data->service_booking_id = $serviceBooking->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amountToPay;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function offersCategory(Request $request, string $category)
    {
        $categorySlug = $this->normalizeOffersCategory($category);
        $search = trim((string) $request->query('search', ''));
        $destination = trim((string) $request->query('destination', ''));

        $offerPageKey = match ($categorySlug) {
            'yearly' => 'year_offers',
            'weekend' => 'weekend_offers',
            'spa' => 'spa_beauty',
            'coupons' => 'coupons',
            'vouchers' => 'vouchers',
            default => 'all',
        };

        $pageTitle = __('offers_pages.' . $offerPageKey . '.document_title');

        $categoryTypeAliases = $this->offersCategoryTypeAliases();
        $matchedListingType = $categorySlug === 'limited'
            ? null
            : $this->resolveListingTypeForOffersCategory($categorySlug, $categoryTypeAliases);

        $listingTypesFilter = ListingType::active()->latest()->get();

        $explicitListingTypeId = null;
        $listingTypeIdRaw = $request->query('listing_type_id');
        if ($listingTypeIdRaw !== null && $listingTypeIdRaw !== '') {
            $explicitListingTypeId = ctype_digit((string) $listingTypeIdRaw)
                ? (int) $listingTypeIdRaw
                : null;
        }

        $buildOffersBase = static function () use (
            $request,
            $search,
            $destination,
            $categorySlug,
            $matchedListingType,
            $explicitListingTypeId,
        ): \Illuminate\Database\Eloquent\Builder {
            $offersQuery = Listing::with('listingType')->active()->latest();

            $offersQuery->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            });

            if ($categorySlug !== 'limited' && $explicitListingTypeId !== null && $explicitListingTypeId > 0) {
                $offersQuery->where('listing_type_id', $explicitListingTypeId);
            } elseif ($categorySlug !== 'limited') {
                if ($matchedListingType) {
                    $offersQuery->where('listing_type_id', $matchedListingType->id);
                } else {
                    $offersQuery->whereRaw('1 = 0');
                }
            }

            $priceMinRaw = str_replace(',', '', (string) $request->query('price_min', ''));
            $priceMaxRaw = str_replace(',', '', (string) $request->query('price_max', ''));
            $priceMin = is_numeric($priceMinRaw) ? (float) $priceMinRaw : null;
            $priceMax = is_numeric($priceMaxRaw) ? (float) $priceMaxRaw : null;

            if ($priceMin !== null) {
                $offersQuery->whereRaw('(price - COALESCE(discount, 0)) >= ?', [$priceMin]);
            }
            if ($priceMax !== null) {
                $offersQuery->whereRaw('(price - COALESCE(discount, 0)) <= ?', [$priceMax]);
            }

            $travelDate = $request->query('travel_date');
            if ($travelDate) {
                try {
                    $d = Carbon::parse($travelDate)->toDateString();
                    $offersQuery->where(function ($query) use ($d) {
                        $query->where(function ($sub) use ($d) {
                            $sub->whereNotNull('start_date')
                                ->whereNotNull('end_date')
                                ->whereDate('start_date', '<=', $d)
                                ->whereDate('end_date', '>=', $d);
                        })->orWhere(function ($sub) {
                            $sub->whereNull('start_date')->whereNull('end_date');
                        });
                    });
                } catch (\Throwable $e) {
                    // Ignore invalid dates
                }
            }

            if ($destination !== '') {
                $offersQuery->where(function ($query) use ($destination) {
                    $query->where('city', 'like', "%{$destination}%")
                        ->orWhere('country', 'like', "%{$destination}%")
                        ->orWhere('address', 'like', "%{$destination}%");
                });
            }

            if ($search !== '') {
                $offersQuery->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhereHas('listingType', function ($typeQuery) use ($search) {
                            $typeQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            return $offersQuery;
        };

        $offersBase = $buildOffersBase();

        $offerHighlights = (clone $offersBase)->limit(8)->get();

        $featuredOffers = (clone $offersBase)->limit(3)->get();
        if ($categorySlug === 'limited' && $featuredOffers->isEmpty()) {
            $featuredOffers = Listing::with('listingType')
                ->active()
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', now()->toDateString());
                })
                ->latest()
                ->limit(3)
                ->get();
        }

        $heroBannerListing = $featuredOffers->isNotEmpty() ? $featuredOffers->first() : null;
        $offers = $offersBase->paginate(request()->rows ?? 8)->appends(request()->all());

        $filterValues = [
            'destination' => $destination,
            'search' => $search,
            'price_min' => $request->query('price_min', ''),
            'price_max' => $request->query('price_max', ''),
            'travel_date' => $request->query('travel_date', ''),
            'listing_type_id' => $request->query('listing_type_id', ''),
        ];

        // Prepare hub categories data if we are in 'limited' (overview) mode
        $hubData = [];
        if ($categorySlug === 'limited' && $search === '' && $destination === '' && $request->query('price_min') === null) {
            $hubCategories = [
                'yearly' => ['slug' => 'yearly', 'title' => __('offers_nav.yearly')],
                'weekend' => ['slug' => 'weekend', 'title' => 'Weekend offers'],
                'spa' => ['slug' => 'spa-beauty', 'title' => __('offers_nav.spa')],
                'coupons' => ['slug' => 'coupons', 'title' => 'Coupons'],
                'vouchers' => ['slug' => 'vouchers', 'title' => 'Vouchers'],
            ];

            foreach ($hubCategories as $key => $cat) {
                $catQuery = Listing::with('listingType')->active();
                $matchingType = $this->resolveListingTypeForOffersCategory($key, $categoryTypeAliases);
                if ($matchingType) {
                    $catQuery->where('listing_type_id', $matchingType->id);
                } else {
                    $catQuery->whereRaw('1 = 0');
                }
                $hubData[$key] = [
                    'title' => $cat['title'],
                    'items' => $catQuery->latest()->limit(4)->get(),
                    'slug' => $key
                ];
            }
        }

        return view($this->activeTemplate . 'offers.index', compact(
            'pageTitle',
            'offers',
            'featuredOffers',
            'offerHighlights',
            'offerPageKey',
            'categorySlug',
            'listingTypesFilter',
            'filterValues',
            'heroBannerListing',
            'hubData',
        ));
    }

    private function normalizeOffersCategory(string $category): string
    {
        $normalized = Str::of($category)->lower()->replace(['_', ' '], '-')->toString();

        return match ($normalized) {
            'all' => 'limited',
            'year-offers', 'years-offers', 'year-offer', 'yearly' => 'yearly',
            'weekend-offers', 'weekend-offer', 'weekend' => 'weekend',
            'spa-beauty-offers', 'spa-beauty-offer', 'spa-beauty', 'spa' => 'spa',
            'coupons' => 'coupons',
            'vouchers' => 'vouchers',
            default => 'limited',
        };
    }

    private function offersCategoryTypeAliases(): array
    {
        return [
            'yearly' => [
                "Year's Offers",
                'Year’s Offers',
                'Year Offers',
                'Year Offer',
            ],
            'weekend' => [
                'Weekend Offers',
            ],
            'spa' => [
                'Spa & Beauty Offers',
                'Spa and Beauty Offers',
                'Spa Beauty Offers',
            ],
            'coupons' => [
                'Coupons',
            ],
            'vouchers' => [
                'Vouchers',
            ],
        ];
    }

    private function resolveListingTypeForOffersCategory(string $category, array $categoryTypeAliases): ?ListingType
    {
        $aliases = $categoryTypeAliases[$category] ?? [];
        if (empty($aliases)) {
            return null;
        }

        $normalizedAliases = collect($aliases)
            ->map(fn ($name) => $this->normalizeOfferTypeName($name))
            ->filter()
            ->values()
            ->all();

        if (empty($normalizedAliases)) {
            return null;
        }

        return ListingType::active()
            ->get()
            ->first(function (ListingType $listingType) use ($normalizedAliases) {
                $normalizedName = $this->normalizeOfferTypeName((string) $listingType->getRawOriginal('name'));
                return in_array($normalizedName, $normalizedAliases, true);
            });
    }

    private function normalizeOfferTypeName(string $name): string
    {
        $normalized = Str::of($name)
            ->lower()
            ->replace(['’', "'", '&', '_'], '')
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();

        return $normalized;
    }

    public function moreTravel(Request $request, $section = null)
    {
        $travelSections = [
            'packages' => [
                'label' => 'Tour Packages',
                'description' => 'Curated escapes and curated holiday packages.',
                'icon' => 'fa-suitcase',
            ],
            'destinations' => [
                'label' => 'Destinations',
                'description' => 'Popular cities and search-ready destinations.',
                'icon' => 'fa-location-dot',
            ],
            'hotels' => [
                'label' => 'Hotels',
                'description' => 'Premium stays with trusted hospitality.',
                'icon' => 'fa-hotel',
            ],
            'flights' => [
                'label' => 'Flights',
                'description' => 'Flight-led browsing for trip planning.',
                'icon' => 'fa-plane-departure',
            ],
            'transportation' => [
                'label' => 'Transportation',
                'description' => 'Reliable and comfortable travel transfers.',
                'icon' => 'fa-car',
            ],
        ];

        $currentSection = Str::slug($section ?? $request->query('section', 'overview')) ?: 'overview';
        if ($currentSection !== 'overview' && ! isset($travelSections[$currentSection])) {
            $currentSection = 'overview';
        }

        $travelPageKey = match ($currentSection) {
            'packages' => 'packages',
            'destinations' => 'destinations',
            'hotels' => 'hotels',
            'flights' => 'flights',
            'transportation' => 'transportation',
            default => 'overview',
        };

        $pageTitle = __('travel_pages.' . $travelPageKey . '.document_title');

        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'latest');
        $destination = trim((string) $request->query('destination', ''));
        $priceMin = $request->query('price_min', '');
        $priceMax = $request->query('price_max', '');
        $travelDate = $request->query('travel_date', '');
        $categoryId = $request->query('category_id', '');

        $tourCategories = Category::latest()->get();

        $filterValues = [
            'destination' => $destination,
            'search' => $search,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'travel_date' => $travelDate,
            'category_id' => $categoryId,
        ];



        $featuredPackages = TourPackage::with(['category', 'TourPackagePrimaryImage'])
            ->whereIn('status', [1, 2, 3])
            ->latest()
            ->limit(8)
            ->get();

        $featuredListings = Listing::with('listingType')
            ->active()
            ->latest()
            ->limit(8)
            ->get();

        $destQuery = Location::where('status', 1);
        if ($currentSection === 'destinations' && $search !== '') {
            $destQuery->where('name', 'like', "%{$search}%");
        }

        $allDestinations = $destQuery->get();
        $destinationCards = $allDestinations->groupBy(function ($item) {
            $parts = explode(',', (string) $item->getRawOriginal('location'));

            return trim((string) end($parts));
        });

        $destinationCountryOrder = ['Singapore', 'Thailand', 'USA', 'Egypt', 'Saudi Arabia', 'UAE'];
        $destinationCountryLabels = [
            'Singapore' => ['en' => 'Singapore', 'ar' => 'سنغافورة'],
            'Thailand' => ['en' => 'Thailand', 'ar' => 'تايلاند'],
            'USA' => ['en' => 'USA', 'ar' => 'الولايات المتحدة'],
            'Egypt' => ['en' => 'Egypt', 'ar' => 'مصر'],
            'Saudi Arabia' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
            'UAE' => ['en' => 'UAE', 'ar' => 'الإمارات العربية المتحدة'],
        ];

        $destinationCountryGroups = collect();
        foreach ($destinationCountryOrder as $country) {
            $locations = $allDestinations->filter(function ($item) use ($country) {
                $locationText = Str::lower((string) $item->getRawOriginal('location'));

                return Str::contains($locationText, Str::lower($country));
            })->take(5)->values();

            if ($locations->isEmpty()) {
                continue;
            }

            $destinationCountryGroups->put($country, [
                'label' => is_rtl()
                    ? ($destinationCountryLabels[$country]['ar'] ?? $country)
                    : ($destinationCountryLabels[$country]['en'] ?? $country),
                'items' => $locations,
            ]);
        }
        
        $destinationDropdown = $allDestinations->map(function($item) {
            return [
                'label' => $item->name,
                'value' => $item->name,
            ];
        })->sortBy('label')->values();

        $results = collect();
        $travelHighlights = collect();

        if ($currentSection !== 'overview') {
            if (in_array($currentSection, ['packages'], true)) {
                $query = TourPackage::with(['category', 'TourPackagePrimaryImage'])
                    ->whereIn('status', [1, 2, 3]);

                if ($search !== '') {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhereHas('category', function ($categoryQuery) use ($search) {
                                $categoryQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                }

                if ($destination !== '') {
                    $query->where(function ($subQuery) use ($destination) {
                        $subQuery->where('city', 'like', "%{$destination}%")
                            ->orWhere('country', 'like', "%{$destination}%")
                            ->orWhere('address', 'like', "%{$destination}%");
                    });
                }

                if ($priceMin !== '') {
                    $query->where('price', '>=', (float) $priceMin);
                }
                if ($priceMax !== '') {
                    $query->where('price', '<=', (float) $priceMax);
                }

                if ($travelDate !== '') {
                    $query->where(function ($subQuery) use ($travelDate) {
                        $subQuery->whereDate('start_date', '<=', $travelDate)
                            ->whereDate('end_date', '>=', $travelDate);
                    });
                }

                if ($categoryId !== '') {
                    $query->where('category_id', (int) $categoryId);
                }

                $travelHighlights = (clone $query)->limit(8)->get();


                $results = match ($sort) {
                    'oldest' => $query->oldest()->paginate(getPaginate(8)),
                    'popular' => $query->orderByDesc('view')->paginate(getPaginate(8)),
                    default => $query->latest()->paginate(getPaginate(8)),
                };
                $results->withQueryString();
            } elseif ($currentSection === 'destinations') {
                $results = $destinationCountryGroups;
                $travelHighlights = $featuredPackages->take(8);
            } elseif (in_array($currentSection, ['hotels', 'flights', 'transportation'])) {
                $results = collect();
                $travelHighlights = collect();
            } else {
                $query = Listing::with('listingType')->active();

                $sectionKeywords = match ($currentSection) {
                    'hotels' => ['hotel', 'stay', 'resort'],
                    'flights' => ['flight', 'air', 'airline'],
                    default => [],
                };

                if (! empty($sectionKeywords)) {
                    $query->where(function ($subQuery) use ($sectionKeywords) {
                        foreach ($sectionKeywords as $keyword) {
                            $subQuery->orWhere('title', 'like', '%' . $keyword . '%')
                                ->orWhere('summary', 'like', '%' . $keyword . '%')
                                ->orWhereHas('listingType', function ($typeQuery) use ($keyword) {
                                    $typeQuery->where('name', 'like', '%' . $keyword . '%');
                                });
                        }
                    });
                }

                if ($search !== '') {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('summary', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%")
                            ->orWhereHas('listingType', function ($typeQuery) use ($search) {
                                $typeQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                }

                if ($destination !== '') {
                    $query->where(function ($subQuery) use ($destination) {
                        $subQuery->where('city', 'like', "%{$destination}%")
                            ->orWhere('country', 'like', "%{$destination}%")
                            ->orWhere('address', 'like', "%{$destination}%");
                    });
                }

                if ($priceMin !== '') {
                    $query->where('price', '>=', (float) $priceMin);
                }
                if ($priceMax !== '') {
                    $query->where('price', '<=', (float) $priceMax);
                }

                if ($travelDate !== '') {
                    $query->where(function ($subQuery) use ($travelDate) {
                        $subQuery->whereDate('start_date', '<=', $travelDate)
                            ->whereDate('end_date', '>=', $travelDate);
                    });
                }



                $travelHighlights = (clone $query)->limit(8)->get();

                $results = match ($sort) {
                    'oldest' => $query->oldest()->paginate(getPaginate(8)),
                    'popular' => $query->orderByDesc('discount')->paginate(getPaginate(8)),
                    default => $query->latest()->paginate(getPaginate(8)),
                };
                $results->withQueryString();
            }

        } else {
            $travelHighlights = $featuredPackages->take(8);
        }

        $heroSpotlightPackage = $featuredPackages->first();
        $heroSpotlightListing = $featuredListings->first();

        $travelBlogTeasers = Frontend::where('data_keys', 'blog.element')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $travelTestimonials = getContent('testimonial.element', false, 12);

        $travelStats = [
            'packages' => TourPackage::whereIn('status', [1, 2, 3])->count(),
            'destinations' => $destinationCards->count(),
            'bookings' => TourBooking::count(),
        ];

        return view($this->activeTemplate . 'travel.index', compact(
            'pageTitle',
            'travelSections',
            'currentSection',
            'results',
            'search',
            'sort',
            'featuredPackages',
            'featuredListings',
            'destinationCards',
            'destinationDropdown',
            'travelPageKey',
            'travelHighlights',
            'heroSpotlightPackage',
            'heroSpotlightListing',
            'travelBlogTeasers',
            'travelTestimonials',
            'travelStats',
            'tourCategories',
            'filterValues',
        ))->with(['locations' => $allDestinations]);
    }

    public function membershipCard()
    {
        $pageTitle = 'Membership Card';
        $user = auth()->user();
        $currentMembership = $user ? $user->currentMembership()->with('plan')->first() : null;
        $plans = MembershipPlan::where('status', 1)->orderBy('id', 'asc')->get();

        return view($this->activeTemplate . 'membership-card.index', compact(
            'pageTitle',
            'user',
            'currentMembership',
            'plans'
        ));
    }

    public function membershipDetails()
    {
        $pageTitle = 'Membership Details';
        $user = auth()->user();
        $currentMembership = $user ? $user->currentMembership()->with('plan')->first() : null;
        $plans = MembershipPlan::where('status', 1)->orderBy('id', 'asc')->get();

        return view($this->activeTemplate . 'membership-details.index', compact(
            'pageTitle',
            'user',
            'currentMembership',
            'plans'
        ));
    }

    public function membershipDetailsShow($id)
    {
        $plan = MembershipPlan::where('status', 1)->findOrFail($id);
        $pageTitle = is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name;
        $user = auth()->user();
        $currentMembership = $user ? $user->currentMembership()->with('plan')->first() : null;
        $plans = MembershipPlan::where('status', 1)->orderBy('id', 'asc')->limit(6)->get();

        return view($this->activeTemplate . 'membership-details.show', compact(
            'pageTitle',
            'plan',
            'user',
            'currentMembership',
            'plans'
        ));
    }

    public function membershipLogin()
    {
        $pageTitle = 'Membership Intro';
        return view($this->activeTemplate . 'membership_intro', compact('pageTitle'));
    }

    public function membershipRegister()
    {
        $pageTitle = 'Membership Registration Benefits';
        return view($this->activeTemplate . 'membership_register', compact('pageTitle'));
    }

    public function packagesIntro()
    {
        $pageTitle = 'Permanent Packages & Membership';
        return view($this->activeTemplate . 'packages_intro', compact('pageTitle'));
    }

    public function privilegeCards(Request $request)
    {
        $pageTitle = 'Privilege Cards';
        $search = trim((string) $request->query('search', ''));

        $cardsQuery = PrivilegeCard::active()->orderByDesc('is_featured')->orderBy('sort_order')->latest();

        if ($search !== '') {
            $cardsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $cards = $cardsQuery->paginate(getPaginate(9));
        $featuredCards = PrivilegeCard::active()->where('is_featured', 1)->orderBy('sort_order')->get();

        return view($this->activeTemplate . 'privilege-cards.index', compact(
            'pageTitle',
            'cards',
            'featuredCards',
            'search'
        ));
    }

    public function engineScreen()
    {
        $pageTitle = __('Engine Screen');
        $user = auth()->user();
        
        // This is a specialized view for the comprehensive search engine
        return view($this->activeTemplate . 'engine_screen', compact('pageTitle', 'user'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function placeholderImage($size = null)
    {
        [$imgWidth, $imgHeight] = array_pad(explode('x', $size), 2, 300);
        $imgWidth = max(1, (int) $imgWidth);
        $imgHeight = max(1, (int) $imgHeight);
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        if (!function_exists('imagecreatetruecolor')) {
            $text = $imgWidth . 'x' . $imgHeight;
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$imgWidth.'" height="'.$imgHeight.'" viewBox="0 0 '.$imgWidth.' '.$imgHeight.'">'
                .'<rect width="100%" height="100%" fill="#1c232f"/>'
                .'<text x="50%" y="50%" fill="#ffffff" font-family="Arial, sans-serif" font-size="'.max(12, min(32, (int) ($imgWidth / 8))).'" text-anchor="middle" dominant-baseline="middle">'.$text.'</text>'
                .'</svg>';

            return response($svg, 200)->header('Content-Type', 'image/svg+xml');
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 255, 255, 255);
        $bgFill    = imagecolorallocate($image, 28, 35, 47);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        ob_start();
        imagejpeg($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)->header('Content-Type', 'image/jpeg');
    }

    public function tourPackageDetails($slug, $id)
    {
        $tourPackage = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images'])->findOrFail($id);
        $pageTitle = $tourPackage->title;
        $breadcrumbTrail = [
            ['label' => 'Tour Details', 'url' => null]
        ];
        $tourPackage->view += 1;
        $tourPackage->save();

        $tourPackages = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        return view($this->activeTemplate . 'tour-package.tour_package_details', compact('pageTitle', 'tourPackage', 'tourPackages', 'breadcrumbTrail'));
    }

    public function tourPackageList()
    {
        $pageTitle = 'Unforgettable Tour Packages';
        $categories = Category::where('status', 1)
            ->whereHas('tour_packages', function ($query) {
                $query->whereIn('status',[1,2,3]);
            })
            ->with(['tour_packages' => function ($query) {
                $query->whereIn('status',[1,2,3]);
            }])
            ->get();
        $query = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])
            ->whereIn('status',[1,2,3]);
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', 'browse')->first();
        $sections = $page->secs;
        $locationSearch = request()->query('location');
        $categorySearch = request()->query('category_id');
        $dateSearch = request()->query('start_date');
        $personSearch = request()->query('person');
        $locationIdSearch = request()->query('location_id');
        $inputLatitude = request()->query('lati');
        $inputLongitude = request()->query('longi');
        if ($locationSearch || $categorySearch || $dateSearch || $personSearch || $locationIdSearch || ($inputLatitude && $inputLongitude)) {
            if ($locationSearch) {
                $query->where('address', 'LIKE', "%{$locationSearch}%")->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($categorySearch) {
                $query->where('category_id', $categorySearch)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($dateSearch) {
                $carbonDate = Carbon::parse($dateSearch)->format('Y-m-d');
                $query->where('tour_start', '<', $carbonDate)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($personSearch) {
                $query->where('person_capability', $personSearch)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($inputLatitude && $inputLongitude) {

                $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$inputLatitude, $inputLongitude, $inputLatitude])
                    ->havingRaw('distance < ?', [100]);
            }

            $tourPackages = $query->orderBy('id', 'desc')->paginate(getPaginate(8));
        } else {
            switch (request()->query('search')) {

                case 'new':
                    $tourPackages = $query->latest()->paginate(getPaginate(8));
                    break;

                case 'rating':
                    $tourPackages = $query->orderBy('average_rating', 'desc')->paginate(getPaginate(8));
                    break;

                case 'trending':
                    $tourPackages = $query->whereHas(
                        'tour_bookings',
                        fn($query) =>
                        $query->where('created_at', '>=', now()->subDays(30))
                    )->withCount('tour_bookings')->orderByDesc('tour_bookings_count', 'desc')->paginate(8);
                    break;

                default:
                    $tourPackages = $query->latest()->paginate(8);
                    break;
            }
        }

        return view($this->activeTemplate . 'tour-package.tour_package_list', compact('pageTitle', 'tourPackages', 'categories', 'sections'));
    }

    public function tourPackageSideFilter(Request $request)
    {

        $pageTitle = 'Searching';
        $searchKey = $request->input('search');
        $star = $request->input('star');


        $categoryId = $request->input('categoryId');

        $priceMin = $request->input('priceMin');
        $priceMax = $request->input('priceMax');

        $query = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])->whereIn('status', [1,2,3])->orderBy('id', 'desc')->limit(8)->latest();
        if (!empty($searchKey) && strlen($searchKey) >= 2) {
            $query->where('title', 'LIKE', "%{$searchKey}%");
        }
        if (!empty($star)) {
            $query->whereIn('average_rating', $star);
        }
        if (!empty($categoryId)) {
            $query->whereIn('category_id', $categoryId);

        }

        if (!empty($priceMin) && !empty($priceMax)) {

            $query->whereBetween('price', [$priceMin, $priceMax]);
        }

        $tourPackages = $query->get();

        $view = view($this->activeTemplate . 'components.single_tour_package', compact('tourPackages', 'pageTitle'))->render();
        return response()->json([
            'html' => $view
        ]);
    }

    public function policyPages($slug, $id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        if (policy_is_website_policy_card($policy)) {
            return redirect()->route('policy.website', [], 301);
        }
        return redirect()->route('policy.terms', [], 301);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->where('code', '!=', 'es')->first();
        if (!$language)
            $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blog(Request $request)
    {
        $pageTitle = __('News & Updates');
        $blogs = Frontend::where('data_keys', 'blog.element')
            ->when($request->search, function ($query) use ($request) {
                $search = strtolower($request->search);
                $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(data_values, '$.title'))) LIKE ?", ["%$search%"]);
            })
            ->orderBy('id', 'desc')
            ->paginate(getPaginate(8));

        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', 'blog')->first();

        return view($this->activeTemplate . 'blog', compact('pageTitle', 'blogs', 'sections'));
    }

    public function blogDetails($slug, $id)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle = 'Blog Details';
        $latests = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(5)->get();
        return view($this->activeTemplate . 'blog_details', compact('blog', 'pageTitle', 'latests'));
    }
    public function cookieAccept()
    {
        $general = gs();
        Cookie::queue('gdpr_cookie', $general->site_name, 43200);
        return back();
    }
    public function cookiePolicy()
    {
        $pageTitle = 'policy_cookie.page_title';
        return view($this->activeTemplate . 'cookie', compact('pageTitle'));
    }

    /*
    public function sellerCollectionFilter(Request $request)
    {
        $pageTitle = 'Seller Collection Filter';
        $agent = Agent::findOrFail($request->agentId);
        $collections = TravelCollection::active()->with(['artworks', 'agent'])->where('agent_id', $agent->id)->latest()->get();
        $view = view($this->activeTemplate . 'components.collection', compact('collections', 'pageTitle'))->render();
        return response()->json([
            'html' => $view
        ]);
    }
    */



    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:subscribers',
        ]);
        $subscribe = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();
        $notify[] = ['success', 'You have successfully subscribed to the Newsletter'];
        return back()->withNotify($notify);
    }

    public function serviceBookingSubmit(Request $request)
    {
        if (!auth()->check()) {
            $notify[] = ['error', 'Please login to submit your request'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'type' => 'required|in:hotel,flight,transportation',
            'title' => 'nullable|string|max:255',
            'service_date' => 'required|date',
            'service_end_date' => 'nullable|date',
            'service_time' => 'nullable|string',
            'notes' => 'nullable|string|max:2000',
        ]);

        $details = [];
        if ($request->type === 'hotel') {
            $details[] = "Destination: " . $request->destination;
            $details[] = "Guests: " . $request->guests;
        } elseif ($request->type === 'flight') {
            $details[] = "From: " . $request->origin;
            $details[] = "To: " . $request->destination;
            $details[] = "Class: " . $request->class;
        } elseif ($request->type === 'transportation') {
            $details[] = "Pickup: " . $request->origin;
            $details[] = "Drop-off: " . $request->destination;
            $details[] = "Vehicle Type: " . $request->vehicle_type;
        }

        $notes = implode("\n", $details) . "\n\nUser Notes:\n" . ($request->notes ?? 'None');

        $booking = new ServiceBooking();
        $booking->user_id = auth()->id();
        $booking->booking_type = $request->type;
        $booking->title = $request->title ?? ucfirst($request->type) . " Booking Request";
        $booking->booking_date = Carbon::now();
        $booking->service_date = Carbon::parse($request->service_date);
        $booking->service_end_date = $request->service_end_date;
        $booking->service_time = $request->service_time;
        $booking->status = 0; // Pending
        $booking->notes = $notes;
        $booking->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = 'New ' . $request->type . ' booking request from ' . auth()->user()->username;
        // Since we don't have a specific admin route yet, we'll link to a general one or wait until I create it.
        // Let's assume the new admin routes will follow this pattern.
        $routeNameMap = [
            'hotel' => 'hotels',
            'flight' => 'flights',
            'transportation' => 'transportation'
        ];
        $typeSuffix = $routeNameMap[$request->type] ?? 'index';
        $adminNotification->click_url = urlPath('admin.service.booking.' . $typeSuffix); 
        $adminNotification->save();

        $notify[] = ['success', 'Your ' . $request->type . ' booking request has been submitted successfully!'];
        return back()->withNotify($notify);
    }

    public function sitemap()
    {
        $urls = [];

        // Static Routes
        $staticRoutes = [
            'home', 'browse', 'blog', 'contact', 'public.membership.details', 
            'public.privilege.cards.index', 'packages.intro', 'listings',
            'pay.online', 'e.payment', 'policy.website', 'policy.terms'
        ];

        foreach ($staticRoutes as $route) {
            try {
                $urls[] = [
                    'loc' => route($route),
                    'lastmod' => now()->toAtomString(),
                    'priority' => ($route == 'home') ? '1.0' : '0.8',
                    'changefreq' => 'daily'
                ];
            } catch (\Exception $e) {
                // Skip if route not defined
            }
        }

        // More Travel Sections
        $travelSections = ['packages', 'destinations', 'hotels', 'flights', 'transportation'];
        try {
            $urls[] = [
                'loc' => route('public.travel.index'),
                'lastmod' => now()->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'daily'
            ];
        } catch (\Exception $e) {}

        foreach ($travelSections as $sect) {
            try {
                $urls[] = [
                    'loc' => route('public.travel.index', [$sect]),
                    'lastmod' => now()->toAtomString(),
                    'priority' => '0.8',
                    'changefreq' => 'daily'
                ];
            } catch (\Exception $e) {
                // Skip if route not defined
            }
        }

        // Offers Categories
        $offersCategories = ['limited', 'yearly', 'weekend', 'spa-beauty', 'coupons', 'vouchers'];
        foreach ($offersCategories as $cat) {
            try {
                $urls[] = [
                    'loc' => route('public.offers.index', [$cat]),
                    'lastmod' => now()->toAtomString(),
                    'priority' => '0.8',
                    'changefreq' => 'daily'
                ];
            } catch (\Exception $e) {
                // Skip if route not defined
            }
        }

        // Membership Plan Details
        try {
            $plans = \App\Models\MembershipPlan::active()->get();
            foreach ($plans as $plan) {
                if (empty($plan->id)) {
                    continue;
                }
                $date = $plan->updated_at ?: $plan->created_at;
                $lastmod = $date ? $date->toAtomString() : now()->toAtomString();
                $urls[] = [
                    'loc' => route('public.membership.details.show', [$plan->id]),
                    'lastmod' => $lastmod,
                    'priority' => '0.7',
                    'changefreq' => 'monthly'
                ];
            }
        } catch (\Exception $e) {
            // Skip if model or route not defined
        }

        // Tour Packages
        $packages = TourPackage::whereIn('status', [1, 2, 3])->get();
        foreach ($packages as $package) {
            if (empty($package->slug) || empty($package->id)) {
                continue;
            }
            $date = $package->updated_at ?: $package->created_at;
            $lastmod = $date ? $date->toAtomString() : now()->toAtomString();
            $urls[] = [
                'loc' => route('tour.package.details', [$package->slug, $package->id]),
                'lastmod' => $lastmod,
                'priority' => '0.9',
                'changefreq' => 'weekly'
            ];
        }

        // Listings
        $listings = Listing::active()->get();
        foreach ($listings as $listing) {
            if (empty($listing->slug) || empty($listing->id)) {
                continue;
            }
            $date = $listing->updated_at ?: $listing->created_at;
            $lastmod = $date ? $date->toAtomString() : now()->toAtomString();
            $urls[] = [
                'loc' => route('listing.details', [$listing->slug, $listing->id]),
                'lastmod' => $lastmod,
                'priority' => '0.9',
                'changefreq' => 'weekly'
            ];
        }

        // Blogs
        $blogs = Frontend::where('data_keys', 'blog.element')->get();
        foreach ($blogs as $blog) {
            if (empty($blog->id) || empty($blog->data_values)) {
                continue;
            }

            $dataValues = $blog->data_values;
            $title = null;
            if (is_object($dataValues)) {
                $title = $dataValues->title ?? null;
            } elseif (is_array($dataValues)) {
                $title = $dataValues['title'] ?? null;
            }

            if (empty($title)) {
                continue;
            }

            $date = $blog->updated_at ?: $blog->created_at;
            $lastmod = $date ? $date->toAtomString() : now()->toAtomString();
            $urls[] = [
                'loc' => route('blog.details', [slug($title), $blog->id]),
                'lastmod' => $lastmod,
                'priority' => '0.7',
                'changefreq' => 'monthly'
            ];
        }

        // Custom Pages
        $pages = Page::where('tempname', $this->activeTemplate)->get();
        foreach ($pages as $page) {
            if (empty($page->slug) || $page->slug == '/') continue;
            $date = $page->updated_at ?: $page->created_at;
            $lastmod = $date ? $date->toAtomString() : now()->toAtomString();
            $urls[] = [
                'loc' => route('pages', [$page->slug]),
                'lastmod' => $lastmod,
                'priority' => '0.6',
                'changefreq' => 'monthly'
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . htmlspecialchars($url['lastmod']) . '</lastmod>';
            $xml .= '<changefreq>' . htmlspecialchars($url['changefreq']) . '</changefreq>';
            $xml .= '<priority>' . htmlspecialchars($url['priority']) . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Sitemap: " . route('sitemap') . "\n";

        return response($content)->header('Content-Type', 'text/plain');
    }
}
