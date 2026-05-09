@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="show-filter mb-3 text-end">
            <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="fa fa-filter"></i> @lang('Filter')</button>
        </div>
        <div class="card b-radius--10 mb-3 ml-b-30 filter-column d-none">
            <div class="card-body p-0">
                <form action="" method="GET" class="filter-form">
                    <div class="row p-3">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="@lang('Username / Amount')" value="{{ $search ?? '' }}">
                                <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive admin-table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td data-label="@lang('User')">
                                    <span class="fw-bold">{{ $request->user->fullname }}</span>
                                    <br>
                                    <span class="small">
                                        <a href="{{ route('admin.users.detail', $request->user_id) }}"><span>@</span>{{ $request->user->username }}</a>
                                    </span>
                                </td>
                                <td data-label="@lang('Date')">
                                    {{ showDateTime($request->created_at) }}
                                </td>
                                <td data-label="@lang('Amount')">
                                    <span class="fw-bold">{{ gs()->cur_sym }}{{ showAmount($request->amount) }}</span>
                                </td>
                                <td data-label="@lang('Type')">
                                    @if($request->type == 'use')
                                        <span class="badge badge-capsule bg--primary">@lang('Usage/Allocation')</span>
                                    @else
                                        <span class="badge badge-capsule bg--info">@lang('Refund')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Status')">
                                    @if($request->status == 0)
                                        <span class="badge badge-capsule bg--warning">@lang('Pending')</span>
                                    @elseif($request->status == 1)
                                        <span class="badge badge-capsule bg--success">@lang('Approved')</span>
                                    @else
                                        <span class="badge badge-capsule bg--danger">@lang('Rejected')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.wallet.detail', $request->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                        <i class="la la-desktop"></i> @lang('Details')
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) ?? __('No wallet requests found') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($requests->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($requests) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <form action="" method="GET" class="form-inline float-sm-end bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Username / Amount')" value="{{ $search ?? '' }}">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
@endpush

@push('script')
<script>
    (function($){
        "use strict";
        $('.showFilterBtn').on('click',function(){
            $('.filter-column').toggleClass('d-none');
        });
    })(jQuery);
</script>
@endpush
