

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
                    <div class="logo--wrap mb-4 d-flex justify-content-center align-items-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                alt="@lang('image')">
                        </a>
                    </div>

                    <h5 class="text-center">{{ __($pageTitle) }}</h5>
                    <div class="card-body">
                        <div class="mb-4">
                            <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                        </div>
                        <form method="POST" action="{{ route('user.password.update') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Password')</label>
                                <input type="password" class="form-control form--control pills" name="password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Confirm Password')</label>
                                <input type="password" class="form-control form--control pills" name="password_confirmation"
                                    required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn--base btn--lg pills w-100"> @lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
@endsection
