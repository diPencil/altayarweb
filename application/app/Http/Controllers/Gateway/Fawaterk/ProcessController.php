<?php

namespace App\Http\Controllers\Gateway\Fawaterk;

use App\Models\Deposit;
use App\Models\PaymentGatewayLog;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

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
        $paidCurrency = data_get($data, 'paidCurrency')
            ?: data_get($data, 'paid_currency')
            ?: data_get($data, 'data.paidCurrency')
            ?: data_get($data, 'data.paid_currency');
        $currency = data_get($data, 'currency')
            ?: data_get($data, 'invoice_currency')
            ?: data_get($data, 'data.currency')
            ?: $paidCurrency;
        $invoiceKey = data_get($data, 'invoice_key')
            ?: data_get($data, 'invoiceKey')
            ?: data_get($data, 'data.invoice_key');
        $paymentMethod = data_get($data, 'payment_method')
            ?: data_get($data, 'paymentMethod')
            ?: data_get($data, 'data.payment_method');
        $hashKey = data_get($data, 'hashKey') ?: data_get($data, 'hash_key');
        $referenceId = data_get($data, 'referenceId')
            ?: data_get($data, 'referenceNumber')
            ?: data_get($data, 'reference_id')
            ?: data_get($data, 'data.referenceId')
            ?: data_get($data, 'data.referenceNumber')
            ?: data_get($data, 'data.reference_id');
        $transactionId = data_get($data, 'transactionId')
            ?: data_get($data, 'transaction_id')
            ?: data_get($data, 'data.transactionId')
            ?: data_get($data, 'data.transaction_id');
        $transactionKey = data_get($data, 'transactionKey')
            ?: data_get($data, 'transaction_key')
            ?: data_get($data, 'data.transactionKey')
            ?: data_get($data, 'data.transaction_key');

        $audit = $this->fawaterkAuditData($request, [
            'invoice_id' => $invoiceId,
            'invoice_key' => $invoiceKey,
            'reference_id' => $referenceId,
            'transaction_id' => $transactionId,
            'transaction_key' => $transactionKey,
            'paid_currency' => $paidCurrency,
            'decision' => 'received',
        ]);

        try {
            $failureReason = $this->fawaterkFailureReason($data, $status);

            if (!$invoiceId) {
                if ($this->isReferenceOnlyCancellationPayload($data, $status, $referenceId, $transactionId, $transactionKey)) {
                    $this->writeFawaterkAuditLog($audit, null, 'unmatched_cancellation_reference', 'Cancellation/expired webhook has no invoice id; referenceId=' . ($referenceId ?: 'n/a'));
                    return response()->json(['status' => 'ignored', 'message' => 'Cancellation reference received without invoice id.']);
                }

                $this->writeFawaterkAuditLog($audit, null, 'missing_reference', 'Missing invoice reference.');
                return response()->json(['status' => 'invalid', 'message' => 'Missing invoice reference.'], 422);
            }

            $matchingDeposits = $this->matchingDepositsForInvoice($invoiceId, [0, 2]);
            if ($matchingDeposits->isEmpty()) {
                $matchingDeposits = $this->matchingDepositsForInvoice($invoiceId, [1, 3]);
            }

            if ($matchingDeposits->isEmpty()) {
                $this->writeFawaterkAuditLog($audit, null, 'deposit_not_found', 'Deposit not found.');
                return response()->json(['status' => 'invalid', 'message' => 'Deposit not found.'], 404);
            }

            if ($matchingDeposits->count() > 1) {
                $candidates = $this->describeDepositCandidates($matchingDeposits);
                $this->writeFawaterkAuditLog($audit, null, 'ambiguous_invoice_match', 'Multiple active deposits match invoice: ' . json_encode($candidates));
                return response()->json(['status' => 'invalid', 'message' => 'Ambiguous invoice match.'], 409);
            }

            $deposit = $matchingDeposits->first();
            $audit['local_status_before'] = $deposit->status;

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
            $failureVerifiedByGateway = false;

            if ($invoiceKey && $paymentMethod && $secretKey) {
                $expectedHash = $this->expectedInvoiceHash($invoiceId, $invoiceKey, $paymentMethod, $secretKey);
                if ($hashKey && !hash_equals($expectedHash, (string) $hashKey)) {
                    $this->writeFawaterkAuditLog($audit, $deposit, 'verification_failed', 'Invalid webhook signature.');
                    return response()->json(['status' => 'invalid', 'message' => 'Invalid webhook signature.'], 403);
                }

                if (!$hashKey) {
                    if (!$failureReason) {
                        $this->writeFawaterkAuditLog($audit, $deposit, 'verification_failed', 'Missing webhook signature.');
                        return response()->json(['status' => 'invalid', 'message' => 'Missing webhook signature.'], 403);
                    }

                    $verifiedFailureReason = $this->verifiedFailureReasonFromGateway($invoiceId, $apiKeyForLookup);
                    if (!$verifiedFailureReason) {
                        $this->writeFawaterkAuditLog($audit, $deposit, 'verification_failed', 'Failed webhook missing signature and gateway verification did not confirm failure.');
                        return response()->json(['status' => 'invalid', 'message' => 'Gateway verification failed.'], 422);
                    }

                    $failureReason = $verifiedFailureReason;
                    $failureVerifiedByGateway = true;
                }
            }

            if ($deposit->status == 1) {
                $this->writeFawaterkAuditLog($audit, $deposit, 'ignored_already_paid', 'Deposit already successful.');
                return response()->json(['status' => 'success']);
            }

            if ($deposit->status == 3) {
                $this->writeFawaterkAuditLog($audit, $deposit, 'duplicate_failed', 'Deposit already failed.');
                return response()->json(['status' => 'failed']);
            }

            if ($failureReason && !$failureVerifiedByGateway && (!$hashKey || !$invoiceKey || !$paymentMethod || !$secretKey)) {
                $verifiedFailureReason = $this->verifiedFailureReasonFromGateway($invoiceId, $apiKeyForLookup);
                if (!$verifiedFailureReason) {
                    $this->writeFawaterkAuditLog($audit, $deposit, 'verification_failed', 'Failed webhook signature could not be verified and gateway verification did not confirm failure.');
                    return response()->json(['status' => 'invalid', 'message' => 'Gateway verification failed.'], 422);
                }

                $failureReason = $verifiedFailureReason;
                $failureVerifiedByGateway = true;
            }

            if ($currency && Str::upper((string) $currency) !== Str::upper((string) $deposit->method_currency)) {
                PaymentController::markDepositFailed($deposit, 'Currency mismatch returned from gateway.');
                $this->writeFawaterkAuditLog($audit, $deposit, 'failed', 'Currency mismatch.');
                return response()->json(['status' => 'failed', 'message' => 'Currency mismatch.'], 422);
            }

            if ($amount !== null && abs((float) $amount - (float) $deposit->final_amo) > 0.01) {
                PaymentController::markDepositFailed($deposit, 'Amount mismatch returned from gateway.');
                $this->writeFawaterkAuditLog($audit, $deposit, 'failed', 'Amount mismatch.');
                return response()->json(['status' => 'failed', 'message' => 'Amount mismatch.'], 422);
            }

            if (in_array($status, ['paid', 'success', 'succeeded', 'completed', 'complete', 'captured'], true)) {
                if (!$this->confirmInvoiceFromGateway($deposit, $invoiceId, $apiKeyForLookup)) {
                    PaymentController::markDepositFailed($deposit, 'Gateway verification failed.');
                    $this->writeFawaterkAuditLog($audit, $deposit, 'verification_failed', 'Gateway verification failed.');
                    return response()->json(['status' => 'failed', 'message' => 'Gateway verification failed.'], 422);
                }

                PaymentController::userDataUpdate($deposit);
                $this->writeFawaterkAuditLog($audit, $deposit, 'paid', 'Payment verified and marked successful.');
                return response()->json(['status' => 'success']);
            }

            if ($failureReason) {
                $deposit->detail = array_merge((array) ($deposit->detail ?? []), [
                    'gateway_status' => $status ?: 'failed',
                    'gateway_error_message' => $failureReason,
                    'gateway_callback' => $data,
                    'gateway_failed_at' => now()->toDateTimeString(),
                    'gateway_reference_id' => $referenceId,
                    'gateway_transaction_id' => $transactionId,
                    'gateway_transaction_key' => $transactionKey,
                ]);
                $deposit->save();

                $message = 'Gateway reported failed/rejected/expired payment: ' . $failureReason;
                PaymentController::markDepositFailed($deposit, $message);
                $this->writeFawaterkAuditLog($audit, $deposit, 'failed', $message);
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

                $this->writeFawaterkAuditLog($audit, $deposit, 'pending', 'Gateway reported pending status.');
                return response()->json(['status' => 'pending']);
            }

            $deposit->detail = array_merge((array) ($deposit->detail ?? []), [
                'gateway_callback' => $data,
                'gateway_status' => $status ?: 'unknown',
            ]);
            $deposit->save();

            $this->writeFawaterkAuditLog($audit, $deposit, 'ignored', 'Gateway status ignored: ' . ($status ?: 'unknown'));
            return response()->json(['status' => 'ignored']);
        } catch (Throwable $throwable) {
            $this->writeFawaterkAuditLog($audit, isset($deposit) ? $deposit : null, 'exception', $throwable->getMessage());

            throw $throwable;
        }
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

    protected function verifiedFailureReasonFromGateway($invoiceId, ?string $apiKey): ?string
    {
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::acceptJson()->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(30)->get('https://app.fawaterk.com/api/v2/getInvoiceData/' . $invoiceId);

            if (!$response->successful()) {
                return null;
            }

            $result = $response->json();
            $gatewayStatus = Str::lower((string) (data_get($result, 'data.invoice_status')
                ?: data_get($result, 'data.status')
                ?: data_get($result, 'status')));

            return $this->fawaterkFailureReason($result, $gatewayStatus);
        } catch (\Throwable $throwable) {
            return null;
        }
    }

    protected function fawaterkAuditData(Request $request, array $references): array
    {
        return array_merge([
            'gateway' => 'Fawaterk',
            'event_type' => 'webhook',
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ], $references);
    }

    protected function expectedInvoiceHash($invoiceId, $invoiceKey, $paymentMethod, $secretKey): string
    {
        return hash_hmac('sha256', 'InvoiceId=' . $invoiceId . '&InvoiceKey=' . $invoiceKey . '&PaymentMethod=' . $paymentMethod, (string) $secretKey, false);
    }

    protected function expectedCancellationHash($referenceId, $paymentMethod, $secretKey): string
    {
        return hash_hmac('sha256', 'referenceId=' . $referenceId . '&PaymentMethod=' . $paymentMethod, (string) $secretKey, false);
    }

    protected function isReferenceOnlyCancellationPayload(array $data, string $status, $referenceId, $transactionId, $transactionKey): bool
    {
        if (!$referenceId && !$transactionId && !$transactionKey) {
            return false;
        }

        $reason = $this->fawaterkFailureReason($data, $status);

        return $reason !== null && Str::contains(Str::lower($reason), [
            'cancelled',
            'canceled',
            'expired',
        ]);
    }

    protected function matchingDepositsForInvoice($invoiceId, array $statuses = [0, 2])
    {
        return Deposit::with(['user', 'tour_booking', 'service_booking'])
            ->whereIn('status', $statuses)
            ->where(function ($query) use ($invoiceId) {
                $query->where('btc_wallet', $invoiceId)
                    ->orWhere('detail->gateway_invoice_id', $invoiceId)
                    ->orWhere('detail->gateway_invoice_id', (string) $invoiceId);
            })
            ->get();
    }

    protected function fawaterkFailureReason(array $data, string $status): ?string
    {
        $values = [
            data_get($data, 'errorMessage'),
            data_get($data, 'response.gatewayCode'),
            data_get($data, 'response.gatewayMessage'),
            data_get($data, 'message'),
            $status,
            data_get($data, 'status'),
            data_get($data, 'invoice_status'),
            data_get($data, 'payment_status'),
        ];

        $keywords = [
            'failed',
            'fail',
            'rejected',
            'reject',
            'declined',
            'decline',
            'cancelled',
            'canceled',
            'expired',
            'void',
            'error',
            'unsuccessful',
        ];

        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $normalized = Str::lower((string) $value);
            foreach ($keywords as $keyword) {
                if (Str::contains($normalized, $keyword)) {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    protected function describeDepositCandidates($deposits): array
    {
        return $deposits->map(function (Deposit $deposit) {
            return [
                'id' => $deposit->id,
                'trx' => $deposit->trx,
                'status' => $deposit->status,
            ];
        })->values()->all();
    }

    protected function writeFawaterkAuditLog(array $audit, ?Deposit $deposit = null, ?string $decision = null, ?string $message = null): void
    {
        try {
            PaymentGatewayLog::create([
                'gateway' => $audit['gateway'] ?? 'Fawaterk',
                'event_type' => $audit['event_type'] ?? 'webhook',
                'invoice_id' => isset($audit['invoice_id']) ? (string) $audit['invoice_id'] : null,
                'invoice_key' => isset($audit['invoice_key']) ? (string) $audit['invoice_key'] : null,
                'reference_id' => isset($audit['reference_id']) ? (string) $audit['reference_id'] : null,
                'transaction_id' => isset($audit['transaction_id']) ? (string) $audit['transaction_id'] : null,
                'transaction_key' => isset($audit['transaction_key']) ? (string) $audit['transaction_key'] : null,
                'deposit_id' => $deposit?->id,
                'trx' => $deposit?->trx,
                'local_status_before' => $audit['local_status_before'] ?? $deposit?->getOriginal('status'),
                'local_status_after' => $deposit?->status,
                'decision' => $decision ?: ($audit['decision'] ?? null),
                'message' => $message,
                'payload' => $audit['payload'] ?? null,
                'headers' => $audit['headers'] ?? null,
            ]);
        } catch (Throwable $throwable) {
            Log::warning('Unable to write Fawaterk payment gateway audit log.', [
                'error' => $throwable->getMessage(),
                'audit' => $audit,
                'decision' => $decision,
                'message' => $message,
                'deposit_id' => $deposit?->id,
                'trx' => $deposit?->trx,
            ]);
        }
    }
}
