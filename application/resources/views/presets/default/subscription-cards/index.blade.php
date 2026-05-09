@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $heroPlan = $plans->first();
        $heroPlanName = $heroPlan ? (is_rtl() && $heroPlan->name_ar ? $heroPlan->name_ar : $heroPlan->name) : __('Subscription Plan');
        $heroPlanImage = $heroPlan && $heroPlan->image_file ? asset(getFilePath('membershipPlanImage') . '/' . $heroPlan->image_file) : asset('assets/presets/default/images/Live-travel-control-center.jpg');
    @endphp

    <section class="membership-card-page py-100">
        <div class="container">
            <div class="membership-card-page__header text-center">
                <span class="membership-card-page__eyebrow">@lang('Subscription Cards')</span>
                <h1 class="membership-card-page__title">@lang('Subscription Cards')</h1>
                <p class="membership-card-page__lead">@lang('A separate view for the membership subscription grid and the available plan details.') </p>
            </div>

            <div class="membership-card-preview mb-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="membership-card-preview__image-wrap">
                            <img src="{{ $heroPlanImage }}" alt="{{ $heroPlanName }}">
                            <span class="membership-card-preview__badge">@lang('Subscription')</span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="membership-card-preview__content">
                            <span class="membership-card-page__eyebrow">@lang('About this view')</span>
                            <h2>@lang('Use this page to browse subscription plans in a cleaner, separate layout.')</h2>
                            <p>@lang('This page keeps the same plan data and subscription actions, but gives it a dedicated name and page title so the user can access it directly from the menu.') </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                @forelse($plans as $plan)
                    @php
                        $planName = is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name;
                        $planDescription = is_rtl() && $plan->description_ar ? $plan->description_ar : $plan->description;
                        $planImage = $plan->image_file ? asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file) : null;
                        $benefits = $plan->benefits ?? [];
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="membership-card-item {{ $currentMembership && $currentMembership->membership_plan_id == $plan->id ? 'is-current' : '' }}">
                            <div class="membership-card-item__top">
                                <div>
                                    <h4 class="mb-1 text-truncate">{{ $planName }}</h4>
                                    <p class="mb-0">{{ $plan->duration_days ? $plan->duration_days . ' ' . __('Days') : __('Lifetime') }}</p>
                                </div>
                                <span class="membership-card-item__arrow"><i class="las la-arrow-up"></i></span>
                            </div>

                            <div class="membership-card-item__image-wrap">
                                @if($planImage)
                                    <img src="{{ $planImage }}" alt="{{ $planName }}">
                                @else
                                    <div class="membership-card-item__empty">@lang('No image')</div>
                                @endif
                                <span class="membership-card-item__tag">@lang('Membership')</span>
                            </div>

                            <div class="membership-card-item__body">
                                <div class="membership-card-item__price-row">
                                    <span class="membership-card-item__price">{{ $general->cur_sym }}{{ showAmount($plan->price) }}</span>
                                    <span class="membership-card-item__points">{{ $plan->bonus_points }} @lang('Points')</span>
                                </div>

                                @if($planDescription)
                                    <p class="membership-card-item__desc">{{ $planDescription }}</p>
                                @endif

                                <div class="membership-card-item__meta">
                                    <span><i class="las la-check-circle"></i> @lang('PDF support') {{ $plan->pdf_file ? __('Available') : __('Not available') }}</span>
                                    <span><i class="las la-gift"></i> @lang('Bonus') {{ $plan->bonus_points }}</span>
                                </div>

                                @if($benefits)
                                    <div class="membership-card-item__benefits">
                                        @foreach(array_slice($benefits, 0, 4) as $benefit)
                                            <div class="membership-card-item__benefit">
                                                <i class="las la-check"></i>
                                                <span>{{ $benefit }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="membership-card-item__footer">
                                @if($plan->pdf_file)
                                    <a class="btn btn--light btn-sm pills" href="{{ asset(getFilePath('membershipPlanPdf') . '/' . $plan->pdf_file) }}">@lang('Download PDF')</a>
                                @endif

                                @auth
                                    @if(!($currentMembership && $currentMembership->membership_plan_id == $plan->id))
                                        <form action="{{ route('user.membership.subscribe') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="membership_plan_id" value="{{ $plan->id }}">
                                            <button class="btn btn--base btn-sm pills" type="submit">@lang('Subscribe')</button>
                                        </form>
                                    @else
                                        <span class="badge badge--success">@lang('Current Plan')</span>
                                    @endif
                                @else
                                    <a href="{{ route('user.register') }}" class="btn btn--base btn-sm pills">@lang('Login to Subscribe')</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">@lang('No subscription plans available right now.')</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .membership-card-page {
            background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        }

        .membership-card-page__header {
            max-width: 720px;
            margin: 0 auto 32px;
        }

        .membership-card-page__eyebrow {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--base-color);
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-size: 12px;
        }

        .membership-card-page__title {
            color: #173c36;
            font-size: clamp(34px, 4vw, 54px);
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 12px;
        }

        .membership-card-page__lead {
            color: #617086;
            line-height: 1.85;
            margin-bottom: 0;
            font-size: 16px;
        }

        .membership-card-preview {
            padding: 28px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(20, 33, 61, 0.06);
            box-shadow: 0 18px 40px rgba(20, 33, 61, 0.05);
            margin-bottom: 32px;
        }

        .membership-card-preview__image-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16 / 10;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(91, 156, 249, 0.15), rgba(2, 153, 126, 0.12));
            box-shadow: 0 16px 34px rgba(20, 33, 61, 0.10);
        }

        .membership-card-preview__image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .membership-card-preview__badge {
            position: absolute;
            left: 16px;
            top: 16px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.90);
            color: #173c36;
            font-weight: 700;
            font-size: 12px;
        }

        .membership-card-preview__content h2 {
            color: #173c36;
            font-size: clamp(26px, 3vw, 38px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 14px;
        }

        .membership-card-preview__content p {
            color: #617086;
            line-height: 1.8;
            margin-bottom: 0;
            font-size: 15px;
        }

        .membership-card-item {
            height: 100%;
            overflow: hidden;
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(20, 33, 61, 0.07);
            box-shadow: 0 14px 30px rgba(20, 33, 61, 0.05);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .membership-card-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 42px rgba(20, 33, 61, 0.10);
        }

        .membership-card-item.is-current {
            border-color: rgba(22, 163, 74, 0.22);
            box-shadow: 0 18px 40px rgba(22, 163, 74, 0.10);
        }

        .membership-card-item__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 18px 14px;
        }

        .membership-card-item__top h4 {
            color: #173c36;
            font-size: 18px;
            font-weight: 800;
        }

        .membership-card-item__top p {
            color: #617086;
            font-size: 13px;
            margin-bottom: 0;
        }

        .membership-card-item__arrow {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #f3f6fb;
            color: #173c36;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .membership-card-item__image-wrap {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(91, 156, 249, 0.15), rgba(2, 153, 126, 0.12));
        }

        .membership-card-item__image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .membership-card-item__empty {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7b8aa7;
            font-weight: 700;
        }

        .membership-card-item__tag {
            position: absolute;
            left: 14px;
            bottom: 14px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
            color: #173c36;
            font-size: 12px;
            font-weight: 700;
        }

        .membership-card-item__body {
            padding: 18px;
        }

        .membership-card-item__price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .membership-card-item__price {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(91, 156, 249, 0.12);
            color: #173c36;
            font-weight: 800;
            font-size: 17px;
        }

        .membership-card-item__points {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(183, 220, 20, 0.18);
            color: #173c36;
            font-size: 12px;
            font-weight: 700;
        }

        .membership-card-item__desc {
            color: #617086;
            line-height: 1.75;
            margin-bottom: 14px;
            font-size: 15px;
        }

        .membership-card-item__meta {
            display: grid;
            gap: 8px;
            margin-bottom: 14px;
        }

        .membership-card-item__meta span,
        .membership-card-item__benefit {
            color: #5f6f89;
            font-size: 14px;
        }

        .membership-card-item__meta i,
        .membership-card-item__benefit i {
            color: var(--base-color);
            margin-right: 6px;
        }

        .membership-card-item__benefits {
            display: grid;
            gap: 8px;
        }

        .membership-card-item__benefit {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .membership-card-item__benefit i {
            margin-top: 3px;
        }

        .membership-card-item__footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 0 18px 18px;
        }

        .membership-card-item__footer .btn,
        .membership-card-item__footer .badge {
            border-radius: 999px;
        }

        @media (max-width: 991px) {
            .membership-card-preview {
                padding: 22px;
            }
        }

        @media (max-width: 575px) {
            .membership-card-page__title {
                font-size: 30px;
            }

            .membership-card-preview,
            .membership-card-item__top,
            .membership-card-item__body,
            .membership-card-item__footer {
                padding-left: 14px;
                padding-right: 14px;
            }
        }
    </style>
@endpush
