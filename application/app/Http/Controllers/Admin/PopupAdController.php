<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MembershipPlan;
use App\Models\PopupAd;
use App\Models\User;
use Illuminate\Http\Request;

class PopupAdController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Popup Ads');
        $ads = PopupAd::query()->orderByDesc('id');

        if ($request->search) {
            $search = $request->search;
            $ads->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%");
            });
        }

        if ($request->status !== null && $request->status !== '') {
            $ads->where('status', (int) $request->status);
        }

        $ads = $ads->paginate(getPaginate());
        return view('admin.popup_ads.index', compact('pageTitle', 'ads'));
    }

    public function create()
    {
        $pageTitle = __('Create Popup Ad');
        return view('admin.popup_ads.form', $this->formData(compact('pageTitle')));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $ad = new PopupAd();
        $this->fillAd($ad, $request, $data);
        $ad->created_by_type = 'admin';
        $ad->created_by_id = auth('admin')->id();
        $ad->save();

        $notify[] = ['success', __('Popup ad created successfully')];
        return to_route('admin.popup-ads.index')->withNotify($notify);
    }

    public function edit(PopupAd $popupAd)
    {
        $pageTitle = __('Edit Popup Ad');
        return view('admin.popup_ads.form', $this->formData(compact('pageTitle', 'popupAd')));
    }

    public function update(Request $request, PopupAd $popupAd)
    {
        $data = $this->validated($request);
        $this->fillAd($popupAd, $request, $data);
        $popupAd->save();

        $notify[] = ['success', __('Popup ad updated successfully')];
        return to_route('admin.popup-ads.index')->withNotify($notify);
    }

    public function status(PopupAd $popupAd)
    {
        $popupAd->status = !$popupAd->status;
        $popupAd->save();

        $notify[] = ['success', __('Status updated successfully')];
        return back()->withNotify($notify);
    }

    public function destroy(PopupAd $popupAd)
    {
        if ($popupAd->image) {
            fileManager()->removeFile(getFilePath('popupAd') . '/' . $popupAd->image);
        }
        $popupAd->delete();

        $notify[] = ['success', __('Popup ad deleted successfully')];
        return back()->withNotify($notify);
    }

    public function analytics(PopupAd $popupAd)
    {
        $pageTitle = __('Popup Ad Analytics');
        $events = $popupAd->events()->latest()->paginate(getPaginate());
        return view('admin.popup_ads.analytics', compact('pageTitle', 'popupAd', 'events'));
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
            'placement' => 'required|string|in:' . implode(',', array_keys(PopupAd::PLACEMENTS)),
            'size' => 'required|string|in:' . implode(',', array_keys(PopupAd::SIZES)),
            'audience_type' => 'required|string|in:' . implode(',', array_keys(PopupAd::AUDIENCES)),
            'display_contexts' => 'nullable|array',
            'display_contexts.*' => 'string|in:' . implode(',', array_keys(PopupAd::CONTEXTS)),
            'page_rules' => 'nullable|string',
            'membership_plan_ids' => 'nullable|array',
            'target_user_ids' => 'nullable|array',
            'target_employee_ids' => 'nullable|array',
            'trigger_type' => 'required|string|in:on_load,delay',
            'trigger_value' => 'nullable|integer|min:0|max:120',
            'frequency' => 'required|string|in:once,session,every_visit,hours,days',
            'frequency_value' => 'nullable|integer|min:0|max:365',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'priority' => 'nullable|integer|min:0|max:999',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);
    }

    protected function fillAd(PopupAd $ad, Request $request, array $data): void
    {
        foreach (['name', 'title', 'title_ar', 'body', 'body_ar', 'cta_text', 'cta_text_ar', 'cta_url', 'placement', 'size', 'audience_type', 'trigger_type', 'frequency'] as $field) {
            $ad->$field = $data[$field] ?? null;
        }

        $ad->display_contexts = $request->display_contexts ?: ['frontend'];
        $ad->page_rules = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $request->page_rules))));
        $ad->membership_plan_ids = array_values(array_filter($request->membership_plan_ids ?? []));
        $ad->target_user_ids = array_values(array_filter($request->target_user_ids ?? []));
        $ad->target_employee_ids = array_values(array_filter($request->target_employee_ids ?? []));
        $ad->trigger_value = (int) $request->trigger_value;
        $ad->frequency_value = (int) $request->frequency_value;
        $ad->starts_at = $request->starts_at ?: null;
        $ad->ends_at = $request->ends_at ?: null;
        $ad->priority = (int) ($request->priority ?? 10);
        $ad->closeable = $request->boolean('closeable');
        $ad->status = $request->boolean('status');

        if ($request->hasFile('image')) {
            $ad->image = fileUploader($request->image, getFilePath('popupAd'), null, $ad->image);
        }
    }

    protected function formData(array $data): array
    {
        return array_merge($data, [
            'placements' => PopupAd::PLACEMENTS,
            'sizes' => PopupAd::SIZES,
            'audiences' => PopupAd::AUDIENCES,
            'contexts' => PopupAd::CONTEXTS,
            'plans' => MembershipPlan::orderBy('name')->get(),
            'users' => User::orderByDesc('id')->limit(300)->get(),
            'employees' => Employee::orderByDesc('id')->limit(300)->get(),
        ]);
    }
}
