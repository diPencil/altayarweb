@extends($activeTemplate . (auth('employee')->check() ? 'layouts.employee.master' : 'layouts.frontend'))
@section('content')
    @php
        $isRtl = is_rtl();
        $isEmployee = auth('employee')->check();
    @endphp

    @push('style')
        <style>
            .epayment-hero {
                background: linear-gradient(145deg, #0f172a 0%, #15213f 52%, #17324f 100%);
                color: #fff;
                border-radius: 28px;
                overflow: hidden;
                min-height: 100%;
                position: relative;
            }

            .epayment-sticky-column {
                position: sticky;
                top: 110px;
                align-self: flex-start;
            }

            @media (max-width: 991.98px) {
                .epayment-sticky-column {
                    position: relative;
                    top: auto;
                    align-self: auto;
                }
            }

            .epayment-hero::after {
                content: '';
                position: absolute;
                inset: auto -90px -120px auto;
                width: 260px;
                height: 260px;
                border-radius: 50%;
                background: rgba(91, 156, 249, 0.18);
                filter: blur(2px);
            }

            .epayment-hero__inner {
                position: relative;
                z-index: 1;
            }

            .epayment-badge {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                padding: .5rem .9rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, .08);
                color: rgba(255, 255, 255, .95);
                border: 1px solid rgba(255, 255, 255, .12);
                font-size: .85rem;
            }

            .epayment-step {
                background: rgba(255, 255, 255, .07);
                border: 1px solid rgba(255, 255, 255, .10);
                border-radius: 20px;
                padding: 1rem 1.1rem;
                display: flex;
                align-items: flex-start;
                gap: .9rem;
            }

            .epayment-step__number {
                width: 40px;
                height: 40px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                background: rgba(255, 255, 255, .12);
                font-weight: 700;
            }

            .epayment-form {
                border-radius: 28px;
                box-shadow: 0 22px 60px rgba(15, 23, 42, .10);
            }

            .epayment-summary {
                border-radius: 22px;
                background: linear-gradient(180deg, rgba(17, 24, 39, .03), rgba(17, 24, 39, .02));
            }
        </style>
    @endpush

    <section class="py-5">
        <div class="container py-2 py-lg-4">
            <div class="row g-4 align-items-stretch {{ $isRtl ? 'flex-lg-row-reverse' : '' }}">
                @if(!$isEmployee)
                    <div class="col-lg-5 epayment-sticky-column">
                        <div class="epayment-hero h-100">
                            <div class="epayment-hero__inner p-4 p-xl-5 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="epayment-badge mb-3">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        @lang('Secure checkout')
                                    </span>
                                    <h1 class="mb-3 fw-bold text-white text-start">@lang('E-Payment')</h1>
                                    <p class="mb-4 fs-5 lh-lg text-start" style="color: rgba(255,255,255,.88);">
                                        @lang('This is our secure payment gateway. You can complete your payment safely.')
                                    </p>
                                </div>

                                <div class="d-grid gap-3">
                                    <div class="epayment-step">
                                        <span class="epayment-step__number">1</span>
                                        <div>
                                            <h6 class="mb-1" style="color:#ffffff;">@lang('Review')</h6>
                                            <p class="mb-0" style="color: rgba(255,255,255,.78);">@lang('Your amount is validated server-side before the payment is created.')</p>
                                        </div>
                                    </div>
                                    <div class="epayment-step">
                                        <span class="epayment-step__number">2</span>
                                        <div>
                                            <h6 class="mb-1" style="color:#ffffff;">@lang('Proceed to Payment')</h6>
                                            <p class="mb-0" style="color: rgba(255,255,255,.78);">@lang('We will redirect you to the gateway after creating your payment record.')</p>
                                        </div>
                                    </div>
                                    <div class="epayment-step">
                                        <span class="epayment-step__number">3</span>
                                        <div>
                                            <h6 class="mb-1" style="color:#ffffff;">@lang('Payment status')</h6>
                                            <p class="mb-0" style="color: rgba(255,255,255,.78);">@lang('Payment completed successfully')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="{{ $isEmployee ? 'col-lg-12' : 'col-lg-7' }}">
                    <div class="epayment-form base--card h-100">
                        <div class="p-4 p-xl-5">
                            @guest
                                <div class="alert alert-info mb-4">
                                    @lang('Please sign in to continue with a payment.')
                                </div>
                                <a href="{{ route(auth('employee')->check() ? 'employee.login' : 'user.login') }}" class="btn btn--base btn--lg w-100 pills">
                                    @lang('Sign in')
                                </a>
                            @endguest

                            @auth
                                @php
                                    $gateway = $gatewayCurrency->first();
                                @endphp
                                @if(!$gateway)
                                    <div class="alert alert-danger mb-4">
                                        {{ __('The payment gateway is currently unavailable.') }}
                                    </div>
                                @else
                                    <div class="mb-4 text-start">
                                        <h3 class="mb-2">@lang('Start a secure payment')</h3>
                                        <p class="text-muted mb-0">@lang('This is our secure payment gateway. You can complete your payment safely.')</p>
                                    </div>

                                    <div class="epayment-summary p-3 p-lg-4 mb-4">
                                        <div class="row g-3">
                                            <div class="col-md-6 text-md-start">
                                                <div class="text-muted small mb-1">@lang('Payment Method')</div>
                                                <div class="fw-semibold">
                                                    @if(strcasecmp($gateway->name, 'Fawaterk') === 0)
                                                        @lang('Pay Online')
                                                    @else
                                                        {{ __($gateway->name) }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <div class="text-muted small mb-1">@lang('Payment limits')</div>
                                                <div class="fw-semibold">{{ showAmount($gateway->min_amount) }} - {{ showAmount($gateway->max_amount) }} {{ $gateway->currency }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route(auth('employee')->check() ? 'employee.e.payment.store' : 'e.payment.store') }}" method="POST" class="row g-4 e-payment-form">
                                        @csrf
                                        <input type="hidden" name="method_code">
                                        <input type="hidden" name="currency">

                                        @if($isEmployee)
                                            <div class="col-12">
                                                <label class="form--label">@lang('Select User')</label>
                                                <select class="form-control form--control form--select form-select select2-basic" name="user_id" required>
                                                    <option value="">@lang('Select User')</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                                            {{ $user->username }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">@lang('The payment will be attributed to this user.')</small>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <label class="form--label">@lang('Select Payment Method')</label>
                                            <select class="form-control form--control form--select form-select" name="gateway" required>
                                                <option value="">@lang('Select One')</option>
                                                @foreach($gatewayCurrency as $data)
                                                    @php
                                                        $gatewayLabel = strcasecmp($data->name, 'Fawaterk') === 0 ? __('Pay Online') : $data->name;
                                                        $gatewayLabel = $gatewayLabel . ' - ' . strtoupper($data->currency);
                                                    @endphp
                                                    <option value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code)
                                                        data-gateway="{{ $data }}">{{ $gatewayLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form--label">@lang('Payment Amount')</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="{{ $gateway->min_amount }}" max="{{ $gateway->max_amount }}" name="amount" value="{{ old('amount') }}" class="form-control form--control" placeholder="0.00" required>
                                                <span class="input-group-text bg--base text-white method_currency">{{ $gateway->currency }}</span>
                                            </div>
                                            @error('amount')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form--label">@lang('Optional Note')</label>
                                            <textarea name="note" rows="4" class="form-control form--control" placeholder="{{ __('Add a note for your transaction') }}">{{ old('note') }}</textarea>
                                            @error('note')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn--base btn--lg pills w-100 ePaymentSubmitBtn">
                                                @lang('Proceed to Payment')
                                            </button>
                                        </div>
                                    </form>

                                    @if($latestPayments->isNotEmpty())
                                        <div class="mt-5">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h5 class="mb-0">@lang('Payment history')</h5>
                                                <a href="{{ route(auth('employee')->check() ? 'employee.deposit.history' : 'user.deposit.history') }}" class="text-decoration-none">@lang('Go to payment history')</a>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('Payment reference')</th>
                                                            <th>@lang('Amount')</th>
                                                            <th>@lang('Status')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($latestPayments as $payment)
                                                            <tr>
                                                                <td>{{ $payment->trx }}</td>
                                                                <td>{{ showAmount($payment->amount) }} {{ $general->cur_text }}</td>
                                                                <td>@php echo $payment->statusBadge @endphp</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="epaymentCheckoutModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Complete Payment')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body p-0" style="min-height: 80vh;">
                    <iframe id="epaymentCheckoutFrame" src="about:blank" title="@lang('Payment Checkout')" style="border:0; width:100%; min-height:80vh;"></iframe>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            (function ($) {
                "use strict";

                function syncGatewaySelection() {
                    var selected = $('select[name=gateway] option:selected');
                    var resource = selected.data('gateway');
                    if (!resource) {
                        return;
                    }

                    $('input[name=method_code]').val(resource.method_code);
                    $('input[name=currency]').val(resource.currency);
                    $('.method_currency').text(resource.currency);
                    $('input[name=amount]').attr('min', resource.min_amount);
                    $('input[name=amount]').attr('max', resource.max_amount);
                }

                $('select[name=gateway]').on('change', syncGatewaySelection);
                $('input[name=amount]').on('input', syncGatewaySelection);

                $('form.e-payment-form').on('submit', function (e) {
                    e.preventDefault();

                    var form = $(this);
                    var button = form.find('.ePaymentSubmitBtn');
                    var checkoutModal = $('#epaymentCheckoutModal');

                    button.prop('disabled', true).text('@lang('Please wait')');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        dataType: 'json',
                        success: function (response) {
                            if (response && response.redirect_url) {
                                $('#epaymentCheckoutFrame').attr('src', response.redirect_url);
                                checkoutModal.modal('show');
                                return;
                            }

                            notify('error', response.message || '@lang('Unable to create payment link.')');
                        },
                        error: function (xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '@lang('Unable to create payment link.')';
                            notify('error', message);
                        },
                        complete: function () {
                            button.prop('disabled', false).text('@lang('Proceed to Payment')');
                        }
                    });
                });

                $('#epaymentCheckoutModal').on('hidden.bs.modal', function () {
                    $(this).find('#epaymentCheckoutFrame').attr('src', 'about:blank');
                });

                if ($('select[name=gateway]').val()) {
                    syncGatewaySelection();
                }

                if($('.select2-basic').length > 0) {
                    $('.select2-basic').select2({
                        dropdownParent: $('.e-payment-form')
                    });
                }
            })(jQuery);
        </script>
    @endpush
@endsection
