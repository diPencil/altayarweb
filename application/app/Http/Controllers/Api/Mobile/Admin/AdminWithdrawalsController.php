<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminWithdrawalsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $statusFilter = $request->query('status'); // PENDING|APPROVED|REJECTED
            $perPage = max(1, min((int) $request->integer('per_page', 30), 100));

            $query = Withdrawal::query()->with('user:id,firstname,lastname,email,username,mobile');

            if ($statusFilter) {
                $statusMap = ['PENDING' => 2, 'APPROVED' => 1, 'REJECTED' => 3];
                if (isset($statusMap[$statusFilter])) {
                    $query->where('status', $statusMap[$statusFilter]);
                }
            }

            /** @var \Illuminate\Pagination\LengthAwarePaginator $withdrawals */
            $withdrawals = $query->orderByDesc('id')->paginate($perPage);

            $statusLabels = [1 => 'APPROVED', 2 => 'PENDING', 3 => 'REJECTED'];

            $mapped = $withdrawals->getCollection()->map(function (Withdrawal $w) use ($statusLabels): array {
                return [
                    'id'             => $w->id,
                    'trx'            => $w->trx,
                    'user_id'        => (string) ($w->user_id ?? ''),
                    'user_name'      => $w->user ? trim($w->user->firstname . ' ' . $w->user->lastname) : null,
                    'user_email'     => $w->user?->email,
                    'user_phone'     => $w->user?->mobile,
                    'amount'         => (float) ($w->amount ?? 0),
                    'charge'         => (float) ($w->charge ?? 0),
                    'after_charge'   => (float) ($w->after_charge ?? 0),
                    'status'         => $statusLabels[$w->status] ?? 'PENDING',
                    'method_name'    => $w->withdraw_information?->method_name ?? null,
                    'created_at'     => optional($w->created_at)->toISOString(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data'    => $mapped,
                'meta'    => [
                    'current_page' => $withdrawals->currentPage(),
                    'per_page'     => $withdrawals->perPage(),
                    'total'        => $withdrawals->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch withdrawal requests: ' . $e->getMessage()], 500);
        }
    }
}
