@extends($activeTemplate . 'layouts.auth')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
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
            <div class="col-xxl-5 col-xl-7 col-lg-6 col-md-8">
                <div class="login-box w--100">


                     <div class="login-user--tab position-absolute d-flex justify-content-start  mb-4 w--100">
                        <div class="btn--wrap user {{ Route::is('user.register') ? 'active' : '' }} position-relative w-50">
                            <a href="{{ route('user.register') }}"
                                class="user btn btn--base btn--lg pills {{ Route::is('user.register') ? 'active' : '' }} w-100"><i class="fas fa-plane"></i> @lang('Traveller')</a>
                        </div>

                        <div class="btn--wrap {{ Route::is('user.register') ? 'active' : '' }} position-relative w-50">
                            <a href="{{ route('employee.register') }}"
                                class="Employee btn btn--base btn--lg pills {{ Route::is('employee.register') ? 'active' : '' }} w-100"><i class="fas fa-map"></i> @lang('Employee')</a>
                        </div>
                    </div>



                    <div class="logo--wrap d-flex justify-content-center align-items-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="Logo" style="max-height: 40px;">
                        </a>
                    </div>

                    <div class="content mb-4 pb-3">
                        <h4 class="title mb-2">@lang('Create Your Account')</h4>
                        <p class="text-center">@lang('Welcome back! Select method to signup')</p>
                    </div>




                    <div class="form--wrap">
                        <form action="{{ route('employee.register') }}" method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('First Name')</label>
                                        <input class="form--control pills" name="firstname"
                                            value="{{ old('firstname') }}" placeholder="@lang('First Name')" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('Last Name')</label>
                                        <input class="form--control pills" name="lastname"
                                            value="{{ old('lastname') }}" placeholder="@lang('Last Name')" required>
                                    </div>
                                </div>
                                @if (session()->get('reference') != null)
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form--label">@lang('Reference by')</label>
                                            <input type="text" class="form--control" name="referBy" id="referenceBy"
                                                value="{{ session()->get('reference') }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('Username')</label>
                                        <input class="form--control pills checkUser" name="username" id="username"
                                            value="{{ old('username') }}" placeholder="@lang('Username')" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('E-Mail Address')</label>
                                        <input class="form--control pills checkUser" name="email" id="email"
                                            value="{{ old('email') }}" placeholder="@lang('Email')" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('Country')</label>
                                        <select name="country" class="form-select form--control form--select pills"
                                             required="" id="country">

                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}"
                                                    value="{{ $country->country }}" data-code="{{ $key }}" {{ $country->country == 'Saudi Arabia' ? 'selected' : '' }}>
                                                    {{ __($country->country) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label">@lang('Mobile')</label>
                                        <div class="input-group with--text mb-4">
                                            <span class="input-group-text bg--base text--white mobile-code">+966</span>
                                            <input type="hidden" name="mobile_code" value="966">
                                            <input type="hidden" name="country_code" value="SA">
                                            <input type="number" name="mobile"
                                                class="form-control form--control checkUser" value="{{ old('mobile') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label ">@lang('Password')</label>
                                        <div class="input--group position-relative">

                                            <input type="password" class="form--control pills" name="password"
                                                id="password" placeholder="@lang('Password')">
                                            <div class="password-show-hide fas fa-eye-slash toggle-password-change text-black"
                                                data-target="password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4 form-group">
                                        <label class="mb-2 form--label ">@lang('Confirm Password')</label>
                                        <div class="input--group position-relative">
                                            <input class="form--control pills" name="password_confirmation"
                                                id="confirm-password" placeholder="@lang('Confirm Password')" type="password">
                                            <div class="password-show-hide fas fa-eye-slash toggle-password-change text-black"
                                                data-target="confirm-password"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <x-captcha></x-captcha>
                                </div>
                            </div>
                            <div class="mb-4">

                                @if ($general->agree)
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="checkbox" id="agree" @checked(old('agree'))
                                                name="agree" required>
                                            <label for="agree">@lang('I agree with') @foreach ($policyPages as $policy)
                                                    <a href="{{ policy_detail_url($policy) }}"
                                                        class="text--base-two">
                                                        {{ __($policy->data_values->title) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn--base btn--lg w-100 pills"
                                id="recaptcha">@lang('Sign Up')</button>

                            <div class="mt-3 text-center">
                                <p>
                                    @lang('Already have an account?') <a href="{{ route('employee.login') }}"
                                        class="text--underline text--base-two">@lang('Sign In')</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>



    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--lg pills"
                        data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--lg  pills">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('style')
    <style>
        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush


