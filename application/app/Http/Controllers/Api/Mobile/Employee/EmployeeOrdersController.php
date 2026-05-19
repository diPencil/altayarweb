<?php

namespace App\Http\Controllers\Api\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeOrdersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $employeeId = $request->user()->id;

        try {
            $invoices = Invoice::query()
                ->whereHas('user', function ($query) use ($employeeId) {
                    $query->where('agent_id', $employeeId);
                })
                ->with('user')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (Invoice $invoice): array {
                    $status = match ((int) $invoice->status) {
                        1 => 'PAID',
                        2 => 'PENDING',
                        3 => 'CANCELLED',
                        default => 'PENDING',
                    };
                    $currency = strtoupper((string) ($invoice->currency ?? 'EGP')) ?: 'EGP';

                    $userObj = null;
                    if ($invoice->user) {
                        $userObj = [
                            'first_name' => $invoice->user->firstname,
                            'last_name' => $invoice->user->lastname ?: '',
                            'email' => $invoice->user->email,
                            'phone' => $invoice->user->mobile,
                            'username' => $invoice->user->username,
                        ];
                    }

                    return [
                        'id' => $invoice->id,
                        'order_number' => $invoice->invoice_number,
                        'is_free' => false,
                        'status' => $status,
                        'payment_status' => $status,
                        'total_amount' => (float) ($invoice->total_amount ?? 0),
                        'currency' => $currency,
                        'user_id' => (string) $invoice->user_id,
                        'user' => $userObj,
                        'created_at' => optional($invoice->created_at)->toISOString(),
                    ];
                });

            return response()->json($invoices);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch employee orders: ' . $e->getMessage()
            ], 500);
        }
    }
}
