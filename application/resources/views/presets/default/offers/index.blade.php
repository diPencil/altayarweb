@extends($activeTemplate . 'layouts.frontend')

@php
    $op = trans('offers_pages.' . $offerPageKey);
    $ui = trans('offers_ui');
    $docLang = is_rtl() ? 'ar' : 'en';
    $docDir = is_rtl() ? 'rtl' : 'ltr';
    $navCategories = [
        ['slug' => 'limited', 'label' => 'all_short'],
        ['slug' => 'yearly', 'label' => 'yearly'],
        ['slug' => 'weekend', 'label' => 'weekend'],
        ['slug' => 'spa-beauty', 'label' => 'spa'],
        ['slug' => 'coupons', 'label' => 'coupons'],
        ['slug' => 'vouchers', 'label' => 'vouchers'],
    ];
    $customPills = '<div class="offers-hub__filters offers-hub__filters--pillnav d-flex justify-content-center gap-2 mb-0 pb-1 px-1 px-md-2 flex-wrap" role="navigation">';
    foreach ($navCategories as $cat) {
        $isActive = $categorySlug === ($cat['slug'] === 'spa-beauty' ? 'spa' : $cat['slug']);
        $customPills .= '<a href="'.route('public.offers.index', ['category' => $cat['slug']]).'" class="offers-hub__chip btn rounded-pill btn-md '.($isActive ? 'btn--base' : 'btn-outline-dark bg-white').'">'.__('offers_nav.' . $cat['label']).'</a>';
    }
    $customPills .= '</div>';
    $heroImage = $heroBannerListing && $heroBannerListing->image
        ? getImage(getFilePath('listingImage') . '/' . $heroBannerListing->image)
        : null;
    $ratingFor = static function ($id): string {
        return number_format(4.4 + ($id % 6) * 0.1, 1);
    };
    $filterAction = route('public.offers.index', ['category' => $categorySlug]);
    $heroSpotlight = $featuredOffers->first();
    $heroThumbOffers = $featuredOffers->count() > 1 ? $featuredOffers->slice(1, 4) : collect();
@endphp

