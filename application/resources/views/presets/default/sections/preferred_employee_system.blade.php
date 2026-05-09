@php
    $pasAccent = $general->base_color ?? '#2266cc';
    $features = [
        [
            'icon' => 'fa-user-plus',
            'title' => __('PAS feature quick registration title'),
            'text' => __('PAS feature quick registration text'),
        ],
        [
            'icon' => 'fa-tags',
            'title' => __('PAS feature offers title'),
            'text' => __('PAS feature offers text'),
        ],
        [
            'icon' => 'fa-gift',
            'title' => __('PAS feature additionals title'),
            'text' => __('PAS feature additionals text'),
        ],
        [
            'icon' => 'fa-car-side',
            'title' => __('PAS feature transport title'),
            'text' => __('PAS feature transport text'),
        ],
        [
            'icon' => 'fa-heart-pulse',
            'title' => __('PAS feature medical title'),
            'text' => __('PAS feature medical text'),
        ],
        [
            'icon' => 'fa-briefcase',
            'title' => __('PAS feature marketing title'),
            'text' => __('PAS feature marketing text'),
        ],
    ];
    $joinRoute = url('/engine-screen');
@endphp

<section class="preferred-employee-system section--bg py-100 position-relative z--1" style="--pas-accent: {{ $pasAccent }};">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="section-content mb-50 text-center">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0.05s">@lang('PAS section badge')</h6>
                        <h2 class="title text-center mb-3 fs--36 fw--800 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0.15s">
                            <span class="d-block">@lang('PAS section heading line1')</span>
                            <span class="d-block">@lang('PAS section heading line2')</span>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center pas-feature-grid">
            @foreach ($features as $index => $item)
                <div class="col-md-6 col-lg-4 wow animate__animated animate__fadeInUp"
                    data-wow-delay="{{ number_format(0.05 + $index * 0.06, 2, '.', '') }}s">
                    <div class="pas-feature-card h-100 radius--20 bg--white p-4 d-flex align-items-start gap-3">
                        <div class="pas-feature-card__icon flex-shrink-0 d-flex align-items-center justify-content-center rounded-3">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                        </div>
                        <div class="pas-feature-card__body flex-grow-1 text-start">
                            <h3 class="pas-feature-card__title fs--18 fw--700 mb-2 mb-lg-2">{{ $item['title'] }}</h3>
                            <p class="pas-feature-card__text mb-0 fs--14 fw--400 text-muted">{{ $item['text'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row justify-content-center mt-5 pt-lg-2">
            <div class="col-12 col-xl-11 wow animate__animated animate__fadeInUp" data-wow-delay="0.15s">
                @php
                    $pasCtaBg = 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)';
                @endphp
                <div
                    class="pas-cta pas-cta--brand d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 radius--20 px-4 py-4 px-lg-5 py-lg-4 position-relative overflow-hidden"
                    style="background: {{ $pasCtaBg }}; border: 1px solid rgba(255,255,255,0.1) !important; box-shadow: none !important;">
                    <div class="pas-cta__glow" aria-hidden="true" style="background: radial-gradient(circle, rgba(34, 197, 94, 0.15) 0%, transparent 70%);"></div>
                    <div class="d-flex align-items-center gap-3 gap-lg-4 position-relative">
                        <div class="pas-cta__icon flex-shrink-0 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.12) !important; box-shadow: none !important; width: 100px; height: 100px;">
                            <dotlottie-wc
                                src="https://lottie.host/89d0f80c-e47e-4af5-a0ea-0d28f3d4b925/KOh9T7SrfY.lottie"
                                style="width: 100px; height: 100px; display: block;"
                                autoplay
                                loop></dotlottie-wc>
                        </div>
                        <div class="text-start pas-cta__copy">
                            <span class="pas-cta__eyebrow d-inline-block mb-2 text-uppercase" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8 !important; border-color: rgba(56, 189, 248, 0.2); box-shadow: none !important;">@lang('PAS cta eyebrow')</span>
                            <p class="pas-cta__lead mb-0 fw--800" style="color: #ffffff !important; text-shadow: none !important;">@lang('PAS cta lead')</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-end flex-shrink-0 position-relative">
                        <a href="{{ $joinRoute }}"
                            class="btn btn--base btn--lg pills px-5 py-3 fw--800 text-decoration-none">
                            @lang('PAS cta button')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@once
    @push('style')
        <style>
            .preferred-employee-system .pas-feature-card {
                border: 1px solid rgba(15, 23, 42, 0.06);
                box-shadow: none !important;
                min-height: 100%;
            }

            .preferred-employee-system .pas-feature-card__icon {
                width: 52px;
                height: 52px;
                flex-shrink: 0;
                overflow: visible;
                background: color-mix(in srgb, var(--pas-accent, #2266cc) 10%, #ffffff);
                color: var(--pas-accent, #2266cc);
                line-height: 1;
            }

            .preferred-employee-system .pas-feature-card__icon i {
                font-size: 1.72rem;
                line-height: 1;
            }

            @supports not (color: color-mix(in lab, red, red)) {
                .preferred-employee-system .pas-feature-card__icon {
                    background: rgba(34, 102, 204, 0.1);
                }
            }

            .preferred-employee-system .pas-feature-card__title {
                color: #0f172a;
                line-height: 1.3;
            }

            .preferred-employee-system .pas-feature-card__text {
                color: rgba(15, 23, 42, 0.62) !important;
                line-height: 1.55;
            }

            .preferred-employee-system .pas-cta.pas-cta--brand {
                color: #fff !important;
                border: 1px solid rgba(255, 255, 255, 0.28) !important;
                box-shadow:
                    0 20px 50px rgba(3, 105, 161, 0.35),
                    0 0 0 1px rgba(255, 255, 255, 0.06) inset !important;
            }

            .preferred-employee-system .pas-cta__glow {
                position: absolute;
                width: 420px;
                height: 420px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, transparent 70%);
                opacity: 0.35;
                pointer-events: none;
                top: -60%;
                left: -8%;
            }

            [dir="rtl"] .preferred-employee-system .pas-cta__glow {
                left: auto;
                right: -8%;
            }

            .preferred-employee-system .pas-cta__icon {
                width: 100px;
                height: 100px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                box-shadow: none !important;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .preferred-employee-system .pas-cta__icon i {
                color: #fff !important;
                filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.12));
            }

            .preferred-employee-system .pas-cta__copy {
                color: #fff !important;
            }

            .preferred-employee-system .pas-cta__eyebrow {
                font-size: 0.8125rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                color: #fff !important;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 999px;
                padding: 0.25rem 0.85rem;
                line-height: 1;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .preferred-employee-system .pas-cta__lead {
                font-size: clamp(1.35rem, 2.8vw, 1.85rem);
                line-height: 1.1;
                font-weight: 900 !important;
                color: #fff !important;
                text-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
            }


            @media (max-width: 575px) {
                .preferred-employee-system .pas-feature-card {
                    padding: 1.25rem !important;
                }

                .preferred-employee-system .pas-feature-card__icon i {
                    font-size: 1.58rem;
                }
            }
        </style>
    @endpush
@endonce

@once
    @push('script-lib')
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
    @endpush
@endonce
