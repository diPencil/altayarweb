@extends($activeTemplate . 'layouts.user.master')

@push('style')
    <style>
        .reels-library-hero {
            background: linear-gradient(135deg, rgba(91, 156, 249, 0.14), rgba(255, 255, 255, 0.95));
            border: 1px solid rgba(91, 156, 249, 0.14);
        }

        .library-reel-card {
            max-width: 140px;
            margin-inline: auto;
            border-radius: 18px;
            overflow: hidden;
            background: #0b1220;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .library-reel-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 55px rgba(15, 23, 42, 0.12);
        }

        .library-reel-card__media {
            position: relative;
            aspect-ratio: 9 / 16;
            max-height: 190px;
            background: #020617;
        }

        .library-reel-card__media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .library-reel-card__overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-end;
            padding: .5rem;
            background: linear-gradient(180deg, rgba(2,6,23,0.02) 0%, rgba(2,6,23,0.15) 48%, rgba(2,6,23,0.92) 100%);
            color: #fff;
        }

        .library-reel-card__overlay h6,
        .library-reel-card__overlay p {
            margin-bottom: 0;
        }

        .library-reel-card__badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .18rem .45rem;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            font-size: .62rem;
            margin-bottom: .25rem;
        }

        .library-comment-card {
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .library-comment-reply {
            border-radius: 16px;
            background: rgba(91, 156, 249, 0.08);
            border: 1px solid rgba(91, 156, 249, 0.12);
        }
    </style>
@endpush

@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-12">
            <div class="base--card radius--20 p-4 p-lg-5 reels-library-hero">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge badge--primary mb-2">@lang('Reels Library')</span>
                        <h4 class="mb-2">@lang('Your saved and liked reels')</h4>
                        <p class="mb-0 text-muted">@lang('Quick access to the short videos you liked or bookmarked while browsing the platform.')</p>
                    </div>
                    <a href="{{ route('reels.index') }}" class="btn btn--base pills">@lang('Watch Reels')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="mb-0">@lang('Liked Reels')</h5>
                    <span class="badge badge--info">{{ $likedReels->count() }}</span>
                </div>
                <div class="row g-2 justify-content-start">
                    @forelse($likedReels as $reel)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <a href="{{ route('reels.index') }}" class="text-decoration-none">
                                <div class="library-reel-card h-100">
                                    <div class="library-reel-card__media">
                                        <video muted playsinline preload="metadata" poster="{{ $reel->thumbnail_url }}">
                                            <source src="{{ $reel->video_url }}" type="video/mp4">
                                        </video>
                                        <div class="library-reel-card__overlay">
                                            <div>
                                                <div class="library-reel-card__badge">
                                                    <i class="fa-solid fa-heart"></i>
                                                    <span>@lang('Liked')</span>
                                                </div>
                                                <h6 class="mb-1 text-white">{{ $reel->title_display }}</h6>
                                                <p class="text-white-50 small">{{ $reel->source_name_display ?: __('No source') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12 text-muted">@lang('No liked reels yet.')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="mb-0">@lang('Saved Reels')</h5>
                    <span class="badge badge--info">{{ $savedReels->count() }}</span>
                </div>
                <div class="row g-2 justify-content-start">
                    @forelse($savedReels as $reel)
                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                            <a href="{{ route('reels.index') }}" class="text-decoration-none">
                                <div class="library-reel-card h-100">
                                    <div class="library-reel-card__media">
                                        <video muted playsinline preload="metadata" poster="{{ $reel->thumbnail_url }}">
                                            <source src="{{ $reel->video_url }}" type="video/mp4">
                                        </video>
                                        <div class="library-reel-card__overlay">
                                            <div>
                                                <div class="library-reel-card__badge">
                                                    <i class="fa-solid fa-bookmark"></i>
                                                    <span>@lang('Saved')</span>
                                                </div>
                                                <h6 class="mb-1 text-white">{{ $reel->title_display }}</h6>
                                                <p class="text-white-50 small">{{ $reel->source_name_display ?: __('No source') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12 text-muted">@lang('No saved reels yet.')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mt-0">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h5 class="mb-0">@lang('My Reel Comments')</h5>
                    <span class="badge badge--info">{{ $comments->count() }}</span>
                </div>

                <div class="d-grid gap-3">
                    @forelse($comments as $comment)
                        <div class="library-comment-card p-3 p-lg-4">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-2">
                                <div>
                                    <span class="badge badge--primary mb-2">@lang('Reel')</span>
                                    <h6 class="mb-1">{{ $comment->reel?->title_display }}</h6>
                                    <p class="text-muted mb-0 small">{{ $comment->reel?->source_name_display ?: __('No source') }}</p>
                                </div>
                                <span class="text-muted small">{{ showDateTime($comment->created_at) }}</span>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted text-uppercase fs--12 mb-1">@lang('Your comment')</div>
                                <div>{{ $comment->comment }}</div>
                            </div>

                            @if($comment->admin_reply)
                                <div class="library-comment-reply p-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <strong>@lang('Admin reply')</strong>
                                        @if($comment->adminReplier)
                                            <span class="text-muted small">{{ $comment->adminReplier->name ?? $comment->adminReplier->username ?? __('Admin') }}</span>
                                        @endif
                                    </div>
                                    <div>{{ $comment->admin_reply }}</div>
                                </div>
                            @else
                                <div class="text-muted small">@lang('No admin reply yet.')</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">@lang('You have not written any reel comments yet.')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
