<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\NotificationLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManageEmployeesController extends Controller
{
    public function allUsers()
    {
        $pageTitle = __('All Employees');
        $users = $this->userData();
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function create()
    {
        $pageTitle = __('Add New Employee');
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('admin.employees.create', compact('pageTitle', 'countries'));
    }

    public function store(Request $request)
    {
        $countryData = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countries = implode(',', array_keys((array)$countryData));

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:agents',
            'mobile' => 'required|string|max:40|unique:agents',
            'country' => 'required|in:'.$countries,
            'username' => 'required|string|max:40|unique:agents|min:6',
            'password' => 'required|string|min:6',
        ]);

        $countryCode = $request->country;
        $country = $countryData->$countryCode->country;
        $dialCode = $countryData->$countryCode->dial_code;

        $user = new Employee();
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->mobile = $dialCode.$request->mobile;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->country_code = $countryCode;
        $user->address = (object) [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $country,
        ];
        $user->status = 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->kv = 1;
        $user->save();

        $notify[] = ['success', __('Employee created successfully')];
        return to_route('admin.employees.detail', $user->id)->withNotify($notify);
    }

    public function activeUsers()
    {
        $pageTitle = __('Active Employees');
        $users = $this->userData('active');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = __('Banned Employees');
        $users = $this->userData('banned');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = __('Email Unverified Employees');
        $users = $this->userData('emailUnverified');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        $pageTitle = __('KYC Unverified Employees');
        $users = $this->userData('kycUnverified');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function kycPendingUsers()
    {
        $pageTitle = __('KYC Unverified Employees');
        $users = $this->userData('kycPending');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = __('Email Verified Employees');
        $users = $this->userData('emailVerified');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }


    public function mobileUnverifiedUsers()
    {
        $pageTitle = __('Mobile Unverified Employees');
        $users = $this->userData('mobileUnverified');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }


    public function mobileVerifiedUsers()
    {
        $pageTitle = __('Mobile Verified Employees');
        $users = $this->userData('mobileVerified');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = __('Employees with Balance');
        $users = $this->userData('withBalance');
        return view('admin.employees.list', compact('pageTitle', 'users'));
    }


    protected function userData($scope = null){
        if ($scope) {
            $users = Employee::$scope();
        }else{
            $users = Employee::query();
        }

        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
            $users  = $users->where(function ($user) use ($search) {
                            $user->where('username', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                      });
        }
        return $users->orderBy('id','desc')->paginate(getPaginate());
    }


    public function detail($id)
    {
        $user = Employee::findOrFail($id);
        $pageTitle = __('Employee Details') . ' / @' . $user->username;
        $totalWithdrawals = Withdrawal::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('agent_id',$user->id)->count();
        $assignedUsersCount = User::where('agent_id', $user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('admin.employees.detail', compact('pageTitle', 'user','totalWithdrawals','totalTransaction','assignedUsersCount','countries'));
    }


    public function kycDetails($id)
    {
        $pageTitle = __('KYC Details');
        $user = Employee::findOrFail($id);
        return view('admin.employees.kyc_detail', compact('pageTitle','user'));
    }

    public function kycApprove($id)
    {
        $user = Employee::findOrFail($id);
        $user->kv = 1;
        $user->save();

        notify($user,'KYC_APPROVE',[]);
        $notify[] = ['success', __('KYC approved successfully')];
        return to_route('admin.employees.kyc.pending')->withNotify($notify);
    }

    public function kycReject($id)
    {
        $user = Employee::findOrFail($id);
        foreach ($user->kyc_data as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
            }
        }
        $user->kv = 0;
        $user->kyc_data = null;
        $user->save();

        notify($user,'KYC_REJECT',[]);
        $notify[] = ['success', __('KYC rejected successfully')];
        return to_route('admin.employees.kyc.pending')->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $user = Employee::findOrFail($id);
        $countryData = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countryArray   = (array)$countryData;
        $countries      = implode(',', array_keys($countryArray));

        $countryCode    = $request->country;
        $country        = $countryData->$countryCode->country;
        $dialCode       = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:agents,email,' . $user->id,
            'mobile' => 'required|string|max:40|unique:agents,mobile,' . $user->id,
            'country' => 'required|in:'.$countries,
        ]);
        $user->mobile = $dialCode.$request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
                            'address' => $request->address,
                            'city' => $request->city,
                            'state' => $request->state,
                            'zip' => $request->zip,
                            'country' => $country,
                        ];
        $user->ev = $request->ev ? 1 : 0;
        $user->sv = $request->sv ? 1 : 0;
        $user->ts = $request->ts ? 1 : 0;
        $user->dashboard_permissions = $request->dashboard_permissions ? array_values($request->dashboard_permissions) : null;
        $user->menu_permissions = $request->menu_permissions ? array_values($request->menu_permissions) : null;
        $user->page_permissions = $request->page_permissions ? array_values($request->page_permissions) : null;
        $user->user_permissions = $request->user_permissions ? array_values($request->user_permissions) : null;
        if (!$request->kv) {
            $user->kv = 0;
            if ($user->kyc_data) {
                foreach ($user->kyc_data as $kycData) {
                    if ($kycData->type == 'file') {
                        fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
                    }
                }
            }
            $user->kyc_data = null;
        }else{
            $user->kv = 1;
        }
        $user->save();

        $notify[] = ['success', __('Employee details has been updated successfully')];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = Employee::findOrFail($id);
        $amount = $request->amount;
        $general = gs();
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $user->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', __(':amount has been added successfully', ['amount' => $general->cur_sym . $amount])];

        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', __(':username doesn\'t have sufficient balance.', ['username' => $user->username])];
                return back()->withNotify($notify);
            }

            $user->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', __(':amount subtracted successfully', ['amount' => $general->cur_sym . $amount])];
        }

        $user->save();

        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx =  $trx;
        $transaction->details = $request->remark;
        $transaction->save();

        notify($user, $notifyTemplate, [
            'trx' => $trx,
            'amount' => showAmount($amount),
            'remark' => $request->remark,
            'post_balance' => showAmount($user->balance)
        ]);

        return back()->withNotify($notify);
    }

    public function login($id)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        Auth::guard('employee')->loginUsingId($id);
        return to_route('employee.home');
    }

    public function status(Request $request,$id)
    {
        $user = Employee::findOrFail($id);
        if ($user->status == 1) {
            $request->validate([
                'reason'=>'required|string|max:255'
            ]);
            $user->status = 0;
            $user->ban_reason = $request->reason;
            $notify[] = ['success', __('Employees banned successfully')];
        }else{
            $user->status = 1;
            $user->ban_reason = null;
            $notify[] = ['success', __('Employees unbanned successfully')];
        }
        $user->save();
        return back()->withNotify($notify);

    }


    public function showNotificationSingleForm($id)
    {
        $user = Employee::findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', __('Notification options are disabled currently')];
            return to_route('admin.employees.detail',$user->id)->withNotify($notify);
        }
        $pageTitle = __('Send Notification to :username', ['username' => $user->username]);
        return view('admin.employees.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $user = Employee::findOrFail($id);
        notify($user,'DEFAULT',[
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);
        $notify[] = ['success', __('Notification sent successfully')];
        return back()->withNotify($notify);
    }

    public function showNotificationAllForm()
    {
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', __('Notification options are disabled currently')];
            return to_route('admin.dashboard')->withNotify($notify);
        }
        $users = Employee::where('ev',1)->where('sv',1)->where('status',1)->count();
        $pageTitle = __('Notification to Verified Employees');
        return view('admin.employees.notification_all', compact('pageTitle','users'));
    }

    public function sendNotificationAll(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'message' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $user = Employee::where('status', 1)->where('ev',1)->where('sv',1)->skip($request->skip)->first();

        notify($user,'DEFAULT',[
            'subject'=>$request->subject,
            'message'=>$request->message,
        ]);

        return response()->json([
            'success'=>'message sent',
            'total_sent'=>$request->skip + 1,
        ]);
    }

    public function notificationLog($id){
        $user = Employee::findOrFail($id);
        $pageTitle = __('Notifications Sent to :username', ['username' => $user->username]);
        $logs = NotificationLog::where('agent_id',$id)->with('Employee')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.employees.reports.notification_history', compact('pageTitle','logs','user'));
    }
}
