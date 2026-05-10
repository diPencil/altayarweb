<?php

namespace App\Http\Controllers\Gateway\Fawaterk;

use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    /*
     * Fawaterk Gateway Process
     */
    public static function process($deposit)
    {
        $gatewayCurrency = $deposit->gatewayCurrency();
        if (!$gatewayCurrency) {
            return json_encode([
                'error' => true,
                'message' => 'Fawaterk configuration not found.',
            ]);
        }

        $fawaterkAcc = json_decode($gatewayCurrency->gateway_parameter);
        $apiKey = data_get($fawaterkAcc, 'api_key.value') ?: data_get($fawaterkAcc, 'api_key');
        $vendorKey = data_get($fawaterkAcc, 'provider_key.value')
            ?: data_get($fawaterkAcc, 'provider_key')
            ?: data_get($fawaterkAcc, 'vendor_key.value')
            ?: data_get($fawaterkAcc, 'vendor_key')
            ?: $apiKey;
        if (!$apiKey) {
            return json_encode([
                'error' => true,
                'message' => 'Fawaterk API key is missing.',
            ]);
        }

        $user = $deposit->user;
        $paymentFlow = data_get($deposit->detail, 'payment_flow');
        $returnUrl = $paymentFlow === 'epayment'
            ? route('e.payment.result', ['trx' => $deposit->trx])
            : route(gatewayRedirectUrl(true));

        $url = "https://app.fawaterk.com/api/v2/createInvoiceLink";
        
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $payload = [
            "cartTotal" => round($deposit->final_amo, 2),
            "currency" => $deposit->method_currency,
            "customer" => [
                "first_name" => $user ? ($user->firstname ?? 'Customer') : ($deposit->guest_name ?? 'Guest'),
                "last_name" => $user ? ($user->lastname ?? 'Name') : 'Payer',
                "email" => $user ? $user->email : ($deposit->guest_email ?? 'guest@example.com'),
                "phone" => $user ? ($user->mobile ?? $user->phone ?? '01000000000') : ($deposit->guest_phone ?? '01000000000'),
                "address" => "Default",
            ],
            "cartItems" => [
                [
                    "name" => (($paymentFlow === 'epayment') ? 'E-Payment ' : 'Booking Payment ') . $deposit->trx,
                    "price" => round($deposit->final_amo, 2),
                    "quantity" => 1,
                ]
            ],
            "redirectionUrls" => [
                "successUrl" => $returnUrl,
                "failUrl" => $returnUrl,
                "pendingUrl" => $returnUrl,
                "webhookUrl" => route('ipn.FawaterkJson'),
            ],
            "sendEmail" => false,
            "sendSMS" => false,
        ];

        try {
            $response = Http::acceptJson()->withHeaders($headers)->timeout(30)->post($url, $payload);
            $result = $response->json();

            $invoiceId = data_get($result, 'data.invoiceId')
                ?: data_get($result, 'data.invoice_id');
            $invoiceKey = data_get($result, 'data.invoiceKey')
                ?: data_get($result, 'data.invoice_key');
            $redirectUrl = data_get($result, 'data.url')
                ?: data_get($result, 'data.invoice_url')
                ?: data_get($result, 'data.checkout_url');

            if ($response->successful() && Str::lower((string) data_get($result, 'status')) === 'success' && $invoiceId && $redirectUrl) {
                $deposit->btc_wallet = $invoiceId;
                $deposit->detail = array_merge((array) ($deposit->detail ?? []), [
                    'payment_flow' => $paymentFlow ?: 'booking',
                    'gateway_invoice_id' => $invoiceId,
                    'gateway_invoice_key' => $invoiceKey,
                    'gateway_invoice_url' => $redirectUrl,
                    'gateway_status' => 'pending',
                    'gateway_vendor_key' => $vendorKey,
                ]);
                $deposit->status = 2;
                $deposit->save();

                $send['redirect'] = true;
                $send['redirect_url'] = $redirectUrl;
            } else {
                PaymentController::markDepositFailed($deposit, data_get($result, 'message') ?? 'Fawaterk Error: Unable to create invoice.');
                $send['error'] = true;
                $send['message'] = $result['message'] ?? 'Fawaterk Error: Unable to create invoice.';
            }
        } catch (\Exception $e) {
            PaymentController::markDepositFailed($deposit, $e->getMessage());
            $send['error'] = true;
            $send['message'] = $e->getMessage();
        }

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $data = $request->all();
        $invoiceId = data_get($data, 'invoice_id')
            ?: data_get($data, 'invoiceId')
            ?: data_get($data, 'invoiceID')
            ?: data_get($data, 'data.invoice_id');
        $status = Str::lower((string) (data_get($data, 'invoice_status')
            ?: data_get($data, 'status')
            ?: data_get($data, 'payment_status')
            ?: data_get($data, 'data.invoice_status')));
        $amount = data_get($data, 'amount')
            ?: data_get($data, 'invoice_total')
            ?: data_get($data, 'cartTotal')
            ?: data_get($data, 'total');
        $currency = data_get($data, 'currency')
            ?: data_get($data, 'invoice_currency')
            ?: data_get($data, 'data.currency');
        $invoiceKey = data_get($data, 'invoice_key')
            ?: data_get($data, 'invoiceKey')
            ?: data_get($data, 'data.invoice_key');
        $paymentMethod = data_get($data, 'payment_method')
            ?: data_get($data, 'paymentMethod')
            ?: data_get($data, 'data.payment_method');
        $hashKey = data_get($data, 'hashKey') ?: data_get($data, 'hash_key');

        if (!$invoiceId) {
            return response()->json(['status' => 'invalid', 'message' => 'Missing invoice reference.'], 422);
        }

        $deposit = Deposit::with(['user', 'tour_booking', 'service_booking'])->where('btc_wallet', $invoiceId)->first();
        if (!$deposit) {
            return response()->json(['status' => 'invalid', 'message' => 'Deposit not found.'], 404);
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        $gatewayConfig = json_decode((string) ($gatewayCurrency->gateway_parameter ?? '{}'));
        $apiKeyForLookup = data_get($gatewayConfig, 'api_key.value')
            ?: data_get($gatewayConfig, 'api_key');
        $secretKey = data_get($deposit->detail, 'gateway_vendor_key')
            ?: data_get($deposit->detail, 'gateway_provider_key')
            ?: data_get($gatewayConfig, 'provider_key.value')
            ?: data_get($gatewayConfig, 'provider_key')
            ?: data_get($gatewayConfig, 'vendor_key.value')
            ?: data_get($gatewayConfig, 'vendor_key')
            ?: data_get($gatewayConfig, 'api_key.value')
            ?: data_get($gatewayConfig, 'api_key');

        if ($invoiceKey && $paymentMethod && $secretKey) {
            $expectedHash = hash_hmac('sha256', 'InvoiceId=' . $invoiceId . '&InvoiceKey=' . $invoiceKey . '&PaymentMethod=' . $paymentMethod, (string) $secretKey, false);
            if (!$hashKey || !hash_equals($expectedHash, (string) $hashKey)) {
                return response()->json(['status' => 'invalid', 'message' => 'Invalid webhook signature.'], 403);
            }
        }

        if ($deposit->status == 1) {
            return response()->json(['status' => 'success']);
        }

        if ($currency && Str::upper((string) $currency) !== Str::upper((string) $deposit->method_currency)) {
            PaymentController::markDepositFailed($deposit, 'Currency mismatch returned from gateway.');
            return response()->json(['status' => 'failed', 'message' => 'Currency mismatch.'], 422);
        }

        if ($amount !== null && abs((float) $amount - (float) $deposit->final_amo) > 0.01) {
            PaymentController::markDepositFailed($deposit, 'Amount mismatch returned from gateway.');
            return response()->json(['status' => 'failed', 'message' => 'Amount mismatch.'], 422);
        }

        if (in_array($status, ['paid', 'success', 'succeeded', 'completed', 'complete', 'captured'], true)) {
            if (!$this->confirmInvoiceFromGateway($deposit, $invoiceId, $apiKeyForLookup)) {
                PaymentController::markDepositFailed($deposit, 'Gateway verification failed.');
                return response()->json(['status' => 'failed', 'message' => 'Gateway verification failed.'], 422);
            }

            PaymentController::userDataUpdate($deposit);
            return response()->json(['status' => 'success']);
        }

        if (in_array($status, ['failed', 'fail', 'canceled', 'cancelled', 'rejected', 'expired', 'void'], true)) {
            PaymentController::markDepositFailed($deposit, data_get($data, 'message') ?: 'Payment declined by the gateway.');
            return response()->json(['status' => 'failed']);
        }

        if (in_array($status, ['pending', 'processing', 'initiated'], true)) {
            if ($deposit->status != 1) {
                $deposit->status = 2;
                $deposit->detail = array_merge((array) ($deposit->detail ?? []), [
                    'gateway_status' => $status,
                    'gateway_callback' => $data,
                ]);
                $deposit->save();
            }

            return response()->json(['status' => 'pending']);
        }

        $deposit->detail = array_merge((array) ($deposit->detail ?? []), [
            'gateway_callback' => $data,
            'gateway_status' => $status ?: 'unknown',
        ]);
        $deposit->save();

        return response()->json(['status' => 'ignored']);
    }

    protected function confirmInvoiceFromGateway(Deposit $deposit, $invoiceId, ?string $apiKey): bool
    {
        if (!$apiKey) {
            return false;
        }

        try {
            $response = Http::acceptJson()->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(30)->get('https://app.fawaterk.com/api/v2/getInvoiceData/' . $invoiceId);

            if (!$response->successful()) {
                return false;
            }

            $result = $response->json();
            return Str::lower((string) data_get($result, 'status')) === 'success'
                && (int) data_get($result, 'data.paid') === 1
                && (string) data_get($result, 'data.currency') === (string) $deposit->method_currency
                && abs((float) data_get($result, 'data.total') - (float) $deposit->final_amo) <= 0.01;
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
