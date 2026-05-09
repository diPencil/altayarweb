@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $pageTitleText = __('Membership Card');
        $heroDescription = __('Browse the membership plans below and choose the one that fits your travel needs, benefits, and budget.');
    @endphp

    <section class="membership-card-page py-100">
        <div class="container">
            <div class="membership-card-page__header text-center">
                <span class="membership-card-page__eyebrow">@lang('Membership Cards')</span>
                <h1 class="membership-card-page__title">@lang('Membership Card')</h1>
                <p class="membership-card-page__lead">{{ $heroDescription }}</p>
            </div>

            <div class="row g-4 membership-card-grid">
                @forelse($plans as $plan)
                    @php
                        $planName = is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name;
                        $planDescription = is_rtl() && $plan->description_ar ? $plan->description_ar : $plan->description;
                        $planImage = $plan->image_file ? asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file) : null;
                        $benefits = is_rtl() && !empty($plan->benefits_ar) ? $plan->benefits_ar : ($plan->benefits ?? []);
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="membership-card-item {{ $currentMembership && $currentMembership->membership_plan_id == $plan->id ? 'is-current' : '' }}">
                            <div class="membership-card-item__top">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="membership-card-item__thumb">
                                        @if($planImage)
                                            <img src="{{ $planImage }}" alt="{{ $planName }}">
                                        @else
                                            <div class="membership-card-item__thumb-empty"><i class="las la-gem"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-truncate">{{ $planName }}</h4>
                                        <p class="mb-0 text-muted" style="font-size: 12px;"><i class="las la-clock"></i> @lang('Valid for'): {{ $plan->duration_days ? $plan->duration_days . ' ' . __('Days') : __('Lifetime') }}</p>
                                    </div>
                                </div>

                            </div>

                            <div class="membership-card-item__body">
                                <div class="membership-card-item__price-row">
                                    <span class="membership-card-item__price">{{ $general->cur_sym }}{{ showAmount($plan->price) }}</span>
                                    <span class="membership-card-item__points">{{ $plan->bonus_points }} @lang('Points')</span>
                                </div>

                                <a href="{{ route('public.membership.details.show', $plan->id) }}" class="btn-view-details-full">@lang('View Details')</a>

                                @if($planDescription)
                                    <p class="membership-card-item__desc mt-3">{{ $planDescription }}</p>
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
                                @auth
                                    @if(!($currentMembership && $currentMembership->membership_plan_id == $plan->id))
                                        <form action="{{ route('user.membership.subscribe') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="membership_plan_id" value="{{ $plan->id }}">
                                            <button class="btn btn--base btn-sm pills px-4" type="submit">@lang('Subscribe Now')</button>
                                        </form>
                                    @else
                                        <span class="badge badge--success pills p-2 px-3">@lang('Active Plan')</span>
                                    @endif
                                @else
                                    <a href="{{ route('user.register') }}" class="btn btn--base btn-sm pills px-4">@lang('Subscribe Now')</a>
                                @endauth
                            </div>
                        </div>
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
            border: 2px solid #3498db;
            box-shadow: 0 20px 42px rgba(52, 152, 219, 0.15);
            position: relative;
        }

        .membership-card-item.is-current::before {
            content: '\f058';
            font-family: "Line Awesome Free";
            font-weight: 900;
            position: absolute;
            top: -12px;
            right: -12px;
            width: 30px;
            height: 30px;
            background: #3498db;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            z-index: 10;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .membership-card-item__thumb {
            width: 68px;
            height: 68px;
            overflow: visible;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .membership-card-item__thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .membership-card-item__thumb-empty {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #7b8aa7;
        }

        .membership-card-item__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 20px 0;
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

        .btn-view-details-full {
            display: flex;
            align-items: center;
            justify-content: center;
            width: calc(100% + 36px);
            margin-left: -18px;
            background: linear-gradient(90deg, #f0f7ff 0%, #e1f0ff 100%);
            color: #1a4d45;
            padding: 16px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: all 0.4s ease;
            text-decoration: none;
            border-top: 1px solid rgba(52, 152, 219, 0.08);
            border-bottom: 1px solid rgba(52, 152, 219, 0.08);
        }

        .btn-view-details-full:hover {
            background: linear-gradient(90deg, #e1f0ff 0%, #d1e8ff 100%);
            color: #3498db;
            letter-spacing: 0.12em;
        }

        .membership-card-item__footer .btn {
            border-radius: 999px;
            font-weight: 700;
            padding: 8px 16px;
        }

        .membership-card-item__footer .btn--light:hover {
            background: #f1f5f9;
            border-color: rgba(0,0,0,0.1);
            color: #173c36;
        }

        .membership-card-item__footer .badge {
            border-radius: 999px;
        }

        @media (max-width: 991px) {
            .membership-card-page__header {
                margin-bottom: 24px;
            }
        }

        @media (max-width: 575px) {
            .membership-card-page__title {
                font-size: 30px;
            }

            .membership-card-item__top,
            .membership-card-item__body,
            .membership-card-item__footer {
                padding-left: 14px;
                padding-right: 14px;
            }
        }
    </style>
@endpush
