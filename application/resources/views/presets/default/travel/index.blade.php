@extends($activeTemplate . 'layouts.frontend')

@php
    $tp = trans('travel_pages.' . $travelPageKey);
    $tshared = trans('travel_pages.shared');
    $cardUi = trans('offers_ui');
    $destinationCountryLabels = [
        'Singapore' => ['en' => 'Singapore', 'ar' => 'سنغافورة'],
        'Thailand' => ['en' => 'Thailand', 'ar' => 'تايلاند'],
        'USA' => ['en' => 'USA', 'ar' => 'الولايات المتحدة'],
        'Egypt' => ['en' => 'Egypt', 'ar' => 'مصر'],
        'Saudi Arabia' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية'],
        'UAE' => ['en' => 'UAE', 'ar' => 'الإمارات العربية المتحدة'],
    ];
    $destinationCountryOrder = ['Singapore', 'Thailand', 'USA', 'Egypt', 'Saudi Arabia', 'UAE'];
    $fmtTravelStat = static function (int $n): string {
        if ($n >= 1_000_000) {
            return round($n / 1_000_000, 1) . 'M+';
        }
        if ($n >= 1_000) {
            return round($n / 1_000, 1) . 'K+';
        }

        return (string) max(0, $n) . '+';
    };
    $docLang = is_rtl() ? 'ar' : 'en';
    $docDir = is_rtl() ? 'rtl' : 'ltr';
    $ctaPrimaryHref =
        $currentSection === 'overview'
            ? route('public.travel.index', ['section' => $tp['hero_cta_primary_url_section']])
            : '#travel-explore';
    $hubRate = static fn ($id) => number_format(4.5 + ($id % 6) * 0.08, 1);
    $ratingForHero = static fn ($id): string => number_format(4.4 + ($id % 6) * 0.1, 1);

    $heroImg = null;
    if (in_array($currentSection, ['hotels', 'flights'], true) && $heroSpotlightListing && $heroSpotlightListing->image) {
        $heroImg = getImage(getFilePath('listingImage') . '/' . $heroSpotlightListing->image);
    } elseif ($heroSpotlightPackage && $heroSpotlightPackage->TourPackagePrimaryImage?->image) {
        $heroImg = getImage(getFilePath('tourPackageImage') . '/' . $heroSpotlightPackage->TourPackagePrimaryImage->image);
    }
@endphp

