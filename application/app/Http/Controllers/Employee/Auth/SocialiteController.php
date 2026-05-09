<?php

namespace App\Http\Controllers\Employee\Auth;

use App\Http\Controllers\Controller;
use App\Lib\SocialLogin;

class SocialiteController extends Controller
{
    public function socialLogin($provider)
    {

        $socialLogin = new \App\Lib\EmployeeSocialLogin($provider);
        return $socialLogin->redirectDriver();
    }

    public function callback($provider)
    {
        $socialLogin = new \App\Lib\EmployeeSocialLogin($provider);
        try {
            return $socialLogin->login();
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return to_route('home')->withNotify($notify);
        }
    }
}
