<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
    
        $pageTitle = __('Transaction Logs');

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::with('user')->orderBy('id','desc')->where('user_id', '!=', 0);
        if ($request->search) {
            $search = request()->search;
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
        }

        if ($request->type) {
            $transactions = $transactions->where('trx_type',$request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark',$request->remark);
        }

        //date search
        if($request->date) {
            $date = explode('-',$request->date);
            $request->merge([
                'start_date'=> trim($date[0]),
                'end_date'  => trim($date[1])
            ]);
            $request->validate([
                'start_date'    => 'required|date_format:m/d/Y',
                'end_date'      => 'nullable|date_format:m/d/Y'
            ]);
            if($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSeconds(59);
                $transactions   = $transactions->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            }else{
                $transactions   = $transactions->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        $transactions = $transactions->paginate(getPaginate());
        return view('admin.reports.transactions', compact('pageTitle', 'transactions','remarks'));
    }

    public function loginHistory(Request $request)
    {
       
        $loginLogs = UserLogin::orderBy('id','desc')->with('user')->where('user_id', '!=', 0);
        $pageTitle = __('User Login History');
        if ($request->search) {
            $search = $request->search;
            $pageTitle = __('User Login History - :search', ['search' => $search]);
            $loginLogs = $loginLogs->whereHas('user', function ($query) use ($search) {
                $query->where('username', $search);
            });
        }
        $loginLogs = $loginLogs->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
     
        $pageTitle = __('Login by - :ip', ['ip' => $ip]);
        $loginLogs = UserLogin::where('user_ip',$ip)->orderBy('id','desc')->where('user_id', '!=', 0)->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs','ip'));

    }

    public function notificationHistory(Request $request){
        $pageTitle = __('Notification History');
        $logs = NotificationLog::orderBy('id','desc');
        $search = $request->search;
        if ($search) {
            $logs = $logs->whereHas('user', function ($user) use ($search) {
                $user->where('username', 'like',"%$search%");
            });
        }
        $logs = $logs->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle','logs'));
    }

    public function emailDetails($id){
        $pageTitle = __('Email Details');
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle','email'));
    }

    // employee
    public function employeeTransaction(Request $request)
    {
        $pageTitle = __('Employee Transaction Logs');
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::with('agent')->where('agent_id', '!=', 0)->orderBy('id', 'desc');
        if ($request->search) {
            $search = request()->search;
            $transactions = $transactions->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")->orWhereHas('agent', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
        }


        if ($request->type) {
            $transactions = $transactions->where('trx_type', $request->type);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        //  date search
        if ($request->date) {
            $date = explode('-', $request->date);
            $request->merge([
                'start_date' => trim($date[0]),
                'end_date' => trim($date[1])
            ]);
            $request->validate([
                'start_date' => 'required|date_format:m/d/Y',
                'end_date' => 'nullable|date_format:m/d/Y'
            ]);
            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSeconds(59);
                $transactions = $transactions->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            } else {
                $transactions = $transactions->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        $transactions = $transactions->paginate(getPaginate());
        return view('admin.employees.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }
    public function employeeLoginHistory(Request $request)
    {
        $loginLogs = UserLogin::orderBy('id', 'desc')->with('agent')->where('agent_id', '!=', 0);
        $pageTitle = __('Employee Login History');
        if ($request->search) {
            $search = $request->search;
            $pageTitle = __('Employee Login History - :search', ['search' => $search]);
            $loginLogs = $loginLogs->whereHas('agent', function ($query) use ($search) {
                $query->where('username', $search);
            });
        }
        $loginLogs = $loginLogs->paginate(getPaginate());
        return view('admin.employees.reports.logins', compact('pageTitle', 'loginLogs'));
    }
    public function employeeLoginIpHistory($ip)
    {
        $pageTitle = __('Login by - :ip', ['ip' => $ip]);
        $loginLogs = UserLogin::where('user_ip', $ip)->where('agent_id', '!=', 0)->orderBy('id', 'desc')->with('agent')->paginate(getPaginate());
        return view('admin.employees.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));

    }
    public function employeeNotificationHistory(Request $request)
    {

        $pageTitle = __('Notification History');
        $logs = NotificationLog::orderBy('id', 'desc');
        $search = $request->search;
        if ($search) {
            $logs = $logs->whereHas('agent', function ($user) use ($search) {
                $user->where('username', 'like', "%$search%");
            });

        }
        $logs = $logs->with('agent')->whereNotNull('agent_id')->paginate(getPaginate());
        return view('admin.employees.reports.notification_history', compact('pageTitle', 'logs'));
    }
    public function employeeEmailDetails($id)
    {
        $pageTitle = __('Email Details');
        $email = NotificationLog::findOrFail($id);
        return view('admin.employees.reports.email_details', compact('pageTitle', 'email'));
    }
}