@section('content')
    @php
        $docLang = is_rtl() ? 'ar' : 'en';
        $docDir = is_rtl() ? 'rtl' : 'ltr';
    @endphp

    @php
        $travelFilterAction =
            $currentSection === 'overview'
                ? route('public.travel.index', ['section' => 'packages'])
                : route('public.travel.index', ['section' => $currentSection]);

        $customPills = '<div class="offers-hub__filters offers-hub__filters--pillnav d-flex justify-content-center gap-2 mb-0 pb-1 px-1 px-md-2 flex-wrap" role="navigation">
            <a href="'.route('public.travel.index').'" class="offers-hub__chip btn rounded-pill btn-md '.($currentSection === 'overview' ? 'btn--base' : 'btn-outline-dark bg-white').'">'.__('Overview').'</a>';
        foreach ($travelSections as $slug => $section) {
            $customPills .= '<a href="'.route('public.travel.index', ['section' => $slug]).'" class="offers-hub__chip btn rounded-pill btn-md '.($currentSection === $slug ? 'btn--base' : 'btn-outline-dark bg-white').'">'.__($section['label']).'</a>';
        }
        $customPills .= '</div>';
    @endphp

    @include($activeTemplate . 'components.hero', [
        'heroTitle' => $tp['hero_kicker'] ?? __('Travel Center'),
        'heroHeading' => $tp['hero_title'] ?? __('Explore More, Travel Smart!'),
        'heroSubHeading' => $tp['hero_lead'] ?? __('Best deals for your next journey.'),
        'customForm' => $customPills
    ])
    <article class="travel-hub travel-hub--saas offers-hub section--bg position-relative" lang="{{ $docLang }}" dir="{{ $docDir }}">
        
        @if($currentSection === 'overview')
        {{-- Existing Card-Based Hero for Overview removed to match request --}}
        @else
        {{-- Full Immersive Hero for Specific Sections removed to match request --}}
        @endif



        {{-- Section pills + filters (matches Limited Offers hub) --}}
        <div class="container py-4">
        </div>

        @php
            $travelFilterAction =
                $currentSection === 'overview'
                    ? route('public.travel.index', ['section' => 'packages'])
                    : route('public.travel.index', ['section' => $currentSection]);
        @endphp

        <section class="offers-hub__explore offers-hub__saas-surface pb-80 pb-lg-100 position-relative pt-40"
            id="travel-explore">
            <div class="container">

                @if (in_array($currentSection, ['overview', 'packages'], true))
                <div class="travel-hub__section-head text-start mb-4">
                    <p class="travel-hub__section-eyebrow mb-2">{{ $tshared['catalogue_eyebrow'] }}</p>
                    <h3 class="offers-hub__h2 mb-2">{{ $tp['iconic_title'] }}</h3>
                    <p class="travel-hub__section-lead text-muted small mb-0">{{ $tp['iconic_subtitle'] }}</p>
                </div>
                @endif

                @if ($currentSection === 'overview')
                    @php
                        $overviewPackages =
                            $featuredPackages->count() > 3
                                ? $featuredPackages->skip(3)->take(6)->values()
                                : $featuredPackages->take(6)->values();
                    @endphp
                    <div class="row g-4 mb-5 offers-hub__grid">
                        @foreach ($overviewPackages as $pkg)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                @include($activeTemplate . 'travel.partials.iconic-tour-card', [
                                    'pkg' => $pkg,
                                    'tp' => $tp,
                                    'hubRate' => $hubRate,
                                    'cardUi' => $cardUi,
                                ])
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mb-5 pb-5 border-bottom">
                        <a href="{{ route('public.travel.index', ['section' => 'packages']) }}" class="btn btn-outline--base rounded-pill px-5 fw-bold">@lang('View All Tour Packages') <i class="las la-arrow-right ms-1"></i></a>
                    </div>

                    {{-- Destinations Section --}}
                    <div class="travel-hub__section-head text-start mb-4">
                        <h3 class="offers-hub__h2 mb-2">@lang('Iconic Destinations')</h3>
                        <p class="travel-hub__section-lead text-muted small mb-0">@lang('Breathtaking locations curated for the modern traveler.')</p>
                    </div>
                    <div class="location--card__wrap d-flex gap--20 flex-wrap mb-4">
                        @foreach ($locations->take(4) as $item)
                            <a href="{{ route('browse', ['location' => $item->name]) }}"
                                class="location__card radius--20 overflow-hidden position-relative" style="width: calc(25% - 15px); min-width: 250px; height: 350px;">
                                <div class="location__card-thumb position-relative w--100 h--100">
                                    <img class="fit--img" src="{{ getImage(getFilePath('location') . '/' . $item->image) }}"
                                        alt="image">
                                </div>
                                <div class="location__card-content position-absolute w--100 d-flex justify-content-between align-items-center" >
                                    <div class="content">
                                        <h6 class="title text--white fs--20 mb-1">
                                            <i class="fa-solid fa-location-dot"></i>
                                            {{ __($item->name) }}
                                        </h6>
                                        <p class="text--white mb-0 small" style="opacity: 0.8; font-size: 12px;">{{ $item->location }}</p>
                                    </div>
                                    <span class="btn circle"><i class="fa-solid fa-arrow-up-long"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="text-center mb-5 pb-5 border-bottom">
                        <a href="{{ route('public.travel.index', ['section' => 'destinations']) }}" class="btn btn-outline--base rounded-pill px-5 fw-bold">@lang('Explore All Destinations') <i class="las la-arrow-right ms-1"></i></a>
                    </div>

                    {{-- Hotels Section --}}
                    <div class="travel-hub__section-head text-start mb-4">
                        <h3 class="offers-hub__h2 mb-2">@lang('Luxury 5-Star Hotels')</h3>
                        <p class="travel-hub__section-lead text-muted small mb-0">@lang('Experience world-class hospitality in our handpicked luxury collection.')</p>
                    </div>
                    <div class="mb-4">
                        @include($activeTemplate . 'travel.sections.hotels')
                    </div>
                    <div class="text-center mb-5 pb-5 border-bottom">
                        <a href="{{ route('public.travel.index', ['section' => 'hotels']) }}" class="btn btn-outline--base rounded-pill px-5 fw-bold">@lang('Browse Luxury Hotels') <i class="las la-arrow-right ms-1"></i></a>
                    </div>

                    {{-- Flights Section --}}
                    <div class="travel-hub__section-head text-start mb-4">
                        <h3 class="offers-hub__h2 mb-2">@lang('Premium Airline Partners')</h3>
                        <p class="travel-hub__section-lead text-muted small mb-0">@lang('Fly with confidence with our globally recognized airline partners.')</p>
                    </div>
                    <div class="mb-4">
                        @include($activeTemplate . 'travel.sections.flights')
                    </div>
                    <div class="text-center mb-5">
                        <a href="{{ route('public.travel.index', ['section' => 'flights']) }}" class="btn btn-outline--base rounded-pill px-5 fw-bold">@lang('Find Your Flight') <i class="las la-arrow-right ms-1"></i></a>
                    </div>

                @elseif ($currentSection === 'destinations')
                    @php
                        $countryLabelFor = static function (string $country) use ($destinationCountryLabels): string {
                            $labels = $destinationCountryLabels[$country] ?? null;

                            if (! $labels) {
                                return $country;
                            }

                            return is_rtl() ? $labels['ar'] : $labels['en'];
                        };
                    @endphp

                    @foreach($results as $country => $group)
                        @php $locations = collect($group['items'] ?? collect())->take(5)->values(); @endphp
                        <div class="destination-country-section mb-5">
                            <h3 class="destination-country-title h4 fw-bold mb-4" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
                                {{ $group['label'] ?? $countryLabelFor($country) }}
                            </h3>

                            <div class="location--card__wrap d-flex gap--20 flex-wrap justify-content-center">
                                @foreach ($locations as $item)
                                    @include($activeTemplate . 'partials.destination-card', [
                                        'item' => $item,
                                        'href' => route('browse', ['location' => $item->displayName()]),
                                    ])
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($results->isEmpty())
                        <div class="col-12 text-center text-muted py-4">@lang('No destinations found yet.')</div>
                    @endif
                @elseif (in_array($currentSection, ['packages'], true))
                    <div class="row g-4 offers-hub__grid">
                        @forelse ($results as $pkg)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                @include($activeTemplate . 'travel.partials.iconic-tour-card', [
                                    'pkg' => $pkg,
                                    'tp' => $tp,
                                    'hubRate' => $hubRate,
                                    'cardUi' => $cardUi,
                                ])
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">@lang('No travel results found.')</div>
                        @endforelse
                    </div>
                    @if (method_exists($results, 'hasPages') && $results->hasPages())
                        <div class="pagination-wrap mt-4 d-flex justify-content-center">{{ $results->links() }}</div>
                    @endif
                @elseif ($currentSection === 'hotels')
                    @include($activeTemplate . 'travel.sections.hotels')
                @elseif ($currentSection === 'flights')
                    @include($activeTemplate . 'travel.sections.flights')
                @elseif ($currentSection === 'transportation')
                    @include($activeTemplate . 'travel.sections.transportation')
                @else
                    <div class="row g-4 offers-hub__grid">
                        @forelse ($results as $listing)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                @include($activeTemplate . 'travel.partials.iconic-listing-card', [
                                    'listing' => $listing,
                                    'hubRate' => $hubRate,
                                    'tp' => $tp,
                                    'cardUi' => $cardUi,
                                ])
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">@lang('No travel results found.')</div>
                        @endforelse
                    </div>
                    @if (method_exists($results, 'hasPages') && $results->hasPages())
                        <div class="pagination-wrap mt-4 d-flex justify-content-center">{{ $results->links() }}</div>
                    @endif
                @endif
            </div>
        </section>
    </article>
