@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $isRtl = is_rtl();
        $paymentNote = data_get($deposit->detail, 'note');
        $gatewayReference = data_get($deposit->detail, 'gateway_invoice_id') ?: $deposit->btc_wallet;
        $paymentFlow = data_get($deposit->detail, 'payment_flow');

        if ($status === 1) {
            $panelClass = 'border-success';
            $badgeClass = 'bg-success';
            $icon = 'fa-circle-check';
            $headline = __('Payment completed successfully');
            $message = __('Your payment has been verified and marked as successful.');
        } elseif ($status === 3) {
            $panelClass = 'border-danger';
            $badgeClass = 'bg-danger';
            $icon = 'fa-circle-xmark';
            $headline = __('Payment failed');
            $message = __('The gateway reported a failed or canceled payment. You can try again safely.');
        } elseif ($status === 2) {
            $panelClass = 'border-warning';
            $badgeClass = 'bg-warning text-dark';
            $icon = 'fa-hourglass-half';
            $headline = __('Payment is pending confirmation');
            $message = __('Your payment has not been confirmed yet. If your card was charged, the status will update automatically once the gateway confirms it. If no amount was deducted, please try again or use another card.');
        } else {
            $panelClass = 'border-secondary';
            $badgeClass = 'bg-secondary';
            $icon = 'fa-receipt';
            $headline = __('Payment reference');
            $message = __('Your payment record has been created and will be updated once the gateway responds.');
        }
    @endphp

    <section class="py-5">
        <div class="container py-2 py-lg-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="base--card border {{ $panelClass }} shadow-sm">
                        <div class="card-body p-4 p-xl-5 {{ $isRtl ? 'text-end' : 'text-start' }}">
                            <div class="d-flex align-items-center gap-3 mb-4 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $badgeClass }} text-white" style="width: 56px; height: 56px; font-size: 1.35rem;">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </span>
                                <div>
                                    <span class="badge {{ $badgeClass }} rounded-pill mb-2">@lang('Payment status')</span>
                                    <h2 class="mb-0">{{ $headline }}</h2>
                                </div>
                            </div>

                            <p class="text-muted mb-4">{{ $message }}</p>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 h-100">
                                        <div class="text-muted small mb-1">@lang('Payment reference')</div>
                                        <div class="fw-semibold">{{ $deposit->trx }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 h-100">
                                        <div class="text-muted small mb-1">@lang('Amount')</div>
                                        <div class="fw-semibold">{{ showAmount($deposit->amount + $deposit->charge) }} {{ $general->cur_text }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 h-100">
                                        <div class="text-muted small mb-1">@lang('Gateway')</div>
                                        <div class="fw-semibold">{{ $deposit->gateway?->name ?? __('Payment') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-4 h-100">
                                        <div class="text-muted small mb-1">@lang('Gateway reference')</div>
                                        <div class="fw-semibold">{{ $gatewayReference ?: __('-') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 p-lg-4 rounded-4 bg-light mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="text-muted small mb-1">@lang('Payment status')</div>
                                        <div class="fw-semibold">@php echo $deposit->statusBadge @endphp</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted small mb-1">@lang('Payment purpose')</div>
                                        <div class="fw-semibold">{{ $deposit->payment_purpose ?: __('-') }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted small mb-1">@lang('Booking reference')</div>
                                        <div class="fw-semibold">{{ $deposit->booking_reference ?: __('-') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 {{ $isRtl ? 'justify-content-start' : 'justify-content-end' }}">
                                @auth
                                    <a href="{{ route(auth('employee')->check() ? 'employee.deposit.history' : 'user.deposit.history') }}" class="btn btn--base btn-lg pills">
                                        @lang('Go to payment history')
                                    </a>
                                @else
                                    <a href="{{ route('home') }}" class="btn btn--base btn-lg pills">
                                        @lang('Go to home')
                                    </a>
                                @endauth
                                
                                @if($status === 3)
                                    <a href="{{ route('e.payment') }}" class="btn btn-outline-secondary btn-lg pills">
                                        @lang('Try again')
                                    </a>
                                @elseif($status === 2 || $status === 0)
                                    @if($deposit->gateway?->alias === 'Fawaterk')
                                        <form action="{{ route(auth('employee')->check() ? 'employee.e.payment.check' : 'e.payment.check', $deposit->trx) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-lg pills">
                                                <i class="fa-solid fa-arrows-rotate me-1"></i> @lang('Check payment status')
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route(auth('employee')->check() ? 'employee.e.payment.result' : 'e.payment.result', $deposit->trx) }}" class="btn btn-outline-secondary btn-lg pills">
                                        @lang('Refresh status')
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
