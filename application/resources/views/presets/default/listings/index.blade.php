@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'components.hero', [
        'heroTitle' => __('Plan Your Trip'),
        'heroHeading' => __('Top Destinations & Experiences'),
        'heroSubHeading' => __('Explore popular locations and find hidden gems around the world.'),
    ])
    <section class="explore-section section--bg py-100">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center gap--16 flex-wrap">
                    <div>
                        <h2 class="title mb-2 fs--40 fw--800">@lang('Listing Offers')</h2>
                        <p class="text-muted mb-0">@lang('Hotels, trips and seasonal offers in one place.')</p>
                    </div>
                    <a href="{{ route('browse') }}" class="btn btn-outline--base pills">@lang('Browse Tours')</a>
                </div>

                <div class="col-12">
                    <div class="row gy-4">
                        @forelse($listings as $listing)
                            <div class="col-lg-4 col-md-6">
                                <div class="listing-card tour-card radius--20 position-relative bg--white h-100">
                                    <div class="listing-card__overlay-badges position-absolute d-flex justify-content-between align-items-start">
                                        @if($listing->discount)
                                            <span class="listing-card__discount-badge text--white fw--600">
                                                <i class="fa-solid fa-tag"></i>
                                                @lang('Save') {{ listingAmount($listing->discountAmount()) }} {{ __($listing->currency ?? 'USD') }}
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('listing.details', [slug($listing->title), $listing->id]) }}" class="listing-card__thumb d-block">
                                        @if($listing->image)
                                            <img class="w-100 h-100" src="{{ getImage(getFilePath('listingImage') . '/' . $listing->image) }}" alt="{{ $listing->title }}">
                                        @else
                                            <div class="listing-card__placeholder d-flex align-items-center justify-content-center w-100 h-100 text-muted">
                                                @lang('No image')
                                            </div>
                                        @endif
                                    </a>

                                    <div class="tour-card__content">
                                        @if($listing->offerSummary() || $listing->listingType?->name)
                                            <div class="listing-card__headline-row">
                                                @if($listing->listingType?->name)
                                                    <span class="listing-card__type-badge listing-card__type-badge--inline text--white fw--500">
                                                        {{ $listing->listingType?->name ?? str_replace('_', ' ', $listing->type) }}
                                                    </span>
                                                @endif

                                                @if($listing->offerSummary())
                                                    <div class="listing-card__offer-inline">
                                                        <i class="fa-solid fa-gift"></i>
                                                        <span>{{ $listing->offerSummary() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="tour-card__location">
                                            <ul class="d-flex justify-content-between align-items-start gap--20">
                                                <li>
                                                    <p title="{{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}" class="fs--14">
                                                        <i class="fa-regular fa-compass"></i>
                                                        {{ strLimit(trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-'), 18) }}
                                                    </p>
                                                </li>
                                                <li class="flex-shrink-0">
                                                    <p class="fs--14">
                                                        <i class="fa-regular fa-clock"></i>
                                                        {{ $listing->durationDays() ? $listing->durationDays() : __('-') }} @lang('Days')
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>

                                        <a href="{{ route('listing.details', [slug($listing->title), $listing->id]) }}">
                                            <h6 class="tour-card__title fs--20 fw--600" title="{{ $listing->title }}">
                                                {{ __(strLimit($listing->title, 20)) }}
                                            </h6>
                                        </a>

                                        <div class="tour-card__price-wrap d-flex justify-content-between align-items-center gap--16 flex-wrap">
                                            <div class="tour-card__price">
                                                @php
                                                    $listingFinalPrice = $listing->finalPrice();
                                                @endphp
                                                <h6 class="fs--20 fw--600 mb-0 body--font listing-card__price-line">
                                                    <span>{{ listingPriceLabel($listingFinalPrice, $listing->currency ?? 'USD') }}</span>
                                                    <span class="text--black7 fs--16">/@lang('listing')</span>
                                                </h6>

                                                @if($listing->discountAmount() > 0)
                                                    <small class="listing-card__old-price">
                                                        {{ listingAmount($listing->originalPrice()) }} {{ __($listing->currency ?? 'USD') }}
                                                    </small>
                                                @endif
                                            </div>

                                            <div class="tour-card__btn-wrap">
                                                <a href="{{ route('listing.booking', [slug($listing->title), $listing->id]) }}" class="text--base">
                                                    <i class="fa-solid fa-arrow-right-long"></i> @lang('Book')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">@lang('No listing offers available right now.')</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($listings->hasPages())
                <div class="row mt-4">
                    <div class="col-lg-12 d-flex justify-content-end">
                        {{ $listings->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('style')
    <style>
        .listing-card {
            border: 1px solid rgba(15, 23, 42, 0.08);
            transition: transform .25s ease, box-shadow .25s ease;
            overflow: hidden;
        }

        .listing-card .tour-card__content {
            padding: 20px 20px 22px;
        }

        .listing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12) !important;
        }

        .listing-card__thumb {
            height: 240px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .listing-card__overlay-badges {
            inset: 18px 18px auto 18px;
            z-index: 2;
            gap: 10px;
            pointer-events: none;
        }

        .listing-card__thumb img {
            object-fit: cover;
            transition: transform .35s ease;
        }

        .listing-card:hover .listing-card__thumb img {
            transform: scale(1.04);
        }

        .listing-card__placeholder {
            min-height: 240px;
            background: linear-gradient(135deg, #eef2f7 0%, #f7f8fb 100%);
        }

        .listing-card__type-badge,
        .listing-card__discount-badge {
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 13px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(6px);
            max-width: 48%;
        }

        .listing-card__type-badge {
            background: rgba(17, 24, 39, 0.8);
        }

        .listing-card__type-badge--inline {
            padding: 8px 12px;
            font-size: 12px;
            box-shadow: none;
            max-width: none;
            width: fit-content;
            flex-shrink: 0;
        }

        .listing-card__discount-badge {
            background: rgba(22, 163, 74, 0.9);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .listing-card__price-line {
            display: flex;
            align-items: baseline;
            gap: 4px;
            flex-wrap: wrap;
        }

        .listing-card__old-price {
            display: inline-block;
            margin-top: 6px;
            color: #94a3b8;
            font-size: 13px;
            text-decoration: line-through;
        }

        .listing-card__offer-inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
            width: fit-content;
        }

        .listing-card__offer-inline i {
            color: #2563eb;
        }

        .listing-card__headline-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
    </style>
@endpush
