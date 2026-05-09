@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-7">
            @if($currentMembership)
                <div class="base--card radius--20 p-4 p-lg-5 h-100" style="background: linear-gradient(135deg, rgba(91, 156, 249, 0.16), rgba(91, 156, 249, 0.05)); border: 1px solid rgba(91, 156, 249, 0.18); box-shadow: 0 14px 32px rgba(91, 156, 249, 0.12);">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <span class="badge badge--primary mb-2">@lang('Member Card')</span>
                            <h3 class="mb-1">{{ $user->fullname }}</h3>
                            <p class="mb-0 text-muted">{{ '@' . $user->username }}</p>
                        </div>
                        <div class="text-end">
                            <div class="text-muted text-uppercase fs--12">@lang('Member ID')</div>
                            <div class="fs--28 fw-bold text--base">{{ $currentMembership->member_code }}</div>
                        </div>
                    </div>

                    <div class="base--card radius--20 p-4" style="background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(6px);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted text-uppercase fs--12">@lang('Membership Card')</span>
                            <span class="badge bg--success">@lang('Active')</span>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <span class="text-muted">@lang('Current Plan')</span>
                                <strong>{{ is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <span class="text-muted">@lang('Validity')</span>
                                <strong>{{ showDateTime($currentMembership->start_date, 'd M Y') }} - {{ $currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <span class="text-muted">@lang('Member Type')</span>
                                <strong>@lang('Premium Travel Member')</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('user.membership.plans') }}" class="btn btn--base">@lang('Upgrade Plan')</a>
                        <a href="{{ route('user.membership.benefits') }}" class="btn btn--light">@lang('View Benefits')</a>
                    </div>
                </div>
            @else
                <div class="base--card radius--20 p-4 p-lg-5 h-100 text-center">
                    <span class="badge badge--warning mb-3">@lang('No Active Membership')</span>
                    <h4 class="mb-2">@lang('Your Member Card will appear here')</h4>
                    <p class="text-muted mb-4">@lang('Subscribe to a membership plan first, then your member card will be generated automatically.')</p>
                    <a href="{{ route('user.membership.plans') }}" class="btn btn--base">@lang('See Membership Plans')</a>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="base--card radius--20 p-4 p-lg-5 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">@lang('Membership Details')</h5>
                    <span class="badge badge--success">@lang('Overview')</span>
                </div>

                @if($currentMembership)
                    <div class="d-grid gap-3">
                        <div class="base--card radius--20 p-3">
                            <span class="text-muted text-uppercase fs--12">@lang('Status')</span>
                            <h6 class="mb-0 mt-1">@lang('Active Membership')</h6>
                        </div>
                        <div class="base--card radius--20 p-3">
                            <span class="text-muted text-uppercase fs--12">@lang('Member Since')</span>
                            <h6 class="mb-0 mt-1">{{ showDateTime($currentMembership->start_date, 'd M Y') }}</h6>
                        </div>
                        <div class="base--card radius--20 p-3">
                            <span class="text-muted text-uppercase fs--12">@lang('Expires At')</span>
                            <h6 class="mb-0 mt-1">{{ $currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime') }}</h6>
                        </div>
                        <div class="base--card radius--20 p-3">
                            <span class="text-muted text-uppercase fs--12">@lang('Subscribed Plan')</span>
                            <h6 class="mb-0 mt-1">{{ is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name }}</h6>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <p class="mb-0">@lang('You do not have an active membership yet.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
