<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (Auth::guard($guard)->check()) {
            return $next($request);
        }

        // Support authenticated employees accessing allowed staff routes
        if (Auth::guard('employee')->check()) {
            $allowedStaffRoutes = [
                'admin.membership.miles.index',
                'admin.membership.miles.operate',
                'admin.membership.miles.reverse',
                'admin.users.add.sub.miles',
                'admin.users.detail',
            ];

            $routeName = $request->route()?->getName();
            if ($routeName && in_array($routeName, $allowedStaffRoutes, true)) {
                return $next($request);
            }

            abort(403, __('Unauthorized staff access.'));
        }

        return to_route('admin.login');
    }
}
