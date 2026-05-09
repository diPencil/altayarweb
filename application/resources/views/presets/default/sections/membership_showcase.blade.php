@php
    $membershipHero = getContent('membership_section.content', true);
    $plans = App\Models\MembershipPlan::where('status', 1)->orderBy('id', 'asc')->get();
    $hubTitle =
        $membershipHero && !empty(trim((string) ($membershipHero->data_values->title ?? '')))
            ? getLangContent($membershipHero->data_values, 'title')
            : __('Point System Activity');
    $hubHeading =
        $membershipHero && !empty(trim((string) ($membershipHero->data_values->heading ?? '')))
            ? getLangContent($membershipHero->data_values, 'heading')
            : __('Checkout All Membership Plans');
    $hubSub =
        $membershipHero && !empty(trim((string) ($membershipHero->data_values->sub_heading ?? '')))
            ? getLangContent($membershipHero->data_values, 'sub_heading')
            : __(
                'All prices on the site are included in the usual taxes in the selected city, which guarantees that there are no additional or hidden expenses.',
            );
    $accent = $general->base_color ?? '#2266cc';
    $statSatisfiedPct = random_int(81, 96);
    $statSuccessPct = random_int(88, 99);
    if ($statSuccessPct <= $statSatisfiedPct) {
        $statSuccessPct = min(99, $statSatisfiedPct + random_int(1, 5));
    }
    $tplImages = 'assets/presets/' . activeTemplateName() . '/images/';
    $repoAssetsRoot = dirname(base_path());
    $membershipFirstAsset = static function (array $filenames) use ($tplImages, $repoAssetsRoot): ?string {
        foreach ($filenames as $fn) {
            $rel = $tplImages . $fn;
            if (is_file(public_path($rel))) {
                return asset($rel);
            }
            if (is_file($repoAssetsRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel))) {
                return asset($rel);
            }
        }

        return null;
    };
    $promoCardArtUrl = $membershipFirstAsset([
        'Membership Promo Card.png',
        'Membership Promo Card.jpg',
        'Promo Right Column.png',
        'Point System Activity.png',
        'Point System Activity.jpg',
        'Point System Activity.webp',
        'Point System Activity.PNG',
        'point-system-activity.png',
    ]);
    $peopleImageUrl = $membershipFirstAsset([
        'people.png',
        'people.jpg',
        'people.webp',
        'People.png',
        'People.jpg',
        'People.webp',
    ]) ?? asset($tplImages . 'people.png');
    $lottieMascotUrl = 'https://lottie.host/db59c550-c29c-4a81-8cf8-55a67385f60f/3ApTdePRlr.lottie';
@endphp

@push('style-lib')
    <link rel="preload"
        href="{{ $lottieMascotUrl }}"
        as="fetch"
        crossorigin>
    <link rel="modulepreload"
        href="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js">
@endpush

