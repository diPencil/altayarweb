@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="listing-details section--bg pt-100 pb-100">
        <div class="container">
            <div class="listing-hero base--card section--bg__two radius--20 border--none overflow-hidden mb-4">
                <div class="row g-0">
                    <div class="col-lg-7">
                        <div class="listing-hero__media h-100">
                            @if($listing->image)
                                <img class="w-100 h-100" src="{{ getImage(getFilePath('listingImage') . '/' . $listing->image) }}" alt="{{ $listing->title }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg--light text-muted listing-hero__empty">
                                    @lang('No image')
                                </div>
                            @endif
                            <div class="listing-hero__overlay"></div>
                            <div class="listing-hero__badge-group">
                                <span class="listing-hero__badge listing-hero__badge--soft">{{ $listing->listingType?->name ?? str_replace('_', ' ', $listing->type) }}</span>
                                <span class="listing-hero__badge {{ $listing->discount ? 'listing-hero__badge--accent' : 'listing-hero__badge--primary' }}">{{ $listing->discount ? __('Limited') : __('Available') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="listing-hero__panel h-100 p-4 p-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <p class="listing-hero__eyebrow mb-2">@lang('Listing Details')</p>
                                <h1 class="listing-hero__title mb-3">{{ __($listing->title) }}</h1>
                                <div class="listing-hero__meta">
                                    <span><i class="fa-solid fa-location-dot"></i>{{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}</span>
                                    @if($listing->start_date && $listing->end_date)
                                        <span><i class="fa-regular fa-calendar-days"></i>{{ showDateTime($listing->start_date, 'd M Y') }} - {{ showDateTime($listing->end_date, 'd M Y') }}</span>
                                        <span><i class="fa-solid fa-clock"></i>{{ $listing->durationDays() }} {{ __('Days') }}</span>
                                    @endif
                                </div>

                                <div class="listing-hero__membership mt-4">
                                    <div class="listing-hero__membership-icon">
                                        <i class="fa-solid fa-crown"></i>
                                    </div>
                                    <div class="listing-hero__membership-content">
                                        <p class="listing-hero__membership-title mb-1">@lang('Available for Club Members')</p>
                                        <p class="listing-hero__membership-text mb-0">@lang('This offer is available to club membership holders with exclusive access and special booking benefits.')</p>
                                    </div>
                                </div>

                                @if($listing->offerSummary())
                                    <div class="listing-hero__promo mt-3">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>{{ $listing->offerSummary() }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="listing-hero__pricebox">
                                <div>
                                    <span class="d-block listing-hero__price-label">@lang('Price')</span>
                                    <strong class="listing-hero__price">{{ listingPriceLabel($listing->finalPrice(), $listing->currency ?? 'USD') }}</strong>
                                </div>
                                <a href="{{ route('listing.booking', [slug($listing->title), $listing->id]) }}" class="btn btn--base btn--lg pills listing-details__cta listing-details__cta--inline">
                                    @lang('Book Now')
                                    <i class="fa-solid fa-arrow-right-long ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="base--card section--bg__two radius--16 border--none p-4 p-lg-5 mb-4">
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="listing-detail__tag">
                                <i class="fa-solid fa-star"></i>
                                {{ $listing->listingType?->name ?? __('Listing') }}
                            </span>
                            <span class="listing-detail__tag">
                                <i class="fa-solid fa-bed"></i>
                                {{ $listing->discount ? __('Discounted') : __('Room Only') }}
                            </span>
                            <span class="listing-detail__tag">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}
                            </span>
                        </div>

                        @if($listing->summary)
                            <div class="listing-detail__section mb-4">
                                <h5 class="listing-detail__section-title">@lang('Summary')</h5>
                                <p class="text--black7 mb-0">{{ __($listing->summary) }}</p>
                            </div>
                        @endif

                        @if($listing->description)
                            <div class="listing-detail__section mb-4">
                                <h5 class="listing-detail__section-title">@lang('Description')</h5>
                                <div class="listing-detail__text text--black7">
                                    {!! nl2br(e($listing->description)) !!}
                                </div>
                            </div>
                        @endif

                        @if(count($listing->facilitiesList()))
                            <div class="listing-detail__section mb-4">
                                <h5 class="listing-detail__section-title">@lang('Facilities')</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($listing->facilitiesList() as $facility)
                                        <span class="listing-detail__pill">
                                            <i class="fa-solid fa-check"></i>
                                            {{ __($facility) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(count($listing->includesList()) || count($listing->excludesList()))
                            <div class="row g-3">
                                @if(count($listing->includesList()))
                                    <div class="col-md-6">
                                        <div class="listing-detail__box listing-detail__box--success">
                                            <h6 class="fs--18 fw--700 mb-3 text-success">@lang('Includes')</h6>
                                            <ul class="listing-detail__list">
                                                @foreach($listing->includesList() as $include)
                                                    <li><i class="fa-solid fa-check text-success"></i> {{ __($include) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                @if(count($listing->excludesList()))
                                    <div class="col-md-6">
                                        <div class="listing-detail__box listing-detail__box--danger">
                                            <h6 class="fs--18 fw--700 mb-3 text-danger">@lang('Excludes')</h6>
                                            <ul class="listing-detail__list">
                                                @foreach($listing->excludesList() as $exclude)
                                                    <li><i class="fa-solid fa-xmark text-danger"></i> {{ __($exclude) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="listing-sidebar position-sticky" style="top: 24px;">
                        <div class="base--card section--bg__two radius--16 border--none p-4 mb-4">
                            <h5 class="fs--22 fw--700 mb-3">@lang('Quick Facts')</h5>
                            <div class="listing-facts">
                                <div class="listing-facts__item">
                                    <span>@lang('Type')</span>
                                    <strong>{{ $listing->listingType?->name ?? __('Listing') }}</strong>
                                </div>
                                <div class="listing-facts__item">
                                    <span>@lang('Location')</span>
                                    <strong>{{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}</strong>
                                </div>
                                <div class="listing-facts__item">
                                    <span>@lang('Duration')</span>
                                    <strong>{{ $listing->durationDays() ? $listing->durationDays() . ' ' . __('Days') : __('-') }}</strong>
                                </div>
                                <div class="listing-facts__item">
                                    <span>@lang('Status')</span>
                                    <strong>{{ $listing->discount ? __('Limited') : __('Available') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="base--card section--bg__two radius--16 border--none p-4 mb-4">
                            <h5 class="fs--22 fw--700 mb-3">@lang('Book This Listing')</h5>
                            <div class="listing-bookbox">
                                <span class="d-block listing-bookbox__label">@lang('Starting From')</span>
                                <div class="listing-bookbox__price mb-3">{{ listingPriceLabel($listing->finalPrice(), $listing->currency ?? 'USD') }}</div>
                                <p class="text--black7 mb-4">
                                    @if($listing->start_date && $listing->end_date)
                                        {{ showDateTime($listing->start_date, 'd M Y') }} - {{ showDateTime($listing->end_date, 'd M Y') }}
                                    @else
                                        @lang('Date not assigned yet')
                                    @endif
                                </p>
                                <a href="{{ route('listing.booking', [slug($listing->title), $listing->id]) }}" class="btn btn--base btn--lg pills w--100 listing-details__cta">
                                    @lang('Proceed to Booking')
                                    <i class="fa-solid fa-arrow-right-long ms-2"></i>
                                </a>
                            </div>
                        </div>

                        @if($relatedListings->count())
                            <div class="base--card section--bg__two radius--16 border--none p-4">
                                <h5 class="fs--22 fw--700 mb-4">@lang('Related Listing Offers')</h5>
                                <div class="d-grid gap-3">
                                    @foreach($relatedListings as $relatedListing)
                                        <a href="{{ route('listing.details', [slug($relatedListing->title), $relatedListing->id]) }}" class="listing-related d-flex align-items-center gap-3 text-decoration-none">
                                            <div class="listing-related__thumb flex-shrink-0 rounded-3 overflow-hidden">
                                                @if($relatedListing->image)
                                                    <img class="w-100 h-100" src="{{ getImage(getFilePath('listingImage') . '/' . $relatedListing->image) }}" alt="{{ $relatedListing->title }}">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg--light text-muted">-</div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h6 class="mb-1 text--black7">{{ __(strLimit($relatedListing->title, 32)) }}</h6>
                                                <small class="text-muted">{{ $relatedListing->listingType?->name ?? str_replace('_', ' ', $relatedListing->type) }}</small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
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
        .listing-details {
            overflow: hidden;
        }

        .listing-hero {
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .listing-hero__media {
            position: relative;
            min-height: 420px;
            background: #e5e7eb;
        }

        .listing-hero__media img {
            object-fit: cover;
        }

        .listing-hero__empty {
            min-height: 420px;
        }

        .listing-hero__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0.42) 100%);
        }

        .listing-hero__badge-group {
            position: absolute;
            left: 20px;
            top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            z-index: 2;
        }

        .listing-hero__badge {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 999px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .listing-hero__badge--soft {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(6px);
        }

        .listing-hero__badge--primary {
            background: #2563eb;
        }

        .listing-hero__badge--accent {
            background: #16a34a;
        }

        .listing-hero__panel {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .listing-hero__eyebrow {
            color: #16a34a;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .listing-hero__title {
            font-size: clamp(28px, 3vw, 44px);
            line-height: 1.08;
            font-weight: 800;
            color: #111827;
        }

        .listing-hero__meta {
            display: grid;
            gap: 12px;
            color: #4b5567;
            font-size: 15px;
        }

        .listing-hero__meta span {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .listing-hero__meta i {
            color: #16a34a;
            width: 16px;
            text-align: center;
        }

        .listing-hero__membership {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(59, 130, 246, 0.06));
            border: 1px solid rgba(37, 99, 235, 0.16);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .listing-hero__membership-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
        }

        .listing-hero__membership-icon i {
            font-size: 18px;
        }

        .listing-hero__membership-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .listing-hero__membership-text {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }

        .listing-hero__promo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 14px;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            font-weight: 700;
            font-size: 14px;
        }

        .listing-hero__promo i {
            color: #2563eb;
        }

        .listing-hero__pricebox {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 32px;
        }

        .listing-hero__price-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .listing-hero__price {
            font-size: 34px;
            line-height: 1;
            color: #111827;
            letter-spacing: -.02em;
        }

        .listing-detail__tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: #f3f5f9;
            color: #4b5567;
            font-size: 14px;
            font-weight: 600;
        }

        .listing-detail__tag i {
            color: #374151;
        }

        .listing-detail__section {
            padding: 18px 20px;
            border: 1px solid rgba(15, 23, 42, 0.07);
            border-radius: 16px;
            background: #fff;
        }

        .listing-detail__section-title {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 14px;
        }

        .listing-detail__text {
            line-height: 1.8;
            font-size: 15px;
        }

        .listing-detail__pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 14px;
            font-weight: 600;
        }

        .listing-detail__pill i {
            color: #16a34a;
        }

        .listing-detail__box {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 18px 20px;
            background: #fff;
        }

        .listing-detail__box--success {
            background: linear-gradient(180deg, #ffffff 0%, #f7fdf8 100%);
        }

        .listing-detail__box--danger {
            background: linear-gradient(180deg, #ffffff 0%, #fff7f7 100%);
        }

        .listing-detail__list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 10px;
        }

        .listing-detail__list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #4b5567;
            font-size: 15px;
        }

        .listing-detail__list i {
            margin-top: 3px;
        }

        .listing-facts {
            display: grid;
            gap: 12px;
        }

        .listing-facts__item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
        }

        .listing-facts__item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .listing-facts__item span {
            color: #6b7280;
            font-size: 14px;
        }

        .listing-facts__item strong {
            color: #111827;
            font-size: 14px;
            text-align: right;
        }

        .listing-bookbox__label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .listing-bookbox__price {
            color: #111827;
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
        }

        .listing-details__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #fff !important;
            font-weight: 700;
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.18);
        }

        .listing-details__cta--inline {
            min-width: 180px;
            padding-left: 22px;
            padding-right: 22px;
        }

        .listing-related {
            padding: 10px;
            border-radius: 14px;
            background: #f8fafc;
            transition: transform .2s ease, background .2s ease;
        }

        .listing-related:hover {
            background: #eef4ff;
            transform: translateY(-1px);
        }

        .listing-related__thumb {
            width: 72px;
            height: 72px;
            background: #e5e7eb;
        }

        .listing-related__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 991px) {
            .listing-hero__media,
            .listing-hero__empty {
                min-height: 320px;
            }

            .listing-hero__panel {
                min-height: auto;
            }

            .listing-sidebar {
                position: static !important;
            }
        }
    </style>
@endpush
