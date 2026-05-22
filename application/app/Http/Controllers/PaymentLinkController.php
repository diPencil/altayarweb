<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Http\Controllers\Gateway\Fawaterk\ProcessController as FawaterkProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentLinkController extends Controller
{
    /**
     * Public route to redirect users to the actual gateway payment page.
     * Regenerates the invoice if it's expired or missing for Fawaterk.
     */
    public function pay($trx)
    {
        $deposit = Deposit::where('trx', $trx)->with(['user', 'gateway'])->firstOrFail();

        // Check if payment is already successful
        if ($deposit->status == 1) {
            return $this->showMessage('success', __('Payment already completed.'));
        }

        // Check if payment is rejected or cancelled
        if ($deposit->status == 3) {
            return $this->showMessage('error', __('Payment is not available.'));
        }

        // If it's a manual gateway, we can't redirect to a payment page easily
        if ($deposit->method_code >= 1000) {
            return $this->showMessage('error', __('Direct payment link is not supported for manual payments.'));
        }

        // Logic for Fawaterk (alias should be 'Fawaterk')
        if ($deposit->gateway && $deposit->gateway->alias == 'Fawaterk') {
            $detail = (array) $deposit->detail;

            if (
                $deposit->status == 2 &&
                !empty($detail['gateway_invoice_url']) &&
                !empty($detail['gateway_invoice_id']) &&
                !empty($deposit->btc_wallet)
            ) {
                return redirect($detail['gateway_invoice_url']);
            }

            return $this->processFawaterk($deposit);
        }

        // Generic fallback for other automatic gateways if they have a URL
        $detail = (array) $deposit->detail;
        $url = $detail['gateway_invoice_url'] ?? null;

        if ($url) {
            return redirect($url);
        }

        return $this->showMessage('error', __('Payment gateway not supported for direct link.'));
    }

    /**
     * Process Fawaterk specific redirection and regeneration.
     */
    protected function processFawaterk(Deposit $deposit)
    {
        // For Fawaterk, we always try to get a fresh link if we are using this internal redirect
        // to avoid "Session expired" issues.
        
        $resultJson = FawaterkProcess::process($deposit);
        $result = json_decode($resultJson);

        if (isset($result->redirect) && $result->redirect) {
            return redirect($result->redirect_url)
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]);
        }

        return $this->showMessage('error', $result->message ?? __('Unable to process payment at this time.'));
    }

    /**
     * Admin route to force-refresh the gateway invoice.
     */
    public function refresh($id)
    {
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();

        if ($deposit->status != 2 && $deposit->status != 0) {
            return response()->json(['error' => true, 'message' => __('Only pending payments can be refreshed.')]);
        }

        if ($deposit->gateway && $deposit->gateway->alias == 'Fawaterk') {
            $resultJson = FawaterkProcess::process($deposit);
            $result = json_decode($resultJson);
            
            if (isset($result->redirect) && $result->redirect) {
                return response()->json([
                    'success' => true, 
                    'message' => __('Payment link refreshed successfully.'),
                    'internal_link' => route('payment.pay', $deposit->trx),
                    'gateway_link' => $result->redirect_url
                ]);
            }
            return response()->json(['error' => true, 'message' => $result->message ?? __('Unable to refresh link.')]);
        }

        return response()->json(['error' => true, 'message' => __('Gateway does not support refreshing.')]);
    }

    /**
     * Show a simple status message page.
     */
    protected function showMessage($status, $message)
    {
        $pageTitle = __('Payment Status');
        // We can use a simple view or redirect with notify if user is logged in
        // but for public links, a simple view is better.
        return view('presets.default.payment_status', compact('status', 'message', 'pageTitle'));
    }
}
