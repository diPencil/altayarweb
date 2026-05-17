<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Models\User;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Models\Refferal;
use App\Models\Extension;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\CommissionLog;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sysInfo()
{
    $system['name'] = 'AltayarVIP';
    $system['version'] = '1.0.0';
    $system['build_version'] = '1.1.3';
    $system['admin_version'] = '10.3.0';
    return $system;
}

function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset)
        return 'assets/presets/' . $template . '/';
    return 'presets.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Extension::where('act', $key)->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function aiChatAssistantExtension()
{
    return Extension::where('act', 'ai-chat-assistant')->first();
}

function aiChatAssistantEnabled()
{
    $extension = aiChatAssistantExtension();
    if (request()->routeIs('chat-assistant.page')) {
        return false;
    }

    return $extension && (int) $extension->status === 1;
}

function aiChatAssistantConfig()
{
    $extension = aiChatAssistantExtension();
    if (!$extension || !$extension->shortcode) {
        return [];
    }

    return collect($extension->shortcode)->mapWithKeys(function ($item, $key) {
        $value = is_array($item) ? ($item['value'] ?? null) : ($item->value ?? null);
        return [$key => $value];
    })->all();
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function isWishlist($tourPackage = null)
{
    if (!auth()->check()) {
        return '<i class="far fa-heart"></i>';
    }

    $userId = auth()->user()->id;

    if ($tourPackage && $tourPackage->wishlists->where('user_id', $userId)->where('tour_package_id', $tourPackage->id)->isNotEmpty()) {
        return '<i class="fas fa-heart text--base"></i>';
    }

    return '<i class="far fa-heart"></i>';
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.wstacks.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function systemDetails()
{
    $system['name'] = 'altayarbooking';
    $system['version'] = '1.0';
    return $system;
}



function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections/builder/builder.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null)
{
    $clean = '';
    if (preg_match('/(https?:\/\/.*)$/', $image, $matches)) {
        return $matches[1];
    }
    try {
        $absolutePath = realpath($image);
        if ($absolutePath && is_readable($absolutePath) && !is_dir($absolutePath)) {
            return asset($image) . $clean;
        }
    } catch (\Exception $e) {
        return asset('assets/images/general/default.png');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/general/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = GeneralSetting::first();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
        'logo_url' => getImage(getFilePath('logoIcon') . '/logo.png'),
        'year' => date('Y'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = getColumnName($user);
    $notify->send();
}

function getColumnName($user)
{
    $array = explode("\\", get_class($user));
    $column = strtolower(end($array)) . '_id';
    if ($column == 'employee_id') {
        $column = 'agent_id';
    }
    return $column;
}

function getPaginate($paginate = 20)
{
    return request()->rows ?? $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3)
        $class = 'side-menu--open';
    elseif ($type == 2)
        $class = 'sidebar-submenu__open';
    else
        $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value))
                return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            if (request()->route($param[0]) == $param[1])
                return $class;
            else
                return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $watermark = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->watermark = $watermark;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function getFileThumbSize($key)
{
    return fileManager()->$key()->thumb;
}
function getFileWatermarkSize($key)
{
    return fileManager()->$key()->watermark;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'M d, Y - h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
        if ($content) {
            $content->data_values = (object) array_merge((array) $content->data_values, (array) collect($content->data_values)->mapWithKeys(function ($value, $key) {
                if (str_ends_with($key, '_' . session('lang'))) {
                    return [str_replace('_' . session('lang'), '', $key) => $value];
                }
                return [$key => $value];
            })->toArray());
        }
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }

        $content->map(function ($item) {
            $item->data_values = (object) array_merge((array) $item->data_values, (array) collect($item->data_values)->mapWithKeys(function ($value, $key) {
                if (str_ends_with($key, '_' . session('lang'))) {
                    return [str_replace('_' . session('lang'), '', $key) => $value];
                }
                return [$key => $value];
            })->toArray());
            return $item;
        });
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"]; // Default to REMOTE_ADDR

    // Check for various headers and validate IPs
    if (isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    // Convert IPv6 localhost (::1) to IPv4 localhost (127.0.0.1)
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}



function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}


function employee()
{
    return auth()->guard('employee')->user();
}
function employeeId()
{
    return auth()->guard('employee')->id();
}

function employeeDashboardWidgets(): array
{
    return [
        'total_assigned_users' => 'Assigned Users',
        'total_tour_package' => 'Total Tour Package',
        'total_listing_offers' => 'Total Listing Offers',
        'total_support_ticker' => 'Total Ticket',
        'total_open_support_ticker' => 'Open Ticket',
        'total_popup_ads' => 'Popup Ads',
        'total_transaction' => 'Total Transactions',
        'total_payment_logs' => 'Payment Logs',
        'charts' => 'Charts',
        'recent_bookings' => 'Recent Bookings',
    ];
}

