<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPlanHistory;
use App\Models\MembershipPlan;
use App\Models\MembershipPointTransaction;
use App\Models\UserMembership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function plans()
    {
        $plans = MembershipPlan::where('status', 1)->orderByDesc('bonus_points')->get();

        return response()->json([
            'remark' => 'membership_plans',
            'status' => 'success',
            'message' => ['success' => ['Membership plans retrieved successfully']],
            'data' => ['plans' => $plans],
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();

        return response()->json([
            'remark' => 'membership_dashboard',
            'status' => 'success',
            'message' => ['success' => ['Membership summary retrieved successfully']],
            'data' => [
                'current_membership' => $user->currentMembership()->with('plan')->first(),
                'points_balance' => $user->membership_points_balance,
                'cashback_balance' => $user->cashback_balance,
                'point_transactions' => $user->membershipPointTransactions()->with('plan')->latest()->paginate(getPaginate()),
                'cashback_transactions' => $user->membershipCashbackTransactions()->latest()->paginate(getPaginate()),
            ],
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
        ]);

        $plan = MembershipPlan::where('status', 1)->findOrFail($request->membership_plan_id);
        $user = auth()->user();
        $previousMembership = $user->currentMembership()->with('plan')->first();

        UserMembership::where('user_id', $user->id)
            ->whereIn('status', [0, 1])
            ->update(['status' => 2]);

        $membership = new UserMembership();
        $membership->user_id = $user->id;
        $membership->membership_plan_id = $plan->id;
        $membership->start_date = now()->startOfDay();
        $membership->end_date = $plan->duration_days > 0 ? now()->addDays($plan->duration_days)->endOfDay() : null;
        $membership->status = 1;
        $membership->save();

        MembershipPlanHistory::recordChange($user, $previousMembership, $membership, $plan, [
            'created_by_user_id' => $user->id,
            'note' => 'api_subscribe',
        ]);

        MembershipPointTransaction::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'trx' => getTrx(),
            'type' => 'earned',
            'points' => (int) $plan->bonus_points,
            'balance_after' => (int) $user->membership_points_balance + (int) $plan->bonus_points,
            'remark' => 'membership_bonus',
            'meta' => ['subscription_id' => $membership->id],
        ]);

        return response()->json([
            'remark' => 'membership_subscribed',
            'status' => 'success',
            'message' => ['success' => ['Membership subscribed successfully']],
            'data' => ['membership' => $membership->load('plan')],
        ]);
    }

    public function pointHistory()
    {
        return response()->json([
            'remark' => 'membership_point_history',
            'status' => 'success',
            'message' => ['success' => ['Points history retrieved successfully']],
            'data' => ['transactions' => auth()->user()->membershipPointTransactions()->with('plan')->latest()->paginate(getPaginate())],
        ]);
    }

    public function cashbackHistory()
    {
        return response()->json([
            'remark' => 'membership_cashback_history',
            'status' => 'success',
            'message' => ['success' => ['Cashback history retrieved successfully']],
            'data' => ['transactions' => auth()->user()->membershipCashbackTransactions()->latest()->paginate(getPaginate())],
        ]);
    }
}
