<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::permanentRedirect('destinations', '/more-travel/destinations');
Route::permanentRedirect('coupons', '/offers/coupons');
Route::permanentRedirect('vouchers', '/offers/vouchers');
Route::permanentRedirect('weekend', '/offers/weekend');
Route::permanentRedirect('video-gallery', '/reels');
Route::permanentRedirect('customer-reviews', '/client-feedback');
Route::permanentRedirect('arm_member_profile', '/user/login');
Route::permanentRedirect('membership-register', '/user/register');
Route::permanentRedirect('register', '/user/register');
Route::permanentRedirect('signup', '/user/register');

// Travel offers (explicit routes — not inside string-based Route::controller group)
Route::permanentRedirect('bookings/hotels', '/listings');
Route::permanentRedirect('bookings/flights', '/browse');
Route::permanentRedirect('memberships', '/membership-details');
Route::permanentRedirect('payments', '/e-payment');

Route::get('offers/{category}', 'App\Http\Controllers\SiteController@offersCategory')
    ->whereIn('category', ['limited', 'all', 'yearly', 'weekend', 'spa', 'spa-beauty', 'coupons', 'vouchers'])
    ->name('public.offers.index');
Route::permanentRedirect('limited-offers', '/offers/limited');
Route::get('limited-offers/{legacyType}', static function (\Illuminate\Http\Request $request, string $legacyType) {
    $map = [
        'year-offers' => 'yearly',
        'weekend-offers' => 'weekend',
        'spa-beauty-offers' => 'spa',
        'coupons' => 'coupons',
        'vouchers' => 'vouchers',
    ];
    $key = Str::lower($legacyType);
    $canonical = $map[$key] ?? 'limited';

    return redirect()->route(
        'public.offers.index',
        array_merge(['category' => $canonical], $request->query())
    )->setStatusCode(301);
})->where('legacyType', '[A-Za-z0-9\-]+');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->group(function () {
    Route::get('/', 'supportTicket')->name('ticket');
    Route::get('/new', 'openSupportTicket')->name('ticket.open');
    Route::post('/create', 'storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'replyTicket')->name('ticket.reply');
    Route::post('/close/{ticket}', 'closeTicket')->name('ticket.close');
    Route::get('/download/{ticket}', 'ticketDownload')->name('ticket.download');
});

Route::controller('Admin\AdController')->group(function () {
    Route::get('/ads/{ad}/{type}/{adType}', 'getAdvertise')->name('adsUrl');
    Route::get('/ad-clicked/{adid}', 'adClicked')->name('adClicked');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('ReelController')->prefix('reels')->group(function () {
    Route::get('/', 'index')->name('reels.index');
    Route::post('{reel}/view', 'trackView')->name('reels.view');
    Route::post('{reel}/like', 'toggleLike')->name('reels.like');
    Route::post('{reel}/save', 'toggleSave')->name('reels.save');
    Route::post('{reel}/comment', 'storeComment')->name('reels.comment');
});

Route::get('chat-assistant', 'ChatAssistantController@page')->name('chat-assistant.page');

Route::controller('ChatAssistantController')->prefix('chat-assistant')->name('chat-assistant.')->group(function () {
    Route::get('bootstrap', 'bootstrap')->name('bootstrap');
    Route::post('message', 'message')->name('message');
    Route::get('poll', 'poll')->name('poll');
    Route::post('handover', 'handover')->name('handover');
    Route::post('clear', 'clear')->name('clear');
});

Route::post('popup-ads/{popupAd}/track', 'App\Http\Controllers\PopupAdTrackController@track')->name('popup-ads.track');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit')->name('contact.submit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');
    Route::permanentRedirect('policy/all', '/our-privacy');
    Route::get('our-privacy/website-policy', 'App\Http\Controllers\PolicyController@websitePolicy')->name('policy.website');
    Route::get('our-privacy/terms-and-conditions', 'App\Http\Controllers\PolicyController@termsAndConditions')->name('policy.terms');
    Route::get('our-privacy', 'App\Http\Controllers\PolicyController@index')->name('policy.index');
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');
    Route::post('image-upload', 'imageUplaod')->name('image.upload');

    Route::get('more-travel/{section?}', 'moreTravel')->name('public.travel.index');
    Route::get('membership-login', 'membershipLogin')->name('membership.login');
    Route::get('membership-register', 'membershipRegister')->name('membership.register');
    Route::get('packages', 'packagesIntro')->name('packages.intro');
    Route::get('membership-card', 'membershipCard')->name('public.membership.card');
    Route::get('membership-details', 'membershipDetails')->name('public.membership.details');
    Route::get('membership-details/{id}', 'membershipDetailsShow')->name('public.membership.details.show');
    Route::redirect('subscription-cards', 'membership-details');
    Route::get('privilege-cards', 'privilegeCards')->name('public.privilege.cards.index');
    Route::get('engine-screen', 'engineScreen')->name('public.engine.screen');
    Route::get('e-payment', 'EPaymentController@index')->name('e.payment');
    Route::post('e-payment', 'EPaymentController@store')->middleware('throttle:10,1')->name('e.payment.store');
    Route::get('e-payment/result/{trx?}', 'EPaymentController@result')->name('e.payment.result');

    Route::get('tour-package/{slug}/{id}', 'tourPackageDetails')->name('tour.package.details');
    Route::get('client-feedback', 'clientFeedback')->name('public.client.feedback');
    Route::get('browse', 'tourPackageList')->name('browse');
    Route::get('listings', 'listings')->name('listings');
    Route::get('listings/{slug}/{id}', 'listingDetails')->name('listing.details');
    Route::get('listings/{slug}/{id}/book', 'listingBooking')->name('listing.booking');
    Route::post('listings/{slug}/{id}/book', 'listingBookingStore')->name('listing.booking.store');

    Route::get('invoice/{invoice_number}', 'App\Http\Controllers\InvoiceController@show')->name('invoice.show');
    Route::get('invoice/{invoice_number}/download', 'App\Http\Controllers\InvoiceController@download')->name('invoice.download');

    Route::post('subscribe','subscribe')->name('subscribe');
    Route::get('tour-side-filter','tourPackageSideFilter')->name('tour.package.side.filter');

    Route::post('service-booking/submit', 'serviceBookingSubmit')->name('service.booking.submit');
    Route::get('sitemap.xml', 'sitemap')->name('sitemap');
    Route::get('robots.txt', 'robots')->name('robots');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

