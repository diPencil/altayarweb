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
        if (!auth()->check() && !auth('employee')->check()) {
            return to_route('user.login');
        }

        $rules = [
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'method_code' => ['required', 'integer'],
            'currency' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
        ];

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
        } else {
            $deposit->user_id = auth()->id();
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
        $deposit->detail = (object) [
            'payment_flow' => 'epayment',
            'note' => trim((string) $request->note),
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
                    'redirect_url' => $response->redirect_url,
                    'trx' => $deposit->trx,
                ])
                : redirect($response->redirect_url);
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
        } else {
            $depositQuery->where('user_id', auth()->id());
        }

        if ($trx) {
            $depositQuery->where('trx', $trx);
        }

        $deposit = $depositQuery->latest('id')->firstOrFail();
        $status = (int) $deposit->status;

        return view($this->activeTemplate . 'e_payment_result', compact('pageTitle', 'deposit', 'status'));
    }
}