<section
    class="membership-showcase-section news-section py-100 z--1 position-relative section--bg membership-showcase--dark membership-showcase--compact is-progress-visible"
    style="--membership-accent: {{ $accent }}; --ms-stat-clients: {{ $statSatisfiedPct }}%; --ms-stat-success: {{ $statSuccessPct }}%;">
    <div class="membership-showcase-bg" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50 membership-showcase-header-block">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0 wow animate__animated animate__fadeInUp w-100"
                            style="text-align: center !important;"
                            data-wow-delay="0.05s">{{ $hubTitle }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp membership-showcase-heading text--white w-100"
                            style="text-align: center !important;"
                            data-wow-delay="0.2s">
                            {{ $hubHeading }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400 membership-showcase-sub mb-0 w-100"
                            style="text-align: center !important;"
                            data-wow-delay="0.3s">
                            {{ $hubSub }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-3 align-items-stretch justify-content-center membership-showcase-three-cols">
            {{-- Column 1: membership tiers --}}
            <div class="col-xl-4 col-lg-4 d-flex flex-column wow animate__animated animate__fadeInUp" data-wow-delay="0.1s">
                <div class="membership-tier-card bg--white radius--20 p-2 h-100 shadow-sm d-flex flex-column justify-content-between flex-grow-1 w-100">
                    @forelse ($plans as $plan)
                        @php
                            $isRtl = is_rtl();
                            $planName = $isRtl && $plan->name_ar ? $plan->name_ar : $plan->name;
                        @endphp
                        <a href="{{ route('public.membership.details.show', $plan->id) }}"
                            class="membership-tier-tab d-flex align-items-center gap-3 w-100 text-decoration-none {{ $loop->first ? 'is-active' : '' }} wow animate__animated animate__fadeInUp"
                            data-wow-delay="{{ 0.1 + $loop->index * 0.05 }}s">
                            <div class="membership-tier-tab__img-wrap flex-shrink-0">
                                <img src="{{ $plan->image_url }}" alt="{{ $planName }}" class="membership-tier-tab__img">
                            </div>
                            <span class="membership-tier-tab__name body--font fw--600 fs--15">{{ $planName }}</span>
                        </a>
                    @empty
                        <p class="mb-0 text-center text-muted">@lang('No membership plans available right now.')</p>
                    @endforelse
                </div>
            </div>

            {{-- Column 2: stats & message (no wow on column — hides Lottie until animation; slows perceived load) --}}
            <div class="col-xl-4 col-lg-4 d-flex flex-column">
                <div
                    class="membership-mid-card membership-showcase-mid h-100 d-flex flex-column justify-content-between px-3 py-3 align-items-center text-center flex-grow-1 w-100 radius--20">
                    <div class="membership-mid-card__top w-100 d-flex flex-column align-items-center">
                    <div class="membership-showcase-lottie mb-2 mb-md-3" style="min-height: 300px;">
                        <dotlottie-wc
                            src="{{ $lottieMascotUrl }}"
                            style="width: 300px; height: 300px; max-width: 100%; display: block;"
                            autoplay
                            loop></dotlottie-wc>
                    </div>
                    <h3 class="fs--20 fw--800 text--white mb-2 wow animate__animated animate__fadeInUp" data-wow-delay="0.25s">
                        <span class="d-block">@lang('We have selected')</span>
                        <span class="d-block">@lang('6 membership cards...')</span>
                    </h3>
                    <p class="membership-showcase-muted fs--14 mb-2 mb-md-3 wow animate__animated animate__fadeInUp" data-wow-delay="0.3s">
                        @lang('To suit your activities for the duration of the subscription, valid for one year.')
                    </p>

                    <div class="membership-people-img-wrap mb-0 mb-sm-1 wow animate__animated animate__zoomIn"
                        data-wow-delay="0.35s">
                        <img src="{{ $peopleImageUrl }}" alt=""
                            class="membership-people-img w-100 d-block mx-auto">
                    </div>
                    </div>

                    <div class="membership-mid-card__stats w-100 pt-2 mt-1">
                    <div class="membership-progress mb-2 membership-progress--clients w-100 wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="membership-showcase-muted fs--14">@lang('Satisfied Clients')</span>
                            <span class="membership-stat-pct fs--14 fw--700">{{ $statSatisfiedPct }}%</span>
                        </div>
                        <div class="membership-progress-track">
                            <div class="membership-progress-fill membership-progress-fill--clients" style="width: 93%;"></div>
                        </div>
                    </div>
                    <div class="membership-progress membership-progress--success w-100 wow animate__animated animate__fadeInUp" data-wow-delay="0.45s">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="membership-showcase-muted fs--14">@lang('Success Rate')</span>
                            <span class="membership-stat-pct fs--14 fw--700">{{ $statSuccessPct }}%</span>
                        </div>
                        <div class="membership-progress-track">
                            <div class="membership-progress-fill membership-progress-fill--success" style="width: 97%;"></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            {{-- Column 3: hero artwork inside card, main heading under it (reference layout) --}}
            <div class="col-xl-4 col-lg-4 d-flex flex-column wow animate__animated animate__fadeInRight" data-wow-delay="0.3s">
                <a href="{{ route('public.membership.details') }}"
                    class="membership-promo-block membership-promo-block--stacked text-decoration-none d-flex flex-column h-100 flex-grow-1 w-100 radius--20 overflow-hidden position-relative">
                    <div class="membership-promo-card-media d-flex flex-column flex-grow-1 w-100">
                        <video
                            src="{{ asset('assets/videos/point-system-activity.mp4') }}"
                            class="membership-promo-hero-full w-100"
                            autoplay
                            loop
                            muted
                            playsinline
                            preload="auto"
                            poster="{{ $promoCardArtUrl }}"
                            style="object-fit: cover;"></video>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .membership-showcase-section.membership-showcase--compact.news-section.py-100 {
            padding-top: clamp(2.65rem, 4.2vw, 3.65rem) !important;
            padding-bottom: clamp(2.65rem, 4.2vw, 3.65rem) !important;
        }

        .membership-showcase-section .membership-showcase-header-block.section-content.mb-50 {
            margin-bottom: 1.65rem !important;
            text-align: center !important;
        }

        [dir="rtl"] .membership-showcase-header-block.section-content {
            text-align: center !important;
        }

        @media (min-width: 992px) {
            .membership-showcase-section .membership-showcase-header-block.section-content.mb-50 {
                margin-bottom: 1.85rem !important;
            }
        }

        /* Force dark shell: do NOT use background:transparent here — it beat our single-class bg rule and showed light .section--bg from the theme */
        section.membership-showcase-section.news-section.section--bg.membership-showcase--dark {
            background: #0d1117 !important;
            background-image: none !important;
            color: rgba(255, 255, 255, 0.92);
        }

        .membership-badge-img {
            max-width: min(420px, 88vw);
            height: auto;
            display: block;
        }

        .membership-showcase-heading,
        .membership-showcase-heading .char,
        .membership-showcase-heading .word {
            color: #fff !important;
        }

        .membership-showcase-title.text--white,
        .membership-showcase-section .title.text--white {
            color: var(--ms-fg) !important;
        }

        .membership-showcase-section .subtitle,
        .membership-showcase-sub {
            color: var(--ms-muted) !important;
        }

        .membership-showcase-section .splite-text .char,
        .membership-showcase-section .splitting .char,
        .membership-showcase-section .splite-text .word,
        .membership-showcase-section .splitting .word {
            color: inherit !important;
        }

        .membership-showcase-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0.5;
            background:
                radial-gradient(circle at 50% 35%, rgba(125, 211, 252, 0.06) 0%, transparent 55%),
                repeating-linear-gradient(0deg, transparent, transparent 35px, rgba(255, 255, 255, 0.04) 35px, rgba(255, 255, 255, 0.04) 36px),
                repeating-linear-gradient(60deg, transparent, transparent 35px, rgba(255, 255, 255, 0.03) 35px, rgba(255, 255, 255, 0.03) 36px),
                repeating-linear-gradient(120deg, transparent, transparent 35px, rgba(255, 255, 255, 0.03) 35px, rgba(255, 255, 255, 0.03) 36px);
        }

        .membership-tier-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .membership-mid-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.22);
        }

        .membership-showcase-three-cols > [class*="col-"] {
            min-height: 0;
        }

        .membership-showcase--compact .membership-tier-tab {
            padding: 0.62rem 0.9rem;
            border-radius: 10px;
            margin-bottom: 0.45rem;
        }

        .membership-showcase--compact .membership-tier-tab__img-wrap {
            width: 38px;
            height: 38px;
            border-radius: 7px;
        }

        .membership-showcase--compact .membership-tier-tab__img {
            padding: 3px;
        }

        .membership-showcase--compact .membership-tier-tab__name {
            font-size: clamp(1.02rem, 0.45vw + 0.94rem, 1.17rem) !important;
            font-weight: 700 !important;
            line-height: 1.32;
            overflow-wrap: break-word;
        }

        .membership-showcase--compact .membership-tier-tab.is-active {
            box-shadow: 0 5px 18px rgba(14, 165, 233, 0.28);
        }

        .membership-tier-tab {
            flex-grow: 1;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 0.65rem;
            color: #1f2937;
            background: #ffffff;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .membership-tier-tab__img-wrap {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
        }

        .membership-tier-tab__img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }

        .membership-tier-tab__name {
            font-size: 1.18rem !important;
            font-weight: 700 !important;
            line-height: 1.32;
            overflow-wrap: break-word;
        }

        .membership-tier-tab:last-child {
            margin-bottom: 0;
        }

        .membership-tier-tab__icon {
            color: #0284c7;
            font-size: 0.95rem;
        }

        .membership-tier-tab:hover:not(.is-active) {
            border-color: #bae6fd;
            box-shadow: 0 2px 12px rgba(14, 165, 233, 0.12);
            background: #f8fafc;
        }

        .membership-tier-tab.is-active {
            background: linear-gradient(135deg, #22d3ee 0%, #0ea5e9 45%, #0284c7 100%) !important;
            color: #fff !important;
            border-color: transparent !important;
            box-shadow: 0 8px 28px rgba(14, 165, 233, 0.35);
        }

        .membership-tier-tab.is-active .membership-tier-tab__icon {
            color: #fff !important;
        }

        .membership-showcase-muted {
            color: var(--ms-muted);
        }

        .membership-showcase-lottie {
            display: flex;
            justify-content: center;
            min-height: 300px;
            align-items: center;
        }

        .membership-showcase-lottie dotlottie-wc {
            display: block;
            visibility: visible;
            opacity: 1;
            max-width: 100%;
        }

        @media (max-width: 575px) {
            .membership-showcase-lottie {
                min-height: 168px;
            }

            .membership-showcase-lottie dotlottie-wc {
                width: 168px !important;
                height: 168px !important;
            }
        }

        .membership-showcase--compact .membership-people-img-wrap {
            max-width: min(240px, 100%);
        }

        .membership-people-img-wrap {
            max-width: min(280px, 100%);
            margin-inline: auto;
        }

        .membership-showcase--compact .membership-people-img {
            max-height: 92px;
        }

        .membership-people-img {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
            object-position: center center;
        }

        .membership-stat-pct {
            color: #22d3ee !important;
        }

        .membership-progress-track {
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            overflow: hidden;
        }

        .membership-progress-fill {
            height: 100%;
            width: 0%; /* Default to 0, animation will drive it */
            border-radius: 999px;
            animation: fillProgressAnim 2.5s cubic-bezier(0.1, 0.5, 0.2, 1) forwards;
        }

        .membership-progress--clients .membership-progress-fill--clients {
            --target-width: 93%;
        }

        .membership-progress--success .membership-progress-fill--success {
            --target-width: 97%;
        }

        @keyframes fillProgressAnim {
            from { width: 0%; }
            to { width: var(--target-width); }
        }

        .membership-progress-fill--clients {
            background: linear-gradient(90deg, #22d3ee, #06b6d4); /* سماوي جذاب */
        }

        .membership-progress-fill--success {
            background: linear-gradient(90deg, #38bdf8, #0284c7); /* أزرق فاتح متناسق */
        }

        .membership-promo-block {
            min-height: 0;
            background: linear-gradient(168deg, #042f2e 0%, #0c4a6e 42%, #082f49 100%);
            border: 1px solid rgba(34, 211, 238, 0.22);
        }

        .membership-promo-card-media {
            background: linear-gradient(168deg, #042f2e 0%, #0b3d5c 55%, #082f49 100%);
            min-height: 0;
        }

        .membership-promo-hero-full {
            display: block;
            width: 100%;
            flex: 1 1 auto;
            min-height: 200px;
            height: 100%;
            max-height: none;
            object-fit: cover;
            object-position: center;
        }

        .membership-promo-ref {
            position: relative;
            padding-bottom: 0.5rem;
            flex: 1 1 auto;
            min-height: 0;
        }

        .membership-promo-ref--fallback .membership-promo-ref__scene {
            margin-top: 0;
            min-height: 170px;
        }

        .membership-promo-ref__head--en .mpr-line {
            display: block;
            line-height: 1.15;
        }

        .membership-promo-ref__head--en .mpr-line--mute {
            color: rgba(255, 255, 255, 0.95);
            font-weight: 700;
            font-size: clamp(0.82rem, 2.2vw, 1rem);
            letter-spacing: 0.02em;
        }

        .membership-promo-ref__head--en .mpr-outline {
            margin-top: 0.15rem;
            font-size: clamp(1.35rem, 4.2vw, 2rem);
            font-weight: 900;
            letter-spacing: 0.12em;
            color: transparent;
            -webkit-text-stroke: 1.5px #fff;
        }

        .membership-promo-ref__head--en .mpr-cyan {
            color: #7dd3fc;
            font-weight: 800;
            font-size: clamp(1.05rem, 3.4vw, 1.45rem);
            letter-spacing: 0.06em;
        }

        .membership-promo-ref__head--en .mpr-cyan--lg {
            font-size: clamp(1.2rem, 3.8vw, 1.65rem);
        }

        .membership-promo-ref__scene {
            min-height: 170px;
            margin-top: 0.5rem;
        }

        .mpr-phone {
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 44%;
            max-width: 168px;
            aspect-ratio: 9 / 16;
            border-radius: 22px;
            border: 4px solid rgba(255, 255, 255, 0.28);
            overflow: hidden;
            background: linear-gradient(180deg, #0c4a6e, #075985);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55);
            z-index: 2;
        }

        .mpr-phone img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .mpr-plane {
            position: absolute;
            left: 0%;
            bottom: 26%;
            width: 44%;
            max-width: 150px;
            z-index: 3;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.45));
            animation: mpr-float-plane 4.2s ease-in-out infinite;
        }

        .mpr-person {
            position: absolute;
            right: -2%;
            bottom: 0;
            width: 50%;
            max-width: 180px;
            z-index: 4;
            filter: drop-shadow(0 14px 28px rgba(0, 0, 0, 0.5));
        }

        .membership-showcase--compact .mpr-phone {
            max-width: 142px;
            border-radius: 18px;
            border: 3px solid rgba(255, 255, 255, 0.28);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
        }

        .membership-showcase--compact .mpr-plane {
            max-width: 120px;
        }

        .membership-showcase--compact .mpr-person {
            max-width: 148px;
        }

        @keyframes mpr-float-plane {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-8px, 6px);
            }
        }

        .mpr-discount-badge {
            background: radial-gradient(circle at 25% 20%, #262626, #0a0a0a);
            border: 2px solid #38bdf8;
            border-radius: 999px;
            padding: 0.55rem 1.35rem 0.65rem;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.45);
            max-width: 220px;
        }

        .membership-showcase--compact .mpr-discount-badge {
            padding: 0.45rem 1.05rem 0.5rem;
            max-width: 200px;
        }

        .mpr-discount-badge__small {
            font-size: 0.72rem;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .mpr-discount-badge__big {
            font-size: 1rem;
            font-weight: 900;
            color: #7dd3fc;
            letter-spacing: 0.03em;
        }

        @media (max-width: 575px) {
            .mpr-person {
                max-width: 150px;
            }

            .mpr-plane {
                max-width: 120px;
            }

            .mpr-phone {
                max-width: 138px;
            }
        }

        @supports not (color: color-mix(in lab, red, red)) {
            .membership-progress-fill--clients {
                background: var(--membership-accent, #2266cc);
            }
        }
    </style>
@endpush

@once
    @push('script-lib')
        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
    @endpush
@endonce

@push('script')
    <script>
        (function() {
            var section = document.querySelector('.membership-showcase-section');
            if (!section) {
                return;
            }

            function revealProgressBars() {
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        section.classList.add('is-progress-visible');
                    });
                });
            }

            if (!('IntersectionObserver' in window)) {
                revealProgressBars();
                return;
            }

            var obs = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        revealProgressBars();
                        obs.disconnect();
                    }
                });
            }, {
                threshold: 0,
                rootMargin: '0px 0px 80px 0px'
            });

            obs.observe(section);

            function checkAlreadyVisible() {
                var r = section.getBoundingClientRect();
                var vh = window.innerHeight || document.documentElement.clientHeight;
                if (r.bottom > 32 && r.top < vh - 32) {
                    revealProgressBars();
                    obs.disconnect();
                }
            }

            checkAlreadyVisible();
            requestAnimationFrame(checkAlreadyVisible);
            requestAnimationFrame(function() {
                requestAnimationFrame(checkAlreadyVisible);
            });

            window.addEventListener('load', checkAlreadyVisible, {
                once: true
            });

            setTimeout(function() {
                if (!section.classList.contains('is-progress-visible')) {
                    revealProgressBars();
                    obs.disconnect();
                }
            }, 2500);
        })();
    </script>
@endpush
