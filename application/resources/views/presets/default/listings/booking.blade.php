@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="product-details section--bg pt-100 pb-100">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="base--card section--bg__two radius--16 border--none p-4 p-lg-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                            <div>
                                <p class="mb-2 text--black7">
                                    <i class="fa-solid fa-circle text-success me-1"></i>
                                    {{ $listing->listingType?->name ?? str_replace('_', ' ', $listing->type) }}
                                </p>
                                <h2 class="fs--36 fw--800 mb-2">@lang('Book') {{ __($listing->title) }}</h2>
                                <p class="text--black7 mb-0">
                                    <i class="fa-solid fa-location-dot text-success me-2"></i>
                                    {{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}
                                </p>
                                @if($listing->start_date && $listing->end_date)
                                    <p class="text--black7 mb-0 mt-2">
                                        <i class="fa-regular fa-calendar-days text-primary me-2"></i>
                                        {{ showDateTime($listing->start_date, 'd M Y') }} - {{ showDateTime($listing->end_date, 'd M Y') }}
                                        <span class="ms-2">({{ $listing->durationDays() }} {{ __('Days') }})</span>
                                    </p>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="d-block text--black7 mb-1">@lang('Price')</span>
                                <h3 class="fs--34 fw--800 mb-0">{{ listingPriceLabel($listing->finalPrice(), $listing->currency ?? 'USD') }}</h3>
                            </div>
                        </div>

                        @auth
                            <form action="{{ route('listing.booking.store', [slug($listing->title), $listing->id]) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('Booking Date')</label>
                                        <div class="listing-booking__date-wrap position-relative">
                                            <input
                                                type="text"
                                                name="service_date"
                                                value="{{ old('service_date', optional($listing->start_date)->format('m/d/Y')) }}"
                                                data-language="en"
                                                class="form-control pills listing-booking__date datepicker-here"
                                                data-date-format="mm/dd/yyyy"
                                                autocomplete="off"
                                                required
                                            >
                                            <button type="button" class="listing-booking__date-btn" aria-label="@lang('Open date picker')" tabindex="-1">
                                                <i class="fa-regular fa-calendar-days"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-1">@lang('Choose a date between the listing start and end date.')</small>
                                    </div>
                                    @if(count($listing->availableTimes()))
                                        <div class="col-md-12">
                                            <label class="form-label">@lang('Travel Time')</label>
                                            <select name="service_time" class="form-control" required>
                                                <option value="">@lang('Select Travel Time')</option>
                                                @foreach($listing->availableTimes() as $availableTime)
                                                    <option value="{{ $availableTime }}">{{ $availableTime }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <label class="form-label">@lang('Notes')</label>
                                        <textarea name="notes" rows="5" class="form-control" placeholder="@lang('Write any special request or booking note')"></textarea>
                                    </div>
                                    <div class="col-12 listing-booking__actions d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <a href="{{ route('listing.details', [slug($listing->title), $listing->id]) }}" class="btn btn--dark pills">@lang('Back')</a>
                                        <button type="submit" class="btn btn--base btn--lg pills listing-booking__submit">
                                            <i class="fa-solid fa-check me-2"></i>
                                            @lang('Confirm Booking')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">
                                @lang('You need to log in before booking this listing.')
                                <div class="mt-3 listing-booking__login-cta-wrap">
                                    <a href="{{ route('user.login') }}" class="btn btn--base btn--lg pills listing-booking__login-cta">
                                        <i class="fa-solid fa-right-to-bracket"></i>
                                        <span>@lang('Sign In to Book')</span>
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="base--card section--bg__two radius--16 border--none overflow-hidden">
                        @if($listing->image)
                            <img class="w-100" style="height: 320px; object-fit: cover;" src="{{ getImage(getFilePath('listingImage') . '/' . $listing->image) }}" alt="{{ $listing->title }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg--light text-muted" style="height: 320px;">
                                @lang('No image')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

    @push('style')
        <style>
            .listing-booking__actions {
                margin-top: 8px;
            }

            .listing-booking__date-wrap {
                position: relative;
            }

            .listing-booking__date {
                padding-right: 54px;
                cursor: pointer;
                background-color: #fff !important;
            }

            .listing-booking__date-btn {
                position: absolute;
                top: 50%;
                right: 12px;
                transform: translateY(-50%);
                width: 34px;
                height: 34px;
                border: 0;
                border-radius: 10px;
                background: #eef4ff;
                color: #2563eb;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                z-index: 2;
                pointer-events: none;
            }

            .listing-booking__date-btn i {
                font-size: 16px;
            }

            .listing-booking__submit {
                min-width: 240px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                color: #fff !important;
                font-weight: 700;
                box-shadow: 0 14px 30px rgba(22, 163, 74, 0.22);
            }

            .listing-booking__login-cta-wrap {
                display: flex;
            }

            .listing-booking__login-cta {
                min-width: 240px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                color: #fff !important;
                font-weight: 700;
                box-shadow: 0 14px 30px rgba(22, 163, 74, 0.22);
                text-decoration: none;
            }

            .listing-booking__login-cta i {
                font-size: 15px;
            }

            @media (max-width: 575px) {
                .listing-booking__submit,
                .listing-booking__login-cta,
                .listing-booking__actions .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.datepicker-here').datepicker({
                autoClose: true,
                minDate: new Date('{{ optional($listing->start_date)->format('Y-m-d') }}'),
                maxDate: new Date('{{ optional($listing->end_date)->format('Y-m-d') }}'),
            });
        })(jQuery);
    </script>
@endpush

