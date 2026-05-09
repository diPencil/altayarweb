<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\Deposit;
use App\Models\Invoice;
use App\Lib\CurlRequest;
use App\Models\Listing;
use App\Models\PopupAd;
use App\Models\ReelComment;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use App\Models\ServiceBooking;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\ChatConversation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\MembershipPlan;
use App\Models\UserMembership;
use App\Rules\FileTypeValidate;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {

        $pageTitle = __('Dashboard');

        // User Info
        $widget['total_users']             = User::count();
        $widget['verified_users']          = User::where('status', 1)->where('ev',1)->where('sv',1)->count();
        $widget['email_unverified_users']  = User::emailUnverified()->count();
        $widget['mobile_unverified_users'] = User::mobileUnverified()->count();
        $widget['tickets_users'] = SupportTicket::where('user_id','!=',0)->count();
        $widget['total_tickets'] = SupportTicket::count();
        $widget['total_invoices'] = Invoice::count();
        $widget['live_chats'] = ChatConversation::open()->count();
        $widget['total_bookings'] = ServiceBooking::count() + TourBooking::count();
        

        // Employee Info
        $widget['total_Employees']             = Employee::count();
        $widget['verified_Employees']          = Employee::where('status', 1)->where('ev',1)->where('sv',1)->count();
        $widget['email_unverified_Employees']  = Employee::emailUnverified()->count();
        $widget['mobile_unverified_Employees'] = Employee::mobileUnverified()->count();
        $widget['tickets_Employees'] = SupportTicket::where('agent_id','!=',0)->count();
        $widget['total_tour_package'] = TourPackage::count();
        $widget['listing_offers'] = Listing::active()->whereNotNull('offer_type')->count();
        $widget['popup_ads'] = PopupAd::count();
        $widget['reel_comments'] = ReelComment::count();


        $deposit['total_deposit_amount']        = Deposit::successful()->sum('amount');
        $deposit['total_deposit_pending']       = Deposit::pending()->count();
        $deposit['total_deposit_rejected']      = Deposit::rejected()->count();
        $deposit['total_deposit_charge']        = Deposit::successful()->sum('charge');

        $withdrawals['total_withdraw_amount']   = Withdrawal::approved()->sum('amount');
        $withdrawals['total_withdraw_pending']  = Withdrawal::pending()->count();
        $withdrawals['total_withdraw_rejected'] = Withdrawal::rejected()->count();
        $withdrawals['total_withdraw_charge']   = Withdrawal::approved()->sum('charge');

        $membership['total_subscriptions'] = UserMembership::count();
        $membership['active_subscriptions'] = UserMembership::active()->count();
        $membership['expiring_soon'] = UserMembership::active()
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->count();
        $membership['active_plans'] = MembershipPlan::where('status', 1)->count();

        // Monthly Deposit & Withdraw Report Graph
        $deposits = Deposit::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
            ->whereYear('created_at', date('Y'))
            ->whereStatus(1)
            ->groupBy('month_name', 'month_num')
            ->orderBy('month_num')
            ->get();
        $depositsChart['labels'] = $deposits->pluck('month_name');
        $depositsChart['values'] = $deposits->pluck('amount');

        $withdrawalsReport = Withdrawal::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
            ->whereYear('created_at', date('Y'))
            ->whereStatus(1)
            ->groupBy('month_name', 'month_num')
            ->orderBy('month_num')
            ->get();
        $withdrawalsChart['labels'] = $withdrawalsReport->pluck('month_name');
        $withdrawalsChart['values'] = $withdrawalsReport->pluck('amount');
        // Monthly Deposit & Withdraw Report Graph

        // UserLogin Report Graph
        $userLoginsReport = UserLogin::selectRaw("COUNT(*) as login_count, DATE_FORMAT(created_at, '%Y-%m-%d') as login_date")
            ->orderBy('login_date', 'desc')
            ->where('user_id','!=', 0)
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
            ->limit(10)
            ->pluck('login_count', 'login_date');
        $userLogins['labels'] = $userLoginsReport->keys();
        $userLogins['values'] = $userLoginsReport->values();
            // UserLogin Report Graph

        // EmployeeLogin Report Graph
        $EmployeeLoginsReport = UserLogin::selectRaw("COUNT(*) as login_count, DATE_FORMAT(created_at, '%Y-%m-%d') as login_date")
            ->orderBy('login_date', 'desc')
            ->where('agent_id','!=', 0)
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m-%d')")
            ->limit(10)
            ->pluck('login_count', 'login_date');
        $EmployeeLogins['labels'] = $EmployeeLoginsReport->keys();
        $EmployeeLogins['values'] = $EmployeeLoginsReport->values();
            // EmployeeLogin Report Graph

        $newTickets = SupportTicket::with('user')->orderBy('created_at', 'desc')->whereStatus(0)->limit(8)->get();

        $tourBookings = TourBooking::with(['user', 'tour_package'])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'user' => $booking->user,
                    'booking_type' => 'tour',
                    'title' => $booking->tour_package?->title ?? __('Tour Booking'),
                    'booking_date' => $booking->created_at,
                    'service_date' => $booking->tour_package?->tour_start,
                    'amount' => $booking->price,
                    'sort_date' => $booking->created_at,
                    'status_html' => $booking->statusBadge($booking->status),
                ];
            });

        $serviceBookings = ServiceBooking::with('user')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'user' => $booking->user,
                    'booking_type' => $booking->booking_type,
                    'title' => $booking->title,
                    'booking_date' => $booking->booking_date ?? $booking->created_at,
                    'service_date' => $booking->service_date,
                    'amount' => $booking->amount,
                    'sort_date' => $booking->booking_date ?? $booking->created_at,
                    'status_html' => $booking->statusBadge(),
                ];
            });

        $recentBookings = $tourBookings
            ->merge($serviceBookings)
            ->sortByDesc(fn ($booking) => $booking->sort_date ? strtotime($booking->sort_date) : 0)
            ->take(8)
            ->values();

        $recentPayments = Deposit::with(['user', 'gateway'])
            ->latest('id')
            ->limit(8)
            ->get();
        return view('admin.dashboard', compact('pageTitle', 'widget', 'withdrawalsChart', 'depositsChart', 'deposit', 'withdrawals', 'membership', 'userLogins','EmployeeLogins', 'newTickets', 'recentBookings', 'recentPayments'));
    }


    public function profile()
    {
        $pageTitle = __('Profile');
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload your image')];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', __('Profile has been updated successfully')];
        return to_route('admin.profile')->withNotify($notify);
    }


    public function password()
    {
        $pageTitle = __('Password Setting');
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', __('Password doesn\'t match!!')];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', __('Password changed successfully.')];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->with('user')->paginate(getPaginate());
        $pageTitle = __('Notifications');
        return view('admin.notifications',compact('pageTitle','notifications'));
    }

    public function notificationLive()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')
            ->with('user')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'count' => AdminNotification::where('read_status', 0)->count(),
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => __($notification->title),
                    'created_at' => $notification->created_at?->diffForHumans(),
                    'read_status' => (int) $notification->read_status,
                    'read_url' => route('admin.notification.read', $notification->id),
                    'image_url' => getImage(getFilePath('userProfile') . '/' . @$notification->user->image, getFileSize('userProfile')),
                ];
            })->values(),
            'empty_message' => __('No unread notification found'),
            'view_all_url' => route('admin.notifications'),
        ]);
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
       
        $notification->read_status = 1;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAll(){
        AdminNotification::where('read_status',0)->update([
            'read_status'=>1
        ]);
        $notify[] = ['success', __('Notifications read successfully')];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name).'- attachments.'.$extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }


}
