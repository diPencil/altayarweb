@extends('admin.layouts.app')
@section('panel')
@include('admin.components.tabs.withdrawal')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th>@lang('Method')</th>
                                <th>@lang('Currency')</th>
                                <th>@lang('Charge')</th>
                                <th>@lang('Withdraw Limit')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($methods as $method)
                            <tr>
                                <td>{{__($method->name)}}</td>

                                <td class="fw-bold">{{ __($method->currency) }}</td>
                                <td class="fw-bold">{{ showAmount($method->fixed_charge)}} {{__($general->cur_text) }}
                                    {{ (0 < $method->percent_charge) ? ' + '. showAmount($method->percent_charge) .' %'
                                        : '' }} </td>
                                <td class="fw-bold">{{ $method->min_limit + 0 }}
                                    - {{ $method->max_limit + 0 }} {{__($general->cur_text) }}</td>
                                <td>
                                    @if($method->status == 1)
                                    <span
                                        class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                    @else
                                    <span
                                        class="text--small badge font-weight-normal badge--warning">@lang('Disabled')</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="button--group">
                                        <a title="@lang('Edit')"
                                            href="{{ route('admin.withdraw.method.edit', $method->id)}}"
                                            class="btn btn-sm btn--primary ms-1"><i class="las la-pen"></i>
                                        </a>
                                        @if($method->status == 1)
                                        <button title="@lang('Disable')"
                                            class="btn btn-sm btn--danger ms-1 confirmationBtn"
                                            data-question="@lang('Are you sure to disable this method?')"
                                            data-action="{{ route('admin.withdraw.method.deactivate',$method->id) }}">
                                            <i class="la la-eye-slash"></i>
                                        </button>
                                        @else
                                        <button title="@lang('Enable')"
                                            class="btn btn-sm btn--success ms-1 confirmationBtn"
                                            data-question="@lang('Are you sure to enable this method?')"
                                            data-action="{{ route('admin.withdraw.method.activate',$method->id) }}">
                                            <i class="la la-check-circle"></i>
                                        </button>
                                        @endif
                                    </div>
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
        </div><!-- card end -->
    </div>
</div>

@endsection



@push('breadcrumb-plugins')

<div class="d-flex flex-wrap justify-content-end">
    <a class="btn btn--primary h-40 me-2 d-flex align-items-center"
        href="{{ route('admin.withdraw.method.create') }}"><i class="las la-plus"></i>@lang('Add New')</a>
    <div class="d-inline">
        <div class="input-group justify-content-end">
            <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
            <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
        </div>
    </div>
</div>
@endpush
