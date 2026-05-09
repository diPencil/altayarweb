<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserWalletRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $pageTitle = __('My Wallet');
        $user = auth()->user();
        $currentMembership = $user->currentMembership()->with('plan')->first();
        $requests = UserWalletRequest::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('presets.default.user.wallet.index', compact('pageTitle', 'user', 'currentMembership', 'requests'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'type' => 'required|in:use,refund',
            'details' => 'nullable|string'
        ]);

        $user = auth()->user();

        if ($request->type == 'refund' && $user->balance < $request->amount) {
            $notify[] = ['error', __('Insufficient balance for refund.')];
            return back()->withNotify($notify);
        }

        $walletRequest = new UserWalletRequest();
        $walletRequest->user_id = $user->id;
        $walletRequest->amount = $request->amount;
        $walletRequest->type = $request->type;
        $walletRequest->details = $request->details;
        $walletRequest->save();

        $notify[] = ['success', __('Wallet request submitted successfully.')];
        return back()->withNotify($notify);
    }
}
