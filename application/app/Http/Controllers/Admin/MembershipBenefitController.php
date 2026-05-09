<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\UserMembershipBenefit;
use Illuminate\Http\Request;

class MembershipBenefitController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Membership Benefits (Grouped by User)');
        $benefits = UserMembershipBenefit::with(['user', 'user.memberships.plan'])
            ->select('user_id')
            ->selectRaw('count(*) as benefits_count')
            ->selectRaw('sum(total_quantity) as total_qty')
            ->selectRaw('sum(used_quantity) as used_qty')
            ->selectRaw('sum(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_count')
            ->groupBy('user_id');

        if ($request->search) {
            $search = $request->search;
            $benefits = $benefits->whereHas('user', function ($qu) use ($search) {
                $qu->where('username', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $benefits = $benefits->paginate(getPaginate());
        return view('admin.membership.benefits.index', compact('pageTitle', 'benefits'));
    }

    public function userBenefits($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = __("Benefits for: ") . $user->username;
        $benefits = UserMembershipBenefit::where('user_id', $id)->with('membershipPlan')->orderByDesc('id')->get();
        $plans = MembershipPlan::orderBy('name')->get();
        return view('admin.membership.benefits.show', compact('pageTitle', 'benefits', 'user', 'plans'));
    }

    public function create(Request $request)
    {
        $pageTitle = __('Create Membership Benefit');
        $users = User::active()->orderBy('username')->get();
        $plans = MembershipPlan::orderBy('name')->get();
        $selectedUserId = $request->user_id;
        return view('admin.membership.benefits.form', compact('pageTitle', 'users', 'plans', 'selectedUserId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_plan_id' => 'nullable|exists:membership_plans,id',
            'benefits' => 'required|array|min:1',
            'benefits.*.title' => 'required|string|max:191',
            'benefits.*.description' => 'nullable|string',
            'benefits.*.total_quantity' => 'required|integer|min:0',
            'benefits.*.used_quantity' => 'nullable|integer|min:0',
            'benefits.*.unit' => 'nullable|string|max:50',
            'benefits.*.starts_at' => 'nullable|date',
            'benefits.*.expires_at' => 'nullable|date|after_or_equal:benefits.*.starts_at',
            'benefits.*.status' => 'required|in:0,1',
            'benefits.*.notes' => 'nullable|string',
        ], [
            'benefits.required' => __('Please add at least one benefit item.'),
            'benefits.min' => __('Please add at least one benefit item.'),
        ]);

        // Manual validation for used_quantity <= total_quantity per row
        foreach ($request->benefits as $index => $benefitData) {
            $total = (int) $benefitData['total_quantity'];
            $used = (int) ($benefitData['used_quantity'] ?? 0);
            
            if ($used > $total) {
                return back()->withInput()->withErrors(["benefits.{$index}.used_quantity" => __("Used quantity cannot exceed total quantity for benefit item #") . ($index + 1)]);
            }
        }

        foreach ($request->benefits as $benefitData) {
            UserMembershipBenefit::create([
                'user_id' => $request->user_id,
                'membership_plan_id' => $request->membership_plan_id,
                'benefit_type' => null,
                'title' => $benefitData['title'],
                'description' => $benefitData['description'],
                'total_quantity' => $benefitData['total_quantity'],
                'used_quantity' => $benefitData['used_quantity'] ?? 0,
                'unit' => $benefitData['unit'],
                'starts_at' => $benefitData['starts_at'],
                'expires_at' => $benefitData['expires_at'],
                'status' => $benefitData['status'],
                'notes' => $benefitData['notes'],
            ]);
        }

        $notify[] = ['success', __('Membership benefits created successfully')];

        if ($request->redirect_to_user) {
            return back()->withNotify($notify);
        }

        return to_route('admin.membership.benefits.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Membership Benefit');
        $benefit = UserMembershipBenefit::findOrFail($id);
        $users = User::orderBy('username')->get(['id', 'username']);
        $plans = MembershipPlan::orderBy('name')->get(['id', 'name']);
        $selectedUserId = null;
        return view('admin.membership.benefits.form', compact('pageTitle', 'benefit', 'users', 'plans', 'selectedUserId'));
    }

    public function update(Request $request, $id)
    {
        $benefit = UserMembershipBenefit::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'membership_plan_id' => 'nullable|exists:membership_plans,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'total_quantity' => 'required|integer|min:0',
            'used_quantity' => 'nullable|integer|min:0|lte:total_quantity',
            'unit' => 'nullable|string|max:50',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:0,1',
            'notes' => 'nullable|string',
        ]);

        $benefit->update([
            'user_id' => $request->user_id,
            'membership_plan_id' => $request->membership_plan_id,
            'benefit_type' => null,
            'title' => $request->title,
            'description' => $request->description,
            'total_quantity' => $request->total_quantity ?? 0,
            'used_quantity' => $request->used_quantity ?? 0,
            'unit' => $request->unit,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $notify[] = ['success', __('Membership benefit updated successfully')];
        return to_route('admin.membership.benefits.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $benefit = UserMembershipBenefit::findOrFail($id);
        $benefit->delete();

        $notify[] = ['success', __('Membership benefit deleted successfully')];
        return back()->withNotify($notify);
    }
}
