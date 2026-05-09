<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PopupAd;
use App\Models\User;
use Illuminate\Http\Request;

class PopupAdController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Personal Popup Offers');
        $ads = PopupAd::where('created_by_type', 'employee')
            ->where('created_by_id', auth('employee')->id())
            ->orderByDesc('id');

        if ($request->search) {
            $search = $request->search;
            $ads->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%");
            });
        }

        $ads = $ads->paginate(getPaginate());
        return view($this->activeTemplate . 'employee.popup_ads.index', compact('pageTitle', 'ads'));
    }

    public function create()
    {
        $pageTitle = __('Create Personal Popup Offer');
        $users = User::orderByDesc('id')->limit(300)->get();
        return view($this->activeTemplate . 'employee.popup_ads.form', compact('pageTitle', 'users'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $ad = new PopupAd();
        $this->fillAd($ad, $request, $data);
        $ad->created_by_type = 'employee';
        $ad->created_by_id = auth('employee')->id();
        $ad->audience_type = 'specific_users';
        $ad->display_contexts = ['frontend', 'user_dashboard'];
        $ad->save();

        $notify[] = ['success', __('Personal popup offer created successfully')];
        return to_route('employee.popup-ads.index')->withNotify($notify);
    }

    public function edit(PopupAd $popupAd)
    {
        abort_unless($this->owns($popupAd), 403);
        $pageTitle = __('Edit Personal Popup Offer');
        $users = User::orderByDesc('id')->limit(300)->get();
        return view($this->activeTemplate . 'employee.popup_ads.form', compact('pageTitle', 'popupAd', 'users'));
    }

    public function update(Request $request, PopupAd $popupAd)
    {
        abort_unless($this->owns($popupAd), 403);
        $data = $this->validated($request);
        $this->fillAd($popupAd, $request, $data);
        $popupAd->audience_type = 'specific_users';
        $popupAd->display_contexts = ['frontend', 'user_dashboard'];
        $popupAd->save();

        $notify[] = ['success', __('Personal popup offer updated successfully')];
        return to_route('employee.popup-ads.index')->withNotify($notify);
    }

    public function status(PopupAd $popupAd)
    {
        abort_unless($this->owns($popupAd), 403);
        $popupAd->status = !$popupAd->status;
        $popupAd->save();

        $notify[] = ['success', __('Status updated successfully')];
        return back()->withNotify($notify);
    }

    public function destroy(PopupAd $popupAd)
    {
        abort_unless($this->owns($popupAd), 403);
        $popupAd->delete();

        $notify[] = ['success', __('Personal popup offer deleted successfully')];
        return back()->withNotify($notify);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:160',
            'title' => 'nullable|string|max:180',
            'title_ar' => 'nullable|string|max:180',
            'body' => 'nullable|string',
            'body_ar' => 'nullable|string',
            'cta_text' => 'nullable|string|max:120',
            'cta_text_ar' => 'nullable|string|max:120',
            'cta_url' => 'nullable|string|max:255',
            'target_user_ids' => 'required|array|min:1',
            'target_user_ids.*' => 'integer|exists:users,id',
            'placement' => 'required|string|in:modal,top_bar,bottom_bar,right_corner,left_corner,right_side,left_side',
            'size' => 'required|string|in:compact,medium,wide,tall,topbar',
            'trigger_type' => 'required|string|in:on_load,delay',
            'trigger_value' => 'nullable|integer|min:0|max:120',
            'frequency' => 'required|string|in:once,session,every_visit,hours,days',
            'frequency_value' => 'nullable|integer|min:0|max:365',
            'ends_at' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);
    }

    protected function fillAd(PopupAd $ad, Request $request, array $data): void
    {
        foreach (['name', 'title', 'title_ar', 'body', 'body_ar', 'cta_text', 'cta_text_ar', 'cta_url', 'placement', 'size', 'trigger_type', 'frequency'] as $field) {
            $ad->$field = $data[$field] ?? null;
        }

        $ad->target_user_ids = array_values(array_filter($request->target_user_ids ?? []));
        $ad->trigger_value = (int) $request->trigger_value;
        $ad->frequency_value = (int) $request->frequency_value;
        $ad->ends_at = $request->ends_at ?: null;
        $ad->starts_at = null;
        $ad->priority = 5;
        $ad->closeable = true;
        $ad->status = $request->boolean('status');

        if ($request->hasFile('image')) {
            $ad->image = fileUploader($request->image, getFilePath('popupAd'), null, $ad->image);
        }
    }

    protected function owns(PopupAd $popupAd): bool
    {
        return $popupAd->created_by_type === 'employee'
            && (int) $popupAd->created_by_id === (int) auth('employee')->id();
    }
}
