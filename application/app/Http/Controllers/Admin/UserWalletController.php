<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserWalletRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class UserWalletController extends Controller
{
    public function index()
    {
        $pageTitle = 'Customer Wallets';
        $requests = UserWalletRequest::query();
        
        $search = request()->search;
        if ($search) {
            $requests = $requests->where(function ($q) use ($search) {
                $q->whereHas('user', function ($user) use ($search) {
                    $user->where('username', 'like', "%$search%")
                        ->orWhere('firstname', 'like', "%$search%")
                        ->orWhere('lastname', 'like', "%$search%");
                })->orWhere('amount', 'like', "%$search%");
            });
        }

        $requests = $requests->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.wallet.requests', compact('pageTitle', 'requests', 'search'));
    }

    public function detail($id)
    {
        $request = UserWalletRequest::with('user')->findOrFail($id);
        $pageTitle = 'Wallet Request Details - ' . $request->user->username;
        return view('admin.wallet.detail', compact('pageTitle', 'request'));
    }

    public function action(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:1,2',
            'admin_feedback' => 'nullable|string'
        ]);

        $walletRequest = UserWalletRequest::with('user')->findOrFail($request->id);
        
        if ($walletRequest->status != 0) {
            $notify[] = ['error', 'This request has already been processed.'];
            return back()->withNotify($notify);
        }

        $user = $walletRequest->user;

        if ($request->status == 1) { // Approve
            if ($walletRequest->type == 'refund') {
                if ($user->balance < $walletRequest->amount) {
                    $notify[] = ['error', 'User does not have enough balance.'];
                    return back()->withNotify($notify);
                }
                $user->balance -= $walletRequest->amount;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $walletRequest->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx_type = '-';
                $transaction->details = 'Wallet Refund Approved';
                $transaction->trx = getTrx();
                $transaction->remark = 'wallet_refund';
                $transaction->save();
            } else { // 'use' type
                // Here we just mark it as approved, user usage is handled manually or by admin
                // but we deduct from balance when it's 'use' as well if required by logic
                $user->balance -= $walletRequest->amount;
                $user->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $walletRequest->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx_type = '-';
                $transaction->details = 'Wallet Usage/Allocation Approved';
                $transaction->trx = getTrx();
                $transaction->remark = 'wallet_usage';
                $transaction->save();
            }
            $walletRequest->status = 1;
        } else { // Reject
            $walletRequest->status = 2;
        }

        $walletRequest->admin_feedback = $request->admin_feedback;
        $walletRequest->save();

        $notify[] = ['success', 'Wallet request updated successfully.'];
        return back()->withNotify($notify);
    }
}
