<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminWalletsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Return recent wallet transactions globally (admin view)
            $transactions = Transaction::query()
                ->with('user:id,firstname,lastname,email,username')
                ->whereIn('remark', ['deposit', 'withdraw', 'wallet_deposit', 'wallet_withdraw', 'admin_deposit', 'admin_withdraw'])
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (Transaction $t): array {
                    return [
                        'id'            => $t->id,
                        'trx'           => $t->trx,
                        'amount'        => (float) ($t->amount ?? 0),
                        'charge'        => (float) ($t->charge ?? 0),
                        'currency'      => strtoupper((string) ($t->currency ?? 'EGP')),
                        'type'          => $t->type == '+' ? 'CREDIT' : 'DEBIT',
                        'remark'        => $t->remark,
                        'details'       => $t->details,
                        'user_id'       => (string) $t->user_id,
                        'user_name'     => $t->user ? trim($t->user->firstname . ' ' . $t->user->lastname) : null,
                        'user_email'    => $t->user?->email,
                        'created_at'    => optional($t->created_at)->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $transactions,
                'meta'    => ['total' => $transactions->count()],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch wallet transactions: ' . $e->getMessage()], 500);
        }
    }
}
