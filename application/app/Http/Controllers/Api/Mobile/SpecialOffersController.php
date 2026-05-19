<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpecialOffersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = $this->perPageFromRequest($request);
        $categoryFilter = $this->resolveCategoryFilter($request);

        $query = $this->baseQuery()
            ->when($categoryFilter !== null, fn (Builder $builder) => $this->applyCategoryFilter($builder, $categoryFilter))
            ->when($request->filled('currency'), fn (Builder $builder) => $this->applyCurrencyFilter($builder, (string) $request->query('currency')))
            ->when($request->filled('search'), fn (Builder $builder) => $this->applySearchFilter($builder, (string) $request->query('search')));

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn (TourPackage $tourPackage) => $this->normalizeOffer($tourPackage))->values(),
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
            ->when($request->filled('currency'), fn (Builder $builder) => $this->applyCurrencyFilter($builder, (string) $request->query('currency')))
            ->when($request->filled('search'), fn (Builder $builder) => $this->applySearchFilter($builder, (string) $request->query('search')))
            ->limit(8)
            ->get()
            ->map(fn (TourPackage $tourPackage) => $this->normalizeOffer($tourPackage, true))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tourPackage = $this->baseQuery()
            ->whereKey($id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->normalizeOffer($tourPackage),
        ]);
    }

    public function categories(): JsonResponse
    {
        $counts = TourPackage::query()
            ->where('user_type', 'admin')
            ->selectRaw('category_id, COUNT(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id')
            ->all();

        $categories = Category::query()
            ->where('status', 1)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Category $category) => $this->normalizeCategory($category, (int) ($counts[$category->id] ?? 0)))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function image(string $filename)
    {
        $filename = basename(trim($filename));

        if ($filename === '') {
            abort(404);
        }

        $relativePath = getFilePath('tourPackageImage') . '/' . $filename;
        $absolutePath = base_path($relativePath);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            abort(404);
        }

        return response()->file($absolutePath);
    }

    private function baseQuery(): Builder
    {
        return TourPackage::query()
            ->with(['category', 'TourPackagePrimaryImage', 'tour_package_images'])
            ->where('user_type', 'admin')
            ->orderByDesc('id');
    }

    private function applyCategoryFilter(Builder $query, string $category): Builder
    {
        $normalizedCategory = $this->normalizeSlugValue($category);

        if ($normalizedCategory === '' || in_array($normalizedCategory, ['all', 'limited'], true)) {
            return $query;
        }

        if (ctype_digit($normalizedCategory)) {
            return $query->where('category_id', (int) $normalizedCategory);
        }

        $matchedCategory = Category::query()->where('status', 1)->get()->first(function (Category $categoryModel) use ($normalizedCategory, $category) {
            return $this->categorySlug($categoryModel) === $normalizedCategory
                || $this->normalizeSlugValue((string) $categoryModel->getRawOriginal('name')) === $normalizedCategory
                || trim((string) $categoryModel->getRawOriginal('name')) === trim((string) $category)
                || trim((string) ($categoryModel->name_ar ?? '')) === trim((string) $category);
        });

        if (! $matchedCategory) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('category_id', $matchedCategory->id);
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
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('description_ar', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhere('address_ar', 'like', '%' . $search . '%')
                ->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                    $categoryQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('name_ar', 'like', '%' . $search . '%');
                });
        });
    }

    private function normalizeOffer(TourPackage $tourPackage, bool $isFeatured = false): array
    {
        $imageUrl = $this->tourPackageImageUrl($tourPackage);
        $category = $tourPackage->category ? $this->normalizeCategory($tourPackage->category) : null;
        $basePrice = (float) ($tourPackage->price_from ?: $tourPackage->price ?: 0);
        $ceilingPrice = (float) ($tourPackage->price_to ?: $tourPackage->price ?: $basePrice);
        $originalPrice = $ceilingPrice > 0 ? $ceilingPrice : $basePrice;
        $discountedPrice = $basePrice > 0 ? $basePrice : $originalPrice;

        return [
            'id' => (string) $tourPackage->id,
            'title_en' => (string) ($tourPackage->getRawOriginal('title') ?? ''),
            'title_ar' => (string) ($tourPackage->getRawOriginal('title_ar') ?? ''),
            'description_en' => (string) ($tourPackage->getRawOriginal('description') ?? ''),
            'description_ar' => (string) ($tourPackage->getRawOriginal('description_ar') ?? ''),
            'image_url' => $imageUrl,
            'category' => $category,
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'currency' => $tourPackage->displayCurrencyCode(),
            'duration_days' => $this->durationDays($tourPackage->day_nights),
            'duration_nights' => $this->durationNights($tourPackage->day_nights),
            'status' => 'ACTIVE',
            'is_featured' => $isFeatured,
        ];
    }

    private function normalizeCategory(Category $category, int $count = 0): array
    {
        return [
            'id' => (string) $category->id,
            'name' => (string) ($category->name ?? $category->getRawOriginal('name') ?? ''),
            'slug' => $this->categorySlug($category),
            'count' => $count,
        ];
    }

    private function categorySlug(Category $category): string
    {
        return Str::slug((string) $category->getRawOriginal('name'));
    }

    private function tourPackageImageUrl(TourPackage $tourPackage): ?string
    {
        $imageName = optional($tourPackage->TourPackagePrimaryImage)->image
            ?? optional($tourPackage->tour_package_images->first())->image
            ?? null;

        if (! filled($imageName)) {
            return null;
        }

        $relativePath = getFilePath('tourPackageImage') . '/' . $imageName;
        $absolutePath = base_path($relativePath);

        if (is_file($absolutePath) && is_readable($absolutePath)) {
            return route('api.mobile.special-offers.image', ['filename' => $imageName]);
        }

        return null;
    }

    private function durationDays(?string $dayNights): ?int
    {
        if (! filled($dayNights)) {
            return null;
        }

        if (preg_match('/(\d+)\s*day/i', $dayNights, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function durationNights(?string $dayNights): ?int
    {
        if (! filled($dayNights)) {
            return null;
        }

        if (preg_match('/(\d+)\s*night/i', $dayNights, $matches)) {
            return (int) $matches[1];
        }

        $days = $this->durationDays($dayNights);

        return $days !== null ? max($days - 1, 0) : null;
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