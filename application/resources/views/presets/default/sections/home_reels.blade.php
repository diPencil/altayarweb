@php
    $reelsAllUrl = route('reels.index');
@endphp

<section class="home-reels-section section--bg py-80 py-lg-100 position-relative z--1">
    <div class="container position-relative">
        <div class="row justify-content-center align-items-end g-4 mb-4 mb-lg-40">
            <div class="col-lg-8 text-center text-lg-start">
                <h6 class="heading third--font fs--18 fw--700 text--base mb-2 wow animate__animated animate__fadeInUp"
                    data-wow-delay="0.05s">@lang('Home reels kicker')</h6>
                <h2 class="title fs--32 fs-lg--36 fw--800 mb-3 mb-lg-3 wow animate__animated animate__fadeInUp splite-text"
                    data-splitting data-wow-delay="0.1s">
                    @lang('Home reels title')
                </h2>
                <p class="mb-0 fs--16 text-muted home-reels-section__lead wow animate__animated animate__fadeInUp"
                    data-wow-delay="0.12s">
                    @lang('Home reels description')
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end wow animate__animated animate__fadeInUp" data-wow-delay="0.15s">
                <a href="{{ $reelsAllUrl }}" class="btn btn--base px-4 py-3 fw--700 radius--10">
                    @lang('Home reels CTA')
                </a>
            </div>
        </div>

        @if (isset($homeReels) && $homeReels->isNotEmpty())
            <div class="home-reels-strip position-relative wow animate__animated animate__fadeInUp" data-wow-delay="0.18s">
                <div class="home-reels-slider">
                    @foreach ($homeReels as $reel)
                        @php
                            $thumb = $reel->thumbnail_url;
                            $label = $reel->source_name_display ?: $reel->title_display;
                            $reelUrl = route('reels.index', ['reel' => $reel->id]);
                        @endphp
                        <div class="home-reels-slider__cell">
                            <a href="{{ $reelUrl }}" class="home-reel-card text-decoration-none"
                                aria-label="{{ $label }}">
                                <div class="home-reel-card__frame">
                                    @php
                                        $videoUrl = $reel->video_url;
                                    @endphp
                                    @if ($videoUrl)
                                        <video class="home-reel-card__cover home-reel-card__cover--video {{ $thumb ? 'home-reel-card__video--deferred' : '' }}"
                                            muted
                                            playsinline
                                            preload="none"
                                            disablePictureInPicture>
                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                        </video>
                                    @elseif (! $thumb)
                                        <div class="home-reel-card__cover home-reel-card__cover--fallback" role="img" aria-hidden="true"></div>
                                    @endif
                                    @if ($thumb)
                                        <img src="{{ $thumb }}" alt="" class="home-reel-card__cover home-reel-card__cover--img" loading="lazy"
                                            width="360" height="640"
                                            onerror="var v=this.previousElementSibling;if(v&amp;&amp;v.tagName==='VIDEO'){this.style.display='none';v.classList.remove('home-reel-card__video--deferred');if(window.primeHomeReelVideo){window.primeHomeReelVideo(v);}}">
                                    @endif
                                    <div class="home-reel-card__shade" aria-hidden="true"></div>
                                    <div class="home-reel-card__avatar-ring" aria-hidden="true">
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="home-reel-card__avatar" loading="lazy" width="80" height="80"
                                                onerror="this.style.display='none';var s=this.nextElementSibling;if(s){s.classList.remove('d-none');}">
                                            <span class="home-reel-card__avatar-icon d-none"><i class="fas fa-play"></i></span>
                                        @elseif ($videoUrl)
                                            <span class="home-reel-card__avatar-icon"><i class="fas fa-play"></i></span>
                                        @else
                                            <span class="home-reel-card__avatar-icon"><i class="fas fa-play"></i></span>
                                        @endif
                                    </div>
                                    <div class="home-reel-card__caption">
                                        <span class="home-reel-card__name">{{ Str::limit($label, 42) }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-center text-muted mb-0 wow animate__animated animate__fadeInUp">@lang('Home reels empty')</p>
        @endif
    </div>
</section>

@once
    @push('style')
        <style>
            .home-reels-section__lead {
                max-width: 46rem;
                margin-inline-start: auto;
                margin-inline-end: auto;
                line-height: 1.65;
            }

            @media (min-width: 992px) {
                .home-reels-section__lead {
                    margin-inline-start: 0;
                }
            }

            .home-reels-strip {
                margin-inline: -0.35rem;
            }

            .home-reels-slider .slick-track {
                display: flex !important;
                align-items: stretch;
            }

            .home-reels-slider .slick-slide {
                height: auto;
            }

            .home-reels-slider__cell {
                padding: 0 6px;
                box-sizing: border-box;
            }

            .home-reel-card {
                display: block;
                color: inherit;
            }

            .home-reel-card__frame {
                position: relative;
                width: 138px;
                aspect-ratio: 9 / 16;
                max-height: min(58vh, 340px);
                margin-inline: auto;
                border-radius: 14px;
                overflow: hidden;
                background: #0f172a;
                box-shadow:
                    0 16px 40px rgba(15, 23, 42, 0.18),
                    0 0 0 1px rgba(15, 23, 42, 0.06);
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .home-reel-card__cover,
            .home-reel-card__cover--img,
            .home-reel-card__cover--video,
            .home-reel-card__cover--fallback {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .home-reel-card__cover--video {
                z-index: 0;
                background: #0f172a;
                pointer-events: none;
            }

            .home-reel-card__video--deferred {
                display: none !important;
            }

            .home-reel-card__cover--img {
                z-index: 1;
            }

            .home-reel-card__cover--fallback {
                z-index: 0;
                background: linear-gradient(145deg, #1e293b 0%, #0f172a 50%, hsl(var(--base) / 0.55) 100%);
            }

            .home-reel-card:hover .home-reel-card__frame {
                transform: translateY(-4px) scale(1.02);
                box-shadow:
                    0 22px 50px rgba(15, 23, 42, 0.22),
                    0 0 0 1px rgba(15, 23, 42, 0.08);
            }

            .home-reel-card__shade {
                position: absolute;
                inset: 0;
                z-index: 2;
                background: linear-gradient(180deg,
                        rgba(3, 7, 18, 0.05) 0%,
                        rgba(3, 7, 18, 0.02) 42%,
                        rgba(3, 7, 18, 0.75) 100%);
                pointer-events: none;
            }

            .home-reel-card__avatar-ring {
                position: absolute;
                top: 10px;
                inset-inline-start: 10px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                padding: 3px;
                background: #fff;
                box-shadow: 0 0 0 3px #1877f2;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                z-index: 3;
            }

            .home-reel-card__avatar {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            .home-reel-card__avatar-icon {
                color: #fff;
                font-size: 0.95rem;
                display: flex;
                width: 100%;
                height: 100%;
                align-items: center;
                justify-content: center;
                background: hsl(var(--base));
                border-radius: 50%;
            }

            .home-reel-card__caption {
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                padding: 0.65rem 0.55rem 0.75rem;
                z-index: 4;
            }

            .home-reel-card__name {
                display: block;
                font-size: 0.8125rem;
                font-weight: 700;
                line-height: 1.25;
                color: #fff;
                text-align: center;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.55);
                word-break: break-word;
            }

            .home-reels-strip .slick-arrow {
                width: 42px;
                height: 42px;
                border-radius: 999px;
                background: rgba(15, 23, 42, 0.58) !important;
                border: 1px solid rgba(255, 255, 255, 0.18);
                z-index: 3;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
                transition: background 0.2s ease, transform 0.2s ease;
            }

            .home-reels-strip .slick-arrow:hover {
                background: rgba(15, 23, 42, 0.82) !important;
            }

            .home-reels-strip .slick-arrow:before {
                display: none;
            }

            .home-reels-strip .slick-arrow i {
                font-size: 0.95rem;
                color: #fff;
                line-height: 1;
            }

            .home-reels-strip .slick-prev {
                inset-inline-start: -6px;
            }

            .home-reels-strip .slick-next {
                inset-inline-end: -6px;
            }

            @media (max-width: 575px) {
                .home-reel-card__frame {
                    width: 118px;
                }

                .home-reels-strip .slick-prev {
                    inset-inline-start: 2px;
                }

                .home-reels-strip .slick-next {
                    inset-inline-end: 2px;
                }
            }
        </style>
    @endpush
    @push('script')
        <script>
            (function () {
                'use strict';

                function primeHomeReelVideo(el) {
                    if (!el || el.tagName !== 'VIDEO' || el.dataset.homeReelPrimed) {
                        return;
                    }
                    if (el.classList.contains('home-reel-card__video--deferred') || !el.src) {
                        return;
                    }
                    el.dataset.homeReelPrimed = '1';

                    var seek = function () {
                        try {
                            var d = el.duration;
                            el.currentTime = (isFinite(d) && d > 0) ? Math.min(0.18, d * 0.03) : 0.12;
                        } catch (err) {}
                    };

                    el.addEventListener('loadeddata', seek, { once: true });
                    el.addEventListener('loadedmetadata', seek, { once: true });
                }

                window.primeHomeReelVideo = primeHomeReelVideo;

                function primeAllInSection() {
                    document.querySelectorAll('.home-reels-section .home-reel-card__cover--video').forEach(function (v) {
                        primeHomeReelVideo(v);
                    });
                }

                if (window.jQuery) {
                    window.jQuery(function () {
                        primeAllInSection();
                        window.jQuery('.home-reels-slider').on('init reInit breakpoint', primeAllInSection);
                    });
                } else {
                    document.addEventListener('DOMContentLoaded', primeAllInSection);
                }
            })();
        </script>
    @endpush
@endonce
