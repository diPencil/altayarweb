<?php

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function(){

    Route::prefix('mobile/auth')->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\Mobile\AuthController::class, 'login']);
        Route::post('register', [\App\Http\Controllers\Api\Mobile\AuthController::class, 'register']);
        Route::post('refresh', [\App\Http\Controllers\Api\Mobile\AuthController::class, 'refresh']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [\App\Http\Controllers\Api\Mobile\AuthController::class, 'me']);
            Route::post('logout', [\App\Http\Controllers\Api\Mobile\AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:sanctum')->prefix('mobile')->group(function () {
        Route::get('wallet/me', [\App\Http\Controllers\Api\Mobile\WalletController::class, 'me']);
        Route::get('wallet/me/transactions', [\App\Http\Controllers\Api\Mobile\WalletController::class, 'transactions']);
        Route::get('wallet/transactions', [\App\Http\Controllers\Api\Mobile\WalletController::class, 'transactions']);
        Route::controller(\App\Http\Controllers\Api\Mobile\ChatController::class)->group(function () {
            Route::get('chat/my', 'my');
            Route::get('chat/my/active', 'active');
            Route::post('chat/start', 'start');
            Route::get('chat/{id}', 'show')->whereNumber('id');
            Route::post('chat/{id}/message', 'message')->whereNumber('id');
            Route::get('chat/admin/all', 'adminAll');
            Route::post('chat/{id}/assign', 'assign')->whereNumber('id');
            Route::get('chat/stats/admin', 'adminStats');
        });
        Route::get('points/me', [\App\Http\Controllers\Api\Mobile\PointsController::class, 'me']);
        Route::get('points/me/transactions', [\App\Http\Controllers\Api\Mobile\PointsController::class, 'transactions']);
        Route::get('points/history', [\App\Http\Controllers\Api\Mobile\PointsController::class, 'transactions']);
        Route::get('notifications', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'index']);
        Route::get('notifications/stats', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'stats']);
        Route::get('notifications/unread-count', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'unreadCount']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'markAsRead'])
            ->whereNumber('id');
        Route::post('notifications/read-all', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'markAllAsRead']);
        Route::post('notifications/token', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'updateDeviceToken']);
        Route::delete('notifications/token', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'deleteDeviceToken']);
        Route::controller(\App\Http\Controllers\Api\Mobile\MembershipController::class)->group(function () {
            Route::get('membership/plans', 'plans');
            Route::get('membership/dashboard', 'dashboard');
            Route::get('membership/cashback', 'cashbackHistory');
            Route::post('membership/subscribe', 'subscribe');
        });

        Route::controller(\App\Http\Controllers\Api\Mobile\OrdersController::class)->group(function () {
            Route::get('orders', 'me');
            Route::get('orders/me', 'me');
            Route::get('orders/{id}', 'show')->whereNumber('id');
        });

        Route::controller(\App\Http\Controllers\Api\Mobile\BookingsController::class)->group(function () {
            Route::get('bookings', 'me');
            Route::get('bookings/me', 'me');
            Route::get('bookings/{id}', 'show')->whereNumber('id');
        });

        Route::post('tour-packages/{id}/book', [\App\Http\Controllers\Api\Mobile\TourPackagesController::class, 'book'])
            ->whereNumber('id');
        Route::get('tour-packages/favorites', [\App\Http\Controllers\Api\Mobile\TourPackagesController::class, 'favorites']);
        Route::post('tour-packages/{id}/favorite', [\App\Http\Controllers\Api\Mobile\TourPackagesController::class, 'addFavorite'])
            ->whereNumber('id');
        Route::delete('tour-packages/{id}/favorite', [\App\Http\Controllers\Api\Mobile\TourPackagesController::class, 'removeFavorite'])
            ->whereNumber('id');

        Route::controller(\App\Http\Controllers\Api\Mobile\PaymentsController::class)->group(function () {
            Route::get('payments', 'myPayments');
            Route::get('payments/my-payments', 'myPayments');
            Route::get('payments/status/{id}', 'status');
            Route::post('payments/create', 'create');
            Route::post('payments/quick-pay', 'quickPay');
        });

        Route::get('invoices', [\App\Http\Controllers\Api\Mobile\OrdersController::class, 'me']);

        Route::get('offers/favorites', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'favorites']);
        Route::post('offers/{id}/favorite', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'addFavorite'])
            ->whereNumber('id');
        Route::delete('offers/{id}/favorite', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'removeFavorite'])
            ->whereNumber('id');
        Route::post('offers/{id}/rate', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'rate'])
            ->whereNumber('id');
    });

    Route::middleware(['auth:sanctum', 'admin.mobile'])->prefix('mobile/admin')->group(function () {
        Route::get('stats/overview', [\App\Http\Controllers\Api\Mobile\Admin\AdminStatsController::class, 'overview']);
        Route::get('users', [\App\Http\Controllers\Api\Mobile\Admin\AdminUserController::class, 'index']);
        Route::get('users/{id}', [\App\Http\Controllers\Api\Mobile\Admin\AdminUserController::class, 'show']);
        Route::get('bookings', [\App\Http\Controllers\Api\Mobile\Admin\AdminBookingsController::class, 'index']);
        Route::get('bookings/{id}', [\App\Http\Controllers\Api\Mobile\Admin\AdminBookingsController::class, 'show'])->whereNumber('id');
        Route::get('orders', [\App\Http\Controllers\Api\Mobile\Admin\AdminOrdersController::class, 'index']);
        Route::get('orders/{id}', [\App\Http\Controllers\Api\Mobile\Admin\AdminOrdersController::class, 'show'])->whereNumber('id');
        Route::get('payments', [\App\Http\Controllers\Api\Mobile\Admin\AdminPaymentsController::class, 'index']);
        Route::get('payments/{id}', [\App\Http\Controllers\Api\Mobile\Admin\AdminPaymentsController::class, 'show'])->whereNumber('id');

        // Phase 4A: Remaining read-only admin endpoints
        Route::get('wallets', [\App\Http\Controllers\Api\Mobile\Admin\AdminWalletsController::class, 'index']);
        Route::get('points', [\App\Http\Controllers\Api\Mobile\Admin\AdminPointsController::class, 'index']);
        Route::get('club-gifts', [\App\Http\Controllers\Api\Mobile\Admin\AdminClubGiftsController::class, 'index']);
        Route::get('withdrawal-requests', [\App\Http\Controllers\Api\Mobile\Admin\AdminWithdrawalsController::class, 'index']);
        Route::get('memberships', [\App\Http\Controllers\Api\Mobile\Admin\AdminMembershipsController::class, 'index']);
        Route::get('memberships/{planId}/benefits', [\App\Http\Controllers\Api\Mobile\Admin\AdminMembershipsController::class, 'benefits'])->whereNumber('planId');
        Route::get('offers', [\App\Http\Controllers\Api\Mobile\Admin\AdminOffersController::class, 'index']);
        Route::get('reels', [\App\Http\Controllers\Api\Mobile\Admin\AdminReelsController::class, 'index']);
        Route::get('reels/comments', [\App\Http\Controllers\Api\Mobile\Admin\AdminReelsController::class, 'comments']);
    });

    Route::middleware(['auth:sanctum', 'employee.mobile'])->prefix('mobile/employee')->group(function () {
        Route::get('stats/overview', [\App\Http\Controllers\Api\Mobile\Employee\EmployeeStatsController::class, 'overview']);
        Route::get('orders', [\App\Http\Controllers\Api\Mobile\Employee\EmployeeOrdersController::class, 'index']);
        Route::get('customers', [\App\Http\Controllers\Api\Mobile\Employee\EmployeeCustomersController::class, 'index']);
        Route::get('notifications', [\App\Http\Controllers\Api\Mobile\NotificationsController::class, 'index']);
    });

    Route::prefix('mobile')->group(function () {
        Route::get('membership/pdf/{membership}', [	\App\Http\Controllers\Api\Mobile\MembershipController::class, 'pdf'])
            ->whereNumber('membership')
            ->name('mobile.membership.pdf');

        Route::get('club-offers', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'index']);
        Route::get('club-offers/featured', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'featured']);
        Route::get('club-offers/categories', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'categories']);
        Route::get('club-offers/{id}', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'show'])
            ->whereNumber('id');

        Route::get('special-offers', [\App\Http\Controllers\Api\Mobile\SpecialOffersController::class, 'index']);
        Route::get('special-offers/featured', [\App\Http\Controllers\Api\Mobile\SpecialOffersController::class, 'featured']);
        Route::get('special-offers/categories', [\App\Http\Controllers\Api\Mobile\SpecialOffersController::class, 'categories']);
        Route::get('special-offers/images/{filename}', [\App\Http\Controllers\Api\Mobile\SpecialOffersController::class, 'image'])
            ->name('mobile.special-offers.image');
        Route::get('special-offers/{id}', [\App\Http\Controllers\Api\Mobile\SpecialOffersController::class, 'show'])
            ->whereNumber('id');

        Route::get('tour-packages/{id}', [\App\Http\Controllers\Api\Mobile\TourPackagesController::class, 'show'])
            ->whereNumber('id');

        Route::get('offers', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'index']);
        Route::get('offers/featured', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'featured']);
        Route::get('offers/categories', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'categories']);
        Route::get('offers/{id}', [\App\Http\Controllers\Api\Mobile\OffersController::class, 'show'])
            ->whereNumber('id');
    });

    Route::prefix('mobile')->group(function () {
        Route::get('reels', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'index']);
        Route::get('reels/{id}', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'show'])
            ->whereNumber('id');
        Route::get('reels/{id}/comments', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'comments'])
            ->whereNumber('id');
    });

    Route::middleware('auth:sanctum')->prefix('mobile')->group(function () {
        Route::get('reels/favorites', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'favorites']);
        Route::post('reels/{id}/view', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'view'])
            ->whereNumber('id');
        Route::post('reels/{id}/like', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'like'])
            ->whereNumber('id');
        Route::post('reels/{id}/save', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'save'])
            ->whereNumber('id');
        Route::delete('reels/{id}/save', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'unsave'])
            ->whereNumber('id');
        Route::post('reels/{id}/comments', [\App\Http\Controllers\Api\Mobile\ReelsController::class, 'storeComment'])
            ->whereNumber('id');
    });

    Route::prefix('mobile')->group(function () {
        Route::get('health', function () {
            return response()->json([
                'success' => true,
                'message' => 'Laravel mobile API is working',
                'source' => 'altayar-website',
            ]);
        });

        Route::prefix('settings/public')->group(function () {
            Route::get('onboarding', [\App\Http\Controllers\Api\Mobile\SettingsController::class, 'onboarding']);
            Route::get('support', [\App\Http\Controllers\Api\Mobile\SettingsController::class, 'support']);
            Route::get('about', [\App\Http\Controllers\Api\Mobile\SettingsController::class, 'about']);
            Route::get('general', [\App\Http\Controllers\Api\Mobile\SettingsController::class, 'general']);
        });
    });

    Route::get('general-setting',function()
    {
        $general = GeneralSetting::select('site_name')->first();
        $notify[] = 'General setting data';
        return response()->json([
            'remark'=>'general_setting',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'general_setting'=>$general,
            ],
        ]);
    });

    Route::get('get-countries',function(){
        $c = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $notify[] = 'General setting data';
        foreach($c as $k => $country){
            $countries[] = [
                'country'=>$country->country,
                'dial_code'=>$country->dial_code,
                'country_code'=>$k,
            ];
        }
        return response()->json([
            'remark'=>'country_data',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'countries'=>$countries,
            ],
        ]);
    });

	Route::namespace('Auth')->group(function(){
		Route::post('login', 'LoginController@login');
		Route::post('register', 'RegisterController@register');

        Route::controller('ForgotPasswordController')->group(function(){
            Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
            Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
            Route::post('password/reset', 'reset')->name('password.update');
        });
	});

    Route::middleware('auth:sanctum')->group(function () {

        //authorization
        Route::controller('AuthorizationController')->group(function(){
            Route::get('authorization', 'authorization')->name('authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
            Route::post('verify-email', 'emailVerification')->name('verify.email');
            Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
            Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
        });

        Route::middleware(['check.status'])->group(function () {
            Route::post('user-data-submit', 'UserController@userDataSubmit')->name('data.submit');

            Route::middleware('registration.complete')->group(function(){
                Route::get('dashboard',function(){
                    return auth()->user();
                });

                Route::get('user-info',function(){
                    $notify[] = 'User information';
                    return response()->json([
                        'remark'=>'user_info',
                        'status'=>'success',
                        'message'=>['success'=>$notify],
                        'data'=>[
                            'user'=>auth()->user()
                        ]
                    ]);
                });

                Route::controller('UserController')->group(function(){

                    //KYC
                    Route::get('kyc-form','kycForm')->name('kyc.form');
                    Route::post('kyc-submit','kycSubmit')->name('kyc.submit');

                    //Report
                    Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                    Route::get('transactions','transactions')->name('transactions');

                });

                Route::controller('MembershipController')->group(function () {
                    Route::get('membership/plans', 'plans');
                    Route::get('membership/dashboard', 'dashboard');
                    Route::post('membership/subscribe', 'subscribe');
                    Route::get('membership/points', 'pointHistory');
                    Route::get('membership/cashback', 'cashbackHistory');
                });

                //Profile setting
                Route::controller('UserController')->group(function(){
                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');
                });

                // Withdraw
                Route::controller('WithdrawController')->group(function(){
                    Route::get('withdraw-method', 'withdrawMethod')->name('withdraw.method')->middleware('kyc');
                    Route::post('withdraw-request', 'withdrawStore')->name('withdraw.money')->middleware('kyc');
                    Route::post('withdraw-request/confirm', 'withdrawSubmit')->name('withdraw.submit')->middleware('kyc');
                    Route::get('withdraw/history', 'withdrawLog')->name('withdraw.history');
                });

                // Payment
                Route::controller('PaymentController')->group(function(){
                    Route::get('deposit/methods', 'methods')->name('deposit');
                    Route::post('deposit/insert', 'depositInsert')->name('deposit.insert');
                    Route::get('deposit/confirm', 'depositConfirm')->name('deposit.confirm');
                    Route::get('deposit/manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
                    Route::post('deposit/manual', 'manualDepositUpdate')->name('deposit.manual.update');
                });

            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });
});
