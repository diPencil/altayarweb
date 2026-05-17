<?php

use Illuminate\Support\Facades\Route;


Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return redirect()->back();
})->name('clear.cache');

Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'sendResetCodeEmail');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notifications/live', 'notificationLive')->name('notifications.live');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request/report', 'requestReport')->name('request.report');
        Route::post('request/report', 'reportSubmit');

        Route::get('download/attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('manage/users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email/verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email/unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile/unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with/balance', 'usersWithBalance')->name('with.balance');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::post('add/sub/points/{id}', 'addSubPoints')->name('add.sub.points');
        Route::post('add/sub/cashback/{id}', 'addSubCashback')->name('add.sub.cashback');
        Route::post('assign-employee/{id}', 'assignEmployee')->name('assign.employee');
        Route::post('assign-membership/{id}', 'assignMembership')->name('membership.assign');
        Route::post('update-member-code/{id}', 'updateMemberCode')->name('membership.code.update');
        Route::post('remove-membership/{id}', 'removeMembership')->name('membership.remove');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::get('stop-impersonate', 'stopImpersonate')->name('stop.impersonate');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send/notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send/notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification/log/{id}', 'notificationLog')->name('notification.log');

        // kyc
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
    });

    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');

        // employee
        Route::get('employee/kyc-setting', 'agentSetting')->name('employee.kyc.setting');
        Route::post('employee/kyc-setting', 'agentSettingUpdate')->name('employee.kyc.setting');
    });

    // employees Manager
    Route::controller('ManageEmployeesController')->name('employees.')->prefix('manage/employees')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email/verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email/unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile/unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with/balance', 'usersWithBalance')->name('with.balance');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send/notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send/notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification/log/{id}', 'notificationLog')->name('notification.log');

        // kyc
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
    });

    Route::controller('LocationController')->name('location.')->prefix('location')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'delete')->name('delete');
    });

    // Subscriber
    Route::controller('SubscriberController')->group(function () {
        Route::get('subscriber', 'index')->name('subscriber.index');
        Route::get('subscriber/send/email', 'sendEmailForm')->name('subscriber.send.email');
        Route::post('subscriber/remove/{id}', 'remove')->name('subscriber.remove');
        Route::post('subscriber/send/email', 'sendEmail')->name('subscriber.send.email');
    });

    // category
    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update', 'update')->name('update');
        Route::post('status-change/{id}', 'statusChange')->name('status.change');
    });

    //Tour Package
    Route::controller('TourPackageController')->name('tour.package.')->prefix('tour-package')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('update/{id}', 'update')->name('update');
        Route::get('status-change/{id}', 'statusChange')->name('status.change');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::get('my-tour', 'myList')->name('my.list');
        Route::get('employee-tour', 'allemployee')->name('all.employee');
        Route::get('search', 'search')->name('search');
        Route::post('image', 'tourPackageImageDelete')->name('image.delete');
        Route::get('pending', 'pending')->name('pending');
        Route::get('cancelled', 'cancelled')->name('cancelled');
        Route::get('active', 'active')->name('active');
        Route::get('expired', 'expired')->name('expired');
        Route::get('running', 'running')->name('running');
    });

    // Listings
    Route::controller('ListingController')->name('listing.')->prefix('listings')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::post('status-change/{id}', 'statusChange')->name('status.change');
    });

    // Listing Type
    Route::controller('ListingTypeController')->name('listing.type.')->prefix('listing-type')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update', 'update')->name('update');
        Route::post('status-change/{id}', 'statusChange')->name('status.change');
    });

    // Reels
    Route::controller('ReelController')->name('reels.')->prefix('reels')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('status-change/{id}', 'statusChange')->name('status.change');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::get('comments', 'comments')->name('comments');
        Route::get('comments/status/{id}', 'commentStatusChange')->name('comments.status');
        Route::post('comments/delete/{id}', 'commentDelete')->name('comments.delete');
        Route::post('comments/reply/{id}', 'commentReply')->name('comments.reply');
    });

    // Membership and loyalty
    Route::controller('MembershipController')->name('membership.')->prefix('membership')->group(function () {
        Route::get('plans', 'plans')->name('plans');
        Route::get('plans/create', 'create')->name('plans.create');
        Route::post('plans/store', 'store')->name('plans.store');
        Route::get('plans/edit/{id}', 'edit')->name('plans.edit');
        Route::post('plans/update/{id}', 'update')->name('plans.update');
        Route::post('plans/delete/{id}', 'delete')->name('plans.delete');

        Route::get('subscriptions', 'subscriptions')->name('subscriptions');
        Route::get('subscriptions/edit/{id}', 'editSubscription')->name('subscriptions.edit');
        Route::post('subscriptions/update/{id}', 'updateSubscription')->name('subscriptions.update');
        Route::get('points', 'points')->name('points');
        Route::post('points/store', 'storePoint')->name('points.store');
        Route::get('cashback', 'cashback')->name('cashback');
        Route::post('cashback/store', 'storeCashback')->name('cashback.store');
        Route::get('reports', 'reports')->name('reports');
    });

    Route::controller('MembershipBenefitController')->name('membership.benefits.')->prefix('membership/benefits')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::get('user/{id}', 'userBenefits')->name('user');
    });

    Route::controller('PrivilegeCardController')->name('privilege.cards.')->prefix('privilege-cards')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::post('status-change/{id}', 'statusChange')->name('status.change');
    });


    //Booking Controller
    Route::controller('BookingController')->name('tour.package.booking.')->group(function () {
        Route::get('/packages-list', 'bookingTourPackageList')->name('index');
        Route::redirect('/package-list', '/packages-list');
        Route::redirect('/tour-booking-list', '/packages-list');
        Route::get('/my-booked', 'myBooked')->name('my.booked');
        Route::post('/booking-preview', 'bookingPreview')->name('preview');
        Route::post('/booking-delete', 'bookingDelete')->name('delete');
        Route::get('/booking-pending', 'bookingTourPackagePending')->name('pending');
        Route::get('/booking-approved', 'bookingTourPackageApproved')->name('approved');
        Route::get('/booking-canceled', 'bookingTourPackageCanceled')->name('canceled');
        Route::get('/booking-list-employees', 'bookingAgentList')->name('employee.index');
        Route::get('/booking-user-list/{id}', 'userList')->name('user.list');
        Route::get('/booking-employee-user-list/{id}', 'employeeBookedUserList')->name('employee.user.list');
        Route::get('/booking-details/{id}', 'bookingDetails')->name('details');
    });

    Route::controller('ServiceBookingController')->name('service.booking.')->prefix('booking-list')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/hotels', 'hotelList')->name('hotels');
        Route::get('/flights', 'flightList')->name('flights');
        Route::get('/transportation', 'transportList')->name('transportation');
        Route::post('/status-update/{id}', 'statusUpdate')->name('status.update');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'destroy')->name('delete');
    });

    // Invoice Management
    Route::controller('InvoiceController')->name('invoice.')->prefix('invoices')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/get-bookings', 'getBookings')->name('getBookings');
        Route::post('/store', 'store')->name('store');
        Route::post('/status/update', 'statusUpdate')->name('status.update');
    });


    // Deposit Gateway
    Route::name('gateway.')->prefix('payment/gateways')->group(function () {

        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->group(function () {
            Route::get('automatic', 'index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'update')->name('automatic.update');
            Route::post('automatic/remove/{id}', 'remove')->name('automatic.remove');
            Route::post('automatic/activate/{code}', 'activate')->name('automatic.activate');
            Route::post('automatic/deactivate/{code}', 'deactivate')->name('automatic.deactivate');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->group(function () {
            Route::get('manual', 'index')->name('manual.index');
            Route::get('manual/new', 'create')->name('manual.create');
            Route::post('manual/new', 'store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'update')->name('manual.update');
            Route::post('manual/activate/{code}', 'activate')->name('manual.activate');
            Route::post('manual/deactivate/{code}', 'deactivate')->name('manual.deactivate');
        });
    });


    // DEPOSIT SYSTEM
    Route::name('deposit.')->controller('DepositController')->prefix('manage/deposits')->group(function () {
        Route::get('/', 'deposit')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('successful', 'successful')->name('successful');
        Route::get('initiated', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');

        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
        Route::post('refresh-link/{id}', 'App\Http\Controllers\PaymentLinkController@refresh')->name('refresh.link');
    });


    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('manage/withdrawals')->group(function () {

        Route::controller('WithdrawalController')->group(function () {
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
            Route::get('log', 'log')->name('log');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->group(function () {
            Route::get('method/', 'methods')->name('method.index');
            Route::get('method/create', 'create')->name('method.create');
            Route::post('method/create', 'store')->name('method.store');
            Route::get('method/edit/{id}', 'edit')->name('method.edit');
            Route::post('method/edit/{id}', 'update')->name('method.update');
            Route::post('method/activate/{id}', 'activate')->name('method.activate');
            Route::post('method/deactivate/{id}', 'deactivate')->name('method.deactivate');
        });
    });

    // USER WALLET SYSTEM
    Route::controller('UserWalletController')->prefix('customer-wallets')->name('wallet.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('action', 'action')->name('action');
    });

    // Report
    Route::controller('ReportController')->group(function () {
        Route::get('report/transaction', 'transaction')->name('report.transaction');
        Route::get('report/login/history', 'loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/notification/history', 'notificationHistory')->name('report.notification.history');
        Route::get('report/email/detail/{id}', 'emailDetails')->name('report.email.details');

        // employee
        Route::get('employee/report/transaction', 'employeeTransaction')->name('employee.report.transaction');
        Route::get('employee/report/login/history', 'employeeLoginHistory')->name('employee.report.login.history');
        Route::get('employee/report/login/ipHistory/{ip}', 'employeeLoginIpHistory')->name('employee.report.login.ipHistory');
        Route::get('employee/report/notification/history', 'employeeNotificationHistory')->name('employee.report.notification.history');
        Route::get('employee/report/email/detail/{id}', 'employeeEmailDetails')->name('employee.report.email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('support')->group(function () {
        Route::get('tickets', 'tickets')->name('ticket');
        Route::get('tickets/pending', 'pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'ticketReply')->name('ticket.view');
        Route::post('ticket/assign/{id}', 'assignTicket')->name('ticket.assign');
        Route::post('ticket/reply/{id}', 'replyTicket')->name('ticket.reply');
        Route::post('ticket/close/{id}', 'closeTicket')->name('ticket.close');
        Route::get('ticket/download/{ticket}', 'ticketDownload')->name('ticket.download');
        Route::post('ticket/delete/{id}', 'ticketDelete')->name('ticket.delete');
        Route::post('ticket/delete-ticket/{id}', 'deleteSupportTicket')->name('ticket.delete_ticket');
        Route::post('ticket/bulk-delete-tickets', 'bulkDeleteSupportTickets')->name('ticket.bulk_delete_tickets');

        //employee
        Route::get('employee/tickets', 'employeeTickets')->name('employee.ticket');
        Route::get('employee/tickets/pending', 'employeePendingTicket')->name('employee.ticket.pending');
        Route::get('employee/tickets/closed', 'employeeClosedTicket')->name('employee.ticket.closed');
        Route::get('employee/tickets/answered', 'employeeAnsweredTicket')->name('employee.ticket.answered');
        Route::get('employee/tickets/view/{id}', 'ticketReply')->name('employee.ticket.view');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('manage')->group(function () {
        Route::get('languages', 'langManage')->name('language.manage');
        Route::post('language', 'langStore')->name('language.manage.store');
        Route::post('language/delete/{id}', 'langDelete')->name('language.manage.delete');
        Route::post('language/update/{id}', 'langUpdate')->name('language.manage.update');
        Route::get('language/edit/{id}', 'langEdit')->name('language.key');
        Route::post('language/import', 'langImport')->name('language.import.lang');
        Route::post('language/store/key/{id}', 'storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'updateLanguageJson')->name('language.update.key');
        Route::get('language/search/', 'langSearch')->name('language.manage.search');
        Route::get('language/search/replace/', 'langSearchReplace')->name('language.manage.search.replace');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('global/settings', 'index')->name('setting.index');
        Route::post('global/settings', 'update')->name('setting.update');

        //configuration
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo', 'logoIconUpdate')->name('setting.logo.icon');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');
        // employee
        Route::get('setting/social/employee/credentials', 'socialiteemployeeCredentials')->name('setting.socialite.employee.credentials');
        Route::post('setting/social/employee/credentials/update/{key}', 'updateemployeeSocialiteCredential')->name('setting.socialite.employee.credentials.update');
        Route::post('setting/social/employee/credentials/status/{key}', 'updateemployeeSocialiteCredentialStatus')->name('setting.socialite.employee.credentials.status.update');
        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notifications')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin
    Route::controller('ExtensionController')->group(function () {
        Route::get('extensions', 'index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'update')->name('extensions.update');
        Route::post('extensions/status/{id}', 'status')->name('extensions.status');
    });

    Route::controller('ChatAssistantController')->prefix('chat-assistant')->name('chat-assistant.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('conversations', 'conversations')->name('conversations');
        Route::get('conversations/{conversation}', 'show')->name('show');
        Route::get('conversations/{conversation}/view', 'view')->name('view');
        Route::post('conversations/{conversation}/reopen', 'reopen')->name('reopen');
        Route::post('conversations/{conversation}/reply', 'reply')->name('reply');
        Route::post('conversations/{conversation}/close', 'close')->name('close');
        Route::post('conversations/{conversation}/delete', 'destroy')->name('delete');
        Route::post('bulk-delete', 'bulkDelete')->name('bulk.delete');
    });

    Route::controller('PopupAdController')->prefix('popup-ads')->name('popup-ads.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{popupAd}/edit', 'edit')->name('edit');
        Route::post('{popupAd}/update', 'update')->name('update');
        Route::post('{popupAd}/status', 'status')->name('status');
        Route::post('{popupAd}/delete', 'destroy')->name('delete');
        Route::get('{popupAd}/analytics', 'analytics')->name('analytics');
    });
    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->prefix('manage')->group(function () {
            Route::get('pages', 'managePages')->name('manage.pages');
            Route::post('pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('section/{id}', 'manageSection')->name('manage.section');
            Route::post('section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });
});
