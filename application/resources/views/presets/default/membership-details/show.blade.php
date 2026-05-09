@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $isRtl = is_rtl();
        $planName = $isRtl && $plan->name_ar ? $plan->name_ar : $plan->name;
        $planDescription = $isRtl && $plan->description_ar ? $plan->description_ar : $plan->description;
        $planBenefits = $isRtl && !empty($plan->benefits_ar) ? $plan->benefits_ar : ($plan->benefits ?? []);
        
        $planImage = asset('assets/presets/default/images/Live-travel-control-center.jpg');
        if ($plan->cover_image) {
            $planImage = asset(getFilePath('membershipPlanCover') . '/' . $plan->cover_image);
        } elseif ($plan->image_file) {
            $planImage = asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file);
        }

        $isCurrentPlan = $currentMembership && $currentMembership->membership_plan_id == $plan->id;
    @endphp

    <section class="membership-detail-hero py-100">
        <div class="container">
            <div class="membership-detail-top-nav mb-4">
                <a href="{{ route('public.membership.details') }}" class="membership-back-btn">
                    <i class="las la-long-arrow-alt-left"></i>
                    <span>@lang('Membership Details')</span>
                </a>
                @if($isCurrentPlan)
                    <span class="badge badge--success ms-3">@lang('Current Plan')</span>
                @endif
            </div>

            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="membership-detail-hero__eyebrow">@lang('Premium subscription card')</span>
                    <h1 class="membership-detail-hero__title">{{ $planName }}</h1>
                    <p class="membership-detail-hero__lead">{{ $planDescription }}</p>

                    <div class="membership-detail-hero__meta">
                        <div class="membership-detail-hero__meta-card">
                            <span>@lang('Price')</span>
                            <strong dir="ltr" class="membership-detail-hero__meta-value--numeric">{{ $general->cur_sym }}{{ showAmount($plan->price) }}</strong>
                        </div>
                        <div class="membership-detail-hero__meta-card">
                            <span>@lang('Duration')</span>
                            <strong dir="auto">{{ $plan->duration_days ? $plan->duration_days . ' ' . __('Days') : __('Lifetime') }}</strong>
                        </div>
                        <div class="membership-detail-hero__meta-card">
                            <span>@lang('Bonus Points')</span>
                            <strong dir="ltr" class="membership-detail-hero__meta-value--numeric">{{ $plan->bonus_points }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="membership-detail-hero__media">
                        <img src="{{ $planImage }}" alt="{{ $planName }}">
                        <div class="membership-detail-hero__media-badge">
                            <i class="las la-crown"></i>
                            <span>@lang('Membership includes')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="membership-detail-benefits pb-100">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="membership-detail-panel h-100">
                        <span class="membership-details-section__eyebrow">@lang('Membership benefits')</span>
                        <h2 class="membership-details-section__title mb-3">@lang('What you get')</h2>

                        <div class="membership-detail-panel__richtext mb-4">
                            <p>{{ $planDescription }}</p>
                        </div>

                        <div class="membership-detail-panel__highlights d-grid gap-4 mt-2">
                            <div class="highlight-item d-flex gap-3">
                                <div class="highlight-icon"><i class="las la-globe-americas"></i></div>
                                <div class="highlight-content">
                                    <h6 class="mb-1">@lang('Global VIP Access')</h6>
                                    <p class="small text-muted mb-0">@lang('Enjoy priority treatment and exclusive perks at our partner hotels and lounges across the globe.')</p>
                                </div>
                            </div>

                            <div class="highlight-item d-flex gap-3">
                                <div class="highlight-icon"><i class="las la-hand-holding-usd"></i></div>
                                <div class="highlight-content">
                                    <h6 class="mb-1">@lang('Maximum Value Rewards')</h6>
                                    <p class="small text-muted mb-0">@lang('Earn points faster on every booking and redeem them for significant savings on future travels.')</p>
                                </div>
                            </div>

                            <div class="highlight-item d-flex gap-3">
                                <div class="highlight-icon"><i class="las la-shield-alt"></i></div>
                                <div class="highlight-content">
                                    <h6 class="mb-1">@lang('Dedicated Concierge')</h6>
                                    <p class="small text-muted mb-0">@lang('Get 24/7 personalized assistance from our travel experts to manage your bookings and requirements.')</p>
                                </div>
                            </div>

                            <div class="highlight-item d-flex gap-3">
                                <div class="highlight-icon"><i class="las la-plane-arrival"></i></div>
                                <div class="highlight-content">
                                    <h6 class="mb-1">@lang('Seamless Travel Flow')</h6>
                                    <p class="small text-muted mb-0">@lang('Experience a friction-free journey from booking to destination with our premium member-only tools.')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="membership-detail-panel h-100">
                        <span class="membership-details-section__eyebrow">@lang('Highlights')</span>
                        <h2 class="membership-details-section__title mb-3">@lang('Top benefits')</h2>
                        @if($planBenefits)
                            <ul class="membership-detail-benefit-list list-unstyled d-grid gap-3 mb-0">
                                @foreach($planBenefits as $benefit)
                                    <li>
                                        <i class="las la-check-circle"></i>
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="membership-details-section__lead mb-0">@lang('Clear value, visual storytelling and a stronger subscription journey.')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="membership-detail-story py-100 section--bg">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="membership-detail-story__visual">
                        <div class="membership-detail-story__visual-card">
                            <div class="membership-detail-story__visual-icon">
                                <dotlottie-wc
                                    src="https://lottie.host/89d0f80c-e47e-4af5-a0ea-0d28f3d4b925/KOh9T7SrfY.lottie"
                                    style="width: 72px; height: 72px; display: block;"
                                    autoplay
                                    loop></dotlottie-wc>
                            </div>
                            <strong>@lang('Membership includes')</strong>
                            <p>@lang('Everything is arranged to make the membership story easy to scan on desktop and mobile.')</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="membership-details-section__eyebrow">@lang('Why Choose Us')</span>
                    <h2 class="membership-details-section__title mb-3">@lang('A premium membership system built for clarity, value, and long-term loyalty.')</h2>
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

    <section class="membership-detail-cta py-100">
        <div class="container">
            <div class="membership-detail-cta__card">
                <div class="row align-items-center g-5">
                    <div class="col-lg-8">
                        <span class="membership-details-section__eyebrow mb-3">@lang('Premium subscription card')</span>
                        <h2 class="membership-detail-cta__title mb-2">{{ $planName }}</h2>
                        <p class="membership-detail-cta__lead mb-4">@lang('Subscribe now to unlock the full set of perks, rewards and premium support.')</p>

                        <div class="membership-detail-cta__chips">
                            <span><i class="las la-check"></i> @lang('Exclusive benefits')</span>
                            <span><i class="las la-check"></i> @lang('Rewards that grow')</span>
                            <span><i class="las la-check"></i> @lang('Better support')</span>
                        </div>
                    </div>

                    <div class="col-lg-4 d-flex flex-column align-items-lg-end {{ $isRtl ? 'align-items-lg-start' : '' }} justify-content-center">
                        <div class="membership-detail-cta__price mb-3">{{ $general->cur_sym }}{{ showAmount($plan->price) }}</div>
                        @auth
                            @if(! $isCurrentPlan)
                                <form action="{{ route('user.membership.subscribe') }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="membership_plan_id" value="{{ $plan->id }}">
                                    <button class="btn btn--base btn-lg pills" type="submit">@lang('Subscribe Now')</button>
                                </form>
                            @else
                                <span class="badge badge--success px-3 py-2">@lang('Current Plan')</span>
                            @endif
                        @else
                            <a href="{{ route('user.register') }}" class="btn btn--base btn-lg pills">@lang('Login to Subscribe')</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .membership-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #617086;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .membership-back-btn i {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .membership-back-btn:hover {
            color: #1a2b4b;
        }

        .membership-back-btn:hover i {
            transform: translateX(-5px);
        }

        .membership-detail-hero {
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .membership-detail-hero__eyebrow,
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

        .membership-detail-hero__title,
        .membership-details-section__title,
        .membership-detail-cta__title {
            color: #173c36;
            font-weight: 800;
            letter-spacing: -0.05em;
            line-height: 1.05;
        }

        .membership-detail-hero__title {
            font-size: clamp(34px, 5vw, 64px);
            margin-bottom: 16px;
        }

        .membership-detail-hero__lead,
        .membership-details-section__lead,
        .membership-detail-cta__lead,
        .membership-detail-panel__richtext p,
        .membership-detail-story__visual-card p {
            color: #617086;
            line-height: 1.85;
            margin-bottom: 0;
            font-size: 16px;
        }

        .membership-detail-hero__meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .membership-detail-hero__meta-card {
            padding: 18px 20px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: 0 14px 28px rgba(20, 33, 61, 0.05);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .membership-detail-hero__meta-card span {
            display: block;
            color: #617086;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: start;
        }

        .membership-detail-hero__meta-card strong {
            color: #173c36;
            display: block;
            font-size: 18px;
            line-height: 1.3;
            text-align: start;
        }

        .membership-detail-hero__media {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.14), rgba(2, 153, 126, 0.12));
            box-shadow: 0 24px 48px rgba(20, 33, 61, 0.10);
        }

        .membership-detail-hero__media img {
            width: 100%;
            display: block;
        }

        .membership-detail-hero__media-badge {
            position: absolute;
            inset-inline-start: 18px;
            top: 18px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #173c36;
            font-weight: 700;
            box-shadow: 0 14px 28px rgba(20, 33, 61, 0.10);
        }

        .membership-detail-hero__media-badge i {
            color: var(--base-color);
            font-size: 18px;
        }

        .membership-detail-panel {
            padding: 28px;
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: 0 14px 30px rgba(20, 33, 61, 0.05);
        }

        .membership-detail-benefit-list li {
            display: flex;
            gap: 12px;
            color: #617086;
            line-height: 1.7;
        }

        .membership-detail-benefit-list i {
            color: #16a34a;
            font-size: 22px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .membership-detail-story__visual {
            min-height: 420px;
            border-radius: 34px;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.10), rgba(2, 153, 126, 0.10));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .membership-detail-story__visual-card {
            max-width: 400px;
            width: 100%;
            padding: 32px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.65);
            box-shadow: 0 18px 38px rgba(20, 33, 61, 0.10);
            text-align: center;
        }

        .membership-detail-story__visual-icon {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.14), rgba(2, 153, 126, 0.12));
            color: var(--base-color);
            font-size: 32px;
        }

        .membership-detail-story__visual-card strong {
            display: block;
            color: #173c36;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 14px;
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

        .membership-detail-cta__card {
            padding: 42px;
            border-radius: 34px;
            background: linear-gradient(135deg, #f0f8ff 0%, #d9e9f9 100%);
            color: #173c36;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .membership-detail-cta__card .membership-details-section__eyebrow,
        .membership-detail-cta__card .membership-detail-cta__title,
        .membership-detail-cta__card .membership-detail-cta__lead {
            color: inherit;
            position: relative;
            z-index: 2;
        }

        .membership-detail-cta__card .membership-details-section__eyebrow {
            display: inline-block;
            background: rgba(145, 34, 34, 0.08);
            color: #811e1e;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11px;
            line-height: 1;
        }

        .membership-detail-cta__title {
            font-size: clamp(28px, 4vw, 44px);
            margin-bottom: 12px;
            color: #173c36 !important;
        }

        .membership-detail-cta__lead {
            color: #617086 !important;
        }

        .membership-detail-cta__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            position: relative;
            z-index: 2;
        }

        .membership-detail-cta__chips span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            color: #617086;
            font-size: 13px;
        }

        .membership-detail-cta__chips i {
            color: #16a34a;
        }

        .membership-detail-cta__price {
            color: #173c36;
            font-size: 48px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1;
            position: relative;
            z-index: 2;
        }

        .membership-detail-cta__card .badge--success {
            background: #16a34a;
            color: #fff;
            box-shadow: 0 10px 20px rgba(22, 163, 74, 0.2);
            border: none;
            padding: 10px 20px !important;
            font-size: 14px;
            position: relative;
            z-index: 2;
        }

        .membership-detail-panel__richtext p + p {
            margin-top: 12px;
        }

        .highlight-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(34, 87, 191, 0.12), rgba(2, 153, 126, 0.10));
            color: var(--base-color);
            font-size: 22px;
            flex-shrink: 0;
        }

        .highlight-content h6 {
            font-weight: 700;
            color: #173c36;
        }

        .highlight-item {
            padding: 4px 0;
        }

        @media (max-width: 991px) {
            .membership-detail-hero__media {
                min-height: 420px;
            }

            .membership-detail-hero__meta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .membership-detail-hero__media {
                min-height: 340px;
            }

            .membership-detail-cta__card,
            .membership-detail-panel {
                padding: 24px;
            }

            .membership-detail-cta__price {
                font-size: 34px;
            }
        }

        /* RTL: Bootstrap 5 RTL already mirrors column order — do not use flex-row-reverse.
           These rules fix Arabic alignment and icon placement in flex rows. */
        [dir="rtl"] .membership-detail-hero .col-lg-6:first-child {
            text-align: start;
        }

        [dir="rtl"] .membership-detail-hero__eyebrow,
        [dir="rtl"] .membership-detail-hero__title,
        [dir="rtl"] .membership-detail-hero__lead {
            text-align: start;
        }

        [dir="rtl"] .membership-detail-cta .col-lg-8 {
            text-align: start;
        }

        [dir="rtl"] .membership-detail-cta__title,
        [dir="rtl"] .membership-detail-cta__lead {
            text-align: start;
        }

        [dir="rtl"] .membership-detail-cta__chips span {
            direction: rtl;
        }

        [dir="rtl"] .membership-detail-cta__price {
            direction: ltr;
            unicode-bidi: isolate;
        }

        [dir="rtl"] .membership-detail-benefit-list li {
            direction: rtl;
        }

        [dir="rtl"] .membership-details-checklist__item {
            direction: rtl;
        }

        [dir="rtl"] .highlight-item {
            direction: rtl;
        }

        /* Numeric/currency values: dir="ltr" + class on markup; do not force all meta strong — breaks "365 أيام". */
        .membership-detail-hero__meta-value--numeric {
            unicode-bidi: isolate;
        }

        [dir="rtl"] .membership-back-btn:hover i {
            transform: translateX(5px);
        }
    </style>
@once
    @push('script-lib')
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
    @endpush
@endonce
@endpush
