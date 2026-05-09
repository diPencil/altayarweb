<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmployeePagePermission
{
    public function handle(Request $request, Closure $next)
    {
        $employee = employee();

        if (! $employee) {
            return to_route('employee.login');
        }

        $routeName = $request->route()?->getName();
        $userPermissionKey = employeeRouteUserPermissionKey($routeName);

        if ($userPermissionKey) {
            if (employeeUserCan($userPermissionKey, $employee)) {
                return $next($request);
            }

            $notify[] = ['error', __('You do not have permission to access this page.')];

            return to_route('employee.home')->withNotify($notify);
        }

        $permissionKey = employeeRoutePermissionKey($routeName);

        if (! $permissionKey || employeePageCan($permissionKey, $employee)) {
            return $next($request);
        }

        $notify[] = ['error', __('You do not have permission to access this page.')];

        return to_route('employee.home')->withNotify($notify);
    }
}