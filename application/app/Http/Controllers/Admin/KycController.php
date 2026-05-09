<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function setting()
    {
        $pageTitle = __('KYC Setting');
        $kycFormName = __('KYC Form for User');
        $kycDescription = __('Define the documents and fields users must submit before KYC review and approval. Each item you add here becomes a field in the user verification form.');
        $form      = Form::where('act', 'kyc')->first();
        return view('admin.kyc.setting', compact('pageTitle', 'form','kycFormName','kycDescription'));
    }

    public function settingUpdate(Request $request)
    {
        $formProcessor       = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'kyc')->first();
        if ($exist) {
            $isUpdate = true;
        } else {
            $isUpdate = false;
        }
        $formProcessor->generate('kyc', $isUpdate, 'act');

        $notify[] = ['success', __('KYC data updated successfully')];
        return back()->withNotify($notify);
    }

    public function agentSetting()
    {
        $pageTitle = __('Employee KYC Setting');
        $kycFormName = __('KYC Form for Employee');
        $kycDescription = __('Define the documents and fields employees must submit before KYC review and approval. Each item you add here becomes a field in the employee verification form.');
        $form = Form::where('act','agent_kyc')->first();
        return view('admin.kyc.setting',compact('pageTitle','form','kycFormName','kycDescription'));
    }
    public function agentSettingUpdate(Request $request)
    {
        $formProcessor = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'],$generatorValidation['messages']);
        $exist = Form::where('act','agent_kyc')->first();
        if ($exist) {
            $isUpdate = true;
        }else{
            $isUpdate = false;
        }
        $formProcessor->generate('agent_kyc',$isUpdate,'act');
        $notify[] = ['success', __('KYC data updated successfully')];
        return back()->withNotify($notify);
    }
}
