@extends($activeTemplate . 'layouts.employee.master')
@php
    $ad = $popupAd ?? null;
    $selectedUsers = old('target_user_ids', $ad->target_user_ids ?? []);
@endphp
@section('content')
    <div class="dashboard-body__bar d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1">{{ $ad ? __('Edit Personal Offer') : __('Create Personal Offer') }}</h4>
            <p class="mb-0 text-muted">@lang('Target selected customers with a dedicated popup offer.')</p>
        </div>
        <a href="{{ route('employee.popup-ads.index') }}" class="btn btn--dark"><i class="las la-arrow-left"></i> @lang('Back')</a>
    </div>

    <form action="{{ $ad ? route('employee.popup-ads.update', $ad) : route('employee.popup-ads.store') }}" method="POST" enctype="multipart/form-data" class="card custom--card">
        @csrf
        <div class="card-body">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <label class="form-label">@lang('Internal Name')</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $ad->name ?? '') }}" required>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">@lang('Customers')</label>
                    <select name="target_user_ids[]" class="form-control" multiple size="5" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(in_array($user->id, array_map('intval', $selectedUsers ?? []), true))>{{ $user->username }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">@lang('Title')</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $ad->title ?? '') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">@lang('Title (Arabic)')</label>
                    <input type="text" name="title_ar" dir="rtl" class="form-control" value="{{ old('title_ar', $ad->title_ar ?? '') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">@lang('Message')</label>
                    <textarea name="body" rows="4" class="form-control">{{ old('body', $ad->body ?? '') }}</textarea>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">@lang('Message (Arabic)')</label>
                    <textarea name="body_ar" rows="4" dir="rtl" class="form-control">{{ old('body_ar', $ad->body_ar ?? '') }}</textarea>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">@lang('CTA Text')</label>
                    <input type="text" name="cta_text" class="form-control" value="{{ old('cta_text', $ad->cta_text ?? '') }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">@lang('CTA Text (Arabic)')</label>
                    <input type="text" name="cta_text_ar" dir="rtl" class="form-control" value="{{ old('cta_text_ar', $ad->cta_text_ar ?? '') }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">@lang('CTA URL')</label>
                    <input type="text" name="cta_url" class="form-control" value="{{ old('cta_url', $ad->cta_url ?? '') }}" placeholder="/membership-details">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Placement')</label>
                    <select name="placement" class="form-control">
                        @foreach(['modal' => 'Center Modal', 'top_bar' => 'Top Bar', 'bottom_bar' => 'Bottom Bar', 'right_corner' => 'Right Corner', 'left_corner' => 'Left Corner', 'right_side' => 'Right Side', 'left_side' => 'Left Side'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('placement', $ad->placement ?? 'modal') === $key)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Size')</label>
                    <select name="size" class="form-control">
                        @foreach(['compact' => 'Compact', 'medium' => 'Medium', 'wide' => 'Wide', 'tall' => 'Tall', 'topbar' => 'Top Bar Size'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('size', $ad->size ?? 'medium') === $key)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Trigger')</label>
                    <select name="trigger_type" class="form-control">
                        <option value="on_load" @selected(old('trigger_type', $ad->trigger_type ?? 'on_load') === 'on_load')>@lang('On Load')</option>
                        <option value="delay" @selected(old('trigger_type', $ad->trigger_type ?? 'on_load') === 'delay')>@lang('Delay')</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Delay Seconds')</label>
                    <input type="number" name="trigger_value" class="form-control" value="{{ old('trigger_value', $ad->trigger_value ?? 0) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Frequency')</label>
                    <select name="frequency" class="form-control">
                        @foreach(['once' => 'Once', 'session' => 'Once Per Session', 'every_visit' => 'Every Visit', 'hours' => 'Every X Hours', 'days' => 'Every X Days'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('frequency', $ad->frequency ?? 'once') === $key)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Frequency Value')</label>
                    <input type="number" name="frequency_value" class="form-control" value="{{ old('frequency_value', $ad->frequency_value ?? 0) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Ends At')</label>
                    <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', optional($ad?->ends_at)->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">@lang('Image')</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-lg-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @checked(old('status', $ad->status ?? true))>
                        <label for="status" class="form-check-label">@lang('Active')</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button class="btn btn--base">{{ $ad ? __('Update') : __('Save') }}</button>
        </div>
    </form>
@endsection