function employeeDashboardCan(string $key, $employee = null): bool
{
    $employee = $employee ?: employee();

    if (!$employee) {
        return false;
    }

    $permissions = $employee->dashboard_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function employeeMenuPermissions(): array
{
    return [
        'profile' => 'Profile (menu)',
        'password' => 'Change Password (menu)',
        'twofactor' => '2FA Security (menu)',
        'kyc' => 'KYC (menu)',
        'tour_packages' => 'Tour Package (menu)',
        'listings' => 'Listing Offers (menu)',
        'bookings' => 'Package Bookings (menu)',
        'popup_ads' => 'Popup Ads (menu)',
        'withdraw' => 'Withdraw (menu)',
        'payments' => 'Payments (menu)',
        'tickets' => 'Support Tickets (menu)',
    ];
}

function employeeMenuCan(string $key, $employee = null): bool
{
    $employee = $employee ?: employee();

    if (!$employee) {
        return false;
    }

    $permissions = $employee->menu_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function userDashboardWidgets(): array
{
    return [
        'total_all_bookings' => 'All Bookings',
        'total_invoices' => 'Invoices',
        'total_pending_tour' => 'Pending Bookings',
        'total_approved_tour' => 'Approved Bookings',
        'saved_reels' => 'Saved Reels',
        'active_tickets' => 'Active Tickets',
        'total_tickets' => 'Total Tickets',
        'open_tickets' => 'Open Tickets',
        'total_transactions' => 'Total Transactions',
        'wallet_balance' => 'Wallet Balance',
        'loyalty_points' => 'Loyalty Points',
        'cashback' => 'Cashback Balance',
        'charts' => 'Charts',
        'recent_bookings' => 'Recent Bookings Table',
        'sidebar_membership' => 'Sidebar Membership Card',
    ];
}

function userDashboardCan(string $key, $user = null): bool
{
    $user = $user ?: auth()->user();

    if (!$user) {
        return false;
    }

    $permissions = $user->dashboard_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function userMenuPermissions(): array
{
    return [
        'profile' => 'Profile (Dropdown Link)',
        'password' => 'Change Password (Dropdown Link)',
        'twofactor' => '2FA Security (Dropdown Link)',
        'payments' => 'Payments (Menu Group)',
        'bookings' => 'Bookings (Menu Group)',
        'membership' => 'Membership (Menu Group)',
        'wallet' => 'My Wallet (Menu Link)',
        'wishlist' => 'Wishlists (Menu Link)',
        'reels' => 'Reels Library (Menu Link)',
        'tickets' => 'Support Tickets (Menu Group)',
    ];
}

function userMenuCan(string $key, $user = null): bool
{
    $user = $user ?: auth()->user();

    if (!$user) {
        return false;
    }

    $permissions = $user->menu_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function employeePagePermissions(): array
{
    return [
        'profile' => 'Profile (page)',
        'password' => 'Change Password (page)',
        'twofactor' => '2FA Security (page)',
        'kyc' => 'KYC (page)',
        'tour_packages' => 'Tour Package (page)',
        'listings' => 'Listing Offers (page)',
        'bookings' => 'Package Bookings (page)',
        'popup_ads' => 'Popup Ads (page)',
        'withdraw' => 'Withdraw (page)',
        'payments' => 'Payments (page)',
        'tickets' => 'Support Tickets (page)',
    ];
}

function employeePageCan(string $key, $employee = null): bool
{
    $employee = $employee ?: employee();

    if (!$employee) {
        return false;
    }

    $permissions = $employee->page_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function employeeUserPermissions(): array
{
    return [
        'users' => 'Users Page',
        'bookings' => 'Booking Customer Lists',
        'booking_details' => 'Booking Details with Customer Data',
        'popup_ads_customers' => 'Popup Ads Customer Selection',
    ];
}

function employeeUserCan(string $key, $employee = null): bool
{
    $employee = $employee ?: employee();

    if (!$employee) {
        return false;
    }

    $permissions = $employee->user_permissions ?? null;

    if (empty($permissions)) {
        return true;
    }

    if ($permissions instanceof \Illuminate\Support\Collection) {
        $permissions = $permissions->all();
    }

    if (is_object($permissions)) {
        $permissions = (array) $permissions;
    }

    if (!is_array($permissions)) {
        return true;
    }

    return in_array($key, $permissions, true);
}

function employeeRouteUserPermissionKey(?string $routeName): ?string
{
    if (!$routeName) {
        return null;
    }

    $routeMap = [
        'employee.users' => 'users',
        'employee.users.detail' => 'users',
        'employee.tour.package.booking.user.list' => 'bookings',
        'employee.tour.package.booking.details' => 'booking_details',
    ];

    return $routeMap[$routeName] ?? null;
}

function employeeRoutePermissionKey(?string $routeName): ?string
{
    if (!$routeName) {
        return null;
    }

    $routeMap = [
        'employee.profile.setting' => 'profile',
        'employee.change.password' => 'password',
        'employee.twofactor' => 'twofactor',
        'employee.kyc.form' => 'kyc',
        'employee.kyc.data' => 'kyc',
        'employee.tour.package.index' => 'tour_packages',
        'employee.tour.package.create' => 'tour_packages',
        'employee.tour.package.edit' => 'tour_packages',
        'employee.tour.package.update' => 'tour_packages',
        'employee.tour.package.store' => 'tour_packages',
        'employee.tour.package.delete' => 'tour_packages',
        'employee.tour.package.status.change' => 'tour_packages',
        'employee.tour.package.my.list' => 'tour_packages',
        'employee.tour.package.all.agent' => 'tour_packages',
        'employee.tour.package.search' => 'tour_packages',
        'employee.listing.index' => 'listings',
        'employee.listing.create' => 'listings',
        'employee.listing.edit' => 'listings',
        'employee.listing.update' => 'listings',
        'employee.listing.store' => 'listings',
        'employee.listing.delete' => 'listings',
        'employee.listing.status.change' => 'listings',
        'employee.tour.package.booking.my.list' => 'bookings',
        'employee.tour.package.booking.all.list' => 'bookings',
        'employee.tour.package.booking.pending' => 'bookings',
        'employee.tour.package.booking.approved' => 'bookings',
        'employee.tour.package.booking.canceled' => 'bookings',
        'employee.popup-ads.index' => 'popup_ads',
        'employee.popup-ads.create' => 'popup_ads',
        'employee.popup-ads.edit' => 'popup_ads',
        'employee.popup-ads.update' => 'popup_ads',
        'employee.popup-ads.store' => 'popup_ads',
        'employee.popup-ads.status' => 'popup_ads',
        'employee.popup-ads.delete' => 'popup_ads',
        'employee.withdraw' => 'withdraw',
        'employee.withdraw.money' => 'withdraw',
        'employee.withdraw.preview' => 'withdraw',
        'employee.withdraw.submit' => 'withdraw',
        'employee.withdraw.history' => 'withdraw',
        'employee.e.payment' => 'payments',
        'employee.e.payment.store' => 'payments',
        'employee.e.payment.result' => 'payments',
        'employee.transactions' => 'payments',
        'employee.deposit.history' => 'payments',
        'employee.ticket' => 'tickets',
        'employee.ticket.open' => 'tickets',
        'employee.ticket.store' => 'tickets',
        'employee.ticket.view' => 'tickets',
        'employee.ticket.reply' => 'tickets',
        'employee.ticket.close' => 'tickets',
        'employee.ticket.download' => 'tickets',
    ];

    return $routeMap[$routeName] ?? null;
}

function isActiveRoute($routes)
{
    return Str::startsWith(Route::currentRouteName(), $routes);
}

function discountShowAmount($amount)
{
    if (fmod($amount, 1) == 0.0) {
        $finalAmount = (int) $amount;
    } else {
        $finalAmount = $amount;
    }
    return $finalAmount;
}

function listingAmount($amount)
{
    return showAmount($amount, 2, true, true);
}

function listingPriceLabel($amount, $currency = null)
{
    $amount = (float) $amount;

    if ($amount <= 0) {
        return __('Free for Membership');
    }

    return listingAmount($amount) . ' ' . __($currency ?? 'USD');
}


function showRatings($rating)
{

    $ratings = '';
    if ($rating > 0) {
        $avgRating = $rating;
        $integerVal = floor($avgRating);
        $fraction = $avgRating - $integerVal;

        if ($fraction < .25) {
            $avgRating = intval($avgRating);
        }
        if ($fraction > .75) {
            $avgRating = intval($avgRating) + 1;
        }
        for ($i = 1; $i <= $avgRating; $i++) {
            $ratings .= '<i class="fas fa-star"></i>';
        }
        if ($fraction > .25 && $fraction < .75) {
            $avgRating += 1;
            $ratings .= '<i class="fas fa-star-half-alt"></i>';
        }
    } else {
        $avgRating = 0;
    }
    $nonStar = 5 - intval($avgRating);
    for ($k = 1; $k <= $nonStar; $k++) {
        $ratings .= '<i class="far fa-star"></i>';
    }
    return $ratings;
}


function calculateIndividualRating($averageRating)
{
    if (empty($averageRating)) {
        return '';
    }
    $fullStars = floor($averageRating);

    $halfStar = ceil($averageRating - $fullStars);
    $emptyStars = 5 - $fullStars - $halfStar;
    $ratingHtml = '';
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="fas fa-star"></i>';
        $ratingHtml .= '</li>';
    }
    // Half star
    if ($halfStar > 0) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="fas fa-star-half-alt"></i>';
        $ratingHtml .= '</li>';
    }
    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="far fa-star"></i>';
        $ratingHtml .= '</li>';
    }
    return $ratingHtml;
}

function showTourPackageCalculateDiscount($mainPrice, $discountPrice)
{

    $totalPrice = $mainPrice - ($mainPrice / 100) * $discountPrice ?? 1;
    return $totalPrice;
}

function iconCheck($icon)
{
    $iconHtml = $icon;
    $contains = Str::contains($iconHtml, 'times');
    if ($contains) {
        $iconHtml = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
            return 'class="' . $matches[1] . ' text--danger"';
        }, $iconHtml);
    } else {
        $iconHtml = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
            return 'class="' . $matches[1] . ' text--success"';
        }, $iconHtml);
    }
    return $iconHtml;
}

