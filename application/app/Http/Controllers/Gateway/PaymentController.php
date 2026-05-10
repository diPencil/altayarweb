<?php

namespace App\Http\Controllers\Gateway;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Agent;
use App\Models\Deposit;
use App\Models\ServiceBooking;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPointTransaction;
use App\Lib\FormProcessor;
use App\Models\MembershipPlan;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Models\UserMembership;
use App\Models\MembershipPlanHistory;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get()->reject(function ($gatewayCurrency) {
            return strcasecmp((string) $gatewayCurrency->name, 'Cash') === 0;
        })->values();

        $membershipSession = session()->get('membershipSession');
        $tourPackageSession = session()->get('tourPackageSession');
        
        $fixedAmount = null;
        if ($membershipSession) {
            $fixedAmount = $membershipSession['amount'];
        } elseif ($tourPackageSession) {
            $fixedAmount = max(0, ($tourPackageSession['booking_amount'] ?? 0) - ($tourPackageSession['cashback_used'] ?? 0));
        }

        $pageTitle = 'E-Payment';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'fixedAmount'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->whereRaw('LOWER(name) != ?', ['cash'])->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $tourPackageSession = collect(Session::get('tourPackageSession', []));
        $membershipSession = collect(Session::get('membershipSession', []));

        $hasBookingContext = $tourPackageSession->has('tour_package_id');
        $hasMembershipContext = $membershipSession->has('membership_plan_id');

        $amountToPay = (float) $request->amount;
        if ($hasBookingContext) {
            $bookingAmount = (float) ($tourPackageSession['booking_amount'] ?? $request->amount);
            $cashbackUsed = (float) ($tourPackageSession['cashback_used'] ?? 0);
            $amountToPay = max(0, $bookingAmount - $cashbackUsed);
        } elseif ($hasMembershipContext) {
            $amountToPay = (float) $membershipSession['amount'];
        }

        if ($gate->min_amount > $amountToPay || $gate->max_amount < $amountToPay) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($amountToPay * $gate->percent_charge / 100);
        $payable = $amountToPay + $charge;
        $final_amo = $payable * $gate->rate;

        $tourBooking = null;
        if ($hasBookingContext) {
            $tourPackage = TourPackage::findOrFail($tourPackageSession['tour_package_id']);

            $tourBooking = new TourBooking();
            $tourBooking->user_id = auth()->id();
            $tourBooking->owner_id = $tourPackage->user_id;
            $tourBooking->owner_type = $tourPackage->user_type;
            $tourBooking->price = showTourPackageCalculateDiscount($tourPackage->price * $tourPackageSession['seat'], $tourPackage->discount);
            $tourBooking->discount = $tourPackage->discount;
            $tourBooking->cashback_used = $tourPackageSession['cashback_used'] ?? 0;
            $tourBooking->tour_package_id = $tourPackage->id;
            $tourBooking->user_proposal_date = $tourPackageSession['user_proposal_date'] ?? $tourPackage->tour_start;
            $tourBooking->seat = $tourPackageSession['seat'];
            $tourBooking->status = 0;
            $tourBooking->save();
        }


        $data = new Deposit();
        $data->user_id = $user->id;
        $data->tour_booking_id = $tourBooking?->id;
        $data->membership_plan_id = $hasMembershipContext ? $membershipSession['membership_plan_id'] : null;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amountToPay;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->detail = (object) [
            'payment_flow' => $hasBookingContext ? 'booking' : ($hasMembershipContext ? 'membership' : 'epayment'),
            'source' => $hasBookingContext ? 'tour_booking' : ($hasMembershipContext ? 'membership_plan' : 'manual_deposit'),
            'note' => $hasMembershipContext ? 'Subscription to plan ID: ' . $membershipSession['membership_plan_id'] : null,
        ];
        $data->save();
        Session::forget('tourPackageSession');
        Session::forget('membershipSession');
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', 0)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', 0)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect()->route('payment.pay', $deposit->trx);
        }

        // for Stripe V3
        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Tour Booking Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        $user = User::findOrFail($deposit->user_id);
        $gatewayCurrency = $deposit->gatewayCurrency();
        $gatewayName = $gatewayCurrency?->name ?? 'Payment';
        $tourBooking = null;

        // deposit status update
        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1;
            $deposit->save();

            // tour booking status update
            if ($deposit->tour_booking_id) {
                $tourBooking = TourBooking::with(['tour_package', 'agent', 'admin', 'user'])->findOrFail($deposit->tour_booking_id);
                $tourBooking->status = 1;
                $tourBooking->save();

                // if tour-package is owner agent then give money
                if ($tourBooking->owner_type == "agent") {
                    $agent = Agent::find($tourBooking->owner_id);
                    $agent->balance += $tourBooking->price;
                    $agent->save();
                }

                // set tourPackage total booking person 
                $tourPackage = $tourBooking->tour_package;
                if ($tourPackage) {
                    $tourPackage->booking_person += $tourBooking->seat;
                    $tourPackage->save();
                }
            }

            // service booking status update
            if ($deposit->service_booking_id) {
                $serviceBooking = ServiceBooking::findOrFail($deposit->service_booking_id);
                $serviceBooking->status = 1;
                $serviceBooking->save();
            }

            if ($tourBooking && $tourBooking->cashback_used > 0) {
                $currentCashbackBalance = (float) $user->cashback_balance;
                MembershipCashbackTransaction::create([
                    'user_id' => $user->id,
                    'tour_booking_id' => $tourBooking->id,
                    'trx' => getTrx(),
                    'type' => 'used',
                    'amount' => $tourBooking->cashback_used,
                    'balance_after' => max(0, $currentCashbackBalance - $tourBooking->cashback_used),
                    'remark' => 'booking_cashback_used',
                    'meta' => [
                        'booking_price' => $tourBooking->price,
                    ],
                ]);
            }

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->agent_id = ($tourBooking && $tourBooking->owner_type == "agent") ? $tourBooking->owner_id : 0;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = ($tourBooking ? 'Payment' : 'E-Payment') . ' Via ' . $gatewayName;
            $transaction->trx = $deposit->trx;
            $transaction->remark = $tourBooking ? 'Payment' : ($deposit->membership_plan_id ? 'Membership Payment' : 'E-Payment');
            $transaction->save();

            // Handle Membership Activation
            if ($deposit->membership_plan_id) {
                $plan = MembershipPlan::findOrFail($deposit->membership_plan_id);
                $previousMembership = $user->currentMembership()->with('plan')->first();

                // Deactivate current active memberships
                UserMembership::where('user_id', $user->id)
                    ->whereIn('status', [0, 1])
                    ->update(['status' => 2]);

                $startDate = now()->startOfDay();
                $endDate = $plan->duration_days > 0 ? now()->addDays($plan->duration_days)->endOfDay() : null;

                $membership = new UserMembership();
                $membership->user_id = $user->id;
                $membership->membership_plan_id = $plan->id;
                $membership->start_date = $startDate;
                $membership->end_date = $endDate;
                $membership->status = 1;
                $membership->save();

                MembershipPlanHistory::recordChange($user, $previousMembership, $membership, $plan, [
                    'created_by_user_id' => $user->id,
                    'note' => 'payment_success_activation',
                    'deposit_id' => $deposit->id
                ]);

                MembershipPointTransaction::create([
                    'user_id' => $user->id,
                    'membership_plan_id' => $plan->id,
                    'trx' => $deposit->trx,
                    'type' => 'earned',
                    'points' => (int) $plan->bonus_points,
                    'balance_after' => (int) $user->membership_points_balance + (int) $plan->bonus_points,
                    'remark' => 'membership_bonus',
                    'meta' => [
                        'subscription_id' => $membership->id,
                        'deposit_id' => $deposit->id
                    ],
                ]);

                notify($user, 'MEMBERSHIP_SUBSCRIBE', [
                    'plan_name' => $plan->name,
                    'amount' => showAmount($deposit->amount),
                    'trx' => $deposit->trx,
                    'end_date' => $endDate ? showDateTime($endDate) : 'Never',
                ]);

                if ($plan->bonus_points > 0) {
                    notify($user, 'POINTS_ADD', [
                        'amount' => (int) $plan->bonus_points,
                        'post_balance' => (int) $user->membership_points_balance + (int) $plan->bonus_points,
                        'trx' => $deposit->trx,
                        'remark' => 'Membership Subscription Bonus',
                    ]);
                }
            }

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                
                if ($deposit->membership_plan_id) {
                    $adminNotification->title = 'New membership subscription: ' . $plan->name . ' by ' . $user->username;
                    $adminNotification->click_url = urlPath('admin.users.detail', $user->id); // Or a specific membership route if exists
                } else {
                    $adminNotification->title = 'Deposit successful via ' . $gatewayName;
                    $adminNotification->click_url = urlPath('admin.deposit.successful');
                }
                
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $gatewayName,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            if ($tourBooking) {
                self::awardMembershipRewards($tourBooking, $user);

                notify($user, 'BOOKING_COMPLETE', [
                    'tour_title' => $tourBooking->tour_package->title,
                    'tour_owner_name' => ($tourBooking->owner_type == "agent") ? $tourBooking->agent->fullname : $tourBooking->admin->name,
                    'tour_owner_email' => ($tourBooking->owner_type == "agent") ? $tourBooking->agent->email : $tourBooking->admin->email,
                    'price' => showAmount($tourBooking->price),
                    'discount' => showAmount($tourBooking->discount),
                    'booking_seats' => $tourBooking->seat,
                    'tour_start' => showDateTime($tourBooking->tour_package->tour_start),
                    'tour_end' => showDateTime($tourBooking->tour_package->tour_end),
                    'tour_stay' => $tourBooking->tour_package->day_nights,
                ]);

                $ownerName = ($tourBooking->owner_type == "agent") ? Agent::findOrFail($tourBooking->owner_id) : Admin::findOrFail($tourBooking->owner_id);
                notify($ownerName, 'TOUR_BOOKED', [
                    'tour_title' => $tourBooking->tour_package->title,
                    'first_name' => $tourBooking->user->firstname,
                    'last_name' => $tourBooking->user->lastname,
                    'email' => $tourBooking->user->email,
                    'phone' => $tourBooking->user->phone
                ]);
            }
        }
    }

    public static function markDepositFailed($deposit, ?string $feedback = null): void
    {
        if ($deposit->status == 1) {
            return;
        }

        $deposit->status = 3;
        if ($feedback) {
            $deposit->admin_feedback = $feedback;
        }
        $deposit->save();

        if ($deposit->tour_booking_id) {
            $tourBooking = TourBooking::find($deposit->tour_booking_id);
            if ($tourBooking) {
                $tourBooking->status = 3;
                $tourBooking->save();
            }
        }

        if ($deposit->service_booking_id) {
            $serviceBooking = ServiceBooking::find($deposit->service_booking_id);
            if ($serviceBooking) {
                $serviceBooking->status = 3;
                $serviceBooking->save();
            }
        }
    }

    protected static function awardMembershipRewards(TourBooking $tourBooking, User $user): void
    {
        $membership = $user->memberships()->active()->where(function ($query) {
            $query->whereNull('end_date')->orWhereDate('end_date', '>=', now());
        })->with('plan')->latest()->first();

        if (!$membership || !$membership->plan) {
            return;
        }

        $plan = $membership->plan;
        $points = (int) max(0, $plan->bonus_points);
        $cashbackAmount = round(($tourBooking->price * 0.02), 2);
        $currentPointBalance = (int) $user->membership_points_balance;
        $currentCashbackBalance = (float) $user->cashback_balance;

        MembershipPointTransaction::create([
            'user_id' => $user->id,
            'membership_plan_id' => $membership->membership_plan_id,
            'tour_booking_id' => $tourBooking->id,
            'trx' => getTrx(),
            'type' => 'earned',
            'points' => $points,
            'balance_after' => $currentPointBalance + $points,
            'remark' => 'booking_reward',
            'meta' => [
                'booking_price' => $tourBooking->price,
                'seat' => $tourBooking->seat,
            ],
        ]);

        if ($points > 0) {
            notify($user, 'POINTS_ADD', [
                'amount' => $points,
                'post_balance' => $currentPointBalance + $points,
                'trx' => $tourBooking->trx,
                'remark' => 'Booking Reward',
            ]);
        }

        if ($cashbackAmount > 0) {
            MembershipCashbackTransaction::create([
                'user_id' => $user->id,
                'tour_booking_id' => $tourBooking->id,
                'trx' => getTrx(),
                'type' => 'earned',
                'amount' => $cashbackAmount,
                'balance_after' => $currentCashbackBalance + $cashbackAmount,
                'remark' => 'booking_cashback',
                'meta' => [
                    'booking_price' => $tourBooking->price,
                    'membership_plan_id' => $membership->membership_plan_id,
                ],
            ]);

            notify($user, 'CASHBACK_ADD', [
                'amount' => showAmount($cashbackAmount),
                'post_balance' => showAmount($currentCashbackBalance + $cashbackAmount),
                'trx' => $tourBooking->trx,
                'remark' => 'Booking Cashback',
            ]);
        }
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway', 'tour_booking')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = 2; // pending
        $data->save();

        $data->tour_booking->status = 2;
        $data->tour_booking->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have payment request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
