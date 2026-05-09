@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $general = App\Models\GeneralSetting::first();
        $credentials = $general->socialite_credentials;
    @endphp

    <section class="login-section position-relative d-flex justify-content-center align-items-center">

        <div class="bg--thumb-one position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-10.png') }}" alt="image">
        </div>

        <div class="bg--thumb-two position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image">
        </div>


        <div class="row justify-content-center w--100">
            <div class="col-xxl-4 col-md-6">
                <div class="login-box w--100">

                    <div class="login-user--tab position-absolute d-flex justify-content-start  mb-4 w--100">
                        <div class="btn--wrap user {{ Route::is('user.login') ? 'active' : '' }} position-relative w-50">
                            <a href="{{ route('user.login') }}"
                                class="user btn btn--base btn--lg pills {{ Route::is('user.login') ? 'active' : '' }} w-100"><i class="fas fa-plane"></i> @lang('Traveller')</a>
                        </div>
                        <div class="btn--wrap {{ Route::is('employee.login') ? 'active' : '' }} position-relative w-50">
                            <a href="{{ route('employee.login') }}"
                                class="Employee btn btn--base btn--lg pills {{ Route::is('employee.login') ? 'active' : '' }} w-100"><i class="fas fa-map"></i> @lang('Employee')</a>
                        </div>
                    </div>


                    <div class="logo--wrap d-flex justify-content-center align-items-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="Logo" style="max-height: 40px;">
                        </a>
                    </div>
                    <div class="content mb-4 pb-3">
                        <h4 class="title mb-2">@lang('Employee Sign In')</h4>
                        <p class="text-center">@lang('Welcome back!')</p>
                    </div>



                    <div class="form--wrap">
                        <form method="POST" action="{{ route('employee.login') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="mb-4 form-group">
                                <label class="mb-2 form--label">@lang('Username or Email')</label>
                                <input class="form--control pills" name="username" id="username"
                                    value="{{ old('username') }}" placeholder="@lang('Username or Email')" required>
                            </div>
                            <div class="mb-4">
                                <label class="mb-2 form--label">@lang('Password')</label>
                                <div class="input--group position-relative">
                                    <input class="form--control pills" type="password" id="password" name="password"
                                        placeholder="@lang('Password')" required>
                                    <div class="password-show-hide fas fa-eye-slash toggle-password-change text--black"
                                        data-target="password">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <x-captcha></x-captcha>
                            </div>


                            <div class="login-meta d-flex flex-wrap justify-content-between align-items-center mb-4"
                                data-wow-delay="0.5s">
                                <div class="d-flex flex-nowrap align-items-center">
                                    <div class="form--check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                    </div>
                                    <div class="condition-text">
                                        <label class="ms-1" for="remember">@lang('Remember me')</label>
                                    </div>
                                </div>
                                <a href="{{ route('employee.password.request') }}"
                                    class="text--underline text--base">@lang('Forgot Password?')</a>
                            </div>

                            <button type="submit" class="btn btn--base btn--lg w-100 pills w-100 "
                                id="recaptcha">@lang('Login')</button>


                        </form>
                    </div>
                    <div class="text-center">
                        <p class="text-center">@lang('Don\'t have any account?')
                            <a href="{{ route('employee.register') }}"
                                class="text--underline text--base-two">@lang('Create Account')</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection


