<?php

use App\Lib\Router;
use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });
    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->prefix('social')->group(function () {
        Route::get('login/{provider}', 'socialLogin')->name('social.login');
        Route::get('login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    Route::get('/clear-cache', function () {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return redirect()->back();
    })->name('clear.cache');

    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend/verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify/email', 'emailVerification')->name('verify.email');
        Route::post('verify/mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify/g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {

        Route::namespace('User')->controller('WalletController')->prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('submit', 'submit')->name('submit');
        });

        Route::get('user/data', 'User\UserController@userData')->name('data');
        Route::post('user/data/submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::post('review-submit', 'reviewSubmit')->name('review.submit');
                Route::get('toggle-wishlist', 'toggleWishlist')->name('wishlist.added');
                Route::get('wishlist-list', 'getWishlist')->name('get.wishlist');
                Route::post('remove-wishlist/{id}', 'removeWishlist')->name('remove.wishlist');
                Route::post('apply-coupon', 'applyCoupon')->name('apply.coupon');

                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');
            });

            //Invoice Controller
            Route::controller('InvoiceController')->name('invoice.')->prefix('invoice')->group(function () {
                Route::get('/', 'index')->name('list');
                Route::get('show/{invoice_number}', 'show')->name('show');
            });

            Route::controller('ReelController')->prefix('saved-reels')->name('reels.')->group(function () {
                Route::get('library', 'library')->name('library');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile/setting', 'profile')->name('profile.setting');
                Route::post('profile/setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

           

            //Booking Controller
            Route::controller('BookingController')->name('tour.package.booking.')->group(function () {
                Route::post('/booking-now', 'bookingNow')->name('now');
                Route::get('/tour-packages', 'bookingList')->name('my.list');
                Route::get('/booking-list', 'allBookings')->name('all.list');
                Route::get('pending', 'pending')->name('pending');
                Route::get('approved', 'approved')->name('approved');
                Route::get('canceled', 'canceled')->name('canceled');
                Route::get('booking-details/{id}', 'bookingDetails')->name('details');
            });

            Route::controller('ServiceBookingController')->name('service.booking.')->prefix('service-bookings')->group(function () {
                Route::get('/{type?}', 'myList')->name('my.list');
                Route::get('details/{id}', 'details')->name('details');
            });

            Route::controller('MembershipController')->name('membership.')->prefix('membership')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('summary', 'summary')->name('summary');
                Route::get('member-card', 'card')->name('card');
                Route::get('benefits', 'benefits')->name('benefits');
                Route::get('plans', 'plans')->name('plans');
                Route::post('subscribe', 'subscribe')->name('subscribe');
                Route::get('view/{id}', 'viewPdf')->name('view');
                Route::get('download/{id}', 'downloadPdf')->name('download');
            });

        });

        // Payment
        Route::middleware('registration.complete')->controller('Gateway\PaymentController')->group(function () {
            Route::any('deposit', 'deposit')->name('deposit');
            Route::any('payment/{id}', 'payment')->name('payment');
            Route::post('deposit/insert', 'depositInsert')->name('deposit.insert');
            Route::get('deposit/confirm', 'depositConfirm')->name('deposit.confirm');
            Route::get('deposit/manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'manualDepositUpdate')->name('deposit.manual.update');
        });
    });
});
