@extends($activeTemplate . 'layouts.employee.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row gy-4 mb-4">
            <div class="col-12">
                <div class="card prod-p-card background-pattern-white h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Membership')</h6>
                                <h3 class="m-b-0">{{ $currentMembership ? (is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name) : __('Not Assigned') }}</h3>
                                <small class="text-muted d-block">
                                    {{ $currentMembership ? $currentMembership->member_code . ' | ' . showDateTime($currentMembership->start_date, 'd M Y') . ' - ' . ($currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime')) : __('No membership') }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-id-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Wallet Balance')</h6>
                                <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-money-bill-wave-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern-white h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Deposits')</h6>
                                <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern-white h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Withdrawals')</h6>
                                <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($totalWithdrawals) }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Transactions')</h6>
                                <h3 class="m-b-0">{{ $totalTransaction }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern-white h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Loyalty Points')</h6>
                                <h3 class="m-b-0">{{ $user->membership_points_balance }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card prod-p-card background-pattern h-100">
                    <div class="card-body">
                        <div class="row align-items-center m-b-0">
                            <div class="col">
                                <h6 class="m-b-5">@lang('Cashback Balance')</h6>
                                <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($user->cashback_balance) }}</h3>
                            </div>
                            <div class="col-auto">
                                <i class="dashboard-widget__icon las la-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Information of') {{ $user->fullname }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Member ID')</label>
                                    <input type="text" class="form-control" value="{{ $currentMembership?->member_code ?? __('-') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Phone')</label>
                                    <input type="text" class="form-control" value="{{ $user->mobile }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Current Membership')</label>
                                    <input type="text" class="form-control" value="{{ $currentMembership ? (is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name) : __('Not Assigned') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card p-2">
                    <a class="d-block btn bg--primary text-white" href="{{ route('employee.users') }}">
                        <i class="las la-arrow-left"></i> @lang('Back to Users')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection