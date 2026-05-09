@php
    $imageCount = $tourPackage->tour_package_images->count();
    $colOne = $imageCount == 1 ? '12' : '6';
    $colTwo = $imageCount == 2 ? '6' : '6';
    $primaryImage = optional($tourPackage->tour_package_images->get(0))->image;
    $secondaryImage = optional($tourPackage->tour_package_images->get(1))->image;
    $tertiaryImage = optional($tourPackage->tour_package_images->get(2))->image;
    $priceRangeText = $tourPackage->priceRangeText();
    $hasPriceRange = filled($tourPackage->price_from) && filled($tourPackage->price_to);
    $bookingPriceText = $hasPriceRange
        ? $priceRangeText
        : ($tourPackage->price ? $tourPackage->displayCurrencySymbol() . showAmount($tourPackage->price) : null);
    $latitude = trim((string) ($tourPackage->latitude ?? ''));
    $longitude = trim((string) ($tourPackage->longitude ?? ''));
    $hasValidCoordinates = filled($latitude) && filled($longitude) && (float) $latitude !== 0.0 && (float) $longitude !== 0.0;
    $displayPackageLabel = $tourPackage->displayPackageLabel();
    $displayDepartureFrom = $tourPackage->displayDepartureFrom();
    $displayArrival = $tourPackage->displayArrival();
    $displayTransportation = $tourPackage->displayTransportation();
    $displayAccommodation = $tourPackage->displayAccommodation();
    $displayTourType = $tourPackage->displayTourType();
    $displayCitiesCovered = $tourPackage->displayCitiesCovered();
    $localizedHighlights = $tourPackage->localizedHighlights();
    $localizedFeatures = $tourPackage->localizedFeatures();
    $localizedIncludes = $tourPackage->localizedIncludes();
    $localizedExcludes = $tourPackage->localizedExcludes();
    $localizedItineraryDays = $tourPackage->localizedItineraryDays();
    $itineraryPlaceholderImage = asset('assets/images/general/default.png');
@endphp


