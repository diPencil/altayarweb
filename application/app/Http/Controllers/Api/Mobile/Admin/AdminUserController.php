<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $role = strtoupper((string) $request->input('role', ''));
            $search = trim((string) $request->input('search', ''));
            $limit = (int) $request->input('limit', 50);

            $usersList = [];

            if ($role === 'EMPLOYEE') {
                $query = Employee::query();
                if ($search !== '') {
                    $query->where(function($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('firstname', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%");
                    });
                }
                $employees = $query->orderByDesc('id')->limit($limit)->get();
                foreach ($employees as $employee) {
                    $usersList[] = [
                        'id' => 'employee_' . $employee->id,
                        'username' => $employee->username,
                        'email' => $employee->email,
                        'first_name' => $employee->firstname,
                        'last_name' => $employee->lastname,
                        'phone' => $employee->mobile,
                        'status' => (int) ($employee->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
                        'plan' => null,
                        'avatar' => $employee->image ?? null
                    ];
                }
            } elseif ($role === 'ADMIN' || $role === 'SUPER_ADMIN') {
                $query = Admin::query();
                if ($search !== '') {
                    $query->where(function($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                    });
                }
                $admins = $query->orderByDesc('id')->limit($limit)->get();
                foreach ($admins as $admin) {
                    $usersList[] = [
                        'id' => 'admin_' . $admin->id,
                        'username' => $admin->username,
                        'email' => $admin->email,
                        'first_name' => $admin->name,
                        'last_name' => '',
                        'phone' => null,
                        'status' => 'ACTIVE',
                        'plan' => [
                            'code' => 'vip',
                            'tier_code' => 'vip',
                            'name' => 'Admin Plan',
                            'name_ar' => 'خطة المسؤول',
                            'color' => '#10B981',
                            'color_hex' => '#10B981'
                        ],
                        'avatar' => $admin->image ?? null
                    ];
                }
            } else {
                // Customer or ALL (default to Customer as main user base)
                $query = User::query()->with(['currentMembership', 'currentMembership.plan']);
                if ($search !== '') {
                    $query->where(function($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('firstname', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%");
                    });
                }
                $users = $query->orderByDesc('id')->limit($limit)->get();
                foreach ($users as $user) {
                    $plan = null;
                    $membership = $user->currentMembership;
                    if ($membership && $membership->plan) {
                        $mPlan = $membership->plan;
                        $code = 'silver';
                        $color = '#C0C0C0';
                        $name = $mPlan->name;
                        $nameLower = strtolower($name);
                        if (str_contains($nameLower, 'gold')) {
                            $code = 'gold';
                            $color = '#FFD700';
                        } elseif (str_contains($nameLower, 'platinum')) {
                            $code = 'platinum';
                            $color = '#8B5CF6';
                        } elseif (str_contains($nameLower, 'vip') || str_contains($nameLower, 'club')) {
                            $code = 'vip';
                            $color = '#10B981';
                        } elseif (str_contains($nameLower, 'diamond')) {
                            $code = 'diamond';
                            $color = '#3B82F6';
                        } elseif (str_contains($nameLower, 'business')) {
                            $code = 'business';
                            $color = '#EF4444';
                        }
                        
                        $plan = [
                            'code' => $code,
                            'tier_code' => $code,
                            'name' => $name,
                            'name_ar' => $mPlan->name_ar ?: $name,
                            'color' => $color,
                            'color_hex' => $color
                        ];
                    }

                    $usersList[] = [
                        'id' => (string) $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->firstname,
                        'last_name' => $user->lastname ?: '',
                        'phone' => $user->mobile,
                        'status' => (int) ($user->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
                        'plan' => $plan,
                        'avatar' => $user->avatar ?? $user->image ?? null
                    ];
                }
            }

            return response()->json([
                'users' => $usersList
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }
}
