@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30 justify-content-center">
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Payment Via') {{ __($deposit->gateway->name) }}</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Date')
                        <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Transaction Number')
                        <span class="fw-bold">{{ $deposit->trx }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @if($deposit->user)
                            @lang('Username')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ $deposit->user->username }}</a>
                            </span>
                        @else
                            @lang('Guest Payer')
                            <span class="fw-bold">
                                {{ $deposit->guest_name }}
                            </span>
                        @endif
                    </li>
                    @if(!$deposit->user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Guest Email')
                        <span class="fw-bold">{{ $deposit->guest_email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Guest Phone')
                        <span class="fw-bold">{{ $deposit->guest_phone }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment Purpose')
                        <span class="fw-bold">{{ $deposit->payment_purpose }}</span>
                    </li>
                    @if($deposit->booking_reference)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Booking Reference')
                        <span class="fw-bold">{{ $deposit->booking_reference }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Method')
                        <span class="fw-bold">{{ __($deposit->gateway->name) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Amount')
                        <span class="fw-bold">{{ showAmount($deposit->amount ) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Charge')
                        <span class="fw-bold">{{ showAmount($deposit->charge ) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('After Charge')
                        <span class="fw-bold">{{ showAmount($deposit->amount+$deposit->charge ) }} {{
                            __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Rate')
                        <span class="fw-bold">1 {{__($general->cur_text)}}
                            = {{ showAmount($deposit->rate) }} {{__($deposit->baseCurrency())}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payable')
                        <span class="fw-bold">{{ showAmount($deposit->final_amo ) }}
                            {{__($deposit->method_currency)}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status')
                        @php echo $deposit->statusBadge @endphp
                    </li>
                    @if($deposit->admin_feedback)
                    <li class="list-group-item">
                        <strong>@lang('Admin Response')</strong>
                        <br>
                        <p>{{__($deposit->admin_feedback)}}</p>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @if($details || $deposit->status == 2)
    <div class="col-xl-8 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('Payment Info')</h5>
                @if($details != null)
                @foreach(json_decode($details) as $val)
                @if($deposit->method_code >= 1000)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>{{__($val->name)}}</h6>
                        @if($val->type == 'checkbox')
                        {{ implode(',',$val->value) }}
                        @elseif($val->type == 'file')
                        @if($val->value)
                        <a href="{{ route('admin.download.attachment',encrypt(getFilePath('verify').'/'.$val->value)) }}"
                            class="me-3"><i class="fa fa-file"></i> @lang('Attachment') </a>
                        @else
                        @lang('No File')
                        @endif
                        @else
                        <p>{{__($val->value)}}</p>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
                @if($deposit->method_code < 1000) @include('admin.deposit.gateway_data',['details'=>
                    json_decode($details)])
                    @endif
                    @endif
                            @if($deposit->status == 2 || $deposit->status == 0)
                            <button class="btn btn--info ms-1 paymentLinkBtn"
                                data-id="{{ $deposit->id }}"
                                data-trx="{{ $deposit->trx }}"
                                data-amount="{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}"
                                data-name="{{ $deposit->user ? $deposit->user->fullname : $deposit->guest_name }}"
                                data-link="{{ route('payment.pay', $deposit->trx) }}">
                                <i class="la la-link"></i> @lang('Payment Link')
                            </button>
                            @endif

                            @if($deposit->status == 2)
                            <button class="btn btn--success ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                data-question="@lang('Are you sure to approve this transaction?')"><i
                                    class="fas fa-check"></i>
                                @lang('Approve')
                            </button>

                            <button class="btn btn--danger ms-1 rejectBtn" data-id="{{ $deposit->id }}"
                                data-info="{{$details}}"
                                data-amount="{{ showAmount($deposit->amount)}} {{ __($general->cur_text) }}"
                                data-username="{{ $deposit->user ? $deposit->user->username : $deposit->guest_name }}"><i class="fas fa-ban"></i>
                                @lang('Reject')
                            </button>
                            @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Reject Payment Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.deposit.reject')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span
                            class="fw-bold withdraw-amount text-success"></span> @lang('payment of') <span
                            class="fw-bold withdraw-user"></span>?</p>

                    <div class="form-group">
                        <label class="fw-bold mt-2">@lang('Reason for Rejection')</label>
                        <textarea name="message" maxlength="255" class="form-control" rows="5"
                            required>{{ old('message') }}</textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
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

<x-confirmation-modal></x-confirmation-modal>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";

        $('.rejectBtn').on('click', function () {
            var modal = $('#rejectModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('.withdraw-amount').text($(this).data('amount'));
            modal.find('.withdraw-user').text($(this).data('username'));
            modal.modal('show');
        });

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
