@extends('admin.layouts.app')

@section('panel')
@include('admin.components.tabs.withdrawal')
<div class="row justify-content-center">
    @if(request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.method') ||
    request()->routeIs('admin.users.withdrawals') || request()->routeIs('admin.users.withdrawals.method'))
    <div class="col-lg-12 mt-3">
        <div class="row gy-4 pb-4">
            <div class="col-xl-4 col-sm-6">
                <a href="{{ route('admin.withdraw.approved') }}">
                    <div class="card prod-p-card background-pattern-white bg--primary">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">@lang('Approved Withdrawals')</h6>
                                    <h3 class="m-b-0 text-white">{{ __($general->cur_sym) }}{{ showAmount($successful)
                                        }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-sm-6">
                <a href="{{ route('admin.withdraw.pending') }}">
                    <div class="card prod-p-card background-pattern-white bg--white">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5">@lang('Pending Withdrawals')</h6>
                                    <h3 class="m-b-0 ">{{ __($general->cur_sym) }}{{ showAmount($pending) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-sm-6">
                <a href="{{ route('admin.withdraw.rejected') }}">
                    <div class="card prod-p-card background-pattern-white bg--primary">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">@lang('Rejected Withdrawals')</h6>
                                    <h3 class="m-b-0 text-white">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">

                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Created at')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Conversion')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $withdraw)
                            @php
                            $details = ($withdraw->withdraw_information != null) ?
                            json_encode($withdraw->withdraw_information) : null;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold"><a href="{{ appendQuery('method',$withdraw->method->id) }}">
                                            {{ __($withdraw->method->name) }}</a></span>
                                </td>
                                <td>
                                    {{ showDateTime($withdraw->created_at) }}
                                </td>

                                <td>
                                    <span class="fw-bold">{{ $withdraw->user->fullname }}</span>
                                </td>


                                <td>
                                    <strong title="@lang('Amount after charge')">
                                        {{ showAmount($withdraw->amount-$withdraw->charge) }} {{ __($general->cur_text)
                                        }}
                                    </strong>

                                </td>

                                <td>
                                    <strong>{{ showAmount($withdraw->final_amount) }} {{ __($withdraw->currency)
                                        }}</strong>
                                </td>

                                <td>
                                    @php echo $withdraw->statusBadge @endphp
                                </td>
                                <td>
                                    <a title="@lang('Details')"
                                        href="{{ route('admin.withdraw.details', $withdraw->id) }}"
                                        class="btn btn-sm btn--primary ms-1">
                                        <i class="la la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($withdrawals->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($withdrawals) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

@endsection




@push('breadcrumb-plugins')
<form action="" method="GET">
    <div class="form-inline float-sm-end ms-0 ms-xl-2 ms-lg-0">
        <div class="input-group">
            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Trx number/Username')"
                value="{{ request()->search }}">
            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endpush
@push('style')
<style>
    .nav-tabs {
        border: 0;
    }

    .nav-tabs li a {
        border-radius: 4px !important;
    }
</style>
@endpush
