<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Employee\Auth')->name('employee.')->group(function () {
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

Route::middleware('employee')->name('employee.')->group(function () {
    //authorization
    Route::namespace('Employee')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend/verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify/email', 'emailVerification')->name('verify.email');
        Route::post('verify/mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify/g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['employee.check', 'employee.page'])->group(function () {

        Route::get('data', 'Employee\EmployeeController@userData')->name('data');
        Route::post('data/submit', 'Employee\EmployeeController@userDataSubmit')->name('data.submit');

        //Profile setting
        Route::controller(\App\Http\Controllers\Employee\ProfileController::class)->group(function () {
            Route::get('profile/setting', 'profile')->name('profile.setting');
            Route::post('profile/setting', 'submitProfile');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('change-password', 'submitPassword');
        });

        Route::middleware('employee.registration.complete')->namespace('Employee')->group(function () {

            Route::controller('EmployeeController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('users', 'users')->name('users');
                Route::get('users/{id}', 'userDetail')->name('users.detail');
                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');
                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');

                // E-Payment
                Route::get('e-payment', '\App\Http\Controllers\EPaymentController@index')->name('e.payment');
                Route::post('e-payment', '\App\Http\Controllers\EPaymentController@store')->name('e.payment.store');
                Route::get('e-payment/result/{trx?}', '\App\Http\Controllers\EPaymentController@result')->name('e.payment.result');
            });

              //KYC
              Route::controller('EmployeeController')->group(function () {
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');
            });

            //Tour Package
           
            Route::controller('TourPackageController')->name('tour.package.')->prefix('tour-package')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create')->middleware('employee.kyc');
                Route::post('store', 'store')->name('store')->middleware('employee.kyc');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('employee.kyc');
                Route::put('update/{id}', 'update')->name('update')->middleware('employee.kyc');
                Route::get('status-change/{id}', 'statusChange')->name('status.change')->middleware('employee.kyc');
                Route::post('delete/{id}', 'delete')->name('delete')->middleware('employee.kyc');
                Route::get('my-tour', 'myList')->name('my.list');
                Route::get('agent-tour', 'allAgent')->name('all.agent');
                Route::get('search', 'search')->name('search');
                Route::post('image', 'tourPackageImageDelete')->name('image.delete');
                Route::get('active', 'active')->name('active');
                Route::get('pending', 'pending')->name('pending');
                Route::get('expired', 'expired')->name('expired');
                Route::get('running', 'running')->name('running');
            });

            // Listings (Offers)
            Route::controller('ListingController')->name('listing.')->prefix('listings')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create')->middleware('employee.kyc');
                Route::post('store', 'store')->name('store')->middleware('employee.kyc');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('employee.kyc');
                Route::post('update/{id}', 'update')->name('update')->middleware('employee.kyc');
                Route::get('status-change/{id}', 'statusChange')->name('status.change')->middleware('employee.kyc');
                Route::post('delete/{id}', 'delete')->name('delete')->middleware('employee.kyc');
            });

            Route::controller('PopupAdController')->name('popup-ads.')->prefix('popup-ads')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create')->middleware('employee.kyc');
                Route::post('store', 'store')->name('store')->middleware('employee.kyc');
                Route::get('{popupAd}/edit', 'edit')->name('edit')->middleware('employee.kyc');
                Route::post('{popupAd}/update', 'update')->name('update')->middleware('employee.kyc');
                Route::post('{popupAd}/status', 'status')->name('status')->middleware('employee.kyc');
                Route::post('{popupAd}/delete', 'destroy')->name('delete')->middleware('employee.kyc');
            });
          


              //Booking Controller
              Route::controller('BookingController')->name('tour.package.booking.')->group(function () {
                Route::post('/booking-now', 'bookingNow')->name('now')->middleware('kyc');
                Route::get('/booking-list', 'bookingTourPackageList')->name('my.list');
                Route::get('pending', 'pending')->name('pending');
                Route::get('approved', 'approved')->name('approved');
                Route::get('canceled', 'canceled')->name('canceled');
                Route::get('/booking-user-list/{id}', 'userList')->name('user.list');
                Route::get('/booking-details/{id}', 'bookingDetails')->name('details');
                

            });


            // ticket
            Route::controller('TicketController')->prefix('ticket')->group(function () {
                Route::get('all', 'supportTicket')->name('ticket');
                Route::get('new', 'openSupportTicket')->name('ticket.open');
                Route::post('create', 'storeSupportTicket')->name('ticket.store');
                Route::get('view/{ticket}', 'viewTicket')->name('ticket.view');
                Route::post('reply/{ticket}', 'replyTicket')->name('ticket.reply');
                Route::post('close/{ticket}', 'closeTicket')->name('ticket.close');
                Route::get('download/{ticket}', 'ticketDownload')->name('ticket.download');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::get('/', 'withdrawMoney');
                Route::post('/', 'withdrawStore')->name('.money');
                Route::get('preview', 'withdrawPreview')->name('.preview');
                Route::post('preview', 'withdrawSubmit')->name('.submit');
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });
    });
});