function tourVacationCount($start_date, $end_date)
{
    $startDate = Carbon::parse($start_date);
    $endDate = Carbon::parse($end_date);
    $tourVacationDate = $startDate->diffInDays($endDate);
    return $tourVacationDate;
}

function getFollower()
{
    if (Auth::check()) {
        return Auth::user();
    } elseif (Auth::guard('employee')->check()) {
        return Auth::guard('employee')->user();
    }
    return null;
}



function is_rtl()
{
    $lang = session()->get('lang');
    $language = \App\Models\Language::where('code', $lang)->first();

    if ($language) {
        return (int) $language->text_align === 1;
    }

    if ($lang == 'ar' || $lang == 'he' || $lang == 'fa') {
        return true;
    }
    return false;
}

/**
 * Get multilingual content from a Frontend data_values object.
 *
 * Usage in blade: {{ getLangContent($content->data_values, 'title') }}
 *
 * If a language-specific key exists (e.g. title_ar), it returns that.
 * Falls back to the default language key, then the plain field via __().
 */
function getLangContent($dataValues, string $field): string
{
    if (!$dataValues)
        return '';

    $lang = session()->get('lang', 'en');

    // Try language-specific key first: title_ar, title_en, etc.
    $langField = $field . '_' . $lang;
    if (isset($dataValues->$langField) && !empty($dataValues->$langField)) {
        return $dataValues->$langField;
    }

    // Fallback: try default language key
    $defaultLang = \App\Models\Language::where('is_default', 1)->value('code') ?? 'en';
    $defaultField = $field . '_' . $defaultLang;
    if (isset($dataValues->$defaultField) && !empty($dataValues->$defaultField)) {
        return $dataValues->$defaultField;
    }

    // Final fallback: plain key — run through translator so values that match
    // lang keys (e.g. "Why Choose Us") still localize when no title_ar exists.
    return __($dataValues->$field ?? '');
}

