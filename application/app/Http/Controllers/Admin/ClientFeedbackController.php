<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientFeedbackRequest;
use App\Models\ClientFeedback;

class ClientFeedbackController extends Controller
{
    public function index()
    {
        $pageTitle = 'Client Feedback';
        $feedback = ClientFeedback::orderBy('is_approved')->latest()->paginate(20);

        return view('admin.client_feedback.index', compact('pageTitle', 'feedback'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Client Feedback';
        $feedback = ClientFeedback::findOrFail($id);

        return view('admin.client_feedback.edit', compact('pageTitle', 'feedback'));
    }

    public function update(ClientFeedbackRequest $request, $id)
    {
        $request->validate(['is_approved' => 'required|boolean']);
        $feedback = ClientFeedback::findOrFail($id);
        $feedback->fill($request->validated());
        $feedback->is_approved = $request->boolean('is_approved');
        $feedback->save();

        return redirect()->route('admin.client-feedback.index')
            ->withNotify([['success', 'Feedback updated successfully']]);
    }

    public function approve($id)
    {
        $feedback = ClientFeedback::findOrFail($id);
        $feedback->is_approved = true;
        $feedback->save();

        return back()->withNotify([['success', 'Feedback approved successfully']]);
    }

    public function destroy($id)
    {
        ClientFeedback::findOrFail($id)->delete();

        return back()->withNotify([['success', 'Feedback deleted successfully']]);
    }
}
