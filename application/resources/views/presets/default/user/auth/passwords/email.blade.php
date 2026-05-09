@extends($activeTemplate . 'layouts.auth')
@section('content')


    <section class="login-section position-relative ">

        <div class="bg--thumb-one position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-10.png') }}" alt="image">
        </div>

        <div class="bg--thumb-two position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image">
        </div>

        <div class="row mx-0 justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="base--card custom--card">
                    <div class="logo--wrap mb-3 d-flex justify-content-center align-items-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                alt="@lang('image')">
                        </a>
                    </div>
                    <h5 class="text-center">{{ __($pageTitle) }}</h5>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="text-center">
                                @lang('To recover your account please provide your email or username to find your account.')
                            </p>
                        </div>
                        <form method="POST" action="{{ route('user.password.email') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">@lang('Email or Username')</label>
                                <input type="text" class="form-control form--control pills" name="value"
                                    value="{{ old('value') }}" required autofocus="off">
                            </div>

                            <div class="form-group text-end">
                                <button type="submit" class="btn btn--base btn--lg pills w-100 mt-3">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
