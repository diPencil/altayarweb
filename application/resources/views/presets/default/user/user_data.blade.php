@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="contact-section section--bg py-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-6 col-lg-12 mb-5 mb-xl-0">
                    <div class="base--card radius--20">
                        <form method="POST" action="{{ route('user.data.submit') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('First Name')</label>
                                    <input type="text" class="form-control form--control pills" name="firstname"
                                        value="{{ old('firstname') }}" required>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Last Name')</label>
                                    <input type="text" class="form-control form--control pills" name="lastname"
                                        value="{{ old('lastname') }}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Address')</label>
                                    <input type="text" class="form-control form--control pills" name="address"
                                        value="{{ old('address') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('State')</label>
                                    <input type="text" class="form-control form--control pills" name="state"
                                        value="{{ old('state') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Zip Code')</label>
                                    <input type="text" class="form-control form--control pills" name="zip"
                                        value="{{ old('zip') }}">
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text" class="form-control form--control pills" name="city"
                                        value="{{ old('city') }}">
                                </div>
                                <div class="col-lg-12 text-end mt-4">
                                    <button class="btn btn--base btn--lg pills w-100">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
