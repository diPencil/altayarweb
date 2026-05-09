<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guard('employee')->check()) {
            $user = employee();
            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first.';
                    return response()->json([
                        'remark'=>'unverified',
                        'status'=>'error',
                        'message'=>['error'=>$notify],
                        'data'=>[
                            'is_ban'=>$user->status,
                            'email_verified'=>$user->ev,
                            'mobile_verified'=>$user->sv,
                            'twofa_verified'=>$user->tv,
                        ],
                    ]);
                }else{
                    return to_route('employee.authorization');
                }
            }
        }
        return to_route('employee.login');
    }
}