@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <!-- < product details  -->
    <section class="product-details section--bg pt-100">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div class="product--img__preview image--popup-group mb-4">
                        <div class="row g-2">
                            <div class="col-lg-{{ $colOne }} col-md-6">
                                <div class="product--thumb  radius--20 overflow-hidden">
                                    <div class="main--thumb__preview radius--20">
                                        <a class="d-flex w--100 h--100"
                                            href="{{ $primaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $primaryImage) : asset('assets/images/general/default.png') }}">
                                            <img class="fit--img" id="productImgSrc1"
                                                src="{{ $primaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $primaryImage) : asset('assets/images/general/default.png') }}"
                                                alt="tour-image">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if ($imageCount >= 2)
                                <div class="col-lg-{{ $colTwo }} col-md-6">
                                    <div class="row {{ $imageCount == 2 ? 'h--100' : '' }}">
                                        <div class="col-lg-12 {{ $imageCount == 2 ? 'h--100' : '' }}">
                                            <div
                                                class="product--thumb thumb--small radius--20 overflow-hidden mb-2 {{ $imageCount == 2 ? 'h--100' : '' }}">
                                                <div class="main--thumb__preview radius--20">
                                                    <a class="d-flex w--100 h--100"
                                                        href="{{ $secondaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $secondaryImage) : asset('assets/images/general/default.png') }}">
                                                        <img class="fit--img" id="productImgSrc2"
                                                            src="{{ $secondaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $secondaryImage) : asset('assets/images/general/default.png') }}"
                                                            alt="tour-image">
                                                    </a>

                                                </div>
                                            </div>
                                        </div>
                                        @if ($imageCount >= 3)
                                            <div class="col-lg-12">
                                                <div class="product--thumb thumb--small radius--20 overflow-hidden mb-2">
                                                    <div class="main--thumb__preview radius--20">
                                                        <a class="d-flex w--100 h--100 position-relative"
                                                            href="{{ $tertiaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $tertiaryImage) : asset('assets/images/general/default.png') }}">
                                                            <img class="fit--img" id="productImgSrc3"
                                                                src="{{ $tertiaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $tertiaryImage) : asset('assets/images/general/default.png') }}"
                                                                alt="tour-image">

                                                            @if ($imageCount - 3 > 0)
                                                                <div class="more-images-overlay heading--font">
                                                                    <span>{{ $imageCount - 3 }}+</span>
                                                                </div>
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($imageCount >= 4)
                                                @foreach ($tourPackage->tour_package_images->slice(3) as $image)
                                                    <div class="col-lg-12 d-none">
                                                        <div
                                                            class="product--thumb thumb--small radius--20 overflow-hidden mb-2">
                                                            <div class="main--thumb__preview radius--20">
                                                                <a class="d-flex w--100 h--100 position-relative"
                                                                    href="{{ getImage(getFilePath('tourPackageImage') . '/' . $image->image) }}">
                                                                    <img class="fit--img"
                                                                        src="{{ getImage(getFilePath('tourPackageImage') . '/' . $image->image) }}"
                                                                        alt="tour-image">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="base--card section--bg__two radius--16 border--none">
                        <div class="product--info__item mb-3">
                            <h6 class="fs--32 fw--600 mb-2">{{ __($tourPackage->displayTitle()) }}</h6>
                            <ul class="d-flex gap--20">
                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-user-group"></i>
                                        {{ $tourPackage->person_capability }}</span>
                                </li>
                                <li>
                                    <span class="text--black7"><i class="fa-regular fa-heart"></i>
                                        {{ $tourPackage->favorite }}</span>
                                </li>
                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-eye"></i> {{ $tourPackage->view }}</span>
                                </li>

                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-stopwatch"></i>
                                        {{ $tourPackage->booking_person }}</span>
                                </li>

                                @if ($displayPackageLabel)
                                    <li>
                                        <span class="text--black7"><i class="fa-solid fa-tag"></i>
                                            {{ $displayPackageLabel }}</span>
                                    </li>
                                @endif

                            </ul>
                        </div>
                        <ul class="custom--tabs buy-sell d-flex flex-wrap gap--4 z--1 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills active" id="Profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#Profile" type="button" role="tab" aria-selected="true"><i
                                        class="fa-solid fa-info-circle"></i> @lang('Details')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills" id="Input-tab" data-bs-toggle="tab"
                                    data-bs-target="#Input" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="fa-solid fa-location-dot"></i> @lang('Location')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills" id="Notes-tab" data-bs-toggle="tab"
                                    data-bs-target="#Notes" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="fa-solid fa-star"></i> @lang('Reviews')</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="Profile" role="tabpanel"
                                aria-labelledby="Profile-tab">
                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Destination Overview')</h6>

                                    <div class="row gy-4">
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-plane-departure"></i>
                                                    </div>
                                                    <p class="title mb-1">@lang('Departure From')</p>
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ $displayDepartureFrom }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-plane-arrival"></i>
                                                    </div>
                                                    <p>@lang('Arrival')</p>     
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ $displayArrival }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-bus-simple"></i>
                                                    </div>
                                                    <p>@lang('Transportation')</p>   
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ $displayTransportation }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-bed"></i>
                                                    </div>
                                                    <p>@lang('Accommodation')</p>   
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ $displayAccommodation }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-cable-car"></i>
                                                    </div>
                                                    <p>@lang('Tour Type')</p> 
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">{{ $displayTourType }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-users"></i>
                                                    </div>
                                                    <p>@lang('Person')</p> 
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->person_capability }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Description')</h6>
                                    <div class="description">
                                        @php
                                            echo $tourPackage->displayDescription();
                                        @endphp

                                    </div>
                                </div>

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Highlights')</h6>
                                    <ul class="highlight__key d-flex flex-column gap--12">

                                        @foreach ($localizedHighlights as $item)
                                            <li class="d-flex gap--8">
                                                <span class="text--base">

                                                    <i class="fa-solid fa-circle-right"></i>
                                                </span>
                                                <p>{{ __($item) }}</p>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Package Features')</h6>
                                    <ul class="highlight__key d-flex flex-column gap--12">
                                        @foreach ($localizedFeatures as $item)
                                            <li class="d-flex gap--8">
                                                <p>{{ __($item->feature) }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                @if ($localizedIncludes)
                                    <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                        <h6 class="fs--22 fw--600">@lang('Includes')</h6>
                                        <ul class="highlight__key d-flex flex-column gap--12">
                                            @foreach ($localizedIncludes as $item)
                                                <li class="d-flex gap--8">
                                                    <span class="text--success"><i class="fa-solid fa-circle-check"></i></span>
                                                    <p>{{ __($item) }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($localizedExcludes)
                                    <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                        <h6 class="fs--22 fw--600">@lang('Excludes')</h6>
                                        <ul class="highlight__key d-flex flex-column gap--12">
                                            @foreach ($localizedExcludes as $item)
                                                <li class="d-flex gap--8">
                                                    <span class="text--danger"><i class="fa-solid fa-circle-xmark"></i></span>
                                                    <p>{{ __($item) }}</p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($localizedItineraryDays)
                                    <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                        <h6 class="fs--22 fw--600">@lang('Itinerary')</h6>
                                        <div class="row gy-3">
                                            @foreach ($localizedItineraryDays as $day)
                                                <div class="col-12">
                                                    <?php $itineraryImage = $tourPackage->itineraryImageUrl($day['image'] ?? null); ?>
                                                    <div class="itinerary-day-card border rounded-3 p-3 h-100">
                                                        <div class="d-flex flex-column flex-md-row gap-3 align-items-stretch">
                                                            <div class="itinerary-thumb-wrap flex-shrink-0" style="width: min(100%, 280px); aspect-ratio: 16 / 9;">
                                                                <?php if ($itineraryImage): ?>
                                                                    <img src="{{ $itineraryImage }}" alt="{{ $day['title'] ?? 'Day image' }}" class="w-100 h-100 rounded-3" style="object-fit: cover;">
                                                                <?php else: ?>
                                                                    <img src="{{ $itineraryPlaceholderImage }}" alt="@lang('No image')" class="w-100 h-100 rounded-3" style="object-fit: cover;">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                @if ($day['day_number'])
                                                                    <p class="mb-1 text--base fw--600">{{ __($day['day_number']) }}</p>
                                                                @endif
                                                                <h6 class="mb-1 fw--600">{{ __($day['title'] ?? '') }}</h6>
                                                                @if ($day['description'])
                                                                    <p class="mb-0">{{ __($day['description']) }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="Input" role="tabpanel" aria-labelledby="Input-tab">
                                @if ($hasValidCoordinates)
                                    <div class="map-section radius--12 overflow-hidden">
                                        <div class="map-box">
                                            <iframe
                                                src="https://maps.google.com/maps?q={{ $latitude }},{{ $longitude }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                                allowfullscreen="" loading="lazy">
                                            </iframe>
                                        </div>
                                    </div>
                                @else
                                    <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                        <h6 class="fs--22 fw--600">@lang('Location Information')</h6>
                                        <div class="row gy-4">
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-location-dot"></i>
                                                        </div>
                                                        <p class="title mb-1">@lang('Address')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->displayAddress() }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-city"></i>
                                                        </div>
                                                        <p class="title mb-1">@lang('City')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->city }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-flag"></i>
                                                        </div>
                                                        <p class="title mb-1">@lang('Country')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->country }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-map-location-dot"></i>
                                                        </div>
                                                        <p class="title mb-1">@lang('Cities Covered')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ $displayCitiesCovered }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="Notes" role="tabpanel" aria-labelledby="Notes-tab">
                                <div class="note--wrap">
                                    <div class="row gy-4">
                                        @forelse ($tourPackage->reviews ?? [] as $item)
                                            <div class="col-lg-6">
                                                <div class="review-card">
                                                    <div class="user-info">
                                                        <div class="thumb-wrap">
                                                            <img class="fit--img"
                                                                src="{{ getImage(getFilePath('userProfile') . '/' . $item->user->image, getFileSize('userProfile')) }}"
                                                                alt="..">
                                                        </div>
                                                        <div class="user-name">
                                                            <div class="d-flex align-items-center gap--8">
                                                                <h1 class="name fs--20 fw--600 mb-0">
                                                                    {{ $item->user->fullname }}
                                                                </h1>
                                                                <p class="fs--14">
                                                                    {{ showDateTime($item->created_at, 'd M') }}
                                                                </p>
                                                            </div>
                                                            <ul class="rating-wrap">
                                                                @php echo calculateIndividualRating($item->star) @endphp
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="content">

                                                        <div class="discription">@php
                                                            echo $item->review;
                                                        @endphp</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <h5 class="text-center no-review">@lang('No Reviews')</h5>
                                        @endforelse

                                        <div class="row mt-4">
                                            <form action="{{ route('user.review.submit') }}" method="POST">
                                                @csrf
                                                <div class="review-box mb-4">
                                                    <input type="hidden" name="tour_package_id"
                                                        value="{{ $tourPackage->id }}">
                                                    <input type="hidden" name="star" id="rating" value="0">
                                                    <div
                                                        class="d-flex align-items-center star rating-wrap rating-stars mb-3 gap-1">
                                                        <i class="far fa-star star--color" data-rating="1"></i>
                                                        <i class="far fa-star star--color" data-rating="2"></i>
                                                        <i class="far fa-star star--color" data-rating="3"></i>
                                                        <i class="far fa-star star--color" data-rating="4"></i>
                                                        <i class="far fa-star star--color" data-rating="5"></i>
                                                    </div>
                                                    <textarea class="form--control mb-3" name="review" placeholder="@lang('Write Your Review')"></textarea>

                                                    <div class="text-end">
                                                        <button type="submit"
                                                            class="btn btn--base btn--lg pills">@lang('Submit')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="product--info__wrap position-sticky" id="tour-package-book">
                        <div class="bg--white radius--20 p-4 mb-4">
                            <form method="POST" action="{{ route('user.tour.package.booking.now') }}">
                                @csrf

                                <div class="product--info__item d-flex flex-column gap--20">
                                    <div>
                                        <p class="mb-1"><i class="fa-solid fa-calendar-days"></i> @lang($tourPackage->flexible_date == 1 ? 'From - To (Flexible)' : 'From - To')</p>
                                        <h6 class="price fs--18 fw--500 mb-0 text--black7">
                                            {{ showDateTime($tourPackage->tour_start, 'M d, Y') }} - {{ showDateTime($tourPackage->tour_end, 'M d, Y') }}
                                        </h6>
                                    </div>
                                </div>

                                @if ($bookingPriceText)
                                    <div class="product--info__item">
                                        <p class="mb-1">@lang($hasPriceRange ? 'Price Range' : 'Price')</p>
                                        <h6 class="price fs--28 fw--600 mb-0 text--black7">
                                            <span dir="ltr" class="d-inline-block">{{ $bookingPriceText }} / @lang('person')</span>
                                        </h6>
                                        @if ((float) $tourPackage->discount > 0)
                                            <small class="text--black7 d-block mt-1">
                                                @lang('Discount'):
                                                {{ $tourPackage->displayCurrencySymbol() }}{{ showAmount(showTourPackageCalculateDiscount($tourPackage->price, $tourPackage->discount)) }}
                                            </small>
                                        @endif
                                    </div>
                                @endif

                                <div class="product--info__item border-0 pb-2 mb-0">
                                    <div class="row">
                                        <input type="number" class="d-none" value="{{ $tourPackage->id }}"
                                            name="tour_package_id">
                                        @if ($tourPackage->flexible_date == 1)
                                            <div class="col-lg-6">
                                                <div class="mb-4 form-group">
                                                    <label class="mb-2 form--label">@lang('Suggested Date')</label>
                                                    <input class="form--control details--datepicker datepicker-active"
                                                        data-language="en" autocomplete="off" placeholder="dd/mm/yyyy"
                                                        name="user_proposal_date">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-lg-{{ $tourPackage->flexible_date == 1 ? '6' : '12' }}">
                                            <div class="mb-4 form-group">
                                                <label class="mb-2 form--label ">@lang('Person')</label>
                                                <input class="form--control" type="number" min="1" value="1"
                                                    step="1" name="seat" placeholder='0'>
                                            </div>
                                        </div>

                                        @auth
                                            @if(auth()->user()->cashback_balance > 0)
                                                <div class="col-12">
                                                    <div class="form-check border rounded-3 p-3">
                                                        <input class="form-check-input" type="checkbox" name="use_cashback" value="1" id="useCashback">
                                                        <label class="form-check-label fw-semibold" for="useCashback">
                                                            @lang('Apply cashback balance')
                                                        </label>
                                                        <small class="text-muted d-block mt-1">
                                                            @lang('Available:') {{ $general->cur_sym }}{{ showAmount(auth()->user()->cashback_balance) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth

                                    </div>
                                </div>
                                <div class="product--info__item">
                                    <button class="btn btn--base btn--lg w--100 pills" type="submit">@lang('Book Now')
                                        <i class="fa-solid fa-arrow-right-long"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="bg--white radius--20 p-4 d-flex flex-column justify-content-center align-items-center">
                            <div class="details--page__datepicker" id="datepicker" data-language="en"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="recent--section section--bg position-relative py-100">
        <div class="container">

            <div class="row justify-content-start">
                <div class="col-lg-6">
                    <div class="section-content mb-50">
                        <div class="title-wrap">
                            <h6 class="heading third--font text-start fs--32 fw--700 text--base mb-0">
                                @lang('Recently Viewed')</h6>
                            <h2 class="title text-start mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                                data-splitting data-wow-delay="0.2s">@lang('Recently Viewed')</h2>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center gy-4">
                @include($activeTemplate . 'components.single_tour_package')
            </div>
        </div>
    </section>

@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        $(function() {
            'use strict'
            $("#datepicker").datepicker({
                showOtherMonths: true,
                defaultViewDate: new Date(),
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            'use strict'
            $(".datepicker-active").datepicker({
                minDate: new Date(),
                timepicker: true,
                timeFormat: ', hh:ii aa',
            });
        });
    </script>
    <script>
        // rating set
        $(document).ready(function() {
            'use strict'

            var initialRating = parseInt($('#rating').val());
            if (initialRating > 0) {
                updateStars(initialRating);
            }

            $('.rating-stars i').on('click', function() {
                var rating = parseInt($(this).data('rating'));
                $('#rating').val(rating);
                updateStars(rating);
            });

            $('#rating').on('input', function() {
                var rating = $(this).val();
                updateStars(rating);
            });

            function updateStars(rating) {
                var stars = $('.rating-stars i');
                stars.removeClass('fas').addClass('far');
                stars.each(function(index) {
                    if (index < rating) {
                        $(this).removeClass('far').addClass('fas');
                    }
                });
            }

        });
        // end rating set
    </script>

    <script>
        function addToWishlist(element) {
            "use strict";

            var isAddingToWishlist = false;
            var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            if (!isAddingToWishlist && isLoggedIn) {
                isAddingToWishlist = true;
                var tourPackageId = $(element).data('tour_package_id');
                var url = $(element).data('url');

                $.ajax({
                    url: url,
                    type: 'get',
                    data: {
                        tourPackageId: tourPackageId,
                    },
                    complete: function() {
                        isAddingToWishlist = false;
                    },
                    success: function(response) {
                        if (response.hasOwnProperty('message')) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            var heartIcon = $(element).find('i');
                            if (response.message.includes('added')) {
                                heartIcon.removeClass('far fa-heart').addClass('fas fa-heart text--base');
                            } else if (response.message.includes('removed')) {
                                heartIcon.removeClass('fas fa-heart text--base').addClass('far fa-heart');
                            }
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: response.error
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Error occurred while updating the wishlist.';
                        Toast.fire({
                            icon: 'error',
                            title: errorMessage
                        });
                    }
                });
            } else if (!isLoggedIn) {
                var errorMessage = 'Please log in to manage your wishlist.';
                Toast.fire({
                    icon: 'warning',
                    title: errorMessage
                });
            }
        }
    </script>
@endpush
