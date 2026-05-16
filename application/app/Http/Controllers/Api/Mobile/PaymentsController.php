<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
}
