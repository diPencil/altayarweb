<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'balance' => number_format((float) ($user->balance ?? 0), 2, '.', ''),
                'currency' => $this->currencyText(),
                'currency_symbol' => $this->currencySymbol(),
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $transactions = Transaction::where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (Transaction $transaction) use ($user) {
                $amount = (float) ($transaction->amount ?? 0);
                $isDebit = (string) ($transaction->trx_type ?? '') === '-';

                return [
                    'id' => $transaction->id,
                    'amount' => number_format(abs($amount), 2, '.', ''),
                    'transaction_type' => $isDebit ? 'WITHDRAWAL' : 'DEPOSIT',
                    'description' => $transaction->details ?: 'Wallet transaction',
                    'description_en' => $transaction->details ?: 'Wallet transaction',
                    'description_ar' => $transaction->details ?: 'معاملة المحفظة',
                    'currency' => $this->currencyText(),
                    'currency_symbol' => $this->currencySymbol(),
                    'created_at' => optional($transaction->created_at)->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    private function currencyText(): string
    {
        return strtoupper((string) (gs()->cur_text ?? 'USD')) ?: 'USD';
    }

    private function currencySymbol(): string
    {
        return (string) (gs()->cur_sym ?? '$');
    }
}