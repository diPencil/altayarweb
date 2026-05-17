<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OffersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = $this->perPageFromRequest($request);
        $categoryFilter = $this->resolveCategoryFilter($request);

        $query = $this->baseQuery()
            ->when($categoryFilter !== null, fn (Builder $builder) => $this->applyCategoryFilter($builder, $categoryFilter))
            ->when($request->filled('destination'), fn (Builder $builder) => $this->applyDestinationFilter($builder, (string) $request->query('destination')))
            ->when($request->filled('currency'), fn (Builder $builder) => $this->applyCurrencyFilter($builder, (string) $request->query('currency')))
            ->when($request->filled('search'), fn (Builder $builder) => $this->applySearchFilter($builder, (string) $request->query('search')));

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn (Listing $listing) => $this->normalizeOffer($listing))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function featured(Request $request): JsonResponse
    {
        $categoryFilter = $this->resolveCategoryFilter($request);

        $offers = $this->baseQuery()
            ->when($categoryFilter !== null, fn (Builder $builder) => $this->applyCategoryFilter($builder, $categoryFilter))
            ->when($request->filled('destination'), fn (Builder $builder) => $this->applyDestinationFilter($builder, (string) $request->query('destination')))
            ->when($request->filled('currency'), fn (Builder $builder) => $this->applyCurrencyFilter($builder, (string) $request->query('currency')))
            ->when($request->filled('search'), fn (Builder $builder) => $this->applySearchFilter($builder, (string) $request->query('search')))
            ->limit(8)
            ->get()
            ->map(fn (Listing $listing) => $this->normalizeOffer($listing, true))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $listing = $this->baseQuery()
            ->whereKey($id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->normalizeOffer($listing),
        ]);
    }

    public function categories(): JsonResponse
    {
        $counts = Listing::query()
            ->active()
            ->where(function (Builder $query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->selectRaw('listing_type_id, COUNT(*) as aggregate')
            ->groupBy('listing_type_id')
            ->pluck('aggregate', 'listing_type_id')
            ->all();

        $categories = ListingType::active()
            ->orderBy('id')
            ->get()
            ->map(fn (ListingType $listingType) => $this->normalizeCategory($listingType, (int) ($counts[$listingType->id] ?? 0)))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    private function baseQuery(): Builder
    {
        return Listing::query()
            ->with('listingType')
            ->active()
            ->where(function (Builder $query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->orderByDesc('id');
    }

    private function applyCategoryFilter(Builder $query, string $category): Builder
    {
        $normalizedCategory = $this->normalizeSlugValue($category);

        if ($normalizedCategory === '' || in_array($normalizedCategory, ['all', 'limited'], true)) {
            return $query;
        }

        if (ctype_digit($normalizedCategory)) {
            return $query->where('listing_type_id', (int) $normalizedCategory);
        }

        $matchedType = ListingType::active()->get()->first(function (ListingType $listingType) use ($normalizedCategory, $category) {
            return $this->listingTypeSlug($listingType) === $normalizedCategory
                || $this->normalizeSlugValue((string) $listingType->getRawOriginal('name')) === $normalizedCategory
                || trim((string) $listingType->getRawOriginal('name')) === trim((string) $category)
                || trim((string) ($listingType->name_ar ?? '')) === trim((string) $category);
        });

        if (! $matchedType) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('listing_type_id', $matchedType->id);
    }

    private function resolveCategoryFilter(Request $request): ?string
    {
        foreach (['category_id', 'type_id', 'category'] as $key) {
            if ($request->filled($key)) {
                return (string) $request->query($key);
            }
        }

        return null;
    }

    private function applyDestinationFilter(Builder $query, string $destination): Builder
    {
        $destination = trim($destination);

        if ($destination === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($destination) {
            $builder->where('city', 'like', '%' . $destination . '%')
                ->orWhere('country', 'like', '%' . $destination . '%')
                ->orWhere('address', 'like', '%' . $destination . '%');
        });
    }

    private function applyCurrencyFilter(Builder $query, string $currency): Builder
    {
        $currency = strtoupper(trim($currency));
        $allowedCurrencies = ['EGP', 'SAR', 'USD', 'EUR'];

        if (! in_array($currency, $allowedCurrencies, true)) {
            return $query;
        }

        return $query->whereRaw('UPPER(currency) = ?', [$currency]);
    }

    private function applySearchFilter(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search) {
            $builder->where('title', 'like', '%' . $search . '%')
                ->orWhere('title_ar', 'like', '%' . $search . '%')
                ->orWhere('summary', 'like', '%' . $search . '%')
                ->orWhere('summary_ar', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('description_ar', 'like', '%' . $search . '%')
                ->orWhere('city', 'like', '%' . $search . '%')
                ->orWhere('country', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhereHas('listingType', function (Builder $typeQuery) use ($search) {
                    $typeQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('name_ar', 'like', '%' . $search . '%');
                });
        });
    }

    private function normalizeOffer(Listing $listing, bool $isFeatured = false): array
    {
        $durationDays = $listing->durationDays();

        return [
            'id' => (string) $listing->id,
            'title_en' => (string) ($listing->getRawOriginal('title') ?? ''),
            'title_ar' => (string) ($listing->getRawOriginal('title_ar') ?? ''),
            'slug' => (string) ($listing->slug ?? ''),
            'description_en' => (string) ($listing->getRawOriginal('description') ?? ''),
            'description_ar' => (string) ($listing->getRawOriginal('description_ar') ?? ''),
            'summary_en' => (string) ($listing->getRawOriginal('summary') ?? ''),
            'summary_ar' => (string) ($listing->getRawOriginal('summary_ar') ?? ''),
            'image_url' => $listing->image_url,
            'category' => $listing->listingType ? $this->normalizeCategory($listing->listingType) : null,
            'destination' => [
                'city' => (string) ($listing->city ?? ''),
                'country' => (string) ($listing->country ?? ''),
                'address' => (string) ($listing->address ?? ''),
            ],
            'original_price' => (float) $listing->originalPrice(),
            'discounted_price' => (float) $listing->finalPrice(),
            'discount_percentage' => $listing->discountPercent(),
            'currency' => strtoupper((string) ($listing->currency ?? 'EGP')) ?: 'EGP',
            'offer_type' => (string) ($listing->offer_type ?? ''),
            'offer_summary' => $listing->offerSummary() ?: (string) ($listing->getRawOriginal('summary') ?? ''),
            'duration_days' => $durationDays,
            'duration_nights' => $durationDays !== null ? max($durationDays - 1, 0) : null,
            'valid_from' => optional($listing->start_date)->toDateString(),
            'valid_until' => optional($listing->end_date)->toDateString(),
            'includes' => $listing->includesList(),
            'excludes' => $listing->excludesList(),
            'status' => 'ACTIVE',
            'is_featured' => $isFeatured,
            'is_hot' => false,
        ];
    }

    private function normalizeCategory(ListingType $listingType, int $count = 0): array
    {
        return [
            'id' => (string) $listingType->id,
            'name' => (string) ($listingType->name ?? $listingType->getRawOriginal('name') ?? ''),
            'slug' => $this->listingTypeSlug($listingType),
            'count' => $count,
        ];
    }

    private function listingTypeSlug(ListingType $listingType): string
    {
        $normalizedName = $this->normalizeSlugValue((string) $listingType->getRawOriginal('name'));

        return match ($normalizedName) {
            'years-offers', 'year-offers', 'year-offer', 'yearly' => 'yearly',
            'weekend-offers', 'weekend-offer', 'weekend' => 'weekend',
            'spa-beauty-offers', 'spa-beauty-offer', 'spa-beauty', 'spa-and-beauty-offers', 'spa-and-beauty-offer' => 'spa-beauty',
            'coupons' => 'coupons',
            'vouchers' => 'vouchers',
            default => Str::slug((string) $listingType->getRawOriginal('name')),
        };
    }

    private function normalizeSlugValue(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replace(['’', "'"], '')
            ->replace(['&', '_'], ' ')
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->toString();
    }

    private function perPageFromRequest(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 10);

        return min(max($perPage, 1), 100);
    }
}