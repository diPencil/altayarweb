@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <form action="" method="GET">
                <div class="mb-3 dashboard-filter-search">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                            placeholder="@lang('Search by transactions')">
                        <button type="submit" class="input-group-text bg--base text-white border-0">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th class="text-center">@lang('Gateway')</th>
                            <th class="text-center">@lang('Initiated')</th>
                            <th class="text-center">@lang('Amount')</th>
                            <th class="text-center">@lang('Conversion')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td class="td-bidi-ltr"><span class="d-inline-block">{{ $deposit->trx }}</span></td>
                                <td>
                                    @if(strcasecmp($deposit->gateway?->name, 'Fawaterk') === 0)
                                        @lang('Pay Online')
                                    @else
                                        {{ __($deposit->gateway?->name) }}
                                    @endif
                                </td>

                                <td class="text-center"> {{ showDateTime($deposit->created_at) }} </td>
                                <td class="text-center td-bidi-ltr">
                                    <span class="d-inline-block">{{ showAmount($deposit->amount + $deposit->charge) }} {{ __($general->cur_text) }}</span>
                                </td>
                                <td class="text-center td-bidi-ltr">
                                    <span class="d-inline-block">{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</span>
                                </td>
                                <td class="text-center">
                                    @php echo $deposit->statusBadge @endphp
                                </td>
                                @php
                                    $details = $deposit->detail != null ? json_encode($deposit->detail) : null;
                                @endphp

                                <td>
                                    <div class="d-flex justify-content-start gap-1 flex-wrap">
                                        <a href="javascript:void(0)"
                                            class="btn btn--base btn-md action--btn @if ($deposit->method_code >= 100 || $deposit->method_code == 115) detailBtn @else disabled @endif"
                                            @if ($deposit->method_code >= 100 || $deposit->method_code == 115) data-info="{{ $details }}" @endif
                                            @if ($deposit->status == 3) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @if(($deposit->status == 0 || $deposit->status == 2) && $deposit->method_code == 115)
                                            @php
                                                $paymentUrl = data_get($deposit->detail, 'gateway_invoice_url');
                                            @endphp
                                            @if($paymentUrl)
                                                <button type="button" class="btn btn--success btn-md action--btn payNowBtn" data-payment-url="{{ $paymentUrl }}" title="@lang('Pay Now')">
                                                    <i class="fa fa-sync" style="color: #fff !important;"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">@lang('No payment history found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include($activeTemplate . 'components.pagination_controls', ['data' => $deposits])

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade deposit-detail-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="btn btn--danger btn--lg pills"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Complete Payment')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body p-0" style="min-height: 80vh;">
                    <iframe id="paymentFrame" src="about:blank" title="@lang('Payment Checkout')" style="border:0; width:100%; min-height:80vh;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    $depositDetailLabels = [
        'payment_flow' => __('Payment flow'),
        'note' => __('Note'),
        'gateway_invoice_id' => __('Gateway invoice id'),
        'gateway_invoice_key' => __('Gateway invoice key'),
        'gateway_invoice_url' => __('Gateway invoice url'),
        'gateway_status' => __('Gateway status'),
        'gateway_vendor_key' => __('Gateway vendor key'),
    ];
    $depositDetailValues = [
        'epayment' => __('E-Payment'),
        'booking' => __('Booking'),
        'membership' => __('Membership'),
        'pending' => __('Pending'),
        'successful' => __('Successful'),
        'succeed' => __('Succeed'),
        'rejected' => __('Rejected'),
        'initiated' => __('Initiated'),
        'manual' => __('Manual'),
    ];
@endphp

@push('script')
    <script>
        window.depositDetailLabels = @json($depositDetailLabels);
        window.depositDetailValues = @json($depositDetailValues);
    </script>
    <script>
        (function($) {
            "use strict";

            function depositDetailRowLabel(key) {
                var map = window.depositDetailLabels || {};
                return Object.prototype.hasOwnProperty.call(map, key) ? map[key] : String(key).replace(/_/g, ' ');
            }

            function depositDetailRowValue(val) {
                if (val === null || val === undefined) {
                    return val;
                }
                var s = String(val).trim();
                var lower = s.toLowerCase();
                var vmap = window.depositDetailValues || {};
                return Object.prototype.hasOwnProperty.call(vmap, lower) ? vmap[lower] : val;
            }

            $('.payNowBtn').on('click', function () {
                var paymentUrl = $(this).data('payment-url');
                var modal = $('#paymentModal');
                modal.find('#paymentFrame').attr('src', paymentUrl);
                modal.modal('show');
            });

            $('#paymentModal').on('hidden.bs.modal', function () {
                $(this).find('#paymentFrame').attr('src', 'about:blank');
            });

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    if (Array.isArray(userData)) {
                        userData.forEach(element => {
                            if (element.type != 'file') {
                                html += `
                                <li class="list-group-item deposit-detail-row d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <span class="deposit-detail-key text-muted">${element.name}</span>
                                    <span class="deposit-detail-val text-break">${depositDetailRowValue(element.value)}</span>
                                </li>`;
                            }
                        });
                    } else {
                        Object.entries(userData).forEach(([key, value]) => {
                            if (typeof value !== 'object') {
                                html += `
                                <li class="list-group-item deposit-detail-row d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <span class="deposit-detail-key text-muted">${depositDetailRowLabel(key)}</span>
                                    <span class="deposit-detail-val text-break">${depositDetailRowValue(value)}</span>
                                </li>`;
                            }
                        });
                    }
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
