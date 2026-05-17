<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\MembershipPlanHistory;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPointTransaction;
use App\Models\UserMembership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class MembershipController extends Controller
{
    public function plans(Request $request): JsonResponse
    {
        $activeOnly = filter_var($request->query('active_only', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        $query = MembershipPlan::query()->orderByDesc('bonus_points');

        if ($activeOnly !== false) {
            $query->active();
        }

        $plans = $query->get()->map(function (MembershipPlan $plan): array {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'name_en' => $plan->name,
                'name_ar' => $plan->name_ar,
                'price' => (float) $plan->price,
                'duration_days' => (int) $plan->duration_days,
                'bonus_points' => (int) $plan->bonus_points,
                'required_points' => (int) $plan->bonus_points,
                'points_threshold' => (int) $plan->bonus_points,
                'points' => (int) $plan->bonus_points,
                'required_amount' => (float) $plan->price,
                'spending_required' => (float) $plan->price,
                'features' => array_values(array_filter((array) ($plan->benefits ?? []))),
                'benefits' => array_values(array_filter((array) ($plan->benefits ?? []))),
                'benefits_ar' => array_values(array_filter((array) ($plan->benefits_ar ?? []))),
                'image_url' => $plan->image_url,
                'badge_image' => $plan->image_url,
                'icon' => null,
                'cover_image_url' => $plan->coverImageUrl(),
                'pdf_url' => $plan->pdf_url,
                'status' => (int) $plan->status === 1 ? 'ACTIVE' : 'INACTIVE',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $membership = $user->currentMembership()->with('plan')->first();
        $planNameEn = $membership->plan->name ?? $membership->plan_name_en ?? $membership->display_name ?? null;
        $planNameAr = $membership->plan->name_ar ?? $membership->plan_name_ar ?? null;
        $membershipCode = $membership->member_code ?? $membership->membership_id_display ?? $user->membership_id_display ?? null;
        $cashbackBalance = isset($user->cashback_balance) ? (float) $user->cashback_balance : 0;
        $currencyText = strtoupper((string) (gs()->cur_text ?? 'USD')) ?: 'USD';
        $currencySymbol = (string) (gs()->cur_sym ?? '$');

        return response()->json([
            'success' => true,
            'data' => [
                'current_membership' => $membership ? array_merge($membership->toArray(), [
                    'membership_id_display' => $membershipCode,
                    'member_code' => $membershipCode,
                    'plan_name' => $planNameEn,
                    'display_name' => $planNameEn,
                    'plan_name_en' => $planNameEn,
                    'plan_name_ar' => $planNameAr,
                    'pdf_url' => $this->membershipPdfUrl($membership),
                    'valid_from' => optional($membership->start_date)->toDateString(),
                    'starts_at' => optional($membership->start_date)->toISOString(),
                    'valid_until' => optional($membership->end_date)->toDateString(),
                    'expiry_date' => optional($membership->end_date)->toDateString(),
                    'cashback_balance' => $cashbackBalance,
                    'club_gifts' => $cashbackBalance,
                    'currency' => $currencyText,
                    'currency_symbol' => $currencySymbol,
                ]) : null,
                'membership_id_display' => $membershipCode,
                'plan_name' => $planNameEn,
                'plan_name_en' => $planNameEn,
                'plan_name_ar' => $planNameAr,
                'wallet_balance' => isset($user->balance) ? (float) $user->balance : 0,
                'cashback_balance' => $cashbackBalance,
                'club_gifts' => $cashbackBalance,
                'currency' => $currencyText,
                'currency_symbol' => $currencySymbol,
                'points' => [
                    'current_balance' => (int) ($user->membership_points_balance ?? 0),
                    'total_earned' => (int) $user->membershipPointTransactions()->where('type', 'earned')->sum('points'),
                ],
            ],
        ]);
    }

    public function cashbackHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $records = $user->membershipCashbackTransactions()
            ->latest()
            ->get()
            ->map(function (MembershipCashbackTransaction $transaction): array {
                $description = $transaction->remark ?: ($transaction->type === 'earned' ? 'Cashback earned' : 'Cashback used');

                return [
                    'id' => $transaction->id,
                    'amount' => (float) $transaction->amount,
                    'type' => $transaction->type,
                    'description_en' => $description,
                    'description_ar' => $description,
                    'created_at' => optional($transaction->created_at)->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'records' => $records,
                'total' => $records->count(),
                'balance' => (float) $user->cashback_balance,
            ],
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'membership_plan_id' => ['required', 'exists:membership_plans,id'],
        ]);

        $plan = MembershipPlan::query()->active()->findOrFail($request->input('membership_plan_id'));
        $user = $request->user();
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
            'note' => 'mobile_subscribe',
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
            'success' => true,
            'data' => [
                'membership' => $membership->load('plan'),
            ],
        ]);
    }

    public function pdf(Request $request, UserMembership $membership): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $membership->loadMissing('plan');
        $plan = $membership->plan;

        abort_unless($plan && filled($plan->pdf_file), 404);

        $absolutePath = getFilePath('membershipPlanPdf') . '/' . $plan->pdf_file;

        abort_unless(is_file($absolutePath) && is_readable($absolutePath), 404);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="membership-benefits.pdf"',
        ]);
    }

    private function membershipPdfUrl(UserMembership $membership): ?string
    {
        $membership->loadMissing('plan');

        if (! $membership->plan || ! filled($membership->plan->pdf_file)) {
            return null;
        }

        return URL::temporarySignedRoute(
            'api.mobile.membership.pdf',
            now()->addDay(),
            ['membership' => $membership->id]
        );
    }
}