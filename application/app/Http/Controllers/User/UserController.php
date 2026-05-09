<?php

namespace App\Http\Controllers\User;

use App\Models\Form;
use App\Models\Review;
use App\Models\Deposit;
use App\Models\Wishlist;
use App\Models\ReelInteraction;
use App\Lib\FormProcessor;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use App\Models\TourPackage;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\AdminNotification;
use App\Models\SupportTicket;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user =  auth()->user();
        $myBookings =  TourBooking::with('tour_package')->userAll()->paginate(getPaginate());
        $widget['total_service_bookings'] = ServiceBooking::where('user_id', $user->id)->count();
        $widget['total_tour_package'] =  TourBooking::where('user_id', $user->id)->count();
        $widget['total_all_bookings'] = $widget['total_tour_package'] + $widget['total_service_bookings'];
        $widget['total_pending_tour_package'] =  TourBooking::userPending()->count();
        $widget['total_approved_tour_package'] =  TourBooking::UserApproved()->count();
        $widget['total_canceled_tour_package'] =  TourBooking::UserCanceled()->count();
        $widget['wishlists'] =  Wishlist::where('user_id', $user->id)->count();
        $widget['total_transaction'] =  Transaction::where('user_id', $user->id)->count();
        $widget['total_support_ticker'] =  SupportTicket::where('user_id',auth()->id())
        ->count();
        $widget['saved_reels'] = ReelInteraction::where('user_id', $user->id)->where('type', 'save')->count();
        $widget['total_active_support_ticker'] =  SupportTicket::where('user_id',auth()->id())
        ->where('status',1)->count();
        $widget['total_open_support_ticker'] =  SupportTicket::where('user_id',auth()->id())
        ->where('status',0)->count();

        $widget['total_invoices'] = Invoice::where('user_id', $user->id)->count();

        $widget['balance'] =   $user->balance;
        $widget['membership_points'] = $user->membership_points_balance;
        $widget['cashback_balance'] = $user->cashback_balance;
        $widget['current_membership'] = $user->currentMembership()->with('plan')->first();

        $deposits = Deposit::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
            ->whereYear('created_at', date('Y'))
            ->whereStatus(1)
            ->where('user_id', $user->id)
            ->groupBy('month_name', 'month_num')
            ->orderBy('month_num')
            ->get();
        $depositsChart['labels'] = $deposits->pluck('month_name');
        $depositsChart['values'] = $deposits->pluck('amount');

        $tour_packageReport = TourBooking::selectRaw("SUM(price) as price, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
            ->whereYear('created_at', date('Y'))
            ->whereStatus(4)
            ->where('user_id', $user->id)
            ->groupBy('month_name', 'month_num')
            ->orderBy('month_num')
            ->get();
        $tourPackageChart['labels'] = $tour_packageReport->pluck('month_name');
        $tourPackageChart['values'] = $tour_packageReport->pluck('amount');

       

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'widget', 'tourPackageChart', 'depositsChart','myBookings'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = __('Payments History');
        $deposits = auth()->user()->deposits();
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = __('Transactions');
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id());

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type', $request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = auth()->user();
        $user->kyc_data = (object)$userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->reg_step == 1 || session()->has('admin_impersonating')) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->reg_step == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = (object)[
            'country' => optional($user->address)->country,
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    // review store
    public function reviewSubmit(Request $request)
    {
        $request->validate([
            'star' => 'required|integer|between:1,5',
            'review' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $tourPackage = TourPackage::findOrFail($request->tour_package_id);

        // Check if the user has ordered this specific tourPackage
        $hasOrdered = TourBooking::where('user_id', $user->id)
        ->where('user_id',auth()->id())
        ->where('tour_package_id', $tourPackage->id)
            ->exists();

        if (!$hasOrdered) {
            $notify[] = ['error', 'You can only leave a review for tour Package you have ordered from'];
            return back()->withNotify($notify);
        }

        $existingReview = Review::where('user_id', $user->id)
            ->where('tour_package_id', $tourPackage->id)
            ->first();

        if ($existingReview) {
            $notify[] = ['error', 'You have already submitted a review for this tour package'];
            return back()->withNotify($notify);
        }

        $review = new Review();
        $review->user_id = $user->id;
        $review->tour_package_id = $request->tour_package_id;
        $review->star  = $request->star;
        $review->review  = $request->review;
        $review->save();

        if ($review->save()) {
            $reviews = $tourPackage->reviews()->get();
            $reviewCount = $reviews->count();
            $totalRating = $reviews->sum('star');
            $newAverageRating = $totalRating / $reviewCount;
            $tourPackage->average_rating = $newAverageRating;
            $tourPackage->review_count += 1;
            $tourPackage->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New ' . $request->star . ' star review for ' . $tourPackage->name;
            $adminNotification->click_url = urlPath('admin.report.review'); // Assuming there is a review report page
            $adminNotification->save();
        }

        $notify[] = ['success', 'Review submitted successfully'];
        return back()->withNotify($notify);
    }

    // wishlist
    public function toggleWishlist(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Please log in to your account']);
        }

        $tourPackageId = $request->tourPackageId;


        if ($tourPackageId) {
            $wishlist = Wishlist::where('user_id', $userId)->where('tour_package_id', $tourPackageId)->first();

            if ($wishlist) {
                $wishlist->delete();
                return response()->json([
                    'message' => 'removed from wishlist'
                ], 200);
            } else {
                $wishlist = new Wishlist();
                $wishlist->user_id = $userId;
                $wishlist->tour_package_id = $tourPackageId;
                $wishlist->save();
                
                $tourPackage = TourPackage::findOrFail($tourPackageId);
                $tourPackage->favorite += 1;
                $tourPackage->save();

                return response()->json([
                    'message' => 'added to wishlist'
                ], 200);
            }
        }

        return response()->json(['error' => 'No valid item to add or remove from wishlist']);
    }
    public function getWishlist(Request $request)
    {
        $pageTitle = 'Wishlists';
        $wishlists = Wishlist::with(['tour_package'])->where('user_id', auth()->id())->getSearch(['tour_package:title'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.wishlists', compact('pageTitle',    'wishlists'));
    }
    public function removeWishlist(Request $request)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->where('id', $request->id)->first();
        $wishlist->delete();

        $notify[] = ['success', 'Wishlist has been removed'];
        return back()->withNotify($notify);
    }

    /*
    // apply coupon
    public function applyCoupon(Request $request)
    {
        $couponCode = Str::lower($request->input('coupon_code'));

        if (Session::has('coupon') && Str::lower(Session::get('coupon.name')) == $couponCode) {
            $notify[] = ['error', 'This coupon code has already been applied.'];
            return back()->withNotify($notify);
        }

        $coupon = Coupon::whereRaw('LOWER(coupon) = ?', [$couponCode])->first();

        if (!$coupon || $coupon->status != 1) {
            $notify[] = ['error', 'This coupon code is invalid or cannot be used.'];
            return back()->withNotify($notify);
        }


        if ($coupon->expire_date < now()) {
            $notify[] = ['error', 'Your coupon code has expired.'];
            return back()->withNotify($notify);
        }

        Session::put('coupon', [
            'name' => $coupon->coupon,
            'discount' => $coupon->discount,
        ]);


        $notify[] = ['success', 'Coupon applied successfully!'];
        return back()->withNotify($notify);
    }
    */
}
