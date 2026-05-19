<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminMembershipsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $plans = MembershipPlan::withCount('memberships')
                ->orderBy('id')
                ->get()
                ->map(function (MembershipPlan $plan): array {
                    return [
                        'id'               => $plan->id,
                        'name'             => $plan->name,
                        'name_ar'          => $plan->name_ar,
                        'price'            => (float) ($plan->price ?? 0),
                        'duration_days'    => $plan->duration_days,
                        'bonus_points'     => $plan->bonus_points,
                        'status'           => $plan->status ? 'ACTIVE' : 'INACTIVE',
                        'memberships_count' => $plan->memberships_count,
                        'created_at'       => optional($plan->created_at)->toISOString(),
                    ];
                });

            $totalActive   = UserMembership::where('status', 1)->count();
            $totalInactive = UserMembership::where('status', '!=', 1)->count();

            return response()->json([
                'success' => true,
                'data'    => [
                    'plans'          => $plans,
                    'summary'        => [
                        'total_plans'      => $plans->count(),
                        'active_members'   => $totalActive,
                        'inactive_members' => $totalInactive,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch memberships: ' . $e->getMessage()], 500);
        }
    }

    public function benefits(Request $request, int $planId): JsonResponse
    {
        try {
            $plan = MembershipPlan::findOrFail($planId);

            return response()->json([
                'success' => true,
                'data'    => [
                    'plan_id'     => $plan->id,
                    'plan_name'   => $plan->name,
                    'plan_name_ar' => $plan->name_ar,
                    'benefits'    => $plan->benefits ?? [],
                    'benefits_ar' => $plan->benefits_ar ?? [],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch membership benefits: ' . $e->getMessage()], 500);
        }
    }
}
