<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Admin;

class EmployeeMobileOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Allow Employees, and also allow Admins because they have a "Switch to Employee View" toggle
        if ($user instanceof Employee || $user instanceof Admin) {
            return $next($request);
        }

        return response()->json([
            'detail' => 'Forbidden. This action is restricted to employees only.'
        ], 403);
    }
}
