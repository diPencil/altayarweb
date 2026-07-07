@extends($activeTemplate . 'layouts.frontend')

@push('style')
    <style>
        body { background: #050816; }
        .header-main-area, .footer-area, .scroll-top, .breadcrumb, .ai-chat-assistant-shell { display: none !important; }
        .reels-page {
            min-height: 100vh;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(0, 200, 255, 0.18), transparent 35%),
                radial-gradient(circle at bottom left, rgba(255, 122, 0, 0.15), transparent 40%),
                #050816;
        }
        .reels-feed {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
            overscroll-behavior-y: contain;
        }
        .reel-item {
            position: relative;
            height: 100vh;
            scroll-snap-align: start;
            background: #020617;
        }
        .reel-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #020617;
        }
        .reel-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            padding: 1.2rem;
            background: linear-gradient(180deg, rgba(3, 7, 18, 0.08) 0%, rgba(3, 7, 18, 0.15) 48%, rgba(3, 7, 18, 0.88) 100%);
            color: #fff;
        }
        .reel-meta {
            max-width: min(680px, 78vw);
            backdrop-filter: blur(16px);
            background: rgba(2, 6, 23, 0.38);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 1rem 1.1rem;
            color: #fff;
        }
        .reel-meta h2,
        .reel-meta p { margin-bottom: 0; }
        .reel-meta h2 { font-size: clamp(1.1rem, 2vw, 1.65rem); font-weight: 800; color: #fff; }
        .reel-meta p { color: rgba(255,255,255,0.82); }
        .reel-actions {
            position: absolute;
            right: 1rem;
            bottom: 5.5rem;
            display: flex;
            flex-direction: column;
            gap: .8rem;
            z-index: 3;
        }
        [dir="rtl"] .reel-actions { right: auto; left: 1rem; }
        .reel-action-btn {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            transition: transform .2s ease, background .2s ease, opacity .2s ease;
        }
        .reel-action-btn:hover { transform: translateY(-2px) scale(1.03); }
        .reel-action-btn.is-active { background: #ff4d6d; }
        .reel-sound-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 4;
            width: 46px;
            height: 46px;
            border: 0;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            transition: transform .2s ease, background .2s ease, opacity .2s ease;
        }
        .reel-sound-btn:hover { transform: translateY(-2px) scale(1.03); }
        .reel-sound-btn.is-active { background: rgba(34,197,94,0.92); }
        .reel-cta {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-top: .9rem;
            padding: .7rem 1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }
        .reel-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            margin-bottom: .6rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            font-size: .82rem;
        }
        .reels-topbar {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 4;
            padding: 1rem;
            pointer-events: none;
        }
        .reels-topbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            pointer-events: auto;
        }
        .reels-brand {
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            letter-spacing: .02em;
            padding: .75rem 1rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(14px);
        }
        .reels-hint {
            color: rgba(255,255,255,0.72);
            font-size: .9rem;
            padding: .7rem 1rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(14px);
        }
        .reels-empty {
            min-height: 100vh;
            display: grid;
            place-items: center;
            color: #fff;
            text-align: center;
            padding: 2rem;
        }
        [dir="rtl"] .reel-meta { text-align: right; }

        @media (min-width: 992px) {
            .reels-page {
                display: grid;
                place-items: center;
                padding: 1.5rem;
            }

            .reels-feed {
                width: min(100%, 460px);
                height: calc(100vh - 3rem);
                border-radius: 34px;
                overflow: hidden;
                box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
                background: #020617;
            }

            .reel-item {
                height: calc(100vh - 3rem);
            }

            .reel-overlay {
                padding: 1.35rem;
            }

            .reel-meta {
                max-width: 100%;
            }

            .reels-topbar {
                padding: 1.25rem 1.5rem;
            }

            .reels-topbar__inner {
                max-width: 460px;
                margin: 0 auto;
            }
        }

        @media (min-width: 1400px) {
            .reels-feed {
                width: 500px;
            }

            .reels-topbar__inner {
                max-width: 500px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="reels-page">
        <div class="reels-topbar">
            <div class="reels-topbar__inner">
                <a href="{{ route('home') }}" class="reels-brand">@lang('Home')</a>
                <div class="reels-hint">@lang('Swipe vertically to explore')</div>
            </div>
        </div>

        @if($reels->isEmpty())
            <div class="reels-empty">
                <div>
                    <h2 class="mb-3">@lang('No reels available yet')</h2>
                    <p class="mb-0 text-white-50">@lang('Please check back soon for short videos, offers, and destination highlights.')</p>
                </div>
            </div>
        @else
            <div class="reels-feed" id="reelsFeed">
                @foreach($reels as $reel)
                    @php
                        $isLiked = auth()->check() && isset($interactions[$reel->id]) && $interactions[$reel->id]->where('type', 'like')->isNotEmpty();
                        $isSaved = auth()->check() && isset($interactions[$reel->id]) && $interactions[$reel->id]->where('type', 'save')->isNotEmpty();
                        $commentsCount = $reel->approvedComments->count();
                    @endphp
                    <article class="reel-item" data-reel-id="{{ $reel->id }}">
                        <video
                            class="reel-video"
                            muted
                            playsinline
                            loop
                            preload="none"
                            poster="{{ $reel->thumbnail_url }}"
                            data-src="{{ $reel->video_url }}"
                        ></video>

                        <button
                            type="button"
                            class="reel-sound-btn js-reel-sound-toggle"
                            aria-label="@lang('Toggle sound')"
                            aria-pressed="false"
                            title="@lang('Toggle sound')"
                        >
                            <i class="fa-solid fa-volume-xmark"></i>
                        </button>

                        <div class="reel-actions">
                            <button type="button" class="reel-action-btn js-reel-like @if($isLiked) is-active @endif" data-action-url="{{ route('reels.like', $reel->id) }}" aria-label="@lang('Like')">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                            <button type="button" class="reel-action-btn js-reel-save @if($isSaved) is-active @endif" data-action-url="{{ route('reels.save', $reel->id) }}" aria-label="@lang('Save')">
                                <i class="fa-solid fa-bookmark"></i>
                            </button>
                            <button type="button" class="reel-action-btn js-reel-share" data-share-url="{{ route('reels.index') }}?reel={{ $reel->id }}" aria-label="@lang('Share')">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                        </div>

                        <div class="reel-overlay">
                            <div class="reel-meta">
                                @if($reel->source_name_display)
                                    <div class="reel-badge">
                                        <i class="fa-solid fa-circle-user"></i>
                                        <span>{{ $reel->source_name_display }}</span>
                                    </div>
                                @endif
                                <h2>{{ $reel->title_display }}</h2>
                                @if($reel->description_display)
                                    <p class="mt-2">{{ $reel->description_display }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-3 mt-3 small text-white-50">
                                    <span><i class="fa-regular fa-eye me-1"></i><span class="js-view-count">{{ $reel->views_count }}</span></span>
                                    <span><i class="fa-regular fa-heart me-1"></i><span class="js-like-count">{{ $reel->likes_count }}</span></span>
                                    <span><i class="fa-regular fa-bookmark me-1"></i><span class="js-save-count">{{ $reel->saves_count }}</span></span>
                                    <span><i class="fa-regular fa-comment me-1"></i>{{ $commentsCount }}</span>
                                </div>
                                @if($reel->related_url)
                                    <a class="reel-cta" href="{{ $reel->related_url }}">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        <span>@lang('Open related offer')</span>
                                    </a>
                                @endif

                                <details class="mt-3">
                                    <summary class="text-white fw-semibold" style="cursor:pointer;">@lang('Comments') ({{ $commentsCount }})</summary>
                                    <div class="mt-3" style="max-height: 220px; overflow:auto;">
                                        @forelse($reel->approvedComments as $comment)
                                            <div class="mb-3 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                                <div class="d-flex align-items-center justify-content-between gap-2 small text-white-50 mb-1">
                                                    <strong class="text-white">{{ $comment->user?->fullname ?? $comment->user?->username }}</strong>
                                                    <span>{{ showDateTime($comment->created_at) }}</span>
                                                </div>
                                                <div>{{ $comment->comment }}</div>
                                                @if($comment->admin_reply)
                                                    <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.08);">
                                                        <div class="small text-white-50 mb-1">@lang('Admin reply')</div>
                                                        <div>{{ $comment->admin_reply }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="mb-0 text-white-50">@lang('No comments yet.')</p>
                                        @endforelse
                                    </div>

                                    <form class="reel-comment-form mt-3" data-action-url="{{ route('reels.comment', $reel->id) }}">
                                        @csrf
                                        <textarea class="form-control mb-2" name="comment" rows="2" placeholder="@lang('Write a comment...')"></textarea>
                                        <button type="submit" class="btn btn--base btn-sm">@lang('Post Comment')</button>
                                    </form>
                                </details>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        (function () {
            'use strict';

            const feed = document.getElementById('reelsFeed');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const viewBase = @json(route('reels.view', ['reel' => '__REEL__']));

            function postJson(url) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                }).then(response => response.json().then(data => ({ status: response.status, data })));
            }

            function activateVideo(item) {
                const video = item.querySelector('video');
                if (!video) return;

                if (!video.dataset.loaded && video.dataset.src) {
                    video.src = video.dataset.src;
                    video.dataset.loaded = '1';
                    video.load();
                }

                const reelId = item.dataset.reelId;
                const seenKey = 'reel-viewed-' + reelId;
                if (!sessionStorage.getItem(seenKey)) {
                    sessionStorage.setItem(seenKey, '1');
                    postJson(viewBase.replace('__REEL__', reelId)).then(result => {
                        if (result.data && result.data.views_count !== undefined) {
                            item.querySelector('.js-view-count').textContent = result.data.views_count;
                        }
                    });
                }

                const playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {});
                }
            }

            function syncSoundButton(item) {
                const video = item.querySelector('video');
                const button = item.querySelector('.js-reel-sound-toggle');
                if (!video || !button) return;

                const icon = button.querySelector('i');
                const muted = !!video.muted;
                button.classList.toggle('is-active', !muted);
                button.setAttribute('aria-pressed', muted ? 'false' : 'true');
                if (icon) {
                    icon.className = muted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high';
                }
            }

            function pauseVideo(item) {
                const video = item.querySelector('video');
                if (video) {
                    video.pause();
                }
            }

            if (feed) {
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            activateVideo(entry.target);
                        } else {
                            pauseVideo(entry.target);
                        }
                    });
                }, { threshold: 0.72 });

                feed.querySelectorAll('.reel-item').forEach(item => observer.observe(item));
                feed.querySelectorAll('.reel-item').forEach(syncSoundButton);

                const params = new URLSearchParams(window.location.search);
                const reelIdParam = params.get('reel');
                if (reelIdParam) {
                    const targetReel = feed.querySelector('.reel-item[data-reel-id="' + reelIdParam + '"]');
                    if (targetReel) {
                        setTimeout(function () {
                            targetReel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 150);
                    }
                }
            }

            document.addEventListener('click', function (event) {
                const soundButton = event.target.closest('.js-reel-sound-toggle');
                const likeButton = event.target.closest('.js-reel-like');
                const saveButton = event.target.closest('.js-reel-save');
                const shareButton = event.target.closest('.js-reel-share');

                if (soundButton) {
                    event.preventDefault();
                    const reelItem = soundButton.closest('.reel-item');
                    const video = reelItem ? reelItem.querySelector('video') : null;
                    if (video) {
                        video.muted = !video.muted;
                        syncSoundButton(reelItem);
                        if (!video.muted) {
                            const playPromise = video.play();
                            if (playPromise && typeof playPromise.catch === 'function') {
                                playPromise.catch(() => {});
                            }
                        }
                    }
                    return;
                }

                const button = likeButton || saveButton;
                if (button) {
                    event.preventDefault();
                    const reelItem = button.closest('.reel-item');
                    postJson(button.dataset.actionUrl).then(result => {
                        if (result.status === 401 && result.data && result.data.redirect) {
                            window.location.href = result.data.redirect;
                            return;
                        }

                        if (result.data && result.data.likes_count !== undefined) {
                            reelItem.querySelector('.js-like-count').textContent = result.data.likes_count;
                            reelItem.querySelector('.js-save-count').textContent = result.data.saves_count;
                        }

                        button.classList.toggle('is-active', !!(result.data && result.data.active));
                    });
                    return;
                }

                if (shareButton) {
                    event.preventDefault();
                    const shareUrl = shareButton.dataset.shareUrl;

                    if (navigator.share) {
                        navigator.share({
                            title: document.title,
                            url: shareUrl,
                        }).catch(() => {});
                    } else {
                        navigator.clipboard.writeText(shareUrl).then(() => {
                            alert(@json(__('Reel link copied to clipboard')));
                        }).catch(() => {
                            window.prompt(@json(__('Copy reel link')), shareUrl);
                        });
                    }
                }
            });

            document.addEventListener('submit', function (event) {
                const commentForm = event.target.closest('.reel-comment-form');
                if (!commentForm) {
                    return;
                }

                event.preventDefault();
                const formData = new FormData(commentForm);
                fetch(commentForm.dataset.actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                }).then(response => response.json().then(data => ({ status: response.status, data }))).then(result => {
                    if (result.status === 401 && result.data && result.data.redirect) {
                        window.location.href = result.data.redirect;
                        return;
                    }

                    if (result.data && result.data.status === 'success') {
                        commentForm.reset();
                        window.location.reload();
                    }
                });
            });
        })();
    </script>
@endpush