@section('content')
    @include($activeTemplate . 'components.hero', [
        'heroTitle' => $op['hero_kicker'] ?? __('Limited Offers'),
        'heroHeading' => $op['hero_title'] ?? __('Travel to make sweet memories'),
        'heroSubHeading' => $op['hero_lead'] ?? __('Seasonal bundles, weekend escapes, spa moments, coupons, and vouchers — curated in one calm, booking-ready place.'),
        'customForm' => $customPills,
    ])

    <article class="offers-hub position-relative section--bg" lang="{{ $docLang }}" dir="{{ $docDir }}">
        @if(false)
        <section class="offers-hub__hero offers-hub__hero--ref pb-80 py-lg-100">
            <div class="container">
                <div class="row align-items-center g-4 g-xl-5 offers-hub__hero-row">
                    <div class="col-lg-6 offers-hub__hero-copy text-start">
                        <div class="offers-hub__hero-copy-inner">
                            <span class="offers-hub__kicker">{{ $op['hero_kicker'] }}</span>
                            <h1 class="offers-hub__title" dir="auto">{{ $op['hero_title'] }}</h1>
                            <p class="offers-hub__lead text-muted" dir="auto">{{ $op['hero_lead'] }}</p>

                            <ol class="offers-hub__steps list-unstyled mb-4 pb-lg-1">
                                @foreach (['1' => ['title' => 'step1_title', 'text' => 'step1_text'], '2' => ['title' => 'step2_title', 'text' => 'step2_text'], '3' => ['title' => 'step3_title', 'text' => 'step3_text']] as $num => $keys)
                                    <li class="offers-hub__step d-flex gap-3 align-items-start">
                                        <span class="offers-hub__step-num flex-shrink-0">{{ str_pad((string) $num, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div class="offers-hub__step-body" dir="auto">
                                            <strong class="d-block offers-hub__step-title">{{ $op[$keys['title']] }}</strong>
                                            <p class="mb-0 offers-hub__step-text">{{ $op[$keys['text']] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>

                            <a href="#offers-explore"
                                class="btn btn-lg offers-hub__cta rounded-pill">{{ $op['hero_cta'] }}</a>
                        </div>
                    </div>

                    <div class="col-lg-6 offers-hub__hero-visual">
                        <div class="offers-hub__hero-stack position-relative">
                            <div class="offers-hub__hero-frame offers-hub__hero-photo-card position-relative mx-lg-0 mx-auto">
                                <div class="offers-hub__hero-img-shell position-relative overflow-hidden">
                                    <img src="{{ asset('assets/presets/default/images/Live-travel-control-center.jpg') }}" alt="{{ $ui['banner_alt'] }}"
                                        class="offers-hub__hero-img w-100" loading="eager" fetchpriority="high"
                                        width="800" height="1060" decoding="async">
                                    <div class="offers-hub__hero-overlay" aria-hidden="true"></div>
                                    <div class="offers-hub__hero-vignette" aria-hidden="true"></div>
                                    <div class="offers-hub__hero-badge">
                                        <span class="offers-hub__floating-pill">{{ $ui['featured_for_you'] }}</span>
                                    </div>



                                    </div>
                                </div>

                                    @if ($heroSpotlight)
                                        @php
                                            $spotImg =
                                                $heroSpotlight->image
                                                    ? getImage(
                                                        getFilePath('listingImage') . '/' . $heroSpotlight->image,
                                                    )
                                                    : null;
                                        @endphp
                                        <div class="offers-hub__hero-spotlight offers-hub__hero-spotlight--on-card">
                                            <a href="{{ route('listing.details', [slug($heroSpotlight->title), $heroSpotlight->id]) }}"
                                                class="travel-card-mini travel-card-mini--spotlight text-decoration-none text-body d-block">
                                                <div class="travel-card-mini__visual travel-card-mini__visual--spotlight">
                                                    @if ($spotImg)
                                                        <img src="{{ $spotImg }}" alt="" loading="lazy" width="360"
                                                            height="200">
                                                    @endif
                                                </div>
                                                <div class="travel-card-mini__content travel-card-mini__content--spotlight">
                                                    <h4 class="travel-card-mini__title" dir="auto">
                                                        {{ __($heroSpotlight->title) }}</h4>
                                                    <div class="travel-card-mini__meta">
                                                        <div class="travel-card-mini__loc">
                                                            <i class="las la-map-marker" aria-hidden="true"></i>
                                                            <span
                                                                dir="auto">{{ __($heroSpotlight->city ?? $heroSpotlight->country ?? '—') }}</span>
                                                        </div>
                                                        <div class="travel-card-mini__rating travel-card-mini__rating--mock"
                                                            dir="ltr">
                                                            <i class="las la-star travel-card-mini__rating-star-icon"></i>
                                                            <span
                                                                class="travel-card-mini__rating-val">({{ $ratingFor($heroSpotlight->id) }})</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if ($heroThumbOffers->isNotEmpty())
                                        <div class="offers-hub__hero-thumbs offers-hub__hero-thumbs--strip d-none">
                                            @foreach ($heroThumbOffers as $thumbOffer)
                                                @php
                                                    $tImg =
                                                        $thumbOffer->image
                                                            ? getImage(
                                                                getFilePath('listingImage') . '/' . $thumbOffer->image,
                                                            )
                                                            : null;
                                                @endphp
                                                <a href="{{ route('listing.details', [slug($thumbOffer->title), $thumbOffer->id]) }}"
                                                    class="offers-hub__hero-thumb rounded-3 overflow-hidden text-decoration-none"
                                                    aria-label="{{ __($thumbOffer->title) }}">
                                                    @if ($tImg)
                                                        <img src="{{ $tImg }}" alt=""
                                                            class="offers-hub__hero-thumb-img w-100 h-100" loading="lazy">
                                                    @else
                                                        <span
                                                            class="offers-hub__hero-thumb-fallback"><i class="las la-image"></i></span>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if (false)
                                    <div class="offers-hub__hero-placeholder d-flex align-items-center justify-content-center">
                                        <i class="las la-image la-3x text-white-50"></i>
                                    </div>
                                @endif
                            </div>

                            @if (false)
                                {{-- Without hero photo, surface spotlight inside frame area --}}
                                @php
                                    $spotImg =
                                        $heroSpotlight->image
                                            ? getImage(getFilePath('listingImage') . '/' . $heroSpotlight->image)
                                            : null;
                                @endphp
                                <div class="offers-hub__hero-spotlight offers-hub__hero-spotlight--only">
                                    <a href="{{ route('listing.details', [slug($heroSpotlight->title), $heroSpotlight->id]) }}"
                                        class="travel-card-mini travel-card-mini--spotlight text-decoration-none text-body d-block w-100">
                                        <div class="travel-card-mini__visual travel-card-mini__visual--spotlight">
                                            @if ($spotImg)
                                                <img src="{{ $spotImg }}" alt="" loading="lazy">
                                            @endif
                                        </div>
                                        <div class="travel-card-mini__content travel-card-mini__content--spotlight">
                                            <h4 class="travel-card-mini__title" dir="auto">{{ __($heroSpotlight->title) }}
                                            </h4>
                                            <div class="travel-card-mini__meta">
                                                <div class="travel-card-mini__loc">
                                                    <i class="las la-map-marker" aria-hidden="true"></i>
                                                    <span
                                                        dir="auto">{{ __($heroSpotlight->city ?? $heroSpotlight->country ?? '—') }}</span>
                                                </div>
                                                <div class="travel-card-mini__rating travel-card-mini__rating--mock"
                                                    dir="ltr">
                                                    <i class="las la-star travel-card-mini__rating-star-icon"></i>
                                                    <span class="travel-card-mini__rating-val">({{ $ratingFor($heroSpotlight->id) }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @endif
        <section class="offers-hub__explore offers-hub__saas-surface pb-80 pb-lg-100 position-relative pt-40"
            id="offers-explore">
            <div class="container">

                @if(!empty($hubData))
                    {{-- Hub Sections View --}}
                    @foreach($hubData as $key => $section)
                        <div class="offers-hub__section mb-5">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <div class="section-header mb-0 text-start">
                                    <h3 class="section-header__title mb-2" style="font-size: 1.75rem; font-weight: 800;">{{ __($section['title']) }}</h3>
                                    <p class="section-header__desc text-muted mb-0">{{ __('Discover our curated selection of :title for your next trip.', ['title' => strtolower(__($section['title']))]) }}</p>
                                </div>
                                <a href="{{ route('public.offers.index', ['category' => $section['slug']]) }}" class="btn btn-outline-dark rounded-pill btn-sm px-4 fw-bold">
                                    {{ __('View All') }} <i class="las la-arrow-right ms-1"></i>
                                </a>
                            </div>

                            <div class="row g-4">
                                @forelse ($section['items'] as $offer)
                                    @php
                                        $offerDiscountAmt = (float) ($offer->discount ?? 0);
                                        $originalPrice = (float) ($offer->price ?? 0);
                                        $finalPrice = $offerDiscountAmt > 0 ? max($originalPrice - $offerDiscountAmt, 0) : $originalPrice;
                                        $loc = trim(implode(', ', array_filter([$offer->city, $offer->country])));
                                        $pctOff = $originalPrice > 0 && $offerDiscountAmt > 0 ? (int) round(100 * ($offerDiscountAmt / $originalPrice)) : null;
                                        $detailsUrl = route('listing.details', [slug($offer->title), $offer->id]);
                                        $ratingDisplay = $ratingFor($offer->id);
                                    @endphp
                                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                        @include($activeTemplate . 'offers.partials.travel-offer-card', [
                                            'offer' => $offer,
                                            'general' => $general,
                                            'ui' => $ui,
                                            'loc' => $loc,
                                            'pctOff' => $pctOff,
                                            'finalPrice' => $finalPrice,
                                            'originalPrice' => $originalPrice,
                                            'offerDiscountAmt' => $offerDiscountAmt,
                                            'detailsUrl' => $detailsUrl,
                                            'ratingDisplay' => $ratingDisplay,
                                            'perPersonLabel' => $op['per_person'],
                                        ])
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-4 bg-light rounded-4">
                                        <p class="mb-0 small">{{ __('No :title available at the moment.', ['title' => strtolower(__($section['title']))]) }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Standard Grid View (Filtered or Specific Category) --}}
                    <div class="row g-4 offers-hub__grid">
                        @forelse ($offers as $offer)
                            @php
                                $offerDiscountAmt = (float) ($offer->discount ?? 0);
                                $originalPrice = (float) ($offer->price ?? 0);
                                $finalPrice =
                                    $offerDiscountAmt > 0
                                        ? max($originalPrice - $offerDiscountAmt, 0)
                                        : $originalPrice;
                                $loc = trim(implode(', ', array_filter([$offer->city, $offer->country])));
                                $pctOff =
                                    $originalPrice > 0 && $offerDiscountAmt > 0
                                        ? (int) round(100 * ($offerDiscountAmt / $originalPrice))
                                        : null;
                                $detailsUrl = route('listing.details', [slug($offer->title), $offer->id]);
                                $ratingDisplay = $ratingFor($offer->id);
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                @include($activeTemplate . 'offers.partials.travel-offer-card', [
                                    'offer' => $offer,
                                    'general' => $general,
                                    'ui' => $ui,
                                    'loc' => $loc,
                                    'pctOff' => $pctOff,
                                    'finalPrice' => $finalPrice,
                                    'originalPrice' => $originalPrice,
                                    'offerDiscountAmt' => $offerDiscountAmt,
                                    'detailsUrl' => $detailsUrl,
                                    'ratingDisplay' => $ratingDisplay,
                                    'perPersonLabel' => $op['per_person'],
                                ])
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-5">
                                <p class="mb-2 fs-18 fw-semibold">{{ $ui['no_offers_title'] }}</p>
                                <p class="mb-0 small">{{ $ui['no_offers_hint'] }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if (method_exists($offers, 'hasPages') && $offers->hasPages())
                        <div class="text-center mt-5">
                            {{ $offers->links() }}
                        </div>
                    @endif
                @endif

            </div>
        </section>

        {{-- Highlights strip --}}
        @if ($offerHighlights->isNotEmpty())
            <section class="offers-hub__highlights py-80 bg--white border-top border-bottom">
                <div class="container">
                    <div class="text-center mb-4 px-lg-5">
                        <h2 class="offers-hub__h2 mb-2">{{ $op['adventure_title'] }}</h2>
                        <p class="text-muted mb-0">{{ $op['adventure_subtitle'] }}</p>
                    </div>
                    <div class="row g-3 g-md-4 justify-content-center offers-hub__strip">
                        @foreach ($offerHighlights->take(6) as $h)
                            @php
                                $hImg = $h->image ? getImage(getFilePath('listingImage') . '/' . $h->image) : null;
                            @endphp
                            <div class="col-6 col-md-4 col-lg-2">
                                <a href="{{ route('listing.details', [slug($h->title), $h->id]) }}"
                                    class="offers-hub__mini text-decoration-none d-block text-center">
                                    <div
                                        class="offers-hub__mini-visual position-relative mx-auto rounded-4 overflow-hidden shadow-sm">
                                        @if ($hImg)
                                            <img src="{{ $hImg }}" alt="" class="w-100 h-100 object-fit-cover"
                                                loading="lazy" width="200" height="240">
                                        @else
                                            <div
                                                class="offers-hub__mini-placeholder d-flex align-items-center justify-content-center">
                                                <i class="las la-mountain text-white-50"></i>
                                            </div>
                                        @endif
                                        <span class="offers-hub__mini-tag"><i class="las la-compass"></i></span>
                                    </div>
                                    <span
                                        class="offers-hub__mini-label d-block mt-2 fw-bold text-uppercase small">{{ __(strLimit($h->title, 22)) }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
@endsection

@push('style')
    <style>
        .offers-hub {
            --offers-accent: hsl(var(--base));
            --offers-accent-soft: hsl(var(--base) / 0.12);
            --offers-dark: #0f172a;
        }

        .offers-hub__hero-visual {
            position: relative;
        }

        @media (min-width: 992px) {
            .offers-hub__hero-visual {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            [dir='rtl'] .offers-hub__hero-visual {
                justify-content: flex-start;
            }
        }

        .offers-hub__hero-stack {
            position: relative;
            padding-block-end: clamp(2.75rem, 7vw, 4.75rem);
            z-index: 10;
        }

        .offers-hub__hero--ref {
            position: relative;
            overflow: visible;
            background: #ffffff;
        }

        .offers-hub__hero-photo-card {
            z-index: 1;
            isolation: isolate;
            max-width: min(540px, 100%);
            box-shadow: none;
            overflow: visible;
            background: transparent;
            margin-inline-end: -2.5rem;
        }

        @media (max-width: 991px) {
            .offers-hub__hero-photo-card {
                margin-inline-end: 0;
            }
        }

        .offers-hub__hero-img-shell {
            border-radius: 36px;
            isolation: isolate;
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: none;
            width: fit-content;
        }

        /* Readable scrim: keeps photo vivid; darker toward bottom for badge + mini-card contrast */
        .offers-hub__hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            border-radius: inherit;
            background: linear-gradient(
                185deg,
                rgba(15, 23, 42, 0.18) 0%,
                rgba(15, 23, 42, 0.05) 42%,
                rgba(15, 23, 42, 0.22) 78%,
                rgba(15, 23, 42, 0.48) 100%
            );
        }

         .offers-hub__hero-badge {
            position: absolute;
            top: 1.25rem;
            left: 50%;
            z-index: 5;
            transform: translateX(-50%);
        };
            font-size: 1.25rem;
        }

        .rating-floating-card__label {
            display: block;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.05em;
            line-height: 1;
            margin-bottom: 2px;
        }

        .rating-floating-card__stars {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rating-floating-card__val {
            font-size: 0.9rem;
            font-weight: 800;
            color: #1e293b;
        }

        .rating-floating-card__stars-list {
            display: flex;
            gap: 1px;
            color: #fcc419;
            font-size: 0.7rem;
        }offers-hub__hero-vignette {
            position: absolute;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            border-radius: inherit;
            background:
                radial-gradient(ellipse 125% 92% at 50% 112%, rgba(15, 23, 42, 0.42) 0%, transparent 54%),
                radial-gradient(ellipse 78% 68% at 12% 10%, rgba(15, 23, 42, 0.2) 0%, transparent 50%);
        }

        .offers-hub__hero-badge {
            position: absolute;
            top: 1.25rem;
            left: 50%;
            z-index: 5;
            transform: translateX(-50%);
        }

        .offers-hub__floating-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.62rem 1.35rem;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            background: #ffffff !important;
            color: #111827 !important;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: none;
            backdrop-filter: none;
        }

        /* Floating mini-card — overlaps bottom corner of main photo (mirrored RTL) */
        .offers-hub__hero-spotlight--on-card {
            position: absolute;
            z-index: 35;
            width: min(300px, 88%);
            inset-inline-start: 0;
            bottom: -1.15rem;
            transform: translateX(calc(-1 * min(44%, 158px)));
        }

        [dir='rtl'] .offers-hub__hero-spotlight--on-card {
            inset-inline-start: auto;
            inset-inline-end: 0;
            transform: translateX(min(44%, 158px));
        }

        /* Fallback only when spotlight sits outside photo layout */
        .offers-hub__hero-spotlight:not(.offers-hub__hero-spotlight--on-card):not(.offers-hub__hero-spotlight--only) {
            position: absolute;
            z-index: 22;
            width: min(304px, 94%);
            bottom: clamp(1.05rem, 3.5vw, 2.5rem);
            inset-inline-start: clamp(-246px, -19vw, -34px);
        }

        [dir='rtl'] .offers-hub__hero-spotlight:not(.offers-hub__hero-spotlight--on-card):not(.offers-hub__hero-spotlight--only) {
            inset-inline-start: auto;
            inset-inline-end: clamp(-246px, -19vw, -34px);
        }

        .offers-hub__hero-thumbs--strip {
            position: absolute;
            bottom: 0.92rem;
            inset-inline-end: 0.92rem;
            display: inline-flex;
            gap: 0.42rem;
            z-index: 6;
            padding: 0.4rem 0.52rem;
            border-radius: 999px;
            background: hsl(217 91% 8% / 0.4);
            backdrop-filter: blur(12px);
            box-shadow: none;
        }

        [dir='rtl'] .offers-hub__hero-thumbs--strip {
            inset-inline-start: auto;
            inset-inline-end: 0.92rem;
            flex-direction: row-reverse;
        }

        .offers-hub__hero-thumb {
            width: 64px;
            height: 64px;
            flex-shrink: 0;
            border: 2px solid hsl(210 40% 98.8% / 0.94);
            box-shadow: none;
            transition: transform 0.2s ease;
        }

        .offers-hub__hero-thumb:hover {
            transform: translateY(-3px);
            z-index: 2;
        }

        .offers-hub__hero-thumb-img {
            object-fit: cover;
            border-radius: 13px !important;
        }

        .offers-hub__hero-thumb-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: hsl(217 91% 8% / 0.62);
            color: hsl(214 31% 91%);
            font-size: 1.46rem;
        }

        .offers-hub__hero-spotlight--only {
            position: relative;
            inset-inline: auto auto;
            inset-inline-start: revert;
            width: min(356px, 100%);
            margin-inline: auto;
            margin-block-start: -2.85rem;
            bottom: revert;
        }

        .travel-card-mini--spotlight {
            width: 100%;
            max-width: 308px;
            padding: 0.82rem !important;
            border-radius: 24px !important;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
            background: #fff !important;
        }

        .travel-card-mini--spotlight:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.22) !important;
        }

        .travel-card-mini__visual--spotlight {
            height: 148px !important;
            border-radius: 18px !important;
        }

        .travel-card-mini__visual--spotlight img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
        }

        .travel-card-mini__content--spotlight {
            padding-inline: 0.35rem 1rem !important;
            padding-block-end: 1.05rem !important;
            padding-block-start: 0.45rem !important;
        }

        .travel-card-mini--spotlight .travel-card-mini__title {
            font-size: 1.0625rem;
            margin-bottom: 0.45rem;
        }

        .travel-card-mini--spotlight .travel-card-mini__loc {
            font-size: 0.8125rem;
            color: #64748b;
        }

        .travel-card-mini--spotlight .travel-card-mini__rating {
            font-size: 0.8125rem;
        }

        .offers-hub__hero .container {
            overflow: visible;
        }

        .offers-hub__hero-row {
            overflow: visible;
            flex-direction: row;
        }

        [dir='rtl'] .offers-hub__hero-row {
            flex-direction: row-reverse;
        }

        .offers-hub__hero-copy-inner {
            max-width: 34.5rem;
        }

        @media (min-width: 992px) {
            .offers-hub__hero-copy {
                padding-inline-end: clamp(0.5rem, 3vw, 2.25rem);
            }
        }

        .offers-hub__kicker {
            display: inline-block;
            font-size: 0.8125rem;
            font-weight: 800;
            letter-spacing: 0.11em;
            text-transform: uppercase;
            color: hsl(var(--base));
            margin-bottom: 0.85rem;
        }

        .offers-hub__title {
            font-family: var(--heading-font);
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.03em;
            color: var(--offers-dark);
            margin-bottom: 1.05rem;
        }

        .offers-hub__lead {
            font-size: 1.0625rem;
            line-height: 1.7;
            max-width: 36rem;
            color: #64748b;
        }

        .offers-hub__steps {
            max-width: 36rem;
        }

        .offers-hub__step {
            margin-bottom: 1.28rem;
        }

        .offers-hub__step-num {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            background: hsl(var(--base) / 0.14);
            color: hsl(var(--base));
            font-weight: 800;
            font-size: 0.8125rem;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid hsl(var(--base) / 0.15);
            box-shadow: none;
        }

        .offers-hub__step-title {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--offers-dark);
            margin-bottom: 0.2rem;
        }

        .offers-hub__step-text {
            font-size: 0.9375rem;
            line-height: 1.55;
            color: #64748b;
        }

        .offers-hub__cta {
            padding: 0.92rem 2.15rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            border: none !important;
            color: #fff !important;
            background: linear-gradient(
                118deg,
                hsl(var(--base)) 0%,
                hsl(var(--base) / 0.92) 48%,
                hsl(192 78% 42%) 100%
            ) !important;
            box-shadow: none;
            transition: transform 0.22s ease, filter 0.22s ease;
        }

        .offers-hub__cta:hover {
            color: #fff !important;
            transform: translateY(-2px);
            filter: brightness(1.04);
            box-shadow: none;
        }

        .offers-hub__cta:focus-visible {
            outline: 3px solid hsl(var(--base) / 0.45);
            outline-offset: 3px;
        }

        .offers-hub__hero-img {
            display: block;
            width: 100%;
            max-width: 100%;
            object-fit: cover;
            object-position: center center;
            filter: saturate(1.05) contrast(1.02);
            border-radius: 0 !important;
        }

        .offers-hub__hero-placeholder {
            min-height: 360px;
            aspect-ratio: 16 / 10;
            background: radial-gradient(circle at 30% 28%, hsl(var(--base) / 0.25), transparent 62%),
                linear-gradient(135deg, hsl(215 24% 26%) 0%, hsl(217 36% 11%) 100%);
        }
        .travel-card-mini {
            background: #ffffff;
            border-radius: 20px;
            padding: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            width: min(190px, 100%);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .travel-card-mini:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
        }

        .travel-card-mini__visual {
            width: 100%;
            height: 118px;
            border-radius: 16px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .travel-card-mini__visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .travel-card-mini__content {
            padding: 0 8px 10px;
        }

        .travel-card-mini__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
            font-family: var(--heading-font);
            letter-spacing: -0.02em;
        }

        .travel-card-mini__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .travel-card-mini__loc {
            font-size: 0.65rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .travel-card-mini__loc i {
            font-size: 0.8rem;
        }

        .travel-card-mini__rating {
            font-size: 0.75rem;
            font-weight: 700;
            color: #111827;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .travel-card-mini__rating i {
            color: #fbbf24;
            font-size: 0.85rem;
        }

        .max-width-600 {
            max-width: 600px;
        }

.offers-filter-bar {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
}

.offers-filter-bar__label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    margin-bottom: 0.25rem;
}

.offers-filter-bar__control {
    min-height: 48px;
    border-radius: 999px !important;
    border: 1px solid #e2e8f0 !important;
    background-color: #ffffff !important;
    font-size: 0.875rem;
    color: #475569;
    transition: all 0.2s ease;
}

.offers-filter-bar__control:hover {
    border-color: #cbd5e1 !important;
}

.offers-filter-bar__control:focus {
    background-color: #fff !important;
    border-color: var(--offers-accent) !important;
    box-shadow: 0 0 0 4px var(--offers-accent-soft) !important;
}

        .offers-hub__chip {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
        }

        .offers-hub__chip.btn-outline-dark:hover {
            border-color: var(--offers-accent);
            color: var(--offers-accent);
            background: transparent;
        }

        .offers-hub__filters--pillnav {
            overflow-x: auto;
            flex-wrap: nowrap;
            scrollbar-width: thin;
        }

        @media (min-width: 992px) {
            .offers-hub__filters--pillnav {
                flex-wrap: wrap;
                overflow-x: visible;
                justify-content: center;
            }
        }

        .offers-hub__h2 {
            font-family: var(--heading-font);
            font-weight: 800;
            font-size: clamp(1.35rem, 2.2vw, 1.85rem);
            color: var(--offers-dark);
        }

        .offers-hub__saas-surface {
            background-color: #f8f9fb;
        }

        @include($activeTemplate . 'partials.css.travel-premium-card')

        /* Legacy selectors — keep hover for non-saas grids if reused */

        .btn-soft {
            background: rgba(15, 23, 42, 0.04);
            color: #0f172a;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .btn-soft:hover {
            background: rgba(15, 23, 42, 0.08);
        }

        .offers-hub__mini-visual {
            aspect-ratio: 3 / 4;
            max-width: 160px;
            background: #e2e8f0;
        }

        .offers-hub__mini-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #334155, #0f172a);
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .offers-hub__mini-tag {
            position: absolute;
            top: 10px;
            inset-inline-start: auto;
            inset-inline-end: 10px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--offers-accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.2);
        }

        .offers-hub__mini-label {
            color: var(--offers-dark);
            letter-spacing: 0.04em;
        }

        .offers-hub__mini {
            color: inherit;
        }

        .offers-hub__mini:hover .offers-hub__mini-label {
            color: var(--offers-accent);
        }

        .pagination {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        @media (max-width: 991px) {
            .offers-hub__hero-spotlight:not(.offers-hub__hero-spotlight--only):not(.offers-hub__hero-spotlight--on-card) {
                position: relative;
                inset-inline-start: auto;
                inset-inline-end: auto;
                width: min(356px, 100%);
                margin-inline: auto;
                margin-block-start: -2rem;
                bottom: auto;
            }

            .offers-hub__hero-spotlight--on-card {
                position: relative;
                inset-inline: auto;
                inset-inline-start: auto;
                inset-inline-end: auto;
                transform: none;
                bottom: auto;
                width: min(320px, 100%);
                margin-inline: auto;
                margin-block-start: -2.35rem;
            }

            .travel-card-mini--spotlight {
                max-width: none;
            }

            .offers-hub__hero-img {
                min-height: clamp(280px, 58vw, 420px);
                width: 100%;
                height: auto;
                aspect-ratio: auto;
            }
        }

        @media (max-width: 575px) {
            .offers-hub__hero-frame--rich {
                min-height: 0;
                border-radius: 20px !important;
            }

            .offers-hub__hero-thumbs--strip {
                display: flex;
                inset-inline-start: auto;
                inset-inline-end: 50%;
                transform: translateX(50%);
            }

            [dir='rtl'] .offers-hub__hero-thumbs--strip {
                transform: translateX(-50%);
            }
        }

        /* Global chrome on this template: scroll-to-top — flat on offers pages */
        .scroll-top {
            box-shadow: none !important;
            filter: none !important;
        }

        /*
            Theme breadcrumb adds large blue decorative images (shape-1 / whychoose-bg2).
            Scoped here because this push only loads on offers pages.
        */
        .breadcrumb .bg--thumb-one,
        .breadcrumb .bg--thumb-two {
            display: none !important;
        }

        .breadcrumb {
            background: #ffffff !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        }
    </style>
@endpush
