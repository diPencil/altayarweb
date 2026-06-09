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

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['nullable', 'string'],
            'payment_method_id' => ['nullable', 'integer'],
            'booking_id' => ['nullable', 'integer'],
            'order_id' => ['nullable', 'integer'],
            'customer_first_name' => ['required', 'string'],
            'customer_last_name' => ['nullable', 'string'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'save_card' => ['nullable', 'boolean'],
        ]);

        $currency = strtoupper($request->input('currency', 'EGP'));
        $paymentMethodId = $request->input('payment_method_id', 2); // Default to 2 (Fawry)

        // Find gateway currency
        $gatewayCurrency = \App\Models\GatewayCurrency::with('method')
            ->whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })
            ->where('method_code', $paymentMethodId)
            ->where('currency', $currency)
            ->first();

        if (!$gatewayCurrency) {
            $gatewayCurrency = \App\Models\GatewayCurrency::with('method')
                ->whereHas('method', function ($gate) {
                    $gate->where('status', 1)->where('alias', 'Fawaterk');
                })
                ->where('currency', $currency)
                ->first();
        }

        if (!$gatewayCurrency) {
            $gatewayCurrency = \App\Models\GatewayCurrency::with('method')
                ->whereHas('method', function ($gate) {
                    $gate->where('status', 1)->where('alias', 'Fawaterk');
                })
                ->first();
        }

        if (!$gatewayCurrency) {
            return response()->json(['message' => 'The payment gateway is currently unavailable.'], 422);
        }

        $amount = round((float) $request->amount, 2);
        $charge = round($gatewayCurrency->fixed_charge + ($amount * $gatewayCurrency->percent_charge / 100), 2);
        $payable = round($amount + $charge, 2);
        $finalAmo = round($payable * $gatewayCurrency->rate, 2);

        $deposit = new Deposit();
        $deposit->user_id = $user->id;
        $deposit->method_code = $gatewayCurrency->method_code;
        $deposit->method_currency = strtoupper($gatewayCurrency->currency);
        $deposit->amount = $amount;
        $deposit->charge = $charge;
        $deposit->rate = $gatewayCurrency->rate;
        $deposit->final_amo = $finalAmo;
        $deposit->btc_amo = 0;
        $deposit->btc_wallet = '';
        $deposit->trx = getTrx();
        $deposit->try = 0;
        $deposit->status = 0; // Initiated

        $paymentFlow = 'epayment';
        $tourBookingId = null;
        $serviceBookingId = null;
        $invoiceId = null;

        // Check for Booking contexts
        if ($request->filled('booking_id')) {
            $bookingId = $request->booking_id;
            
            // Check if TourBooking exists
            $tourBooking = \App\Models\TourBooking::where('user_id', $user->id)->find($bookingId);
            if ($tourBooking) {
                $tourBookingId = $tourBooking->id;
                $paymentFlow = 'booking';
            } else {
                // Check if ServiceBooking exists
                $serviceBooking = \App\Models\ServiceBooking::where('user_id', $user->id)->find($bookingId);
                if ($serviceBooking) {
                    $serviceBookingId = $serviceBooking->id;
                    $paymentFlow = 'booking';
                }
            }
        }

        // Check for Invoice/Order contexts
        if ($request->filled('order_id')) {
            $invoice = \App\Models\Invoice::where('user_id', $user->id)->find($request->order_id);
            if ($invoice) {
                $invoiceId = $invoice->id;
                $paymentFlow = 'invoice';
            }
        }

        $deposit->tour_booking_id = $tourBookingId;
        $deposit->service_booking_id = $serviceBookingId;
        $deposit->guest_name = trim($request->customer_first_name . ' ' . $request->input('customer_last_name', ''));
        $deposit->guest_email = $request->customer_email;
        $deposit->guest_phone = $request->input('customer_phone', $user->mobile ?? 'Not provided');
        $deposit->payment_purpose = $request->input('description', "Mobile Payment - $amount $currency");
        $deposit->booking_reference = $deposit->trx;
        
        $deposit->detail = (object) [
            'payment_flow' => $paymentFlow,
            'invoice_id' => $invoiceId,
            'source' => $paymentFlow === 'booking' ? 'mobile_booking' : ($paymentFlow === 'invoice' ? 'mobile_invoice' : 'mobile_deposit'),
            'note' => $request->input('description', 'Submitted from mobile app'),
        ];

        $deposit->save();

        $dirName = $gatewayCurrency->method?->alias ?: 'Fawaterk';
        $processController = 'App\\Http\\Controllers\\Gateway\\' . $dirName . '\\ProcessController';
        
        try {
            $response = json_decode($processController::process($deposit));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Payment processor error: ' . $e->getMessage()], 500);
        }

        if (isset($response->error)) {
            return response()->json(['message' => $response->message ?? 'The payment gateway is currently unavailable.'], 422);
        }

        // Fetch refreshed/saved deposit to get fawaterk info
        $deposit->refresh();
        $detail = (object) ($deposit->detail ?? []);

        return response()->json([
            'payment_id' => $deposit->trx,
            'payment_number' => $deposit->trx,
            'payment_url' => route('payment.pay', $deposit->trx),
            'invoice_id' => (string) ($detail->gateway_invoice_id ?? ''),
            'invoice_key' => (string) ($detail->gateway_invoice_key ?? ''),
            'status' => 'PENDING',
        ]);
    }

    public function quickPay(Request $request): JsonResponse
    {
        return $this->create($request);
    }
}
