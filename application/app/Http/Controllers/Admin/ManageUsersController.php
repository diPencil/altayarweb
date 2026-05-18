<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Deposit;
use App\Models\MembershipCashbackTransaction;
use App\Models\MembershipPlan;
use App\Models\MembershipPlanHistory;
use App\Models\MembershipPointTransaction;
use App\Models\NotificationLog;
use App\Models\UserMembership;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManageUsersController extends Controller
{

    public function allUsers()
    {
        $pageTitle = __('All Users');
        $users = $this->userData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function create()
    {
        $pageTitle = __('Add New User');
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('admin.users.create', compact('pageTitle', 'countries'));
    }

    public function store(Request $request)
    {
        $countryData = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countries = implode(',', array_keys((array)$countryData));

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:users',
            'mobile' => 'required|string|max:40|unique:users',
            'country' => 'required|in:'.$countries,
            'username' => 'required|string|max:40|unique:users|min:6',
            'password' => 'required|string|min:6',
        ]);

        $countryCode = $request->country;
        $country = $countryData->$countryCode->country;
        $dialCode = $countryData->$countryCode->dial_code;

        $user = new User();
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

        $notify[] = ['success', __('User created successfully')];
        return to_route('admin.users.detail', $user->id)->withNotify($notify);
    }

    public function activeUsers()
    {
        $pageTitle = __('Active Users');
        $users = $this->userData('active');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = __('Banned Users');
        $users = $this->userData('banned');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = __('Email Unverified Users');
        $users = $this->userData('emailUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycUnverifiedUsers()
    {
        $pageTitle = __('KYC Unverified Users');
        $users = $this->userData('kycUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function kycPendingUsers()
    {
        $pageTitle = __('KYC Unverified Users');
        $users = $this->userData('kycPending');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = __('Email Verified Users');
        $users = $this->userData('emailVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileUnverifiedUsers()
    {
        $pageTitle = __('Mobile Unverified Users');
        $users = $this->userData('mobileUnverified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function mobileVerifiedUsers()
    {
        $pageTitle = __('Mobile Verified Users');
        $users = $this->userData('mobileVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = __('Users with Balance');
        $users = $this->userData('withBalance');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }


    protected function userData($scope = null){
        if ($scope) {
            $users = User::$scope();
        }else{
            $users = User::query();
        }

        if (request()->filled('agent_id')) {
            $users->where('agent_id', request()->agent_id);
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
        return $users->with('currentMembership.plan')
            ->withCount([
                'memberships',
                'membershipPlanHistories',
                'userMembershipBenefits',
                'membershipPointTransactions',
                'membershipCashbackTransactions',
                'tourBookings',
                'serviceBookings',
                'supportTickets',
                'invoices',
                'transactions',
                'depositRecords',
                'withdrawalRecords',
                'loginLogs',
            ])
            ->orderBy('id','desc')->paginate(getPaginate());
    }


    public function detail($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = __('User Details') . ' / @' . $user->username;
        $employees = Employee::where('status', 1)->orderBy('firstname')->get();

        $totalDeposit = Deposit::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalWithdrawals = Withdrawal::where('user_id',$user->id)->where('status',1)->sum('amount');
        $totalTransaction = Transaction::where('user_id',$user->id)->count();
        $membershipPlans = MembershipPlan::where('status', 1)->orderBy('bonus_points', 'desc')->get();
        $currentMembership = $user->currentMembership()->with('plan')->first();
        $countries = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        return view('admin.users.detail', compact('pageTitle', 'user','totalDeposit','totalWithdrawals','totalTransaction','countries','membershipPlans','currentMembership','employees'));
    }

    public function assignEmployee(Request $request, $id)
    {
        $request->validate([
            'agent_id' => 'nullable|integer|exists:agents,id',
        ]);

        $user = User::findOrFail($id);
        $user->agent_id = $request->agent_id ?: 0;
        $user->save();

        $notify[] = ['success', __('Employee assignment updated successfully')];
        return back()->withNotify($notify);
    }

    public function assignMembership(Request $request, $id)
    {
        $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
        ]);

        $user = User::findOrFail($id);
        $plan = MembershipPlan::where('status', 1)->findOrFail($request->membership_plan_id);
        $previousMembership = $user->currentMembership()->with('plan')->first();

        UserMembership::where('user_id', $user->id)
            ->whereIn('status', [0, 1])
            ->update(['status' => 2]);

        $membership = new UserMembership();
        $membership->user_id = $user->id;
        $membership->membership_plan_id = $plan->id;
        $membership->start_date = now()->startOfDay()->toDateTimeString();
        $membership->end_date = $plan->duration_days > 0 ? now()->addDays($plan->duration_days)->endOfDay()->toDateTimeString() : null;
        $membership->status = 1;
        $membership->save();

        MembershipPlanHistory::recordChange($user, $previousMembership, $membership, $plan, [
            'created_by_admin_id' => auth('admin')->id(),
            'note' => 'admin_assignment',
        ]);

        notify($user, 'MEMBERSHIP_SUBSCRIBE', [
            'plan_name' => $plan->name,
            'amount' => 'System Assigned',
            'trx' => 'ADMIN_ASSIGNED',
            'end_date' => $membership->end_date ? showDateTime($membership->end_date) : 'Never',
        ]);

        if ($plan->bonus_points > 0) {
            $trx = getTrx();
            MembershipPointTransaction::create([
                'user_id' => $user->id,
                'membership_plan_id' => $plan->id,
                'trx' => $trx,
                'type' => 'earned',
                'points' => (int) $plan->bonus_points,
                'balance_after' => (int) $user->membership_points_balance + (int) $plan->bonus_points,
                'remark' => 'admin_membership_bonus',
                'meta' => [
                    'assigned_by' => auth('admin')->id(),
                    'membership_id' => $membership->id,
                ],
            ]);

            notify($user, 'POINTS_ADD', [
                'amount' => (int) $plan->bonus_points,
                'post_balance' => (int) $user->membership_points_balance + (int) $plan->bonus_points,
                'trx' => $trx,
                'remark' => 'Membership Subscription Bonus',
            ]);
        }

        $notify[] = ['success', __('Membership assigned successfully')];
        return back()->withNotify($notify);
    }

    public function updateMemberCode(Request $request, $id)
    {
        $request->validate([
            'member_code' => 'required|string|max:30|unique:user_memberships,member_code,' . $id,
        ]);

        $membership = UserMembership::findOrFail($id);
        $membership->member_code = strtoupper(trim($request->member_code));
        $membership->save();

        $notify[] = ['success', __('Member ID updated successfully')];
        return back()->withNotify($notify);
    }

    public function removeMembership($id)
    {
        $user = User::findOrFail($id);
        $currentMembership = $user->currentMembership()->first();

        if (!$currentMembership) {
            $notify[] = ['error', __('User has no active membership.')];
            return back()->withNotify($notify);
        }

        // Deactivate membership
        $currentMembership->status = 2; // Expired/Inactive
        $currentMembership->save();

        // Record history
        MembershipPlanHistory::recordChange($user, $currentMembership, null, null, [
            'created_by_admin_id' => auth('admin')->id(),
            'note' => 'Removed by admin',
        ]);

        $notify[] = ['success', __('Membership removed successfully')];
        return back()->withNotify($notify);
    }


    public function kycDetails($id)
    {
        $pageTitle = __('KYC Details');
        $user = User::findOrFail($id);
        return view('admin.users.kyc_detail', compact('pageTitle','user'));
    }

    public function kycApprove($id)
    {
        $user = User::findOrFail($id);
        $user->kv = 1;
        $user->save();

        notify($user,'KYC_APPROVE',[]);

        $notify[] = ['success', __('KYC approved successfully')];
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }

    public function kycReject($id)
    {
        $user = User::findOrFail($id);
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
        return to_route('admin.users.kyc.pending')->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $countryData = json_decode(file_get_contents(resource_path('views/includes/country.json')));
        $countryArray   = (array)$countryData;
        $countries      = implode(',', array_keys($countryArray));

        $countryCode    = $request->country ?: $user->country_code;
        if (!$countryCode || !isset($countryData->$countryCode)) {
            $countryCode = array_key_first($countryArray);
        }
        $country        = $countryData->$countryCode->country;
        $dialCode       = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:40|unique:users,mobile,' . $user->id,
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

        $user->dashboard_permissions = $request->dashboard_permissions ? array_values($request->dashboard_permissions) : null;
        $user->menu_permissions = $request->menu_permissions ? array_values($request->menu_permissions) : null;

        $user->save();

        $notify[] = ['success', __('User details has been updated successfully')];
        return back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
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

    public function addSubPoints(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = (int) $request->amount;
        $currentBalance = (int) $user->membership_points_balance;
        $trx = getTrx();

        if ($request->act === 'sub' && $amount > $currentBalance) {
            $notify[] = ['error', __(':username doesn\'t have sufficient points.', ['username' => $user->username])];
            return back()->withNotify($notify);
        }

        MembershipPointTransaction::create([
            'user_id' => $user->id,
            'trx' => $trx,
            'type' => $request->act === 'add' ? 'earned' : 'used',
            'points' => $amount,
            'balance_after' => $request->act === 'add' ? $currentBalance + $amount : $currentBalance - $amount,
            'remark' => $request->remark,
            'meta' => [
                'action_by' => auth('admin')->id(),
                'source' => 'admin_panel',
            ],
        ]);

        $notify[] = ['success', $request->act === 'add' ? __('Points added successfully') : __('Points deducted successfully')];
        return back()->withNotify($notify);
    }

    public function addSubCashback(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $amount = (float) $request->amount;
        $currentBalance = (float) $user->cashback_balance;
        $trx = getTrx();

        if ($request->act === 'sub' && $amount > $currentBalance) {
            $notify[] = ['error', __(':username doesn\'t have sufficient cashback balance.', ['username' => $user->username])];
            return back()->withNotify($notify);
        }

        MembershipCashbackTransaction::create([
            'user_id' => $user->id,
            'trx' => $trx,
            'type' => $request->act === 'add' ? 'earned' : 'used',
            'amount' => $amount,
            'balance_after' => $request->act === 'add' ? $currentBalance + $amount : $currentBalance - $amount,
            'remark' => $request->remark,
            'meta' => [
                'action_by' => auth('admin')->id(),
                'source' => 'admin_panel',
            ],
        ]);

        $notify[] = ['success', $request->act === 'add' ? __('Cashback added successfully') : __('Cashback deducted successfully')];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        if (Auth::guard('agent')->check()) {
            Auth::guard('agent')->logout();
        }
        Auth::guard('web')->loginUsingId($id);
        session()->put('admin_impersonating', true);
        return to_route('user.home');
    }

    public function stopImpersonate()
    {
        Auth::guard('web')->logout();
        session()->forget('admin_impersonating');
        return to_route('admin.dashboard');
    }

    public function status(Request $request,$id)
    {
        $user = User::findOrFail($id);
        if ($user->status == 1) {
            $request->validate([
                'reason'=>'required|string|max:255'
            ]);
            $user->status = 0;
            $user->ban_reason = $request->reason;
            $notify[] = ['success', __('User banned successfully')];
        }else{
            $user->status = 1;
            $user->ban_reason = null;
            $notify[] = ['success', __('User unbanned successfully')];
        }
        $user->save();
        return back()->withNotify($notify);

    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->loadMissing('currentMembership');
        $user->loadCount([
            'memberships',
            'membershipPlanHistories',
            'userMembershipBenefits',
            'membershipPointTransactions',
            'membershipCashbackTransactions',
            'tourBookings',
            'serviceBookings',
            'supportTickets',
            'invoices',
            'transactions',
            'depositRecords',
            'withdrawalRecords',
            'loginLogs',
        ]);

        $blockReason = $user->deleteBlockReason();

        if ($blockReason) {
            $notify[] = ['error', $blockReason];
            return back()->withNotify($notify);
        }

        try {
            $user->delete();
        } catch (\Throwable $throwable) {
            $notify[] = ['error', __('This user has related records and cannot be deleted. Please ban/deactivate instead.')];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', __('User deleted successfully')];
        return back()->withNotify($notify);
    }


    public function showNotificationSingleForm($id)
    {
        $user = User::findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', __('Notification options are disabled currently')];
            return to_route('admin.users.detail',$user->id)->withNotify($notify);
        }
        $pageTitle = __('Send Notification to :username', ['username' => $user->username]);
        return view('admin.users.notification_single', compact('pageTitle', 'user'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $user = User::findOrFail($id);
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
        $users = User::where('ev',1)->where('sv',1)->where('status',1)->count();
        $pageTitle = __('Notification to Verified Users');
        return view('admin.users.notification_all', compact('pageTitle','users'));
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

        $user = User::where('status', 1)->where('ev',1)->where('sv',1)->skip($request->skip)->first();

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
        $user = User::findOrFail($id);
        $pageTitle = __('Notifications Sent to :username', ['username' => $user->username]);
        $logs = NotificationLog::where('user_id',$id)->with('user')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs','user'));
    }

}