@endsection

@push('style')
    <style>
        @include($activeTemplate . 'partials.css.travel-offers-align')
        @include($activeTemplate . 'partials.css.travel-premium-card')

        .travel-hub {
            --th-dark: #141b2d;
            --th-navy: #141b2d;
            --th-terracotta: #c45c3e;
            --th-terracotta-soft: rgba(196, 92, 62, 0.12);
            --th-sand: #f6f4f1;
            --th-accent: hsl(var(--base));
            --th-accent-soft: hsl(var(--base) / 0.12);
        }

        .travel-hub__section-eyebrow {
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: hsl(var(--base));
            margin-bottom: 0;
        }

        .travel-hub__section-title {
            font-family: var(--heading-font);
            font-weight: 800;
            color: var(--th-dark);
            font-size: clamp(1.45rem, 2.6vw, 1.95rem);
            line-height: 1.2;
        }

        .travel-hub__section-lead {
            max-width: 36rem;
            line-height: 1.65;
        }

        .travel-hub__link-terracotta {
            color: hsl(var(--base)) !important;
        }

        .ultra-small {
            font-size: 0.72rem;
        }

        .travel-hub__partners {
            border-block: 1px solid rgba(20, 27, 45, 0.07);
            background: #fff;
        }

        .travel-hub__partner-pill {
            font-size: 1rem;
            color: #94a3b8;
            letter-spacing: 0.06em;
            font-weight: 700;
            text-transform: uppercase;
            filter: grayscale(1);
            opacity: 0.85;
        }

        .ls-wide {
            letter-spacing: 0.12em;
        }

        .travel-hub__h2 {
            font-weight: 800;
            font-size: clamp(1.2rem, 2vw, 1.55rem);
            color: var(--th-dark);
        }

        .travel-hub__h3 {
            font-weight: 800;
            font-size: clamp(1.1rem, 1.8vw, 1.35rem);
            color: var(--th-dark);
        }

        .travel-spot {
            border-color: rgba(20, 27, 45, 0.06) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .travel-spot:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08) !important;
        }

        .travel-spot__disc {
            width: 48px;
            height: 48px;
            background: hsl(var(--base) / 0.12);
            color: hsl(var(--base));
        }

        [dir='rtl'] .travel-spot__arrow {
            transform: scaleX(-1);
        }

        .travel-section-card__icon {
            width: 54px;
            height: 54px;
            background: hsl(var(--base) / 0.12);
            color: hsl(var(--base));
            font-size: 1.35rem;
        }

        .travel-section-card {
            border-color: rgba(20, 27, 45, 0.06) !important;
        }

        .travel-section-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.1) !important;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .destination-country-section {
            scroll-margin-top: 120px;
        }

        .destination-country-title {
            color: var(--th-dark);
            line-height: 1.2;
            text-align: start;
            border-inline-start: 4px solid hsl(var(--base));
            padding-inline-start: 1rem;
        }

        [dir='rtl'] .destination-country-title {
            text-align: end;
            width: fit-content;
            margin-inline-end: auto;
        }

    </style>
@endpush
