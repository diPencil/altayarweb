@extends($activeTemplate . 'layouts.auth')
@section('content')
    <section class="login-section position-relative ">

        <div class="bg--thumb-one position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-10.png') }}" alt="image">
        </div>

        <div class="bg--thumb-two position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image">
        </div>

        <div class="row mx-0 justify-content-center align-items-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="base--card">
                    <div class="verification-area">
                        <h5 class="pb-3 text-center border-bottom">@lang('2FA Verification')</h5>
                        <form action="{{route('user.go2fa.verify')}}" method="POST" class="submit-form">
                            @csrf

                            @include($activeTemplate . 'components.verification_code')

                            <div class="form--group">
                                <button type="submit" class="btn btn--base btn--lg w-100 pills">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .base--card {
            padding: 40px;
        }
    </style>
@endpush


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('#code').on('input change', function () {
                var xx = document.getElementById('code').value;

                $(this).val(function (index, value) {
                    value = value.substr(0, 7);
                    return value.replace(/\W/gi, '').replace(/(.{3})/g, '$1 ');
                });

            });
        })(jQuery)
    </script>
@endpush