/**
 * Policy hub (/our-privacy): use CMS text only when that locale has its own field,
 * so we do not show Arabic copy under English (or vice versa) via default-language fallback.
 */
function policy_hub_cms_field($dataValues, string $field): string
{
    if (!$dataValues) {
        return '';
    }
    $lang = session()->get('lang', app()->getLocale());
    $key = $field . '_' . $lang;
    if (isset($dataValues->$key) && trim((string) $dataValues->$key) !== '') {
        return trim((string) $dataValues->$key);
    }

    return '';
}

/**
 * Uses admin id 42 (existing image mapping) plus common English title tokens.
 */
function policy_is_website_policy_card($policy): bool
{
    $id = (int) ($policy->id ?? 0);
    if ($id === 42) {
        return true;
    }
    $raw = strtolower($policy->data_values->title ?? '');
    return str_contains($raw, 'privacy')
        || str_contains($raw, 'website policy');
}

/** Public H1 / card title / document title (legacy site labels). */
function policy_public_heading($policy): string
{
    return policy_is_website_policy_card($policy) ? __('Website Policy') : __('Terms & Conditions');
}

/** Optional blue eyebrow line on listing cards (terms card only on old site). */
function policy_card_eyebrow($policy): ?string
{
    return policy_is_website_policy_card($policy) ? null : __('Terms And Conditions');
}

/** Canonical URL for a policy CMS row (static pages under /our-privacy/). */
function policy_detail_url($policy): string
{
    if (policy_is_website_policy_card($policy)) {
        return route('policy.website');
    }
    return route('policy.terms');
}
