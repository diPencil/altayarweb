@extends($activeTemplate . 'layouts.user.master')
@section('content')
    @php
        $hasBookingContext = isset($tourPackage) && isset($seat);
        $hasFixedAmount = isset($fixedAmount);
        $defaultAmount = $hasFixedAmount 
            ? $fixedAmount 
            : ($hasBookingContext
                ? ($payableAmount ?? showTourPackageCalculateDiscount($tourPackage->price * $seat, $tourPackage->discount))
                : old('amount'));
        $currencySymbol = $hasBookingContext ? $tourPackage->displayCurrencySymbol() : $general->cur_sym;
    @endphp
    <div class="row mb-4 justify-content-center">
        <div class="col-lg-6">
            <div class="base--card">
                <form action="{{route('user.deposit.insert')}}" method="post">
                    @csrf
                    <input type="hidden" name="method_code">
                    <input type="hidden" name="currency">
                    <div class="row gy-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form--label">@lang('Select Payment Method')</label>
                                <select class="form-control form--control form--select form-select" name="gateway" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($gatewayCurrency as $data)
                                        @php
                                            $gatewayLabel = strcasecmp($data->name, 'Fawaterk') === 0 ? __('Pay Online') : $data->name;
                                            $gatewayLabel = $gatewayLabel . ' - ' . strtoupper($data->currency);
                                        @endphp
                                        <option value="{{$data->method_code}}" @selected(old('gateway') == $data->method_code)
                                            data-gateway="{{ $data }}">{{ $gatewayLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form--label">@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="amount" class="form-control form--control"
                                        value="{{ $defaultAmount }}" autocomplete="off" @if($hasBookingContext || $hasFixedAmount) readonly @endif required>
                                    <span class="input-group-text bg--base text-white">{{ $currencySymbol }}</span>
                                </div>
                                @if($hasBookingContext && !empty($cashbackUsed) && $cashbackUsed > 0)
                                    <small class="text-success d-block mt-2">@lang('Cashback applied:') {{ $currencySymbol }}{{ showAmount($cashbackUsed) }}</small>
                                @elseif($hasFixedAmount)
                                    <small class="text-muted d-block mt-2">@lang('Fixed amount for your selected plan.')</small>
                                @elseif(!$hasBookingContext)
                                    <small class="text-muted d-block mt-2">@lang('Enter the amount you want to pay, then select a gateway.')</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12 text-end">
                            <button type="submit" class="btn btn--base btn--lg pills">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('select[name=gateway]').on('change',function () {
                if (!$('select[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                var resource = $('select[name=gateway] option:selected').data('gateway');
                var fixed_charge = parseFloat(resource.fixed_charge);
                var percent_charge = parseFloat(resource.percent_charge);
                var rate = parseFloat(resource.rate)
                if (resource.method.crypto == 1) {
                    var toFixedDigit = 8;
                    $('.crypto_currency').removeClass('d-none');
                } else {
                    var toFixedDigit = 2;
                    $('.crypto_currency').addClass('d-none');
                }
                $('.min').text(parseFloat(resource.min_amount).toFixed(2));
                $('.max').text(parseFloat(resource.max_amount).toFixed(2));
                var amount = parseFloat($('input[name=amount]').val());
                if (!amount) {
                    amount = 0;
                }
                if (amount <= 0) {
                    $('.preview-details').addClass('d-none');
                    return false;
                }
                $('.preview-details').removeClass('d-none');
                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                $('.charge').text(charge);
                var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
                $('.payable').text(payable);
                var final_amo = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(toFixedDigit);
                $('.final_amo').text(final_amo);
                if (resource.currency != '{{ $general->cur_text }}') {
                    var rateElement = `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 {{__($general->cur_text)}} = <span class="rate">${rate}</span>  <span class="base-currency">${resource.currency}</span></span></span>`;
                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none');
                    $('.in-site-cur').removeClass('d-none');
                    $('.rate-element').addClass('d-flex');
                    $('.in-site-cur').addClass('d-flex');
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none');
                    $('.in-site-cur').addClass('d-none');
                    $('.rate-element').removeClass('d-flex');
                    $('.in-site-cur').removeClass('d-flex');
                }
                $('.base-currency').text(resource.currency);
                $('.method_currency').text(resource.currency);
                $('input[name=currency]').val(resource.currency);
                $('input[name=method_code]').val(resource.method_code);
                $('input[name=amount]').on('input');
            });
            $('input[name=amount]').on('input', function () {
                $('select[name=gateway]').change();
                $('.amount').text(parseFloat($(this).val()).toFixed(2));
            });
        })(jQuery);
    </script>
@endpush
