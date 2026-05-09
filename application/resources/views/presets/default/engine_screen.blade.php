@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="engine-screen-shell">
        <div class="engine-screen-hero">
            <div class="engine-screen-hero__glow engine-screen-hero__glow--one"></div>
            <div class="engine-screen-hero__glow engine-screen-hero__glow--two"></div>

            <div class="container position-relative">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6">
                        <span class="engine-screen-kicker">@lang('Travel Operations Hub')</span>
                        <h1 class="engine-screen-title">
                            @lang('ALTAYARVIP Travel Engine')
                        </h1>
                        <p class="engine-screen-lead">
                            @lang('A focused travel workspace built to keep bookings, flights, hotels, transfers, and support in one place. Fast access, clear workflow, and a premium experience designed around your customers.')
                        </p>

                        <div class="engine-screen-actions">
                            <a href="https://altayarvip.net/" target="_blank" rel="noopener"
                                class="btn btn--base btn--lg pills">
                                @lang('Login to Your Screen') <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                            <a href="{{ route('public.membership.card') }}" class="btn btn-outline--base btn--lg pills">
                                @lang('Explore Services')
                            </a>
                        </div>

                        <div class="engine-screen-mini-grid">
                            <div class="engine-mini-card">
                                <span class="engine-mini-card__icon"><i class="fas fa-shield-alt"></i></span>
                                <div>
                                    <h6>@lang('Secure access')</h6>
                                    <p>@lang('Protected logins and structured operations.')
                                    </p>
                                </div>
                            </div>
                            <div class="engine-mini-card">
                                <span class="engine-mini-card__icon"><i class="fas fa-bolt"></i></span>
                                <div>
                                    <h6>@lang('Fast workflow')</h6>
                                    <p>@lang('Move between products and booking steps quickly.')
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
                                <h5>@lang('One screen. Full control.')</h5>
                                <p>@lang('Manage the journey from discovery to confirmation with a cleaner interface.')
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
                    <span>@lang('Book faster')</span>
                    <strong>@lang('Hotels, flights, tours, and transfers') </strong>
                </div>
                <div class="engine-info-item">
                    <span>@lang('Serve better')</span>
                    <strong>@lang('Organized tools for daily operations')</strong>
                </div>
                <div class="engine-info-item">
                    <span>@lang('Work smarter')</span>
                    <strong>@lang('Clear navigation and premium usability')</strong>
                </div>
            </div>

            <div class="row g-4 align-items-stretch engine-section-gap">
                <div class="col-lg-4">
                    <div class="engine-feature-card engine-feature-card--accent">
                        <i class="fas fa-compass"></i>
                        <h5>@lang('Simple navigation')</h5>
                        <p>@lang('A clean dashboard-style experience that makes it easy to move through the booking flow without confusion.')
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="engine-feature-card">
                        <i class="fas fa-lock"></i>
                        <h5>@lang('Trusted operations')</h5>
                        <p>@lang('Everything is structured for secure access, stable handling, and a professional customer journey.')
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="engine-feature-card">
                        <i class="fas fa-headset"></i>
                        <h5>@lang('Support ready')</h5>
                        <p>@lang('Built to keep your team close to the customer, with services and follow-up in one branded place.')
                        </p>
                    </div>
                </div>
            </div>

            <div class="row align-items-stretch g-4 engine-section-gap">
                <div class="col-lg-6">
                    <div class="engine-panel">
                        <span class="engine-panel__label">@lang('What this screen is for')</span>
                        <h2>@lang('A premium entry point for employees and travel customers')</h2>
                        <p>@lang('Use this page to introduce the travel engine, explain the system, and direct employees to their login area with a clearer message and a stronger first impression.')
                        </p>

                        <div class="engine-panel__list">
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('Showcase the travel system in a clean, branded layout.')</span>
                            </div>
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('Guide the user toward employee login and service discovery.')</span>
                            </div>
                            <div class="engine-panel__list-item">
                                <i class="fas fa-check"></i>
                                <span>@lang('Keep the content short, professional, and easy to scan.')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="engine-quote-card engine-quote-card--image">
                        <span class="engine-quote-card__tag">@lang('Important note')</span>
                        <div class="engine-quote-card__content">
                            <h4>@lang('Please use the account sent by the customer service team to access your reservations area.')
                            </h4>
                            <p>@lang('If you have both a public account and a reservations account, they may not be the same. Always sign in with the account provided for the booking system to avoid access issues.')
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="engine-stats-grid engine-section-gap">
                <div class="engine-stat-card">
                    <i class="fas fa-person-running"></i>
                    <strong>9.7k+</strong>
                    <span>@lang('Happy travelers')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fas fa-plane-arrival"></i>
                    <strong>14.2k+</strong>
                    <span>@lang('Completed tours')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fas fa-star-half-alt"></i>
                    <strong>91.6%</strong>
                    <span>@lang('Positive reviews')</span>
                </div>
                <div class="engine-stat-card">
                    <i class="fas fa-certificate"></i>
                    <strong>8.9k+</strong>
                    <span>@lang('Membership cards')</span>
                </div>
            </div>

            <div class="engine-cta-band engine-section-gap">
                <div>
                    <span class="engine-cta-band__eyebrow">@lang('Membership Required')</span>
                    <h3>@lang('Join our elite community and unlock full <br> access to the booking engine by subscribing <br> to a membership plan.')
                    </h3>
                </div>
                <a href="{{ route('public.membership.details') }}"
                    class="btn btn--base btn--lg pills bg-white text--base border-0 custom-hover-btn">
                    @lang('Explore Memberships')
                </a>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            .custom-hover-btn:hover {
                color: #fff !important;
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