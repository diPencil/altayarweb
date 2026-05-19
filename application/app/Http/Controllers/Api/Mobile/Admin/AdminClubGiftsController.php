<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipCashbackTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminClubGiftsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = max(1, min((int) $request->integer('per_page', 30), 100));

            $records = MembershipCashbackTransaction::query()
                ->with('user:id,firstname,lastname,email,username')
                ->orderByDesc('id')
                ->paginate($perPage);

            $mapped = $records->getCollection()->map(function (MembershipCashbackTransaction $r): array {
                return [
                    'id'            => $r->id,
                    'trx'           => $r->trx,
                    'user_id'       => (string) $r->user_id,
                    'user_name'     => $r->user ? trim($r->user->firstname . ' ' . $r->user->lastname) : null,
                    'user_email'    => $r->user?->email,
                    'amount'        => (float) ($r->amount ?? 0),
                    'balance_after' => (float) ($r->balance_after ?? 0),
                    'type'          => $r->type,
                    'remark'        => $r->remark,
                    'created_at'    => optional($r->created_at)->toISOString(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data'    => $mapped,
                'meta'    => [
                    'current_page' => $records->currentPage(),
                    'per_page'     => $records->perPage(),
                    'total'        => $records->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch club gifts: ' . $e->getMessage()], 500);
        }
    }
}
