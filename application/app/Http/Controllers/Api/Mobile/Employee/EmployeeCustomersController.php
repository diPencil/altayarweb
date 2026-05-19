<?php

namespace App\Http\Controllers\Api\Mobile\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeCustomersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $employeeId = $request->user()->id;

        try {
            $users = User::query()
                ->where('agent_id', $employeeId)
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (User $user): array {
                    return [
                        'id' => $user->id,
                        'first_name' => $user->firstname,
                        'last_name' => $user->lastname ?: '',
                        'email' => $user->email,
                        'phone' => $user->mobile,
                        'username' => $user->username,
                        'status' => $user->status ? 'ACTIVE' : 'BANNED',
                        'role' => 'Customer',
                        'created_at' => optional($user->created_at)->toISOString(),
                    ];
                });

            return response()->json($users);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch assigned customers: ' . $e->getMessage()
            ], 500);
        }
    }
}
