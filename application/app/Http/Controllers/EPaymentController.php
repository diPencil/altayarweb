<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Gateway\PaymentController as GatewayPaymentController;
use App\Models\Deposit;
use App\Models\User;
use App\Models\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EPaymentController extends Controller
{
    protected function fawaterkCurrenciesQuery()
    {
        return GatewayCurrency::with('method')
            ->whereHas('method', function ($query) {
                $query->where('status', 1)->where('alias', 'Fawaterk');
            })
            ->orderByRaw("CASE currency WHEN 'USD' THEN 1 WHEN 'SAR' THEN 2 WHEN 'EGP' THEN 3 WHEN 'EUR' THEN 4 ELSE 5 END")
            ->orderBy('id');
    }

    protected function gatewayCurrencies()
    {
        return $this->fawaterkCurrenciesQuery()->first();
    }

    public function index()
    {
        $pageTitle = 'E-Payment';
        $gatewayCurrency = $this->fawaterkCurrenciesQuery()->get();
        
        $latestPayments = collect();
        $users = collect();

        if (auth('employee')->check()) {
            $latestPayments = Deposit::with('gateway')
                ->where('agent_id', auth('employee')->id())
                ->orderByDesc('id')
                ->limit(5)
                ->get();
            
            $users = User::active()->orderBy('username')->get(['id', 'username', 'email']);
        } elseif (auth()->check()) {
            $latestPayments = Deposit::with('gateway')
                ->where('user_id', auth()->id())
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        return view($this->activeTemplate . 'e_payment', compact('pageTitle', 'gatewayCurrency', 'latestPayments', 'users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'method_code' => ['required', 'integer'],
            'currency' => ['required', 'string'],
            'payment_purpose' => ['nullable', 'string', 'max:255'],
            'booking_reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ];

        if (!auth()->check() && !auth('employee')->check()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:40'];
        }

        if (auth('employee')->check()) {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        $request->validate($rules);

        $gatewayCurrency = GatewayCurrency::with('method')
            ->whereHas('method', function ($query) {
                $query->where('status', 1)->where('alias', 'Fawaterk');
            })
            ->where('method_code', $request->method_code)
            ->where('currency', $request->currency)
            ->first();

        if (!$gatewayCurrency) {
            $notify[] = ['error', 'The payment gateway is currently unavailable.'];
            return back()->withNotify($notify);
        }

        $amount = round((float) $request->amount, 2);
        if ($gatewayCurrency->min_amount > $amount || $gatewayCurrency->max_amount < $amount) {
            $notify[] = ['error', 'Please follow payment limits'];
            return back()->withNotify($notify);
        }

        $charge = round($gatewayCurrency->fixed_charge + ($amount * $gatewayCurrency->percent_charge / 100), 2);
        $payable = round($amount + $charge, 2);
        $finalAmo = round($payable * $gatewayCurrency->rate, 2);

        $deposit = new Deposit();
        if (auth('employee')->check()) {
            $deposit->agent_id = auth('employee')->id();
            $deposit->user_id = $request->user_id;
            $user = User::find($request->user_id);
            if ($user) {
                $deposit->guest_name = $user->fullname;
                $deposit->guest_email = $user->email;
                $deposit->guest_phone = $user->mobile;
            }
        } elseif (auth()->check()) {
            $user = auth()->user();
            $deposit->user_id = $user->id;
            $deposit->guest_name = $user->fullname;
            $deposit->guest_email = $user->email;
            $deposit->guest_phone = $user->mobile;
        } else {
            $deposit->user_id = null;
            $deposit->guest_name = $request->name;
            $deposit->guest_email = $request->email;
            $deposit->guest_phone = $request->phone ?: 'Not provided';
        }

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
        $deposit->status = 0;
        
        $currency = strtoupper($gatewayCurrency->currency);
        $paymentPurpose = $request->payment_purpose ?: "Online E-Payment Request - $amount $currency";
        
        $bookingReference = $request->booking_reference;
        if (empty($bookingReference)) {
            $datePart = now()->format('Ymd');
            $randomPart = strtoupper(substr(uniqid(), -4));
            $bookingReference = "EPAY-$datePart-$randomPart";
        }
        
        $notes = $request->note;
        if (empty($notes)) {
            $notes = (auth()->check() || auth('employee')->check()) 
                ? "User payment submitted from /e-payment page" 
                : "Guest payment submitted from public /e-payment page";
        }

        $deposit->payment_purpose = $paymentPurpose;
        $deposit->booking_reference = $bookingReference;
        $deposit->notes = $notes;

        $deposit->detail = (object) [
            'payment_flow' => 'epayment',
            'note' => $notes,
            'payment_purpose' => $paymentPurpose,
            'booking_reference' => $bookingReference,
        ];

        $deposit->save();

        Session::put('Track', $deposit->trx);

        $dirName = $gatewayCurrency->method?->alias ?: 'Fawaterk';
        $processController = __NAMESPACE__ . '\\Gateway\\' . $dirName . '\\ProcessController';
        $response = json_decode($processController::process($deposit));

        if (isset($response->error)) {
            $notify[] = ['error', $response->message ?? 'The payment gateway is currently unavailable.'];
            return ($request->ajax() || $request->expectsJson())
                ? response()->json(['status' => false, 'message' => $response->message ?? 'The payment gateway is currently unavailable.'], 422)
                : back()->withNotify($notify);
        }

        if (isset($response->redirect)) {
            return ($request->ajax() || $request->expectsJson())
                ? response()->json([
                    'status' => true,
                    'redirect_url' => route('payment.pay', $deposit->trx),
                    'trx' => $deposit->trx,
                ])
                : redirect()->route('payment.pay', $deposit->trx);
        }

        return ($request->ajax() || $request->expectsJson())
            ? response()->json(['status' => false, 'message' => 'Unable to create payment link.'], 422)
            : back()->withNotify([['error', 'Unable to create payment link.']]);
    }

    public function result(Request $request, ?string $trx = null)
    {
        $pageTitle = 'Payment Result';
        $trx = $trx ?: session('Track');

        $depositQuery = Deposit::with('gateway');
        if (auth('employee')->check()) {
            $depositQuery->where('agent_id', auth('employee')->id());
        } elseif (auth()->check()) {
            $depositQuery->where('user_id', auth()->id());
        }

        if ($trx) {
            $depositQuery->where('trx', $trx);
        } else {
            abort(404);
        }

        $deposit = $depositQuery->latest('id')->firstOrFail();
        $status = (int) $deposit->status;

        return view($this->activeTemplate . 'e_payment_result', compact('pageTitle', 'deposit', 'status'));
    }
}
