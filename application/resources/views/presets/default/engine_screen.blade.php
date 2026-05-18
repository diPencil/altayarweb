@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="engine-screen-shell">
        <div class="engine-screen-hero">
            <div class="engine-screen-hero__glow engine-screen-hero__glow--one"></div>
            <div class="engine-screen-hero__glow engine-screen-hero__glow--two"></div>

            <div class="container position-relative">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <span class="engine-screen-kicker">@lang('AltayarVIP Search Center')</span>
                        <h1 class="engine-screen-title">
                            @lang('AltayarVIP Booking Engine')
                        </h1>
                        <p class="engine-screen-lead">
                            @lang('Search hotels, flights, packages, transfers, and travel services through our dedicated booking engine. Accessible anytime for our VIP members and business clients.')
                        </p>

                        <div class="engine-screen-actions">
                            <a href="https://altayarvip.net/" target="_blank" rel="noopener"
                                class="btn btn--base btn--lg pills">
                                @lang('Access Booking Engine') <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                            <a href="{{ route('public.membership.details') }}" class="btn btn-outline--base btn--lg pills">
                                @lang('View Membership Plans')
                            </a>
                        </div>

                        <div class="engine-screen-mini-grid">
                            <div class="engine-mini-card">
                                <span class="engine-mini-card__icon"><i class="fas fa-shield-alt"></i></span>
                                <div>
                                    <h6>@lang('Secure Access')</h6>
                                    <p>@lang('Protected logins and secure travel operations for all subscribers.')
                                    </p>
                                </div>
                            </div>
                            <div class="engine-mini-card">
                                <span class="engine-mini-card__icon"><i class="fas fa-bolt"></i></span>
                                <div>
                                    <h6>@lang('Fast Search')</h6>
                                    <p>@lang('Compare thousands of rates and travel products in one screen.')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="engine-screen-card engine-screen-card--visual">
                            <img src="{{ asset('assets/presets/default/images/Live-travel-control-center.jpg') }}"
                                class="img-fluid engine-screen-visual" alt="Live travel control center">
                            <div class="engine-screen-card__overlay">
                                <h5>@lang('One search. End-to-end luxury.')</h5>
                                <p>@lang('Find the best rates, hotels, flights, and packages through a premium tailored travel experience.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container engine-screen-content">
            <div class="engine-info-strip">
                <div class="engine-info-item">
                    <span>@lang('Global Hotels')</span>
                    <strong>@lang('Access exclusive luxury rates worldwide') </strong>
                </div>
                <div class="engine-info-item">
                    <span>@lang('Flight Booking')</span>
                    <strong>@lang('Compare and book flights across all airlines')</strong>
                </div>
                <div class="engine-info-item">
                    <span>@lang('Premium Services')</span>
                    <strong>@lang('First-class transfers and cruise packages')</strong>
                </div>
            </div>

            <div class="row g-4 align-items-stretch engine-section-gap">
                <div class="col-lg-4">
                    <div class="engine-feature-card engine-feature-card--accent">
                        <i class="fa-solid fa-list-check"></i>
                        <h5>@lang('Unified Search Hub')</h5>
                        <p>@lang('Compare flights, hotels, Nile cruises, and tours instantly inside a single luxury workspace.')
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="engine-feature-card">
                        <i class="fa-solid fa-bolt"></i>
                        <h5>@lang('Faster VIP Rates')</h5>
                        <p>@lang('Get rapid access to curated travel offers and pre-negotiated elite rates specifically for our members.')
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="engine-feature-card">
                        <i class="fa-solid fa-user-shield"></i>
                        <h5>@lang('Tailored for Members')</h5>
                        <p>@lang('Designed with high-end tools that align with your membership plan benefits and VIP traveler profile.')
                        </p>
                    </div>
                </div>
            </div>

            <div class="row align-items-stretch g-4 engine-section-gap">
                <div class="col-lg-6">
                    <div class="engine-panel">
                        <span class="engine-panel__label">@lang('Who Is This For?')</span>
                        <h2>@lang('Designed for Premium and Corporate Travel')</h2>
                        <p>@lang('The AltayarVIP booking engine is tailored to meet the needs of subscribed members, business accounts, and travel partners who demand fast, secure, and exclusive rates.')
                        </p>

                        <div class="engine-panel__list">
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('AltayarVIP members who want self-service luxury bookings.')</span>
                            </div>
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('Business and corporate clients seeking managed travel options.')</span>
                            </div>
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('Travel companies and agents needing fast and robust travel searches.')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="engine-quote-card engine-quote-card--image">
                        <span class="engine-quote-card__tag">@lang('Membership Access Notice')</span>
                        <div class="engine-quote-card__content">
                            <h4>@lang('An active membership plan is required to access reservation features.')
                            </h4>
                            <p>@lang('Please sign in with your VIP credentials or subscribe to one of our membership plans to unlock full searching, comparisons, and booking requests on the engine.')
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="engine-stats-grid engine-section-gap">
                <div class="engine-stat-card">
                    <i class="fa-solid fa-user-plus"></i>
                    <strong>@lang('Step 1')</strong>
                    <span>@lang('Sign in or Join AltayarVIP')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fa-solid fa-id-card"></i>
                    <strong>@lang('Step 2')</strong>
                    <span>@lang('Choose your plan')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fa-solid fa-arrow-pointer"></i>
                    <strong>@lang('Step 3')</strong>
                    <span>@lang('Access Booking Engine')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                    <strong>@lang('Step 4')</strong>
                    <span>@lang('Search & request bookings')</span>
                </div>
            </div>

            <div class="engine-cta-band engine-section-gap flex-column flex-md-row text-center text-md-start">
                <div>
                    <span class="engine-cta-band__eyebrow">@lang('Ready to start searching?')</span>
                    <h3>@lang('Unlock the premium booking engine experience now.')</h3>
                </div>
                <div class="d-flex flex-wrap gap-3 mt-3 mt-md-0 justify-content-center justify-content-md-end">
                    <a href="https://altayarvip.net/" target="_blank" rel="noopener"
                        class="btn btn--base btn--lg pills bg-white text--base border-0 custom-hover-btn">
                        @lang('Access Booking Engine')
                    </a>
                    <a href="{{ route('public.membership.details') }}"
                        class="btn btn-outline-light btn--lg pills border-white text-white custom-hover-btn-outline">
                        @lang('Explore Membership Plans')
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            .custom-hover-btn:hover {
                color: #fff !important;
            }

            .custom-hover-btn-outline:hover {
                background: #fff !important;
                color: #2257bf !important;
            }

            .engine-screen-shell {
                background:
                    radial-gradient(circle at top left, rgba(34, 87, 191, 0.16), transparent 30%),
                    radial-gradient(circle at top right, rgba(0, 188, 212, 0.12), transparent 26%),
                    linear-gradient(180deg, #f7faff 0%, #ffffff 100%);
                color: #1f2a44;
            }

            .engine-screen-hero {
                position: relative;
                overflow: hidden;
                padding: 90px 0 70px;
            }

            .engine-screen-hero__glow {
                position: absolute;
                border-radius: 50%;
                filter: blur(12px);
                opacity: 0.45;
            }

            .engine-screen-hero__glow--one {
                width: 280px;
                height: 280px;
                background: rgba(0, 188, 212, 0.18);
                top: -120px;
                right: -80px;
            }

            .engine-screen-hero__glow--two {
                width: 220px;
                height: 220px;
                background: rgba(34, 87, 191, 0.12);
                left: -90px;
                bottom: 0;
            }

            .engine-screen-kicker,
            .engine-panel__label,
            .engine-cta-band__eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-radius: 999px;
                padding: 8px 14px;
                background: rgba(34, 87, 191, 0.08);
                color: #2257bf;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .engine-screen-title {
                margin: 18px 0 18px;
                font-size: clamp(40px, 5vw, 64px);
                line-height: 1.05;
                font-weight: 900;
                letter-spacing: -0.03em;
                color: #14213d;
            }

            .engine-screen-lead {
                max-width: 620px;
                margin: 0;
                font-size: 17px;
                line-height: 1.85;
                color: #5c677d;
            }

            .engine-screen-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 28px;
            }

            .engine-btn-primary,
            .engine-btn-secondary {
                border-radius: 14px;
                padding: 12px 22px;
                font-weight: 700;
                transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            }

            .engine-btn-primary {
                background: linear-gradient(135deg, #2257bf 0%, #39bff9 100%);
                border: none;
                color: #fff;
                box-shadow: 0 16px 28px rgba(34, 87, 191, 0.22);
            }

            .engine-btn-primary:hover,
            .engine-btn-secondary:hover {
                transform: translateY(-2px);
            }

            .engine-btn-primary.engine-btn-primary--light:hover {
                background: rgba(255, 255, 255, 0.96);
                color: #1f4fb6;
                box-shadow: 0 12px 28px rgba(8, 18, 43, 0.18);
            }

            .engine-btn-secondary {
                background: rgba(34, 87, 191, 0.08);
                border: 1px solid rgba(34, 87, 191, 0.14);
                color: #2257bf;
            }

            .engine-btn-primary--light {
                background: #ffffff;
                color: #2257bf;
                box-shadow: none;
            }

            .engine-screen-mini-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
                margin-top: 28px;
            }

            .engine-mini-card,
            .engine-feature-card,
            .engine-panel,
            .engine-quote-card,
            .engine-stat-card,
            .engine-screen-card,
            .engine-info-strip,
            .engine-cta-band {
                background: rgba(255, 255, 255, 0.88);
                border: 1px solid rgba(34, 87, 191, 0.08);
                box-shadow: 0 20px 50px rgba(20, 33, 61, 0.06);
                backdrop-filter: blur(10px);
            }

            .engine-mini-card {
                border-radius: 20px;
                padding: 18px;
                display: flex;
                gap: 14px;
                align-items: flex-start;
            }

            .engine-mini-card__icon {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                background: linear-gradient(135deg, rgba(34, 87, 191, 0.12), rgba(57, 191, 249, 0.18));
                color: #2257bf;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .engine-mini-card h6,
            .engine-feature-card h5,
            .engine-panel h2,
            .engine-quote-card h4,
            .engine-stat-card strong,
            .engine-screen-card__overlay h5 {
                margin-bottom: 8px;
                color: #14213d;
                font-weight: 800;
            }

            .engine-mini-card p,
            .engine-feature-card p,
            .engine-panel p,
            .engine-quote-card p {
                margin: 0;
                color: #63708a;
                line-height: 1.8;
                font-size: 14px;
            }

            .engine-screen-card--visual {
                position: relative;
                border-radius: 28px;
                overflow: hidden;
                min-height: 520px;
                padding: 28px;
                background: linear-gradient(160deg, rgba(34, 87, 191, 0.98) 0%, rgba(57, 191, 249, 0.95) 100%);
            }

            .engine-screen-card__badge {
                position: absolute;
                top: 20px;
                left: 20px;
                z-index: 2;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.16);
                color: #fff;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .engine-screen-visual {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 1;
                mix-blend-mode: normal;
            }

            .engine-screen-card__overlay {
                position: absolute;
                left: 28px;
                right: 28px;
                bottom: 28px;
                z-index: 2;
                padding: 22px;
                border-radius: 22px;
                background: rgba(15, 23, 42, 0.82);
                color: #fff;
                box-shadow: 0 18px 36px rgba(8, 18, 43, 0.22);
            }

            .engine-screen-card__overlay h5,
            .engine-screen-card__overlay p {
                color: #fff;
            }

            .engine-screen-content {
                padding-top: 8px;
                padding-bottom: 90px;
            }

            .engine-info-strip {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
                border-radius: 24px;
                padding: 22px;
                margin-top: -22px;
            }

            .engine-info-item span {
                display: block;
                color: #39bff9;
                font-size: 12px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                margin-bottom: 6px;
            }

            .engine-info-item strong {
                color: #14213d;
                font-size: 16px;
                line-height: 1.5;
            }

            .engine-section-gap {
                margin-top: 36px;
            }

            .engine-feature-card {
                height: 100%;
                border-radius: 24px;
                padding: 28px;
            }

            .engine-feature-card i {
                width: 54px;
                height: 54px;
                border-radius: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 18px;
                color: #2257bf;
                background: rgba(34, 87, 191, 0.08);
                font-size: 22px;
            }

            .engine-feature-card--accent {
                background: linear-gradient(180deg, rgba(34, 87, 191, 0.08), rgba(255, 255, 255, 0.95));
            }

            .engine-panel,
            .engine-quote-card {
                border-radius: 26px;
                padding: 30px;
                height: 100%;
            }

            .engine-panel h2 {
                font-size: clamp(28px, 3vw, 40px);
                margin-top: 16px;
            }

            .engine-panel__list {
                margin-top: 22px;
                display: grid;
                gap: 14px;
            }

            .engine-panel__list-item {
                display: flex;
                gap: 12px;
                align-items: flex-start;
                color: #14213d;
            }

            .engine-panel__list-item i {
                color: #39bff9;
                margin-top: 4px;
            }

            .engine-quote-card {
                background: linear-gradient(160deg, rgba(34, 87, 191, 0.98), rgba(57, 191, 249, 0.92));
                color: #fff;
            }

            .engine-quote-card--image {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                overflow: hidden;
                min-height: 420px;
                padding: 24px;
                background:
                    linear-gradient(160deg, rgba(15, 23, 42, 0.72), rgba(34, 87, 191, 0.72)),
                    url('{{ asset('assets/presets/default/images/Important-note.jpg') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            .engine-quote-card--image::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(15, 23, 42, 0.22));
                pointer-events: none;
            }

            .engine-quote-card--image>* {
                position: relative;
                z-index: 1;
            }

            .engine-quote-card__content {
                margin-top: auto;
                padding: 18px 20px;
                border-radius: 22px;
                background: rgba(15, 23, 42, 0.42);
                border: 1px solid rgba(255, 255, 255, 0.14);
                backdrop-filter: blur(8px);
                box-shadow: 0 18px 32px rgba(8, 18, 43, 0.18);
            }

            .engine-quote-card__tag {
                position: absolute;
                top: 24px;
                left: 24px;
                z-index: 2;
                display: inline-flex;
                border-radius: 999px;
                padding: 6px 12px;
                font-size: 12px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                background: rgba(255, 255, 255, 0.16);
                margin-bottom: 18px;
            }

            .engine-quote-card h4,
            .engine-quote-card p {
                color: #fff;
            }

            .engine-quote-card h4 {
                margin-bottom: 10px;
                font-size: 22px;
                line-height: 1.35;
            }

            .engine-quote-card p {
                margin-bottom: 0;
                line-height: 1.8;
                font-size: 15px;
            }

            .engine-stats-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 18px;
            }

            .engine-stat-card {
                border-radius: 24px;
                padding: 24px;
                text-align: center;
            }

            .engine-stat-card i {
                display: inline-flex;
                width: 54px;
                height: 54px;
                margin-bottom: 16px;
                border-radius: 18px;
                align-items: center;
                justify-content: center;
                color: #2257bf;
                background: rgba(34, 87, 191, 0.08);
                font-size: 22px;
            }

            .engine-stat-card strong {
                display: block;
                font-size: 32px;
                line-height: 1;
            }

            .engine-stat-card span {
                display: block;
                margin-top: 8px;
                color: #63708a;
                font-size: 13px;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .engine-cta-band {
                border-radius: 28px;
                padding: 28px 30px;
                display: flex;
                gap: 20px;
                align-items: center;
                justify-content: space-between;
                background: linear-gradient(135deg, rgba(0, 188, 212, 0.98), rgba(57, 191, 249, 0.96));
                color: #fff;
            }

            .engine-cta-band>div {
                min-width: 0;
            }

            .engine-cta-band__eyebrow {
                background: rgba(255, 255, 255, 0.16);
                color: #ffffff;
                border: 1px solid rgba(255, 255, 255, 0.18);
                box-shadow: 0 8px 18px rgba(8, 18, 43, 0.10);
            }

            .engine-cta-band h3 {
                margin: 10px 0 0;
                color: #fff;
                font-size: clamp(24px, 2.4vw, 34px);
                font-weight: 800;
            }

            @media (max-width: 991px) {
                .engine-screen-hero {
                    padding-top: 60px;
                }

                .engine-screen-mini-grid,
                .engine-info-strip,
                .engine-stats-grid {
                    grid-template-columns: 1fr;
                }

                .engine-screen-card--visual {
                    min-height: 420px;
                }

                .engine-cta-band {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            @media (max-width: 575px) {
                .engine-screen-title {
                    font-size: 34px;
                }

                .engine-screen-lead {
                    font-size: 15px;
                }

                .engine-panel,
                .engine-quote-card,
                .engine-feature-card,
                .engine-stat-card,
                .engine-cta-band {
                    padding: 22px;
                }

                .engine-screen-card--visual {
                    padding: 20px;
                    min-height: 360px;
                }

                .engine-screen-card__overlay {
                    left: 20px;
                    right: 20px;
                    bottom: 20px;
                }
            }
        </style>
    @endpush
@endsection