@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $isRtl = is_rtl();
        $heroPlan = $plans->first();
        $heroPlanName = $heroPlan ? ($isRtl && $heroPlan->name_ar ? $heroPlan->name_ar : $heroPlan->name) : __('Membership Details');
        $heroPlanDescription = $heroPlan ? ($isRtl && $heroPlan->description_ar ? $heroPlan->description_ar : $heroPlan->description) : __('Browse the membership details page to understand the experience, compare plans, and choose the right level of access.');
        $heroPlanImage = asset('assets/presets/default/images/membership-details.jpg');
        $membershipIcons = ['la-crown', 'la-gem', 'la-shield-alt', 'la-rocket', 'la-diamond', 'la-star'];
    @endphp

    <section class="membership-details-hero py-100">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="membership-details-hero__eyebrow">@lang('Membership Experience')</span>
                    <h1 class="membership-details-hero__title">@lang('Membership Details')</h1>
                    <p class="membership-details-hero__lead">@lang('Discover a premium membership system designed to help travelers unlock more value, better support, and stronger rewards.')</p>

                    <div class="membership-details-hero__points">
                        <div class="membership-details-hero__point">
                            <i class="las la-check-circle"></i>
                            <span>@lang('Dynamic membership data')</span>
                        </div>
                        <div class="membership-details-hero__point">
                            <i class="las la-check-circle"></i>
                            <span>@lang('Full RTL support')</span>
                        </div>
                        <div class="membership-details-hero__point">
                            <i class="las la-check-circle"></i>
                            <span>@lang('Conversion-focused subscribe flow')</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="#membership-plans" class="btn btn--base btn-lg pills">@lang('Explore membership plans')</a>
                        @auth
                            <a href="{{ route('user.home') }}" class="btn btn--light btn-lg pills btn-register-hero">@lang('Go to Dashboard')</a>
                        @else
                            <a href="{{ route('user.register') }}" class="btn btn--light btn-lg pills btn-register-hero">@lang('Register Now')</a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="membership-details-hero__visual">
                        <div class="membership-details-hero__image-wrap">
                            <img src="{{ $heroPlanImage }}" alt="{{ $heroPlanName }}">
                        </div>
                        <div class="membership-details-hero__visual-card membership-details-hero__visual-card--primary">
                            <span class="membership-details-hero__visual-label">@lang('Premium Memberships')</span>
                            <strong>{{ $plans->count() }}</strong>
                            <small>@lang('Dynamic membership data')</small>
                        </div>
                        <div class="membership-details-hero__visual-card membership-details-hero__visual-card--secondary">
                            <span class="membership-details-hero__visual-label">@lang('Membership benefits')</span>
                            <strong>{{ $heroPlan ? $heroPlan->bonus_points : 0 }}</strong>
                            <small>@lang('Rewards that grow')</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="membership-details-benefits pb-100">
        <div class="container">
            <div class="row align-items-end mb-4 g-3">
                <div class="col-lg-7">
                    <span class="membership-details-section__eyebrow">@lang('Membership benefits')</span>
                    <h2 class="membership-details-section__title mb-2">@lang('Benefits that feel premium')</h2>
                    <p class="membership-details-section__lead mb-0">@lang('Everything is arranged to make the membership story easy to scan on desktop and mobile.')</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="membership-feature-card h-100">
                        <div class="membership-feature-card__icon"><i class="las la-crown"></i></div>
                        <h5>@lang('Exclusive benefits')</h5>
                        <p>@lang('Clear value, visual storytelling and a stronger subscription journey.')</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="membership-feature-card h-100">
                        <div class="membership-feature-card__icon"><i class="las la-headset"></i></div>
                        <h5>@lang('Better support')</h5>
                        <p>@lang('Fast service, clear plan guidance and a polished customer journey.')</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="membership-feature-card h-100">
                        <div class="membership-feature-card__icon"><i class="las la-bolt"></i></div>
                        <h5>@lang('Rewards that grow')</h5>
                        <p>@lang('Bonus points and member perks that compound over time.')</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="membership-details-story py-100 section--bg">
        <div class="container">
            <div class="row align-items-stretch g-5">
                <div class="col-lg-6">
                    <div class="membership-details-story__media">
                        <img src="{{ asset('assets/presets/default/images/premium-membership.jpg') }}" alt="@lang('Premium Membership')">
                    </div>
                </div>

                <div class="col-lg-6">
                    <span class="membership-details-section__eyebrow">@lang('Why Choose Us')</span>
                    <h2 class="membership-details-section__title mb-3">@lang('A premium membership system built for clarity, value, and long-term loyalty.')</h2>
                    <p class="membership-details-section__lead mb-4">{{ $heroPlanDescription }}</p>

                    <div class="membership-details-checklist">
                        <div class="membership-details-checklist__item">
                            <i class="las la-check-circle"></i>
                            <div>
                                <strong>@lang('Dynamic membership data')</strong>
                                <p>@lang('Admin-managed plans, descriptions and features flow directly into the public experience.')</p>
                            </div>
                        </div>
                        <div class="membership-details-checklist__item">
                            <i class="las la-check-circle"></i>
                            <div>
                                <strong>@lang('Full RTL support')</strong>
                                <p>@lang('Layout, spacing and text alignment mirror correctly for Arabic readers.')</p>
                            </div>
                        </div>
                        <div class="membership-details-checklist__item">
                            <i class="las la-check-circle"></i>
                            <div>
                                <strong>@lang('Conversion-focused subscribe flow')</strong>
                                <p>@lang('Every membership page ends with a clear path to subscribe.')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="membership-plans" class="membership-details-plans py-100">
        <div class="container">
            <div class="row align-items-end mb-4 g-3">
                <div class="col-lg-8">
                    <span class="membership-details-section__eyebrow">@lang('Six membership plans')</span>
                    <h2 class="membership-details-section__title mb-2">@lang('Explore membership plans')</h2>
                    <p class="membership-details-section__lead mb-0">@lang('Compare the six featured memberships below and open a dedicated page for any plan that fits your travel style.')</p>
                </div>
            </div>

            <div class="row g-4">
                @forelse($plans->take(6) as $index => $plan)
                    <div class="col-lg-4 col-md-6">
                        @include($activeTemplate . 'membership-details.partials.plan-card', [
                            'plan' => $plan,
                            'currentMembership' => $currentMembership,
                            'cardIcon' => $membershipIcons[$index % count($membershipIcons)],
                        ])
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">@lang('No membership plans available right now.')</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .membership-details-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .membership-details-hero::before {
            content: '';
            position: absolute;
            inset-inline-start: -120px;
            top: -120px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(34, 87, 191, 0.10);
            filter: blur(12px);
            pointer-events: none;
        }

        .membership-details-hero::after {
            content: '';
            position: absolute;
            inset-inline-end: -100px;
            bottom: -120px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(2, 153, 126, 0.08);
            filter: blur(16px);
            pointer-events: none;
        }

        .membership-details-hero__eyebrow,
        .membership-details-section__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            color: var(--base-color);
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-size: 12px;
        }

        .membership-details-hero__title,
        .membership-details-section__title {
            color: #173c36;
            font-weight: 800;
            letter-spacing: -0.05em;
            line-height: 1.05;
        }

        .membership-details-hero__title {
            font-size: clamp(38px, 6vw, 68px);
            margin-bottom: 16px;
        }

        .membership-details-section__title {
            font-size: clamp(28px, 4vw, 44px);
        }

        .membership-details-hero__lead,
        .membership-details-section__lead {
            color: #617086;
            line-height: 1.85;
            margin-bottom: 0;
            font-size: 16px;
        }

        .membership-details-hero__points {
            display: grid;
            gap: 16px;
            margin-bottom: 32px;
        }

        .btn-register-hero {
            background: #fff !important;
            color: #173c36 !important;
            border: 1px solid rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease;
        }

        .btn-register-hero:hover {
            background: #3498db !important;
            color: #fff !important;
            border-color: #3498db !important;
            transform: translateY(-2px);
        }

        .membership-details-hero__point {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(20, 33, 61, 0.06);
            box-shadow: 0 12px 28px rgba(20, 33, 61, 0.04);
            color: #173c36;
            font-weight: 600;
        }

        .membership-details-hero__point i {
            color: var(--base-color);
            font-size: 18px;
        }

        .membership-details-hero__visual {
            position: relative;
            min-height: 540px;
            display: grid;
            align-items: stretch;
        }

        .membership-details-hero__image-wrap {
            overflow: hidden;
            border-radius: 32px;
            min-height: 540px;
            background: linear-gradient(135deg, rgba(91, 156, 249, 0.16), rgba(2, 153, 126, 0.12));
            box-shadow: 0 24px 48px rgba(20, 33, 61, 0.10);
        }

        .membership-details-hero__image-wrap img,
        .membership-details-story__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .membership-details-hero__visual-card {
            position: absolute;
            min-width: 180px;
            padding: 16px 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            box-shadow: 0 18px 36px rgba(20, 33, 61, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.66);
        }

        .membership-details-hero__visual-card--primary {
            inset-inline-start: 18px;
            bottom: 18px;
        }

        .membership-details-hero__visual-card--secondary {
            inset-inline-end: 18px;
            top: 22px;
        }

        .membership-details-hero__visual-label {
            display: block;
            margin-bottom: 6px;
            color: #617086;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .membership-details-hero__visual-card strong {
            display: block;
            color: #173c36;
            font-size: 30px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .membership-details-hero__visual-card small {
            color: #617086;
            display: block;
            line-height: 1.5;
        }

        .membership-feature-card {
            padding: 28px;
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: 0 14px 30px rgba(20, 33, 61, 0.05);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .membership-feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(20, 33, 61, 0.10);
        }

        .membership-feature-card__icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.12), rgba(2, 153, 126, 0.10));
            color: var(--base-color);
            font-size: 24px;
            margin-bottom: 18px;
        }

        .membership-feature-card h5 {
            color: #173c36;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .membership-feature-card p {
            color: #617086;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .membership-details-story__media {
            overflow: hidden;
            height: 100%;
            min-height: 420px;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.14), rgba(2, 153, 126, 0.12));
            box-shadow: 0 20px 46px rgba(20, 33, 61, 0.10);
        }

        .membership-details-story__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .membership-details-story__empty {
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(23, 60, 54, 0.50);
            font-size: 72px;
        }

        .membership-details-checklist {
            display: grid;
            gap: 16px;
        }

        .membership-details-checklist__item {
            display: flex;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: 0 12px 28px rgba(20, 33, 61, 0.04);
        }

        .membership-details-checklist__item i {
            color: #16a34a;
            font-size: 22px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .membership-details-checklist__item strong {
            display: block;
            color: #173c36;
            font-size: 17px;
            margin-bottom: 4px;
        }

        .membership-details-checklist__item p {
            color: #617086;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .membership-plan-card {
            overflow: hidden;
            color: inherit;
            border: 1px solid rgba(20, 33, 61, 0.08);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .membership-plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 42px rgba(20, 33, 61, 0.10);
            border-color: rgba(34, 87, 191, 0.18);
        }

        .membership-plan-card.is-current {
            border-color: rgba(22, 163, 74, 0.25);
            box-shadow: 0 20px 42px rgba(22, 163, 74, 0.10);
        }

        .membership-plan-card__media {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.14), rgba(2, 153, 126, 0.12));
        }

        .membership-plan-card__media img {
            width: 100%;
            display: block;
        }

        .membership-plan-card__placeholder {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(23, 60, 54, 0.45);
            font-size: 64px;
        }

        .membership-plan-card__media-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(8, 15, 34, 0) 35%, rgba(8, 15, 34, 0.34) 100%);
        }

        .membership-plan-card__badge {
            position: absolute;
            inset-inline-start: 16px;
            bottom: 16px;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #173c36;
            font-weight: 700;
            font-size: 12px;
        }

        .membership-plan-card__body {
            padding: 24px;
        }

        .membership-plan-card__title {
            color: #173c36;
            font-size: 22px;
            font-weight: 800;
        }

        .membership-plan-card__subtitle {
            color: #617086;
            line-height: 1.7;
            min-height: 54px;
        }

        .membership-plan-card__price {
            color: #173c36;
            font-size: 24px;
            font-weight: 800;
        }

        .membership-plan-card__points {
            color: #617086;
            font-size: 13px;
            font-weight: 600;
        }

        .membership-plan-card__benefits li {
            display: flex;
            gap: 10px;
            color: #617086;
            line-height: 1.6;
        }

        .membership-plan-card__benefits i {
            color: #16a34a;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .membership-plan-card__cta-label {
            color: #173c36;
            font-weight: 700;
        }

        .membership-plan-card__cta-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f6fb;
            color: #173c36;
        }

        @media (max-width: 991px) {
            .membership-details-hero__visual {
                min-height: 420px;
            }

            .membership-details-hero__image-wrap {
                min-height: 420px;
            }
        }

        @media (max-width: 767px) {
            .membership-details-hero__visual-card {
                position: static;
                margin-top: 14px;
            }

            .membership-details-hero__visual {
                min-height: unset;
            }

            .membership-details-hero__image-wrap {
                min-height: 320px;
            }
        }

        [dir="rtl"] .membership-details-hero .col-lg-6:first-child {
            text-align: start;
        }

        [dir="rtl"] .membership-details-hero__eyebrow,
        [dir="rtl"] .membership-details-hero__title,
        [dir="rtl"] .membership-details-hero__lead {
            text-align: start;
        }

        [dir="rtl"] .membership-details-hero__point {
            direction: rtl;
        }

        [dir="rtl"] .membership-details-section__title,
        [dir="rtl"] .membership-details-section__lead,
        [dir="rtl"] .membership-details-section__eyebrow {
            text-align: start;
        }

        [dir="rtl"] .membership-details-checklist__item {
            direction: rtl;
        }

        [dir="rtl"] .membership-plan-card__price {
            direction: ltr;
            unicode-bidi: isolate;
        }

        [dir="rtl"] .membership-plan-card__benefits li {
            direction: rtl;
        }

        [dir="rtl"] .membership-plan-card__body > .d-flex.justify-content-between {
            direction: rtl;
        }
    </style>
@endpush
