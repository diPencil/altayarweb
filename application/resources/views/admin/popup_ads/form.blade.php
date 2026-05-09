@extends('admin.layouts.app')
@php
    $ad = $popupAd ?? null;
    $selectedContexts = old('display_contexts', $ad->display_contexts ?? ['frontend']);
    $selectedPlans = old('membership_plan_ids', $ad->membership_plan_ids ?? []);
    $selectedUsers = old('target_user_ids', $ad->target_user_ids ?? []);
    $selectedEmployees = old('target_employee_ids', $ad->target_employee_ids ?? []);
@endphp
@section('panel')
    <form action="{{ $ad ? route('admin.popup-ads.update', $ad) : route('admin.popup-ads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gy-4">
            <div class="col-xl-8">
                <div class="card b-radius--10">
                    <div class="card-header"><h5 class="mb-0">@lang('Campaign Content')</h5></div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Internal Name')</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $ad->name ?? '') }}" required>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Placement')</label>
                                <select name="placement" class="form-control">
                                    @foreach($placements as $key => $label)
                                        <option value="{{ $key }}" @selected(old('placement', $ad->placement ?? 'modal') === $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Size')</label>
                                <select name="size" class="form-control">
                                    @foreach($sizes as $key => $label)
                                        <option value="{{ $key }}" @selected(old('size', $ad->size ?? 'medium') === $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Title')</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $ad->title ?? '') }}">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Title (Arabic)')</label>
                                <input type="text" name="title_ar" class="form-control" dir="rtl" value="{{ old('title_ar', $ad->title_ar ?? '') }}">
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Message')</label>
                                <textarea name="body" rows="5" class="form-control">{{ old('body', $ad->body ?? '') }}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">@lang('Message (Arabic)')</label>
                                <textarea name="body_ar" rows="5" class="form-control" dir="rtl">{{ old('body_ar', $ad->body_ar ?? '') }}</textarea>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">@lang('CTA Text')</label>
                                <input type="text" name="cta_text" class="form-control" value="{{ old('cta_text', $ad->cta_text ?? '') }}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">@lang('CTA Text (Arabic)')</label>
                                <input type="text" name="cta_text_ar" class="form-control" dir="rtl" value="{{ old('cta_text_ar', $ad->cta_text_ar ?? '') }}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">@lang('CTA URL')</label>
                                <input type="text" name="cta_url" class="form-control" value="{{ old('cta_url', $ad->cta_url ?? '') }}" placeholder="/membership-details">
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label">@lang('Image')</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @if($ad?->image)
                                    <small class="text-muted d-block mt-2">@lang('Current image:') {{ $ad->image }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card b-radius--10 mb-4">
                    <div class="card-header"><h5 class="mb-0">@lang('Targeting')</h5></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">@lang('Display In')</label>
                            @foreach($contexts as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="display_contexts[]" value="{{ $key }}" id="ctx{{ $key }}" @checked(in_array($key, $selectedContexts ?? [], true))>
                                    <label class="form-check-label" for="ctx{{ $key }}">{{ __($label) }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Page Rules')</label>
                            <textarea name="page_rules" rows="4" class="form-control" placeholder="*&#10;membership-details&#10;offers/*">{{ old('page_rules', implode("\n", $ad->page_rules ?? [])) }}</textarea>
                            <small class="text-muted">@lang('One path per line. Use * for all pages.')</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Audience')</label>
                            <select name="audience_type" class="form-control" id="audienceType">
                                @foreach($audiences as $key => $label)
                                    <option value="{{ $key }}" @selected(old('audience_type', $ad->audience_type ?? 'all') === $key)>{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ad-target ad-target-membership">
                            <label class="form-label">@lang('Membership Plans')</label>
                            <select name="membership_plan_ids[]" class="form-control" multiple size="5">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(in_array($plan->id, array_map('intval', $selectedPlans ?? []), true))>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ad-target ad-target-users">
                            <label class="form-label">@lang('Specific Users')</label>
                            <select name="target_user_ids[]" class="form-control" multiple size="6">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(in_array($user->id, array_map('intval', $selectedUsers ?? []), true))>{{ $user->username }} - {{ $user->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ad-target ad-target-employees">
                            <label class="form-label">@lang('Specific Employees')</label>
                            <select name="target_employee_ids[]" class="form-control" multiple size="6">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(in_array($employee->id, array_map('intval', $selectedEmployees ?? []), true))>{{ $employee->username }} - {{ $employee->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card b-radius--10">
                    <div class="card-header"><h5 class="mb-0">@lang('Behavior')</h5></div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-6">
                                <label class="form-label">@lang('Trigger')</label>
                                <select name="trigger_type" class="form-control">
                                    <option value="on_load" @selected(old('trigger_type', $ad->trigger_type ?? 'on_load') === 'on_load')>@lang('On Load')</option>
                                    <option value="delay" @selected(old('trigger_type', $ad->trigger_type ?? 'on_load') === 'delay')>@lang('Delay')</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Delay Seconds')</label>
                                <input type="number" name="trigger_value" class="form-control" min="0" value="{{ old('trigger_value', $ad->trigger_value ?? 0) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Frequency')</label>
                                <select name="frequency" class="form-control">
                                    @foreach(['once' => 'Once', 'session' => 'Once Per Session', 'every_visit' => 'Every Visit', 'hours' => 'Every X Hours', 'days' => 'Every X Days'] as $key => $label)
                                        <option value="{{ $key }}" @selected(old('frequency', $ad->frequency ?? 'once') === $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Frequency Value')</label>
                                <input type="number" name="frequency_value" class="form-control" min="0" value="{{ old('frequency_value', $ad->frequency_value ?? 0) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Starts At')</label>
                                <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', optional($ad?->starts_at)->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Ends At')</label>
                                <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', optional($ad?->ends_at)->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">@lang('Priority')</label>
                                <input type="number" name="priority" class="form-control" min="0" value="{{ old('priority', $ad->priority ?? 10) }}">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="closeable" value="1" id="closeable" @checked(old('closeable', $ad->closeable ?? true))>
                                    <label for="closeable" class="form-check-label">@lang('Show close button')</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @checked(old('status', $ad->status ?? true))>
                                    <label for="status" class="form-check-label">@lang('Active')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.popup-ads.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                        <button class="btn btn--primary">{{ $ad ? __('Update') : __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
<script>
    (function () {
        'use strict';
        const select = document.getElementById('audienceType');
        const groups = document.querySelectorAll('.ad-target');
        function syncTargets() {
            groups.forEach(group => group.style.display = 'none');
            if (!select) return;
            if (select.value === 'membership') document.querySelector('.ad-target-membership').style.display = '';
            if (select.value === 'specific_users') document.querySelector('.ad-target-users').style.display = '';
            if (select.value === 'specific_employees') document.querySelector('.ad-target-employees').style.display = '';
        }
        select?.addEventListener('change', syncTargets);
        syncTargets();
    })();
</script>
@endpush
