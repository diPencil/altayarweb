@extends($activeTemplate.'layouts.employee.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <div class="base--card radius--20 filter--wrap mb-4 px-3">
            <form action="">
                <div class="d-flex flex-wrap gap-4">
                    <div class="flex-grow-1">
                        <label>@lang('Transaction Number')</label>
                        <input type="text" name="search" value="{{ request()->search }}" class="form--control">
                    </div>
                    <div class="flex-grow-1">
                        <label>@lang('Type')</label>
                        <select name="type" class="form--control form--select form-select">
                            <option value="">@lang('All')</option>
                            <option value="+" @selected(request()->type == '+')>@lang('Plus')</option>
                            <option value="-" @selected(request()->type == '-')>@lang('Minus')</option>
                        </select>
                    </div>
                    <div class="flex-grow-1">
                        <label>@lang('Remark')</label>
                        <select class="form--control form--select form-select" name="remark">
                            <option value="">@lang('Any')</option>
                            @foreach($remarks as $remark)
                            <option value="{{ $remark->remark }}" @selected(request()->remark ==
                                $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-grow-1 align-self-end">
                        <button class="btn btn--base btn--lg w-100 pills"><i class="las la-filter"></i> @lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('Trx')</th>
                    <th>@lang('Transacted')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Post Balance')</th>
                    <th>@lang('Detail')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td data-label="@lang('Trx')" class="fw-500 td-bidi-ltr"><span class="d-inline-block">#{{ $trx->trx }}</span></td>
                    <td data-label="@lang('Transacted')">
                        <i class="fa-regular fa-clock"></i>
                        {{ showDateTime($trx->created_at) }}</td>
                    <td data-label="@lang('Amount')" class="budget td-bidi-ltr">
                        <span
                            class="fw-bold d-inline-block @if($trx->trx_type == '+')text-success @else text-danger @endif">
                            {{ $trx->trx_type }} {{showAmount($trx->amount)}} {{ __($general->cur_text) }}
                        </span>
                    </td>
                    <td data-label="@lang('Post Balance')" class="budget td-bidi-ltr">
                        <span class="d-inline-block">{{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}</span>
                    </td>
                    <td data-label="@lang('Detail')">{{ __($trx->details) }}</td>
                </tr>
                @empty
                <tr>
                    <td class="text-muted text-center" data-label="@lang('Transactions Table')" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>
@if($transactions->hasPages())
    <div class="row mx-xxl-5 mx-lg-0 my-4">
        <div class="col-lg-12 justify-content-end d-flex">
            {{ $transactions->links() }}
        </div>
    </div>
@endif
@endsection


