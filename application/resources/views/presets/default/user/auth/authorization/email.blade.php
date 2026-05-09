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
                        <div class="logo--wrap mb-4 d-flex justify-content-center align-items-center">
                            <a href="{{ route('home') }}">
                                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                    alt="@lang('image')">
                            </a>
                        </div>

                        <h5 class="mb-3 text-center">@lang('Verify Email Address')</h5>
                        <form action="{{ route('user.verify.email') }}" method="POST" class="submit-form">
                            @csrf
                            <p class="verification-text mb-3">@lang('A 6 digit verification code sent to your email address'):
                                {{ showEmailAddress(auth()->user()->email) }}</p>

                            @include($activeTemplate . 'components.verification_code')

                            <div class="mb-3">
                                <button type="submit" class="btn btn--base btn--lg w-100 pills">@lang('Submit')</button>
                            </div>

                            <div class="mb-3">
                                <p>
                                    @lang('If you don\'t get any code'), <a href="{{ route('user.send.verify.code', 'email') }}"
                                        class="text--base-two"> @lang('Try again')</a>
                                </p>

                                @if ($errors->has('resend'))
                                    <small class="text-danger d-block">{{ $errors->first('resend') }}</small>
                                @endif
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
