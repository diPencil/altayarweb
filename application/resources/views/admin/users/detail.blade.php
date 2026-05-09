@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-xl-4">
                <div class="row gy-2 pb-2 gx-2">
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                            <div class="card prod-p-card background-pattern">
                                <div class="card-body">
                                    <div class="row align-items-center m-b-0">
                                        <div class="col">
                                            <h6 class="m-b-5">@lang('Wallet Balance')</h6>
                                            <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                            </h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="dashboard-widget__icon las la-money-bill-wave-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.deposit.list') }}?search={{ $user->username }}">
                            <div class="card prod-p-card background-pattern-white">
                                <div class="card-body">
                                    <div class="row align-items-center m-b-0">
                                        <div class="col">
                                            <h6 class="m-b-5">@lang('Deposits')</h6>
                                            <h3 class="m-b-0">{{ $general->cur_sym }}{{
                                                showAmount($totalDeposit) }}
                                            </h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="dashboard-widget__icon las la-wallet"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.withdraw.log') }}?search={{ $user->username }}">
                            <div class="card prod-p-card background-pattern-white">
                                <div class="card-body">
                                    <div class="row align-items-center m-b-0">
                                        <div class="col">
                                            <h6 class="m-b-5">@lang('Withdrawals')</h6>
                                            <h3 class="m-b-0">{{ $general->cur_sym }}{{
                                                showAmount($totalWithdrawals)
                                                }}</h3>
                                        </div>
                                        <div class="col-auto">
                                            <i class="dashboard-widget__icon las fa-wallet"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                            <div class="card prod-p-card background-pattern">
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
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <div class="card prod-p-card background-pattern-white">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">@lang('Membership')</h6>
                                        <h3 class="m-b-0">
                                            {{ $currentMembership ? (is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name) : __('Not Assigned') }}
                                        </h3>
                                        <small class="text-muted d-block">
                                            {{ $currentMembership ? $currentMembership->member_code . ' | ' . showDateTime($currentMembership->start_date, 'd M Y') . ' - ' . ($currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime')) : __('Assign a plan from the form below') }}
                                        </small>

                                        @if($currentMembership)
                                            <button type="button" class="btn btn-outline--danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeMembershipModal">
                        <i class="las la-user-minus"></i> @lang('Remove Membership')
                    </button>
                                        @endif
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon las la-id-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($currentMembership)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('Edit Member ID')</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.users.membership.code.update', $currentMembership->id) }}" method="POST" class="row g-3">
                                    @csrf
                                    <div class="col-md-8">
                                        <label class="form-label">@lang('Member ID')</label>
                                        <input type="text" name="member_code" class="form-control" value="{{ $currentMembership->member_code }}" required>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn--primary w-100">@lang('Update')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.membership.points') }}">
                            <div class="card prod-p-card background-pattern">
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
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-12">
                        <a href="{{ route('admin.membership.cashback') }}">
                            <div class="card prod-p-card background-pattern-white">
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
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card p-2">
                        <ul class="d-flex flex-wrap gap-1">
                   

                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary" href="{{route('admin.users.login',$user->id)}}"
                                    target="_blank">
                                    <i class="las la-sign-in-alt"></i> @lang('Login as User')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary"
                                    href="{{ route('admin.users.notification.log',$user->id) }}">
                                    <i class="las la-bell"></i> @lang('Notifiactions')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary"
                                    href="{{route('admin.report.login.history')}}?search={{ $user->username }}">
                                    <i class="las la-list-alt"></i> @lang('Login History')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                @if($user->status == 1)
                                <a class="d-block btn bg--primary" class="userStatus" data-bs-toggle="modal"
                                    data-bs-target="#userStatusModal" href="javascript:void(0)">
                                    <i class="las la-ban"></i> @lang('Ban User')
                                </a>
                                @else
                                <a class="userStatus bg--primary" data-bs-toggle="modal"
                                    data-bs-target="#userStatusModal" href="javascript:void(0)">
                                    <i class="las la-undo"></i> @lang('Unban User')
                                </a>
                                @endif
                            </li>
                            @if($user->kyc_data)
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary"
                                    href="{{ route('admin.users.kyc.details', $user->id) }}">
                                    <i class="las la-paper-plane"></i> @lang('KYC Data')
                                </a>
                            </li>
                            @endif
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary"
                                    href="{{route('admin.users.notification.single', $user->id)}}">
                                    <i class="las la-paper-plane"></i> @lang('Send Email')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary bal-btn" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#addSubModal" data-act="add">
                                    <i class="las la-money-bill-wave-alt"></i> @lang('Wallet Balance')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary bal-btn" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#pointsModal" data-act="add">
                                    <i class="las la-star"></i> @lang('Points')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary bal-btn" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cashbackModal" data-act="add">
                                    <i class="las la-wallet"></i> @lang('Cashback')
                                </a>
                            </li>
                            <li class="flex-grow-1 flex-shrink-0">
                                <a class="d-block btn bg--primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#assignEmployeeModal">
                                    <i class="las la-user-check"></i> @lang('Assign Employee')
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Information of') {{$user->fullname}}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group  col-xl-3 col-md-6 col-12">
                                    <label>@lang('Assigned Employee')</label>
                                    <input type="text" class="form-control" value="{{ $user->assignedEmployee?->fullname ?? __('Unassigned') }}" readonly>
                                </div>
                                <div class="form-group  col-xl-3 col-md-6 col-12">
                                    <label>@lang('Email Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="ev" {{ $user->ev ?
                                        'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group  col-xl-3 col-md-6 col-12">
                                    <label>@lang('Mobile Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="sv" {{ $user->sv ?
                                        'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group  col-xl-3 col-md-6 col-12">
                                    <label>@lang('2FA Verification') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="ts" {{ $user->ts ?
                                        'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="form-group  col-xl-3 col-md-6 col-12">
                                    <label>@lang('KYC') </label>
                                    <label class="switch m-0">
                                        <input type="checkbox" class="toggle-switch" name="kv" {{ $user->kv ?
                                        'checked' : null }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('First Name')</label>
                                        <input class="form-control" type="text" name="firstname" required
                                            value="{{ old('firstname', $user->firstname) }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">@lang('Last Name')</label>
                                        <input class="form-control" type="text" name="lastname" required
                                            value="{{ old('lastname', $user->lastname) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Email') </label>
                                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}"
                                            required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('Mobile Number') </label>
                                        <div class="input-group ">
                                            <span class="input-group-text mobile-code"></span>
                                            <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile"
                                                class="form-control checkUser" required>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label>@lang('Address')</label>
                                        <input class="form-control" type="text" name="address"
                                            value="{{ old('address', optional($user->address)->address) }}">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group">
                                        <label>@lang('City')</label>
                                        <input class="form-control" type="text" name="city"
                                            value="{{ old('city', optional($user->address)->city) }}">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('State')</label>
                                        <input class="form-control" type="text" name="state"
                                            value="{{ old('state', optional($user->address)->state) }}">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Zip/Postal')</label>
                                        <input class="form-control" type="text" name="zip"
                                            value="{{ old('zip', optional($user->address)->zip) }}">
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Country')</label>
                                        <select name="country" class="form-control">
                                            @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">{{
                                                __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group  text-end mb-0">
                                        <button type="submit" class="btn btn--primary btn-global">@lang('Save')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Assign Membership')</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.membership.assign', $user->id) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <label class="form-label">@lang('Membership Plan')</label>
                                <select name="membership_plan_id" class="form-control" required>
                                    <option value="">@lang('Choose one')</option>
                                    @foreach($membershipPlans as $plan)
                                        <option value="{{ $plan->id }}">{{ is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name }} - {{ $general->cur_sym }}{{ showAmount($plan->price) }} - +{{ $plan->bonus_points }} @lang('Points')</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn--primary w-100">@lang('Assign')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Add Sub Balance MODAL --}}
<div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="type"></span> <span>@lang('Wallet Balance')</span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.users.add.sub.balance',$user->id)}}" method="POST">
                @csrf
                <input type="hidden" name="act">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Amount')</label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control"
                                placeholder="@lang('Please provide positive amount')" required>
                            <div class="input-group-text">{{ __($general->cur_text) }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('Remark')</label>
                        <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="pointsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="type"></span> <span>@lang('Points')</span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.add.sub.points', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>@lang('Action')</label>
                        <select name="act" class="form-control points-act">
                            <option value="add">@lang('Add')</option>
                            <option value="sub">@lang('Subtract')</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>@lang('Points')</label>
                        <input type="number" step="1" min="1" name="amount" class="form-control" placeholder="@lang('Please provide positive points')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Remark')</label>
                        <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="cashbackModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="type"></span> <span>@lang('Cashback')</span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.add.sub.cashback', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>@lang('Action')</label>
                        <select name="act" class="form-control cashback-act">
                            <option value="add">@lang('Add')</option>
                            <option value="sub">@lang('Subtract')</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>@lang('Amount')</label>
                        <input type="number" step="any" min="0.01" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Remark')</label>
                        <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="assignEmployeeModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Assign Employee')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.assign.employee', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>@lang('Employee')</label>
                        <select name="agent_id" class="form-control">
                            <option value="">@lang('Unassigned')</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected($user->agent_id == $employee->id)>
                                    {{ $employee->fullname }} ({{ '@' . $employee->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if($user->status == 1)
                    <span>@lang('Ban User')</span>
                    @else
                    <span>@lang('Unban User')</span>
                    @endif
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.users.status',$user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($user->status == 1)
                    <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her
                        dashboard.')</h6>
                    <div class="form-group">
                        <label>@lang('Reason')</label>
                        <textarea class="form-control" name="reason" rows="4" required></textarea>
                    </div>
                    @else
                    <p><span>@lang('Ban reason was'):</span></p>
                    <p>{{ $user->ban_reason }}</p>
                    <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($user->status == 1)
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                    @else
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div id="removeMembershipModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Remove Membership')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.users.membership.remove', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="las la-exclamation-circle text--danger" style="font-size: 3.5rem;"></i>
                    </div>

                    <div class="text-center mb-4">
                        <h5 class="mb-2">@lang('Are you sure you want to remove this user’s active membership?')</h5>
                    </div>

                    <div class="p-3 bg--light radius--10">
                        <p class="mb-0 fs--14 text-muted text-center">@lang('The user will lose access to membership benefits until a new membership is assigned.')</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger">@lang('Yes, Remove Membership')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('script')
<script>
    (function ($) {
        "use strict"
        $('.bal-btn').on('click', function () {
            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            } else {
                $('.type').text('Subtract');
            }
        });
        $('.points-act').on('change', function () {
            var act = $(this).val();
            $('#pointsModal').find('input[name=act]').val(act);
            $('#pointsModal').find('.type').text(act == 'add' ? 'Add' : 'Subtract');
        }).trigger('change');
        $('.cashback-act').on('change', function () {
            var act = $(this).val();
            $('#cashbackModal').find('input[name=act]').val(act);
            $('#cashbackModal').find('.type').text(act == 'add' ? 'Add' : 'Subtract');
        }).trigger('change');
        let mobileElement = $('.mobile-code');
        $('select[name=country]').on('change',function () {
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

        $('select[name=country]').val('{{$user->country_code}}');
        let dialCode = $('select[name=country] :selected').data('mobile_code');
        let mobileNumber = `{{ $user->mobile }}`;
        mobileNumber = mobileNumber.replace(dialCode, '');
        $('input[name=mobile]').val(mobileNumber);
        mobileElement.text(`+${dialCode}`);

    })(jQuery);
</script>
@endpush
