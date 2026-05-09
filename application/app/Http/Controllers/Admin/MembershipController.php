<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPlanHistory;
use App\Models\MembershipPlan;
use App\Models\MembershipPointTransaction;
use App\Models\User;
use App\Models\UserMembership;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function plans(Request $request)
    {
        $pageTitle = __('Membership Plans');
        $plans = MembershipPlan::orderByDesc('id');

        if ($request->search) {
            $search = $request->search;
            $plans = $plans->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $plans = $plans->paginate(getPaginate());
        return view('admin.membership.plans.index', compact('pageTitle', 'plans'));
    }

    public function create()
    {
        $pageTitle = __('Create Membership Plan');
        return view('admin.membership.plans.form', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'name_ar' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:0',
            'benefits' => 'nullable|array',
            'benefits_ar' => 'nullable|array',
            'bonus_points' => 'required|integer|min:0',
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'pdf_file' => ['nullable', 'file', new FileTypeValidate(['pdf'])],
        ]);

        $plan = new MembershipPlan();
        $plan->name = $request->name;
        $plan->name_ar = $request->name_ar;
        $plan->description = $request->description;
        $plan->description_ar = $request->description_ar;
        $plan->price = $request->price;
        $plan->duration_days = $request->duration_days;
        $plan->benefits = array_values(array_filter($request->benefits ?? []));
        $plan->benefits_ar = array_values(array_filter($request->benefits_ar ?? []));
        $plan->bonus_points = $request->bonus_points;
        $plan->status = $request->status ? 1 : 0;

        if ($request->hasFile('image_file')) {
            $plan->image_file = fileUploader($request->image_file, getFilePath('membershipPlanImage'), null, $plan->image_file ?? null);
        }

        if ($request->hasFile('cover_image')) {
            $plan->cover_image = fileUploader($request->cover_image, getFilePath('membershipPlanCover'), null, $plan->cover_image ?? null);
        }

        if ($request->hasFile('pdf_file')) {
            $plan->pdf_file = fileUploader($request->pdf_file, getFilePath('membershipPlanPdf'), null, $plan->pdf_file ?? null);
        }

        $plan->save();

        $notify[] = ['success', __('Membership plan created successfully')];
        return to_route('admin.membership.plans')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Membership Plan');
        $plan = MembershipPlan::findOrFail($id);
        return view('admin.membership.plans.form', compact('pageTitle', 'plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:120',
            'name_ar' => 'nullable|string|max:120',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:0',
            'benefits' => 'nullable|array',
            'benefits_ar' => 'nullable|array',
            'bonus_points' => 'required|integer|min:0',
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'pdf_file' => ['nullable', 'file', new FileTypeValidate(['pdf'])],
        ]);

        $plan->name = $request->name;
        $plan->name_ar = $request->name_ar;
        $plan->description = $request->description;
        $plan->description_ar = $request->description_ar;
        $plan->price = $request->price;
        $plan->duration_days = $request->duration_days;
        $plan->benefits = array_values(array_filter($request->benefits ?? []));
        $plan->benefits_ar = array_values(array_filter($request->benefits_ar ?? []));
        $plan->bonus_points = $request->bonus_points;
        $plan->status = $request->status ? 1 : 0;

        if ($request->hasFile('image_file')) {
            $plan->image_file = fileUploader($request->image_file, getFilePath('membershipPlanImage'), null, $plan->image_file);
        }

        if ($request->hasFile('cover_image')) {
            $plan->cover_image = fileUploader($request->cover_image, getFilePath('membershipPlanCover'), null, $plan->cover_image);
        }

        if ($request->hasFile('pdf_file')) {
            $plan->pdf_file = fileUploader($request->pdf_file, getFilePath('membershipPlanPdf'), null, $plan->pdf_file);
        }

        $plan->save();

        $notify[] = ['success', __('Membership plan updated successfully')];
        return to_route('admin.membership.plans')->withNotify($notify);
    }

    public function delete($id)
    {
        $plan = MembershipPlan::findOrFail($id);
        if ($plan->image_file) {
            fileManager()->removeFile(getFilePath('membershipPlanImage') . '/' . $plan->image_file);
        }
        if ($plan->cover_image) {
            fileManager()->removeFile(getFilePath('membershipPlanCover') . '/' . $plan->cover_image);
        }
        if ($plan->pdf_file) {
            fileManager()->removeFile(getFilePath('membershipPlanPdf') . '/' . $plan->pdf_file);
        }
        $plan->delete();

        $notify[] = ['success', __('Membership plan deleted successfully')];
        return back()->withNotify($notify);
    }

    public function subscriptions(Request $request)
    {
        $pageTitle = __('Membership Subscriptions');
        $subscriptions = UserMembership::with(['user', 'plan'])->orderByDesc('id');

        if ($request->search) {
            $search = $request->search;
            $subscriptions = $subscriptions->whereHas('user', function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('plan', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $subscriptions = $subscriptions->paginate(getPaginate());
        return view('admin.membership.subscriptions.index', compact('pageTitle', 'subscriptions'));
    }

    public function editSubscription($id)
    {
        $pageTitle = __('Edit Membership Subscription');
        $subscription = UserMembership::with(['user', 'plan'])->findOrFail($id);
        $plans = MembershipPlan::active()->orWhere('id', $subscription->membership_plan_id)->orderBy('name')->get();
        return view('admin.membership.subscriptions.edit', compact('pageTitle', 'subscription', 'plans'));
    }

    public function updateSubscription(Request $request, $id)
    {
        $subscription = UserMembership::findOrFail($id);
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:0,1',
        ]);

        $oldPlan = $subscription->plan;
        $newPlan = MembershipPlan::findOrFail($request->membership_plan_id);

        $previousSubscription = clone $subscription;

        $subscription->membership_plan_id = $request->membership_plan_id;
        $subscription->start_date = $request->start_date;
        $subscription->end_date = $request->end_date;
        $subscription->status = $request->status;
        $subscription->save();

        // Log the change
        $this->recordMembershipHistory(
            $subscription->user,
            $previousSubscription,
            $subscription,
            $newPlan,
            [
                'created_by_admin_id' => auth('admin')->id(),
                'note' => 'Admin manual update',
            ]
        );

        $notify[] = ['success', __('Subscription updated successfully')];
        return to_route('admin.membership.subscriptions')->withNotify($notify);
    }

    public function histories(Request $request)
    {
        $pageTitle = __('Membership Plan Summary');
        $histories = MembershipPlanHistory::with(['user', 'previousPlan', 'newPlan', 'previousMembership', 'newMembership', 'admin'])
            ->orderByDesc('id');

        if ($request->search) {
            $search = $request->search;
            $histories = $histories->where(function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('previousPlan', function ($planQuery) use ($search) {
                    $planQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('newPlan', function ($planQuery) use ($search) {
                    $planQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $histories = $histories->paginate(getPaginate());

        return view('admin.membership.histories.index', compact('pageTitle', 'histories'));
    }

    public function reports()
    {
        $pageTitle = __('Membership Reports');
        $points = MembershipPointTransaction::with(['user', 'plan'])->orderByDesc('id')->paginate(getPaginate());
        $cashbacks = MembershipCashbackTransaction::with(['user'])->orderByDesc('id')->paginate(getPaginate());

        return view('admin.membership.reports.index', compact('pageTitle', 'points', 'cashbacks'));
    }

    public function points(Request $request)
    {
        $pageTitle = __('Membership Points');
        $points = MembershipPointTransaction::with(['user', 'plan', 'booking', 'user.currentMembership', 'user.currentMembership.plan', 'user.memberships', 'user.memberships.plan'])->orderByDesc('id');
        $users = User::orderBy('username')->get(['id', 'username']);

        if ($request->search) {
            $search = $request->search;
            $points = $points->where(function ($query) use ($search) {
                $query->where('trx', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $points = $points->paginate(getPaginate());
        return view('admin.membership.points.index', compact('pageTitle', 'points', 'users'));
    }

    public function cashback(Request $request)
    {
        $pageTitle = __('Membership Cashback');
        $cashbacks = MembershipCashbackTransaction::with(['user', 'booking', 'user.currentMembership', 'user.currentMembership.plan'])->orderByDesc('id');
        $users = User::orderBy('username')->get(['id', 'username']);

        if ($request->search) {
            $search = $request->search;
            $cashbacks = $cashbacks->where(function ($query) use ($search) {
                $query->where('trx', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $cashbacks = $cashbacks->paginate(getPaginate());
        return view('admin.membership.cashback.index', compact('pageTitle', 'cashbacks', 'users'));
    }

    public function storePoint(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|gt:0',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = (int) $request->amount;
        $currentBalance = (int) $user->membership_points_balance;
        $trx = getTrx();

        MembershipPointTransaction::create([
            'user_id' => $user->id,
            'membership_plan_id' => $user->currentMembership?->membership_plan_id,
            'trx' => $trx,
            'type' => 'earned',
            'points' => $amount,
            'balance_after' => $currentBalance + $amount,
            'remark' => $request->remark,
            'meta' => [
                'action_by' => auth('admin')->id(),
                'source' => 'admin_membership_points_page',
            ],
        ]);

        notify($user, 'POINTS_ADD', [
            'amount' => $amount,
            'post_balance' => $currentBalance + $amount,
            'trx' => $trx,
            'remark' => $request->remark,
        ]);

        $notify[] = ['success', __('Points added successfully')];
        return back()->withNotify($notify);
    }

    public function storeCashback(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|gt:0',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = (float) $request->amount;
        $currentBalance = (float) $user->cashback_balance;
        $trx = getTrx();

        MembershipCashbackTransaction::create([
            'user_id' => $user->id,
            'trx' => $trx,
            'type' => 'earned',
            'amount' => $amount,
            'balance_after' => $currentBalance + $amount,
            'remark' => $request->remark,
            'meta' => [
                'action_by' => auth('admin')->id(),
                'source' => 'admin_membership_cashback_page',
            ],
        ]);

        notify($user, 'CASHBACK_ADD', [
            'amount' => showAmount($amount),
            'post_balance' => showAmount($currentBalance + $amount),
            'trx' => $trx,
            'remark' => $request->remark,
        ]);

        $notify[] = ['success', __('Cashback added successfully')];
        return back()->withNotify($notify);
    }

    protected function recordMembershipHistory(User $user, ?UserMembership $previousMembership, UserMembership $newMembership, MembershipPlan $plan, array $context = []): void
    {
        MembershipPlanHistory::recordChange($user, $previousMembership, $newMembership, $plan, $context);
    }
}
