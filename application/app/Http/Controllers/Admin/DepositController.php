<?php

namespace App\Http\Controllers\Admin;

use App\Models\Deposit;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function pending()
    {
        $pageTitle = __('Pending Tour Payments');
        $deposits = $this->depositData('pending');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }


    public function approved()
    {
        $pageTitle = __('Approved Tour Payments');
        $deposits = $this->depositData('approved');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function successful()
    {
        $pageTitle = __('Successful Tour Payments');
        $deposits = $this->depositData('successful');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected()
    {
        $pageTitle = __('Rejected Tour Payments');
        $deposits = $this->depositData('rejected');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function initiated()
    {
        $pageTitle = __('Initiated Tour Payments');
        $deposits = $this->depositData('initiated');
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit()
    {
        $pageTitle = __('Tour Payments History');
        $depositData = $this->depositData($scope = null, $summery = true);
        $deposits = $depositData['data'];
        $summery = $depositData['summery'];
        $successful = $summery['successful'];
        $pending = $summery['pending'];
        $rejected = $summery['rejected'];
        $initiated = $summery['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits','successful','pending','rejected','initiated'));
    }

    protected function depositData($scope = null,$summery = false)
    {
    
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        }else{
            $deposits = Deposit::with(['user', 'gateway']);
        }

        $request = request();
        //search
        if ($request->search) {
            $search = request()->search;
            $deposits = $deposits->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%$search%")
                  ->orWhere('guest_name', 'like', "%$search%")
                  ->orWhere('guest_email', 'like', "%$search%")
                  ->orWhereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%");
                });
            });
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
                $endDate = Carbon::parse($request->end_date)->addHours(23)->addMinutes(59)->addSecond(59);
                $deposits   = $deposits->whereBetween('created_at', [Carbon::parse($request->start_date), $endDate]);
            }else{
                $deposits   = $deposits->whereDate('created_at', Carbon::parse($request->start_date));
            }
        }

        //vai method
        if ($request->method) {
            $method = Gateway::where('alias',$request->method)->firstOrFail();
            $deposits = $deposits->where('method_code',$method->code);
        }

        if (!$summery) {
            return $deposits->orderBy('id','desc')->paginate(getPaginate());
        }else{
            $successful = clone $deposits;
            $pending = clone $deposits;
            $rejected = clone $deposits;
            $initiated = clone $deposits;

            $successfulSummery = $successful->where('status',1)->sum('amount');
            $pendingSummery = $pending->where('status',2)->sum('amount');
            $rejectedSummery = $rejected->where('status',3)->sum('amount');
            $initiatedSummery = $initiated->where('status',0)->sum('amount');

            return [
                'data'=>$deposits->orderBy('id','desc')->paginate(getPaginate()),
                'summery'=>[
                    'successful'=>$successfulSummery,
                    'pending'=>$pendingSummery,
                    'rejected'=>$rejectedSummery,
                    'initiated'=>$initiatedSummery,
                ]
            ];
        }
    }

    public function details($id)
    {
        $general = gs();
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = __('Payment Request of :amount :currency', [
            'amount' => showAmount($deposit->amount),
            'currency' => $general->cur_text,
        ]);
        $details = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        
        $latestGatewayLog = \App\Models\PaymentGatewayLog::where('deposit_id', $deposit->id)
            ->orWhere('trx', $deposit->trx)
            ->latest('id')
            ->first();

        return view('admin.deposit.detail', compact('pageTitle', 'deposit','details', 'latestGatewayLog'));
    }


    public function approve($id)
    {
        $deposit = Deposit::where('id',$id)->where('status',2)->firstOrFail();

        PaymentController::userDataUpdate($deposit,true);

        $notify[] = ['success', __('Deposit request approved successfully')];

        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'message' => 'required|string|max:255'
        ]);
        $deposit = Deposit::with('tour_booking')->where('id',$request->id)->where('status',2)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status = 3;
        $deposit->save();

        $deposit->tour_booking->status = 3;
        $deposit->tour_booking->save();

        notify($deposit->user, 'DEPOSIT_REJECT', [
            'method_name' => $deposit->gatewayCurrency()->name,
            'method_currency' => $deposit->method_currency,
            'method_amount' => showAmount($deposit->final_amo),
            'amount' => showAmount($deposit->amount),
            'charge' => showAmount($deposit->charge),
            'rate' => showAmount($deposit->rate),
            'trx' => $deposit->trx,
            'rejection_message' => $request->message
        ]);

        $notify[] = ['success', __('Payment request rejected successfully')];
        return  to_route('admin.deposit.pending')->withNotify($notify);

    }
}
