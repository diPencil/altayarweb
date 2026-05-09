<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Form;
// use App\Models\Artwork;
use App\Models\Deposit;
use App\Models\Listing;
use App\Models\OrderItem;
use App\Lib\FormProcessor;
// use App\Models\Collection;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\PopupAd;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Lib\GoogleAuthenticator;
// use App\Models\ArtworkCommission;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $employee =  employee();
        $myBooked =  TourPackage::with('tour_bookings.deposit', 'tour_bookings.user', 'tour_bookings.owner', 'TourPackagePrimaryImage')->where('user_id',auth('employee')->id())->where('user_type','employee')->paginate(getPaginate());
     
        $widget['total_assigned_users'] = User::where('agent_id', $employee->id)->count();
        $widget['total_tour_package'] =  TourPackage::where('user_type','employee')->where('user_id', auth('employee')->id())->count();
        $widget['total_listing_offers'] = Listing::where('user_type', 'employee')
            ->where('user_id', $employee->id)
            ->whereNotNull('offer_type')
            ->count();
        $widget['total_support_ticker'] =  SupportTicket::where('agent_id',auth('employee')->id())
        ->count();
        $widget['total_open_support_ticker'] =  SupportTicket::where('agent_id',auth('employee')->id())
        ->where('status',0)->count();

        $widget['total_popup_ads'] = PopupAd::where('created_by_type', 'employee')
            ->where('created_by_id', $employee->id)
            ->count();
        $widget['total_transaction'] =  Transaction::where('agent_id', $employee->id)->count();
        $widget['total_payment_logs'] = Deposit::where('agent_id', $employee->id)->count();
        $widget['balance'] =   $employee->balance;

        $withdrawalsReport = Withdrawal::selectRaw("SUM(amount) as amount, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
        ->whereYear('created_at', date('Y'))
        ->whereStatus(1)
        ->where('user_id', $employee->id)
        ->groupBy('month_name', 'month_num')
        ->orderBy('month_num')
        ->get();
        $withdrawalsChart['labels'] = $withdrawalsReport->pluck('month_name');
        $withdrawalsChart['values'] = $withdrawalsReport->pluck('amount');

        $tour_packageReport = TourBooking::with('tour_package')
        ->selectRaw("SUM(price) as price, MONTHNAME(created_at) as month_name, MONTH(created_at) as month_num")
        ->whereYear('created_at', date('Y'))
        ->where('owner_id', $employee->id)
        ->where('owner_type', 'employee')
        ->groupBy('month_name', 'month_num')
        ->orderBy('month_num')
        ->get();
        
        $tourPackageChart['labels'] = $tour_packageReport->pluck('month_name');
        $tourPackageChart['values'] = $tour_packageReport->pluck('price');
      
        return view($this->activeTemplate . 'employee.dashboard', compact('pageTitle','widget','withdrawalsChart','myBooked','tourPackageChart'));
    }
    public function show2faForm()
    {
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = employee();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate.'employee.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }
    public function create2fa(Request $request)
    {
        $user = employee();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
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

        $user = employee();
        $response = verifyG2fa($user,$request->code);
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
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('agent_id',employeeId());

        if ($request->search) {
            $transactions = $transactions->where('trx',$request->search);
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type',$request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        $transactions = $transactions->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'employee.transactions', compact('pageTitle','transactions','remarks'));
    }

    public function users(Request $request)
    {
        $pageTitle = 'Users';
        $employeeId = auth('employee')->id();

        $usersQuery = User::query()
            ->select([
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.username',
                'users.email',
                'users.mobile',
                'users.created_at',
            ])
            ->where('users.agent_id', $employeeId)
            ->orderByDesc('users.id');

        if ($request->search) {
            $search = $request->search;
            $usersQuery->where(function ($query) use ($search) {
                $query->where('users.firstname', 'like', "%{$search}%")
                    ->orWhere('users.lastname', 'like', "%{$search}%")
                    ->orWhere('users.username', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.mobile', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->paginate(getPaginate());

        $usersCount = User::where('agent_id', $employeeId)->count();

        return view($this->activeTemplate . 'employee.users.index', compact('pageTitle', 'users', 'usersCount'));
    }

    public function userDetail($id)
    {
        $employeeId = auth('employee')->id();
        $user = User::findOrFail($id);

        abort_unless((int) $user->agent_id === (int) $employeeId, 403);

        $pageTitle = __('User Details') . ' / @' . $user->username;

        $totalDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $totalTransaction = Transaction::where('user_id', $user->id)->count();
        $currentMembership = $user->currentMembership()->with('plan')->first();

        return view($this->activeTemplate . 'employee.users.detail', compact(
            'pageTitle',
            'user',
            'totalDeposit',
            'totalWithdrawals',
            'totalTransaction',
            'currentMembership'
        ));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = __('Payment History');
        $deposits = Deposit::where('agent_id', employeeId());
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'employee.deposit_history', compact('pageTitle', 'deposits'));
    }
    public function kycForm()
    {
        if (employee()->kv == 2) {
            $notify[] = ['error','Your KYC is under review'];
            return to_route('employee.home')->withNotify($notify);
        }
        if (employee()->kv == 1) {
            $notify[] = ['error','You are already KYC verified'];
            return to_route('employee.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act','agent_kyc')->first();
        return view($this->activeTemplate.'employee.kyc.form', compact('pageTitle','form'));
    }

    public function kycData()
    {
        $employee = employee();
        $pageTitle = 'KYC Data';
        $form = Form::where('act', 'agent_kyc')->first();
        return view($this->activeTemplate.'employee.kyc.info', compact('pageTitle', 'employee', 'form'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act','agent_kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = employee();
        $user->kyc_data = (object) $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success','KYC data submitted successfully'];
        return to_route('employee.home')->withNotify($notify);

    }
    public function attachmentDownload($fileHash)
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
    public function userData()
    {
        $user = employee();
        if (! $user) {
            return to_route('employee.login');
        }
        $user->address = $user->address ?? (object) [
            'country' => '',
            'address' => '',
            'state' => '',
            'zip' => '',
            'city' => '',
        ];
        if ($user->reg_step == 1) {
            return to_route('employee.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate.'employee.user_data', compact('pageTitle','user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = employee();
        if (! $user) {
            return to_route('employee.login');
        }
        if ($user->reg_step == 1) {
            return to_route('employee.home');
        }
        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $country = $user->address->country ?? '';
        $user->address = (object)[
            'country'=>$country,
            'address'=>$request->address,
            'state'=>$request->state,
            'zip'=>$request->zip,
            'city'=>$request->city,
        ];
        $user->reg_step = 1;
        $user->save();

        $notify[] = ['success','Registration process completed successfully'];
        return to_route('employee.home')->withNotify($notify);

    }
    /*
    public function artworkCommission(){
        $pageTitle  = 'Artwork Commissions';
        $artworkCommissions = ArtworkCommission::with( 'artwork')->where('agent_id',employeeId())->getSearch(['artwork:title'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'employee.commissions', compact('pageTitle','artworkCommissions'));
    }
    public function artworkOrders(){
        $pageTitle  = 'Artwork Orders';
        $orders = OrderItem::with( ['order','artwork'])->where('agent_id',employeeId())->getSearch(['artwork:title'])->latest()->paginate(getPaginate());
        return view($this->activeTemplate.'employee.orders', compact('pageTitle','orders'));
    }
    */

}
