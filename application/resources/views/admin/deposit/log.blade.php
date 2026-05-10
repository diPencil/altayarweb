@extends('admin.layouts.app')

@section('panel')
@include('admin.components.tabs.deposit')
<div class="row justify-content-center gy-4">
    @if(request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method') ||
    request()->routeIs('admin.users.deposits') || request()->routeIs('admin.users.deposits.method'))
    <div class="col-lg-12 mt-5">
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6">
                <a href="{{ route('admin.deposit.successful') }}">
                    <div class="card prod-p-card background-pattern-white bg--primary">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">@lang('Successful Tour Payments')</h6>
                                    <h3 class="m-b-0 text-white">{{ __($general->cur_sym) }}{{ showAmount($successful)
                                        }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <a href="{{ route('admin.deposit.pending') }}">
                    <div class="card prod-p-card background-pattern-white bg--white">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5">@lang('Pending Tour Payments')</h6>
                                    <h3 class="m-b-0">{{ __($general->cur_sym) }}{{ showAmount($pending) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <a href="{{ route('admin.deposit.rejected') }}">
                    <div class="card prod-p-card background-pattern-white bg--primary">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5 text-white">@lang('Rejected Tour Payments')</h6>
                                    <h3 class="m-b-0 text-white">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <a href="{{ route('admin.deposit.initiated') }}">
                    <div class="card prod-p-card background-pattern-white">
                        <div class="card-body">
                            <div class="row align-items-center m-b-0">
                                <div class="col">
                                    <h6 class="m-b-5">@lang('Initiated Tour Payments')</h6>
                                    <h3 class="m-b-0">{{ __($general->cur_sym) }}{{ showAmount($initiated) }}
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

    <div class="col-md-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Transaction')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Conversion')</th>
                                <th>@lang('Created at')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                            @php
                            $details = $deposit->detail ? json_encode($deposit->detail) : null;
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold"> <a
                                            href="{{ appendQuery('method',$deposit->gateway->alias) }}">{{
                                            __($deposit->gateway->name) }}</a> </span>
                                </td>

                                <td>
                                    {{ $deposit->trx }}
                                </td>
                                <td>
                                    @if($deposit->user)
                                        <a class="text-muted"
                                            href="{{ appendQuery('search',$deposit->user->username) }}">{{
                                            $deposit->user->fullname }}</a>
                                    @else
                                        <span class="text--small badge badge--info">@lang('Guest')</span>
                                        <br>
                                        {{ $deposit->guest_name }}
                                    @endif
                                </td>
                                <td>
                                    <strong title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->amount+$deposit->charge) }} {{ __($general->cur_text) }}
                                    </strong>
                                </td>
                                <td>
                                    <strong>{{ showAmount($deposit->final_amo) }}
                                        {{__($deposit->method_currency)}}</strong>
                                </td>
                                <td>
                                    {{ showDateTime($deposit->created_at) }}
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center justify-content-center">
                                        @php echo $deposit->statusBadge @endphp
                                    </span>
                                </td>
                                <td>
                                    <a title="@lang('Details')"
                                        href="{{ route('admin.deposit.details', $deposit->id) }}"
                                        class="btn btn-sm btn--primary ms-1">
                                        <i class="la la-eye"></i>
                                    </a>

                                    @if($deposit->status == 2 || $deposit->status == 0)
                                    <button title="@lang('Payment Link')"
                                        class="btn btn-sm btn--info ms-1 paymentLinkBtn"
                                        data-id="{{ $deposit->id }}"
                                        data-trx="{{ $deposit->trx }}"
                                        data-amount="{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}"
                                        data-name="{{ $deposit->user ? $deposit->user->fullname : $deposit->guest_name }}"
                                        data-link="{{ route('payment.pay', $deposit->trx) }}">
                                        <i class="la la-link"></i>
                                    </button>
                                    @endif
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
            @if ($deposits->hasPages())
            <div class="card-footer py-4">
                @php echo paginateLinks($deposits) @endphp
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

<div id="paymentLinkModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Payment Link')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        @lang('Customer')
                        <span class="customer-name fw-bold"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        @lang('Amount')
                        <span class="payment-amount fw-bold"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        @lang('Transaction')
                        <span class="payment-trx fw-bold"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="fw-bold">@lang('Shareable Link')</label>
                    <div class="input-group">
                        <input type="text" id="paymentLinkInput" class="form-control" readonly>
                        <button type="button" class="btn btn--primary copyLinkBtn">@lang('Copy')</button>
                    </div>
                    <p class="text--small text-muted mt-2">
                        <i class="la la-info-circle"></i> @lang('This internal link will automatically refresh the gateway session if it expires.')
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--warning refreshLinkBtn" data-id="">@lang('Refresh Link')</button>
                <a href="" target="_blank" class="btn btn--info openLinkBtn">@lang('Open Link')</a>
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
    (function($){
        "use strict";
        $('.paymentLinkBtn').on('click', function() {
            var modal = $('#paymentLinkModal');
            var data = $(this).data();

            modal.find('.customer-name').text(data.name);
            modal.find('.payment-amount').text(data.amount);
            modal.find('.payment-trx').text(data.trx);
            modal.find('#paymentLinkInput').val(data.link);
            modal.find('.openLinkBtn').attr('href', data.link);
            modal.find('.refreshLinkBtn').data('id', data.id);
            
            modal.modal('show');
        });

        $('.refreshLinkBtn').on('click', function() {
            var btn = $(this);
            var id = btn.data('id');
            var url = "{{ route('admin.deposit.refresh.link', ':id') }}".replace(':id', id);
            
            btn.prop('disabled', true).html('<i class="la la-spinner la-spin"></i> @lang("Refreshing...")');
            
            $.post(url, { _token: "{{ csrf_token() }}" }, function(response) {
                if(response.success) {
                    $('#paymentLinkInput').val(response.internal_link);
                    $('.openLinkBtn').attr('href', response.internal_link);
                    notify('success', response.message);
                } else {
                    notify('error', response.message);
                }
            }).fail(function() {
                notify('error', "@lang('Refresh failed. Please try again.')");
            }).always(function() {
                btn.prop('disabled', false).text("@lang('Refresh Link')");
            });
        });

        $('.copyLinkBtn').on('click', function() {
            var copyText = document.getElementById("paymentLinkInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            
            $(this).text("@lang('Copied!')").removeClass('btn--primary').addClass('btn--success');
            
            setTimeout(() => {
                $(this).text("@lang('Copy')").removeClass('btn--success').addClass('btn--primary');
            }, 2000);

            notify('success', 'Copied to clipboard');
        });
    })(jQuery);
</script>
@endpush


@push('breadcrumb-plugins')
@if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
<form action="" method="GET">
    <div class="form-inline float-sm-end ms-0 ms-xl-2 ms-lg-0">
        <div class="input-group">
            <input type="text" name="search" class="form-control bg--white"
                placeholder="@lang('Search by Username or Trx')" value="{{ request()->search ?? '' }}">
            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endif
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
