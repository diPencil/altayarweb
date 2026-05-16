<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MembershipPointTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'current_balance' => (int) ($user->membership_points_balance ?? 0),
                'total_earned' => (int) $user->membershipPointTransactions()->where('type', 'earned')->sum('points'),
                'tier' => $this->tierFor($user),
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $transactions = MembershipPointTransaction::where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (MembershipPointTransaction $transaction) {
                $isUsed = (string) ($transaction->type ?? '') === 'used';

                return [
                    'id' => $transaction->id,
                    'points' => $isUsed ? -abs((int) ($transaction->points ?? 0)) : abs((int) ($transaction->points ?? 0)),
                    'transaction_type' => (string) ($transaction->type ?? 'earned'),
                    'description_en' => $transaction->remark ?: ($isUsed ? 'Points used' : 'Points earned'),
                    'description_ar' => $transaction->remark ?: ($isUsed ? 'نقاط مستخدمة' : 'نقاط مكتسبة'),
                    'created_at' => optional($transaction->created_at)->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    private function tierFor($user): string
    {
        $membership = $user->currentMembership()->with('plan')->first();

        if (! $membership || ! $membership->plan) {
            return 'Basic';
        }

        return $membership->plan->name_en
            ?? $membership->plan->title_en
            ?? $membership->plan->tier_name_en
            ?? $membership->plan->tier_code
            ?? 'Basic';
    }
}