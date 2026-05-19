<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPaymentsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $deposits = Deposit::query()
                ->with(['user', 'gateway'])
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (Deposit $deposit): array {
                    $status = 'PENDING';
                    if ($deposit->status == 1) {
                        $status = 'PAID';
                    } elseif ($deposit->status == 3) {
                        $status = 'FAILED';
                    }

                    $userObj = null;
                    if ($deposit->user) {
                        $userObj = [
                            'first_name' => $deposit->user->firstname,
                            'last_name' => $deposit->user->lastname ?: '',
                            'email' => $deposit->user->email,
                            'phone' => $deposit->user->mobile,
                            'username' => $deposit->user->username,
                        ];
                    }

                    $method = $deposit->gateway ? $deposit->gateway->name : 'Online Payment';

                    return [
                        'id' => $deposit->id,
                        'user_id' => (string) $deposit->user_id,
                        'user' => $userObj,
                        'amount' => (float) ($deposit->amount ?? 0),
                        'currency' => strtoupper((string) ($deposit->method_currency ?: 'EGP')),
                        'status' => $status,
                        'payment_method' => $method,
                        'created_at' => optional($deposit->created_at)->toISOString(),
                    ];
                });

            return response()->json([
                'items' => $deposits,
                'total' => $deposits->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch admin payments: ' . $e->getMessage()
            ], 500);
        }
    }
}
