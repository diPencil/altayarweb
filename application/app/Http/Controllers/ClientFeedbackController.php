<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFeedbackRequest;
use App\Models\ClientFeedback;

class ClientFeedbackController extends Controller
{
    public function store(ClientFeedbackRequest $request)
    {
        ClientFeedback::create($request->validated());

        return redirect()->route('public.client.feedback')
            ->with('feedback_success', 'تم إرسال تعليقك بنجاح، وسيظهر بعد موافقة الإدارة.');
    }
}
