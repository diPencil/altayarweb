@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row mb-4 justify-content-center">
    <div class="col-lg-6">
        <div class="base--card">
            <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                @csrf
                <input type="hidden" value="{{$data->track}}" name="track">
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <label class="form--label">@lang('Name on Card')</label>
                        <div class="input-group">
                            <input type="text" class="form-control form--control" name="name"
                                value="{{ old('name') }}" required autocomplete="off" autofocus />
                            <span class="input-group-text"><i class="fa fa-font"></i></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label">@lang('Card Number')</label>
                        <div class="input-group">
                            <input type="tel" class="form-control form--control" name="cardNumber"
                                autocomplete="off" value="{{ old('cardNumber') }}" required autofocus />
                            <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 gy-3">
                    <div class="col-lg-6">
                        <label class="form--label">@lang('Expiration Date')</label>
                        <input type="tel" class="form-control form--control" name="cardExpiry"
                            value="{{ old('cardExpiry') }}" autocomplete="off" required />
                    </div>
                    <div class="col-lg-6 ">
                        <label class="form--label">@lang('CVC Code')</label>
                        <input type="tel" class="form-control form--control" name="cardCVC"
                            value="{{ old('cardCVC') }}" autocomplete="off" required />
                    </div>
                </div>
                <br>
                <div class="row mt-2">
                    <div class="col-lg-12">
                        <button class="btn btn--base btn--lg" type="submit"> @lang('Submit')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/common/js/card.js') }}"></script>
<script>
    (function ($) {
        "use strict";
        var card = new Card({
            form: '#payment-form',
            container: '.card-wrapper',
            formSelectors: {
                numberInput: 'input[name="cardNumber"]',
                expiryInput: 'input[name="cardExpiry"]',
                cvcInput: 'input[name="cardCVC"]',
                nameInput: 'input[name="name"]'
            }
        });
    })(jQuery);
</script>
@endpush
