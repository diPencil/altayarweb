@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contact = getContent('contact_us.content', true);
        $dv = $contact?->data_values ?? (object) [];
        $contactPhone = trim((string) ($dv->contact_number ?? '')) ?: trim((string) config('site_contact.phone'));
        $contactEmail = trim((string) ($dv->email_address ?? '')) ?: trim((string) config('site_contact.email'));
        $contactAddress = trim((string) ($dv->contact_details ?? '')) ?: trim((string) config('site_contact.address'));
        $latitude = trim((string) ($dv->latitude ?? '')) ?: trim((string) config('site_contact.latitude'));
        $longitude = trim((string) ($dv->longitude ?? '')) ?: trim((string) config('site_contact.longitude'));
        $mapOk = $latitude !== '' && $longitude !== '' && is_numeric($latitude) && is_numeric($longitude);
    @endphp

    <section class="contact-section section--bg py-100">
        <div class="container">
            <div class="row gy-4 align-items-stretch justify-content-center mb-4">
                <div class="col-xxl-3 col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div
                        class="contact--box bg--white radius--20 d-flex flex-column justify-content-start align-items-stretch position-relative h-100 w-100">
                        <div class="icon-wrap d-flex justify-content-center align-items-center position-absolute">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div class="content-wrap d-flex flex-column justify-content-start align-items-start w--100">
                            <p class="text-start fw--400 fs--14 mb-2">@lang('Phone Number')</p>
                            @if ($contactPhone)
                                <a class="contact-ltr-value fs--18 text--black7 fw--600 d-block"
                                    dir="ltr"
                                    href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}">{{ $contactPhone }}</a>
                            @else
                                <span class="text-start fs--18 text--black7 fw--600 d-block">&mdash;</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div
                        class="contact--box bg--white radius--20 d-flex flex-column justify-content-start align-items-stretch position-relative h-100 w-100">
                        <div class="icon-wrap d-flex justify-content-center align-items-center position-absolute">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="content-wrap d-flex flex-column justify-content-start align-items-start w--100">
                            <p class="text-start fw--400 fs--14 mb-2">@lang('Email Address')</p>
                            @if ($contactEmail)
                                <a class="contact-ltr-value fs--18 text--black7 fw--600 d-block"
                                    dir="ltr"
                                    href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                            @else
                                <span class="text-start fs--18 text--black7 fw--600 d-block">&mdash;</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div
                        class="contact--box bg--white radius--20 d-flex flex-column justify-content-start align-items-stretch position-relative h-100 w-100">
                        <div class="icon-wrap d-flex justify-content-center align-items-center position-absolute">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="content-wrap d-flex flex-column justify-content-start align-items-start w--100">
                            <p class="text-start fw--400 fs--14 mb-2">@lang('Address')</p>
                            @if ($contactAddress)
                                <p class="text-start fs--15 text--black7 fw--600 d-block mb-0" dir="auto">{{ $contactAddress }}</p>
                            @else
                                <span class="text-start fs--18 text--black7 fw--600 d-block">&mdash;</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div
                        class="contact--box bg--white radius--20 d-flex flex-column justify-content-start align-items-stretch position-relative h-100 w-100">
                        <div class="icon-wrap d-flex justify-content-center align-items-center position-absolute">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div class="content-wrap d-flex flex-column justify-content-start align-items-start w--100">
                            <p class="text-start fw--400 fs--14 mb-2">@lang('Support Team')</p>
                            <span class="text-start fs--18 text--black7 fw--600 d-block">@lang('24/7 hours')</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">

                <div class="col-xxl-6 col-xl-6 col-lg-12 mb-5 mb-xl-0">
                    <div class="contact-card radius--20">
                        <div class="content--wrap mb-4">
                            <h6 class="fs--32 mb-1 fw--600">@lang('Send Your Message')</h6>
                        </div>
                        <form method="post" action="{{ route('contact.submit') }}" class="verify-gcaptcha">
                            @csrf
                            <div class="row gy-3 mb-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Name')</label>
                                        <input class="form--control" type="text" placeholder="@lang('Enter Full Name')" name="name"
                                            value="@if (auth()->user()){{ auth()->user()->fullname }}@else{{ old('name') }}@endif"  @if (auth()->user()) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Email Address')</label>
                                        <input class="form--control" type="email" placeholder="@lang('Enter Email Address')" name="email"
                                            value="@if (auth()->user()) {{ auth()->user()->email }}@else{{ old('email') }} @endif" @if (auth()->user()) readonly @endif required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form--label">@lang('Subject')</label>
                                    <input type="text" name="subject" id="msg_subject" class=" form--control"
                                        placeholder="@lang('Subject')" value="{{ old('subject') }}" required>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-4 form-group">
                                    <label class="form--label mt-4">@lang('Message')</label>
                                    <textarea class="form--control" name="message" placeholder="@lang('Write Your Message')">{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <x-captcha></x-captcha>
                            <button class="btn btn--base btn--lg pills w-100" id="recaptcha">@lang('Send Message')</button>
                        </form>
                    </div>
                </div>

                <div class="col-xxl-6 col-xl-6">
                    <div class="map-section radius--20 overflow-hidden">
                        <div class="map-box">
                            @if ($mapOk)
                                <iframe
                                    src="https://maps.google.com/maps?q={{ urlencode($latitude) }},{{ urlencode($longitude) }}&amp;hl={{ str_replace('_', '-', app()->getLocale()) }}&amp;z=14&amp;output=embed"
                                    width="100%" allowfullscreen="" loading="lazy">
                                </iframe>
                            @else
                                <div class="d-flex align-items-center justify-content-center bg--light p-5"
                                    style="min-height: 360px;">
                                    <p class="text-center mb-0 text--black7">@lang('The map will appear when a location is configured.')</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
