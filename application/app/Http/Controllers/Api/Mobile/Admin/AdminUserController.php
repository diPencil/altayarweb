<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $role = strtoupper((string) $request->input('role', ''));
            $search = trim((string) $request->input('search', ''));
            $status = strtoupper(trim((string) $request->input('status', '')));
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', 50)), 100));

            $usersList = [];

            if ($role === 'EMPLOYEE') {
                $query = Employee::query();
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('firstname', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%");
                    });
                }
                if (in_array($status, ['ACTIVE', 'SUSPENDED'], true)) {
                    $query->where('status', $status === 'ACTIVE' ? 1 : 0);
                }
                $employees = $query->orderByDesc('id')->get();
                foreach ($employees as $employee) {
                    $usersList[] = $this->employeeSummaryPayload($employee);
                }
            } elseif ($role === 'ADMIN' || $role === 'SUPER_ADMIN') {
                $query = Admin::query();
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                    });
                }
                $admins = $query->orderByDesc('id')->get();
                foreach ($admins as $admin) {
                    $usersList[] = $this->adminSummaryPayload($admin);
                }
            } else {
                $query = User::query()->with(['currentMembership.plan']);
                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('firstname', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%");
                    });
                }
                if (in_array($status, ['ACTIVE', 'SUSPENDED'], true)) {
                    $query->where('status', $status === 'ACTIVE' ? 1 : 0);
                }
                $users = $query->orderByDesc('id')->get();
                foreach ($users as $user) {
                    $usersList[] = $this->customerSummaryPayload($user);
                }
            }

            $total = count($usersList);
            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($usersList, $offset, $perPage);

            return response()->json([
                'success' => true,
                'users' => array_values($pageItems),
                'meta' => $this->paginationMeta($page, $perPage, $total),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $resolved = $this->resolveUserReference($id);

            if (! $resolved) {
                return response()->json([
                    'detail' => 'User not found',
                ], 404);
            }

            [$type, $record] = $resolved;
            $payload = match ($type) {
                'employee' => $this->employeeDetailPayload($record),
                'admin' => $this->adminDetailPayload($record),
                default => $this->customerDetailPayload($record),
            };

            return response()->json([
                'success' => true,
                ...$payload,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch user details: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function resolveUserReference(string $identifier): ?array
    {
        $identifier = trim($identifier);

        if (preg_match('/^employee_(\d+)$/', $identifier, $matches)) {
            $employee = Employee::find((int) $matches[1]);
            return $employee ? ['employee', $employee] : null;
        }

        if (preg_match('/^admin_(\d+)$/', $identifier, $matches)) {
            $admin = Admin::find((int) $matches[1]);
            return $admin ? ['admin', $admin] : null;
        }

        if (ctype_digit($identifier)) {
            $user = User::find((int) $identifier);
            if ($user) {
                return ['customer', $user];
            }

            $employee = Employee::find((int) $identifier);
            if ($employee) {
                return ['employee', $employee];
            }

            $admin = Admin::find((int) $identifier);
            if ($admin) {
                return ['admin', $admin];
            }
        }

        return null;
    }

    private function customerSummaryPayload(User $user): array
    {
        $membership = $user->currentMembership?->plan;
        $plan = $membership ? $this->membershipPlanPayload($membership->name, $membership->name_ar) : null;

        return [
            'id' => (string) $user->id,
            'user_type' => 'customer',
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->firstname,
            'last_name' => $user->lastname ?: '',
            'phone' => $user->mobile,
            'status' => (int) ($user->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
            'plan' => $plan,
            'avatar' => $user->avatar ?? $user->image ?? null,
        ];
    }

    private function employeeSummaryPayload(Employee $employee): array
    {
        return [
            'id' => 'employee_' . $employee->id,
            'user_type' => 'employee',
            'username' => $employee->username,
            'email' => $employee->email,
            'first_name' => $employee->firstname,
            'last_name' => $employee->lastname,
            'phone' => $employee->mobile,
            'status' => (int) ($employee->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
            'plan' => null,
            'avatar' => $employee->image ?? null,
        ];
    }

    private function adminSummaryPayload(Admin $admin): array
    {
        return [
            'id' => 'admin_' . $admin->id,
            'user_type' => 'admin',
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
                'color_hex' => '#10B981',
            ],
            'avatar' => $admin->image ?? null,
        ];
    }

    private function customerDetailPayload(User $user): array
    {
        $membership = $user->currentMembership?->plan;

        return [
            'user' => [
                'id' => (string) $user->id,
                'user_type' => 'customer',
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname ?: '',
                'phone' => $user->mobile,
                'status' => (int) ($user->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
                'avatar' => $user->avatar ?? $user->image ?? null,
            ],
            'wallet' => [
                'balance' => (float) ($user->balance ?? 0),
                'currency' => 'EGP',
                'currency_symbol' => 'EGP',
            ],
            'points' => [
                'current_balance' => (int) ($user->membershipPointsBalance ?? 0),
                'total_earned' => (int) $user->membershipPointTransactions()->where('type', 'earned')->sum('points'),
            ],
            'cashback' => [
                'current_balance' => (float) ($user->cashbackBalance ?? 0),
            ],
            'membership' => $membership ? [
                'id' => $membership->id,
                'name' => $membership->name,
                'name_ar' => $membership->name_ar,
                'tier_code' => $membership->tier_code,
            ] : null,
            'verification' => [
                'email_verified' => (int) ($user->ev ?? 0) === 1,
                'phone_verified' => (int) ($user->sv ?? 0) === 1,
                'kyc_verified' => (int) ($user->kv ?? 0) === 1,
                'kyc_pending' => (int) ($user->kv ?? 0) === 2,
            ],
            'stats' => [
                'tour_bookings' => $user->tourBookings()->count(),
                'service_bookings' => $user->serviceBookings()->count(),
                'invoices' => $user->invoices()->count(),
            ],
        ];
    }

    private function employeeDetailPayload(Employee $employee): array
    {
        return [
            'user' => [
                'id' => 'employee_' . $employee->id,
                'user_type' => 'employee',
                'username' => $employee->username,
                'email' => $employee->email,
                'first_name' => $employee->firstname,
                'last_name' => $employee->lastname,
                'phone' => $employee->mobile,
                'status' => (int) ($employee->status ?? 1) === 1 ? 'ACTIVE' : 'SUSPENDED',
                'avatar' => $employee->image ?? null,
            ],
            'wallet' => [
                'balance' => 0,
                'currency' => 'EGP',
                'currency_symbol' => 'EGP',
            ],
            'points' => [
                'current_balance' => 0,
                'total_earned' => 0,
            ],
            'cashback' => [
                'current_balance' => 0,
            ],
            'membership' => null,
            'verification' => [
                'email_verified' => true,
                'phone_verified' => true,
                'kyc_verified' => false,
                'kyc_pending' => false,
            ],
            'stats' => [],
        ];
    }

    private function adminDetailPayload(Admin $admin): array
    {
        return [
            'user' => [
                'id' => 'admin_' . $admin->id,
                'user_type' => 'admin',
                'username' => $admin->username,
                'email' => $admin->email,
                'first_name' => $admin->name,
                'last_name' => '',
                'phone' => null,
                'status' => 'ACTIVE',
                'avatar' => $admin->image ?? null,
            ],
            'wallet' => [
                'balance' => 0,
                'currency' => 'EGP',
                'currency_symbol' => 'EGP',
            ],
            'points' => [
                'current_balance' => 0,
                'total_earned' => 0,
            ],
            'cashback' => [
                'current_balance' => 0,
            ],
            'membership' => [
                'id' => null,
                'name' => 'Admin Plan',
                'name_ar' => 'خطة المسؤول',
                'tier_code' => 'vip',
            ],
            'verification' => [
                'email_verified' => true,
                'phone_verified' => false,
                'kyc_verified' => false,
                'kyc_pending' => false,
            ],
            'stats' => [],
        ];
    }

    private function membershipPlanPayload(string $name, ?string $nameAr = null): array
    {
        $nameLower = strtolower($name);
        $code = 'silver';
        $color = '#C0C0C0';

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

        return [
            'code' => $code,
            'tier_code' => $code,
            'name' => $name,
            'name_ar' => $nameAr ?: $name,
            'color' => $color,
            'color_hex' => $color,
        ];
    }

    private function paginationMeta(int $page, int $perPage, int $total): array
    {
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
            'to' => min($page * $perPage, $total),
        ];
    }
}
