@php
    use App\Models\PopupAd;
    use Illuminate\Support\Facades\Schema;

    $popupAds = collect();
    if (Schema::hasTable('popup_ads')) {
        $popupAds = PopupAd::active()
            ->orderBy('priority')
            ->orderByDesc('id')
            ->get()
            ->filter(fn ($ad) => $ad->matchesCurrentRequest())
            ->take(3)
            ->values();
    }
@endphp

@if($popupAds->count())
    <style>
        .popup-ads-layer {
            position: fixed;
            inset: 0;
            z-index: 9998;
            pointer-events: none;
        }
        .popup-ad-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.38);
            z-index: 9997;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }
        .popup-ad-backdrop.is-visible {
            opacity: 1;
            pointer-events: auto;
        }
        .popup-ad {
            position: fixed;
            z-index: 9999;
            display: none;
            pointer-events: auto;
            background: #fff;
            color: #111827;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }
        .popup-ad.is-visible {
            display: block;
            animation: popupAdIn .22s ease-out;
        }
        @keyframes popupAdIn {
            from { opacity: 0; transform: translateY(10px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .popup-ad__close {
            position: absolute;
            top: 10px;
            inset-inline-end: 10px;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.08);
            color: #111827;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }
        .popup-ad__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .popup-ad__body {
            padding: 22px;
        }
        .popup-ad__title {
            margin: 0 0 8px;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
            color: #101827;
        }
        .popup-ad__text {
            margin: 0;
            color: #4b5563;
            line-height: 1.65;
        }
        .popup-ad__cta {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            padding: 11px 20px;
            background: hsl(var(--base, 198 93% 61%));
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }
        .popup-ad--modal {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(520px, calc(100vw - 32px));
            border-radius: 18px;
        }
        .popup-ad--wide { width: min(760px, calc(100vw - 32px)); }
        .popup-ad--compact { width: min(380px, calc(100vw - 32px)); }
        .popup-ad--tall { width: min(420px, calc(100vw - 32px)); }
        .popup-ad--tall .popup-ad__media { height: 260px; }
        .popup-ad--modal .popup-ad__media { height: 220px; }
        .popup-ad--top_bar,
        .popup-ad--bottom_bar {
            left: 50%;
            width: min(940px, calc(100vw - 24px));
            transform: translateX(-50%);
            border-radius: 0 0 14px 14px;
        }
        .popup-ad--top_bar { top: 0; }
        .popup-ad--bottom_bar { bottom: 0; border-radius: 14px 14px 0 0; }
        .popup-ad--top_bar .popup-ad__inner,
        .popup-ad--bottom_bar .popup-ad__inner {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .popup-ad--top_bar .popup-ad__media,
        .popup-ad--bottom_bar .popup-ad__media {
            width: 160px;
            height: 86px;
            flex: 0 0 160px;
        }
        .popup-ad--right_corner,
        .popup-ad--left_corner,
        .popup-ad--right_side,
        .popup-ad--left_side {
            width: min(360px, calc(100vw - 24px));
            border-radius: 16px;
            bottom: 22px;
        }
        .popup-ad--right_corner { right: 22px; }
        .popup-ad--left_corner { left: 22px; }
        .popup-ad--right_side {
            right: 22px;
            top: 50%;
            bottom: auto;
            transform: translateY(-50%);
        }
        .popup-ad--left_side {
            left: 22px;
            top: 50%;
            bottom: auto;
            transform: translateY(-50%);
        }
        .popup-ad--right_corner .popup-ad__media,
        .popup-ad--left_corner .popup-ad__media,
        .popup-ad--right_side .popup-ad__media,
        .popup-ad--left_side .popup-ad__media {
            height: 150px;
        }
        @media (max-width: 575px) {
            .popup-ad--top_bar .popup-ad__inner,
            .popup-ad--bottom_bar .popup-ad__inner {
                display: block;
            }
            .popup-ad--top_bar .popup-ad__media,
            .popup-ad--bottom_bar .popup-ad__media {
                width: 100%;
                height: 130px;
            }
            .popup-ad--right_corner,
            .popup-ad--left_corner,
            .popup-ad--right_side,
            .popup-ad--left_side {
                right: 12px;
                left: 12px;
                bottom: 12px;
                top: auto;
                transform: none;
                width: auto;
            }
        }
    </style>

    <div class="popup-ad-backdrop" data-popup-ad-backdrop></div>
    <div class="popup-ads-layer" aria-live="polite">
        @foreach($popupAds as $ad)
            @php
                $title = $ad->localizedTitle();
                $body = $ad->localizedBody();
                $ctaText = $ad->localizedCtaText();
                $imageUrl = $ad->imageUrl();
                $needsBackdrop = $ad->placement === 'modal';
            @endphp
            <section class="popup-ad popup-ad--{{ $ad->placement }} popup-ad--{{ $ad->size }}"
                data-popup-ad
                data-id="{{ $ad->id }}"
                data-frequency="{{ $ad->frequency }}"
                data-frequency-value="{{ $ad->frequency_value }}"
                data-trigger="{{ $ad->trigger_type }}"
                data-trigger-value="{{ $ad->trigger_value }}"
                data-backdrop="{{ $needsBackdrop ? '1' : '0' }}"
                role="dialog"
                aria-label="{{ $title ?: __('Popup Ad') }}">
                @if($ad->closeable)
                    <button type="button" class="popup-ad__close" data-popup-ad-close aria-label="@lang('Close')">
                        <i class="las la-times"></i>
                    </button>
                @endif
                <div class="popup-ad__inner">
                    @if($imageUrl)
                        <div class="popup-ad__media"><img src="{{ $imageUrl }}" alt=""></div>
                    @endif
                    <div class="popup-ad__body">
                        @if($title)
                            <h3 class="popup-ad__title">{{ $title }}</h3>
                        @endif
                        @if($body)
                            <p class="popup-ad__text">{{ $body }}</p>
                        @endif
                        @if($ctaText && $ad->cta_url)
                            <a href="{{ url($ad->cta_url) }}" class="popup-ad__cta" data-popup-ad-click>{{ $ctaText }}</a>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <script>
        (function () {
            'use strict';
            const ads = Array.from(document.querySelectorAll('[data-popup-ad]'));
            const backdrop = document.querySelector('[data-popup-ad-backdrop]');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            let visitorKey = localStorage.getItem('popup_ads_visitor_key');
            if (!visitorKey) {
                visitorKey = 'v_' + Math.random().toString(36).slice(2) + Date.now().toString(36);
                localStorage.setItem('popup_ads_visitor_key', visitorKey);
            }

            function key(ad, suffix) {
                return 'popup_ad_' + ad.dataset.id + '_' + suffix;
            }

            function canShow(ad) {
                const frequency = ad.dataset.frequency;
                if (frequency === 'every_visit') return true;
                if (frequency === 'session') {
                    return sessionStorage.getItem(key(ad, 'seen')) !== '1';
                }
                const last = parseInt(localStorage.getItem(key(ad, 'seen_at')) || '0', 10);
                if (!last) return true;
                if (frequency === 'once') return false;
                const value = parseInt(ad.dataset.frequencyValue || '0', 10) || 1;
                const ms = frequency === 'days' ? value * 86400000 : value * 3600000;
                return Date.now() - last > ms;
            }

            function remember(ad) {
                if (ad.dataset.frequency === 'session') {
                    sessionStorage.setItem(key(ad, 'seen'), '1');
                } else {
                    localStorage.setItem(key(ad, 'seen_at'), String(Date.now()));
                }
            }

            function track(ad, event) {
                fetch("{{ url('popup-ads') }}/" + ad.dataset.id + "/track", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ event, visitor_key: visitorKey, url: window.location.href })
                }).catch(function () {});
            }

            function show(ad) {
                if (!canShow(ad)) return;
                ad.classList.add('is-visible');
                if (ad.dataset.backdrop === '1') backdrop?.classList.add('is-visible');
                remember(ad);
                track(ad, 'impression');
            }

            function close(ad) {
                ad.classList.remove('is-visible');
                if (!document.querySelector('[data-popup-ad].is-visible[data-backdrop="1"]')) {
                    backdrop?.classList.remove('is-visible');
                }
                track(ad, 'close');
            }

            ads.forEach(function (ad, index) {
                const delay = ad.dataset.trigger === 'delay' ? (parseInt(ad.dataset.triggerValue || '0', 10) * 1000) : 0;
                setTimeout(function () { show(ad); }, delay + (index * 250));
                ad.querySelector('[data-popup-ad-close]')?.addEventListener('click', function () { close(ad); });
                ad.querySelector('[data-popup-ad-click]')?.addEventListener('click', function () { track(ad, 'click'); });
            });

            backdrop?.addEventListener('click', function () {
                document.querySelectorAll('[data-popup-ad].is-visible[data-backdrop="1"]').forEach(close);
            });
        })();
    </script>
@endif
