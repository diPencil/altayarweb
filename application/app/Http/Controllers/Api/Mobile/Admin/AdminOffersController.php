<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminOffersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $search = trim((string) $request->input('search', ''));
            $statusFilter = trim((string) $request->input('status', ''));
            $categoryFilter = trim((string) $request->input('category', $request->input('category_id', '')));
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', 30)), 100));

            $query = Listing::query()
                ->with('listingType')
                ->whereNotNull('offer_type');

            if ($statusFilter !== '') {
                $query->where('status', $statusFilter);
            }

            if ($categoryFilter !== '') {
                if (ctype_digit($categoryFilter)) {
                    $query->where('listing_type_id', (int) $categoryFilter);
                } else {
                    $query->whereHas('listingType', function ($listingTypeQuery) use ($categoryFilter): void {
                        $listingTypeQuery->where('name', 'like', "%{$categoryFilter}%")
                            ->orWhere('name_ar', 'like', "%{$categoryFilter}%")
                            ->orWhere('slug', 'like', "%{$categoryFilter}%");
                    });
                }
            }

            $offers = $query->orderByDesc('id')
                ->get()
                ->map(function (Listing $offer) use ($search): array {
                    $listingType = $offer->listingType;
                    $payload = [
                        'id' => $offer->id,
                        'title_en' => $offer->title,
                        'title_ar' => $offer->title_ar ?? $offer->title,
                        'offer_type' => $offer->offer_type,
                        'status' => strtoupper((string) ($offer->status ?? 'ACTIVE')),
                        'original_price' => (float) ($offer->price ?? 0),
                        'category_id' => $offer->listing_type_id,
                        'category_name' => $listingType?->name,
                        'category_name_ar' => $listingType?->name_ar,
                        'created_at' => optional($offer->created_at)->toISOString(),
                    ];

                    if ($search !== '') {
                        $haystack = implode(' ', array_filter([
                            $payload['title_en'] ?? '',
                            $payload['title_ar'] ?? '',
                            $payload['offer_type'] ?? '',
                            $payload['category_name'] ?? '',
                            $payload['category_name_ar'] ?? '',
                        ]));

                        if (stripos($haystack, $search) === false) {
                            return [];
                        }
                    }

                    return $payload;
                })
                ->filter()
                ->values()
                ->all();

            $total = count($offers);
            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($offers, $offset, $perPage);

            return response()->json([
                'success' => true,
                'data' => array_values($pageItems),
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => max(1, (int) ceil($total / $perPage)),
                    'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $total),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch offers: ' . $e->getMessage()], 500);
        }
    }
}
