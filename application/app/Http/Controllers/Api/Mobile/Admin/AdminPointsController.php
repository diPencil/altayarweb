<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPointsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = max(1, min((int) $request->integer('per_page', 30), 100));

            /** @var \Illuminate\Pagination\LengthAwarePaginator $transactions */
            $transactions = MembershipPointTransaction::query()
                ->with('user:id,firstname,lastname,email,username')
                ->orderByDesc('id')
                ->paginate($perPage);

            $mapped = $transactions->getCollection()->map(function (MembershipPointTransaction $t): array {
                return [
                    'id'            => $t->id,
                    'trx'           => $t->trx,
                    'user_id'       => (string) $t->user_id,
                    'user_name'     => $t->user ? trim($t->user->firstname . ' ' . $t->user->lastname) : null,
                    'user_email'    => $t->user?->email,
                    'points'        => (int) ($t->points ?? 0),
                    'balance_after' => (int) ($t->balance_after ?? 0),
                    'type'          => $t->type,
                    'remark'        => $t->remark,
                    'created_at'    => optional($t->created_at)->toISOString(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data'    => $mapped,
                'meta'    => [
                    'current_page' => $transactions->currentPage(),
                    'per_page'     => $transactions->perPage(),
                    'total'        => $transactions->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch points transactions: ' . $e->getMessage()], 500);
        }
    }
}
