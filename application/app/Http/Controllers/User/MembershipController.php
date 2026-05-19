<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPlanHistory;
use App\Models\MembershipPlan;
use App\Models\MembershipPointTransaction;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index()
    {
        $pageTitle = __('Membership Center');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();
        $pointTransactions = $user->membershipPointTransactions()->with(['plan', 'booking'])->paginate(10, ['*'], 'points_page');
        $cashbackTransactions = $user->membershipCashbackTransactions()->with('booking')->paginate(10, ['*'], 'cashback_page');

        return view($this->activeTemplate . 'user.membership.index', compact(
            'pageTitle',
            'user',
            'currentMembership',
            'pointTransactions',
            'cashbackTransactions'
        ));
    }

    public function summary()
    {
        $pageTitle = __('Plan Summary');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();
        /** @var \Illuminate\Pagination\LengthAwarePaginator $histories */
        $histories = MembershipPlanHistory::with(['previousPlan', 'newPlan', 'newMembership', 'previousMembership'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(10);
        $latestHistory = $histories->getCollection()->first();

        return view($this->activeTemplate . 'user.membership.summary', compact('pageTitle', 'user', 'currentMembership', 'histories', 'latestHistory'));
    }

    public function card()
    {
        $pageTitle = __('Member Card');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();

        return view($this->activeTemplate . 'user.membership.card', compact('pageTitle', 'user', 'currentMembership'));
    }

    public function benefits()
    {
        $pageTitle = __('Membership Benefits');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();

        // Load benefits assigned to the current user (Phase 1)
        $benefits = \App\Models\UserMembershipBenefit::where('user_id', $user->id)->with('membershipPlan')->orderByDesc('id')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.membership.benefits', compact('pageTitle', 'user', 'currentMembership', 'benefits'));
    }

    public function plans()
    {
        $pageTitle = __('Membership Plans');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();
        $plans = MembershipPlan::where('status', 1)->orderBy('bonus_points', 'desc')->get();

        return view($this->activeTemplate . 'user.membership.plans', compact('pageTitle', 'user', 'currentMembership', 'plans'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
        ]);

        $plan = MembershipPlan::where('status', 1)->findOrFail($request->membership_plan_id);
        
        session()->put('membershipSession', [
            'membership_plan_id' => $plan->id,
            'amount' => $plan->price,
        ]);

        return to_route('user.deposit');
    }

    public function downloadPdf($id)
    {
        $user = auth()->user();
        $currentMembership = UserMembership::where('user_id', $user->id)
            ->where('status', 1)
            ->where('membership_plan_id', $id)
            ->first();

        if (!$currentMembership) {
            $notify[] = ['error', 'You must be subscribed to this membership plan or upgrade your current plan to download this PDF.'];
            return back()->withNotify($notify);
        }

        $plan = MembershipPlan::findOrFail($id);
        abort_unless($plan->pdf_file, 404);

        return response()->download(getFilePath('membershipPlanPdf') . '/' . $plan->pdf_file);
    }

    public function viewPdf($id)
    {
        $user = auth()->user();
        $currentMembership = UserMembership::where('user_id', $user->id)
            ->where('status', 1)
            ->where('membership_plan_id', $id)
            ->first();

        if (!$currentMembership) {
            $notify[] = ['error', 'You must be subscribed to this membership plan or upgrade your current plan to view this PDF.'];
            return back()->withNotify($notify);
        }

        $plan = MembershipPlan::findOrFail($id);
        abort_unless($plan->pdf_file, 404);

        $absolutePath = getFilePath('membershipPlanPdf') . '/' . $plan->pdf_file;
        abort_unless(file_exists($absolutePath), 404);

        $fileName = Str::slug($plan->name ?: 'membership-plan') . '.pdf';

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $fileName),
        ]);
    }
}
