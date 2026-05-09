<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingType;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Listing Offers');
        $listings = Listing::with('listingType')->latest();

        if ($request->search) {
            $search = $request->search;
            $listings = $listings->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('listingType', function ($typeQuery) use ($search) {
                        $typeQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $listings = $listings->paginate(getPaginate());

        return view('admin.listing.index', compact('pageTitle', 'listings'));
    }

    public function create()
    {
        $pageTitle = __('Create Listing');
        $listingTypes = ListingType::active()->latest()->get();
        return view('admin.listing.create', compact('pageTitle', 'listingTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'listing_type_id' => 'required|exists:listing_types,id',
            'summary' => 'nullable|string|max:255',
            'summary_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'address' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'available_times' => 'nullable|string',
            'facilities' => 'nullable|string',
            'facilities_ar' => 'nullable|string',
            'includes' => 'nullable|string',
            'includes_ar' => 'nullable|string',
            'excludes' => 'nullable|string',
            'excludes_ar' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|in:EGP,SAR,USD,EUR',
            'discount' => 'nullable|numeric|min:0',
            'offer_type' => 'nullable|in:stay_pay,day_bundle,custom',
            'offer_first_value' => 'nullable|integer|min:1',
            'offer_second_value' => 'nullable|integer|min:1',
            'offer_text' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp'])],
            'image_url' => 'nullable|url',
        ]);

        $listingType = ListingType::findOrFail($request->listing_type_id);
        $listing = new Listing();
        $listing->title = $request->title;
        $listing->title_ar = $request->title_ar;
        $listing->slug = Str::slug($request->title) . '-' . Str::lower(Str::random(6));
        $listing->listing_type_id = $listingType->id;
        $listing->type = $listingType->name;
        $listing->summary = $request->summary;
        $listing->summary_ar = $request->summary_ar;
        $listing->description = $request->description;
        $listing->description_ar = $request->description_ar;
        $listing->city = $request->city;
        $listing->country = $request->country;
        $listing->address = $request->address;
        $listing->start_date = $request->start_date;
        $listing->end_date = $request->end_date;
        $listing->available_times = $this->normalizeAvailableTimes($request->available_times);
        $listing->facilities = $this->normalizeLines($request->facilities);
        $listing->facilities_ar = $this->normalizeLines($request->facilities_ar);
        $listing->includes = $this->normalizeLines($request->includes);
        $listing->includes_ar = $this->normalizeLines($request->includes_ar);
        $listing->excludes = $this->normalizeLines($request->excludes);
        $listing->excludes_ar = $this->normalizeLines($request->excludes_ar);
        $listing->price = $request->price ?? 0;
        $listing->currency = $request->currency;
        $listing->discount = $request->discount;
        $listing->offer_type = $request->offer_type;
        $listing->offer_first_value = $request->offer_first_value;
        $listing->offer_second_value = $request->offer_second_value;
        $listing->offer_text = $request->offer_text;
        $listing->status = $request->status ? 1 : 0;
        $listing->user_id = auth('admin')->id();
        $listing->user_type = 'admin';

        if ($request->hasFile('image')) {
            try {
                $listing->image = fileUploader($request->image, getFilePath('listingImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload your image')];
                return back()->withNotify($notify);
            }
        } elseif ($request->image_url) {
            $listing->image = $request->image_url;
        }

        $listing->save();

        $notify[] = ['success', __('Listing created successfully')];
        return to_route('admin.listing.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Listing');
        $listing = Listing::findOrFail($id);
        $listingTypes = ListingType::active()->latest()->get();
        return view('admin.listing.edit', compact('pageTitle', 'listing', 'listingTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'listing_type_id' => 'required|exists:listing_types,id',
            'summary' => 'nullable|string|max:255',
            'summary_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'address' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'available_times' => 'nullable|string',
            'facilities' => 'nullable|string',
            'facilities_ar' => 'nullable|string',
            'includes' => 'nullable|string',
            'includes_ar' => 'nullable|string',
            'excludes' => 'nullable|string',
            'excludes_ar' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|in:EGP,SAR,USD,EUR',
            'discount' => 'nullable|numeric|min:0',
            'offer_type' => 'nullable|in:stay_pay,day_bundle,custom',
            'offer_first_value' => 'nullable|integer|min:1',
            'offer_second_value' => 'nullable|integer|min:1',
            'offer_text' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp'])],
            'image_url' => 'nullable|url',
        ]);

        $listingType = ListingType::findOrFail($request->listing_type_id);
        $listing = Listing::findOrFail($id);
        $listing->title = $request->title;
        $listing->title_ar = $request->title_ar;
        $listing->slug = Str::slug($request->title) . '-' . Str::lower(Str::random(6));
        $listing->listing_type_id = $listingType->id;
        $listing->type = $listingType->name;
        $listing->summary = $request->summary;
        $listing->summary_ar = $request->summary_ar;
        $listing->description = $request->description;
        $listing->description_ar = $request->description_ar;
        $listing->city = $request->city;
        $listing->country = $request->country;
        $listing->address = $request->address;
        $listing->start_date = $request->start_date;
        $listing->end_date = $request->end_date;
        $listing->available_times = $this->normalizeAvailableTimes($request->available_times);
        $listing->facilities = $this->normalizeLines($request->facilities);
        $listing->facilities_ar = $this->normalizeLines($request->facilities_ar);
        $listing->includes = $this->normalizeLines($request->includes);
        $listing->includes_ar = $this->normalizeLines($request->includes_ar);
        $listing->excludes = $this->normalizeLines($request->excludes);
        $listing->excludes_ar = $this->normalizeLines($request->excludes_ar);
        $listing->price = $request->price ?? 0;
        $listing->currency = $request->currency;
        $listing->discount = $request->discount;
        $listing->offer_type = $request->offer_type;
        $listing->offer_first_value = $request->offer_first_value;
        $listing->offer_second_value = $request->offer_second_value;
        $listing->offer_text = $request->offer_text;
        $listing->status = $request->status ? 1 : 0;

        if ($request->hasFile('image')) {
            try {
                if ($listing->image && !filter_var($listing->image, FILTER_VALIDATE_URL)) {
                    fileManager()->removeFile(getFilePath('listingImage') . '/' . $listing->image);
                }
                $listing->image = fileUploader($request->image, getFilePath('listingImage'));
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload your image')];
                return back()->withNotify($notify);
            }
        } elseif ($request->image_url) {
            if ($listing->image && !filter_var($listing->image, FILTER_VALIDATE_URL)) {
                fileManager()->removeFile(getFilePath('listingImage') . '/' . $listing->image);
            }
            $listing->image = $request->image_url;
        }

        $listing->save();

        $notify[] = ['success', __('Listing updated successfully')];
        return to_route('admin.listing.index')->withNotify($notify);
    }

    private function normalizeAvailableTimes(?string $availableTimes): array
    {
        if (!$availableTimes) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $availableTimes))
            ->map(fn ($time) => trim($time))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeLines(?string $lines): array
    {
        if (!$lines) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $lines))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    public function statusChange($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->status = $listing->status == 1 ? 0 : 1;
        $listing->save();

        $notify[] = ['success', __('Status changed successfully')];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $listing = Listing::findOrFail($id);

        if ($listing->image) {
            fileManager()->removeFile(getFilePath('listingImage') . '/' . $listing->image);
        }

        $listing->delete();

        $notify[] = ['success', __('Listing deleted successfully')];
        return back()->withNotify($notify);
    }
}
