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
            $perPage      = max(1, min((int) $request->integer('per_page', 30), 100));
            $statusFilter = $request->query('status');

            $query = Listing::query()->whereNotNull('offer_type');

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            /** @var \Illuminate\Pagination\LengthAwarePaginator $offers */
            $offers = $query->orderByDesc('id')->paginate($perPage);

            $mapped = $offers->getCollection()->map(function (Listing $offer): array {
                return [
                    'id'             => $offer->id,
                    'title_en'       => $offer->title,
                    'title_ar'       => $offer->title_ar ?? $offer->title,
                    'offer_type'     => $offer->offer_type,
                    'status'         => $offer->status ?? 'ACTIVE',
                    'original_price' => (float) ($offer->price ?? 0),
                    'created_at'     => optional($offer->created_at)->toISOString(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data'    => $mapped,
                'meta'    => [
                    'current_page' => $offers->currentPage(),
                    'per_page'     => $offers->perPage(),
                    'total'        => $offers->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch offers: ' . $e->getMessage()], 500);
        }
    }
}
