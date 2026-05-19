<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function myPayments(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = Transaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (Transaction $transaction) use ($user): array {
                $method = $this->resolveMethod($transaction);
                $status = (string) ($transaction->trx_type ?? '') === '-' ? 'used' : 'paid';

                return [
                    'id' => $transaction->id,
                    'payment_number' => $transaction->trx,
                    'amount' => (float) abs($transaction->amount ?? 0),
                    'currency' => strtoupper((string) ($user->currency ?? 'EGP')) ?: 'EGP',
                    'status' => $status,
                    'method' => $method,
                    'created_at' => optional($transaction->created_at)->toISOString(),
                    'source' => 'transaction',
                    'description' => $transaction->details ?: $transaction->remark ?: 'Transaction history',
                ];
            })
            ->values();

        return response()->json([
            'items' => $items,
            'total' => $items->count(),
        ]);
    }

    private function resolveMethod(Transaction $transaction): string
    {
        $details = strtolower((string) ($transaction->details ?? ''));
        $remark = strtolower((string) ($transaction->remark ?? ''));

        if (str_contains($details, 'wallet') || str_contains($remark, 'wallet')) {
            return 'wallet';
        }

        if (str_contains($details, 'fawaterk')) {
            return 'fawaterk';
        }

        if (str_contains($details, 'payment')) {
            return 'payment';
        }

        return 'wallet';
    }

    /**
     * Retrieve the status of a specific payment/deposit.
     */
    public function status(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $deposit = Deposit::query()
            ->where(function ($query) use ($user) {
                if (get_class($user) === \App\Models\User::class) {
                    $query->where('user_id', $user->id);
                } elseif (get_class($user) === \App\Models\Employee::class) {
                    $query->where('agent_id', $user->id);
                }
            })
            ->where(function ($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('id', $id)->orWhere('trx', $id);
                } else {
                    $query->where('trx', $id);
                }
            })
            ->first();

        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found',
            ], 404);
        }

        // Normalize status:
        // 0 = Initiated/Pending, 1 = Succeed/Approved, 2 = Pending manual approval, 3 = Rejected
        $status = 'PENDING';
        if ($deposit->status == 1) {
            $status = 'PAID';
        } elseif ($deposit->status == 3) {
            $status = 'FAILED';
        }

        return response()->json([
            'payment_id' => (string) $deposit->id,
            'status' => $status,
            'amount' => (float) $deposit->amount,
            'currency' => strtoupper($deposit->method_currency ?: 'EGP'),
            'paid_at' => $deposit->status == 1 ? optional($deposit->updated_at)->toISOString() : null,
            'error_message' => $deposit->status == 3 ? 'Payment was rejected or failed' : null,
        ]);
    }
}
