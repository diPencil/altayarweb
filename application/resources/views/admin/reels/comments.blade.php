@extends('admin.layouts.app')

@push('style')
    <style>
        .reel-comment-card,
        .reel-reply-card {
            border: 1px solid rgba(13, 110, 253, 0.10);
            border-radius: 14px;
            background: #fff;
            padding: .7rem .8rem;
        }

        .reel-comment-card {
            background: linear-gradient(180deg, rgba(13, 110, 253, 0.03), rgba(13, 110, 253, 0.01));
        }

        .reel-comment-label,
        .reel-reply-label {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: .4rem;
        }

        .reel-comment-text {
            color: #2c3345;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: .92rem;
        }

        .reel-reply-card {
            background: linear-gradient(180deg, rgba(91, 156, 249, 0.08), rgba(91, 156, 249, 0.03));
            border-color: rgba(91, 156, 249, 0.18);
        }

        .reel-reply-meta {
            font-size: .78rem;
            color: #6c757d;
            margin-top: .4rem;
        }

        .reel-reply-preview {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #2c3345;
            line-height: 1.45;
            font-size: .92rem;
        }

        .reel-reply-empty {
            color: #9aa3b2;
            font-style: italic;
            font-size: .9rem;
        }

        .reel-reply-card textarea {
            min-height: 100px;
            resize: vertical;
        }

        .reel-cell-wrap {
            max-width: 320px;
        }

        .reel-cell-wrap--reply {
            max-width: 300px;
        }

        .reel-comment-card,
        .reel-reply-card {
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.03);
        }

        .reel-reply-card.is-empty {
            background: linear-gradient(180deg, rgba(148, 163, 184, 0.08), rgba(148, 163, 184, 0.03));
            border-color: rgba(148, 163, 184, 0.16);
        }
    </style>
@endpush

@section('panel')
    <div class="row gy-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1">@lang('Reel Comments')</h4>
                <p class="mb-0 text-muted">@lang('Moderate comments left by users on reels.')</p>
            </div>
            <a href="{{ route('admin.reels.index') }}" class="btn btn--dark">@lang('Back to Reels')</a>
        </div>

        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5">
                                <label class="form-label">@lang('Reel')</label>
                                <select name="reel_id" class="form-control">
                                    <option value="">@lang('All Reels')</option>
                                    @foreach($reels as $reel)
                                        <option value="{{ $reel->id }}" @selected(request()->reel_id == $reel->id)>{{ $reel->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @selected(request()->status === '1')>@lang('Active')</option>
                                    <option value="0" @selected(request()->status === '0')>@lang('Inactive')</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn--primary w-100">@lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Reel')</th>
                                    <th>@lang('Views')</th>
                                    <th>@lang('Comment')</th>
                                    <th>@lang('Reply')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Time')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr>
                                        <td>{{ $comment->user?->fullname ?? $comment->user?->username ?? __('Deleted user') }}</td>
                                        <td>{{ $comment->reel?->title ?? __('Deleted reel') }}</td>
                                        <td>{{ $comment->reel?->views_count ?? 0 }}</td>
                                        <td class="align-middle">
                                            <div class="reel-comment-card reel-cell-wrap">
                                                <div class="reel-comment-label">
                                                    <i class="las la-comment"></i>
                                                    @lang('User Comment')
                                                </div>
                                                <div class="reel-comment-text">{{ $comment->comment }}</div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="reel-reply-card reel-cell-wrap reel-cell-wrap--reply {{ $comment->admin_reply ? '' : 'is-empty' }}">
                                                <div class="reel-comment-label">
                                                    <i class="las la-reply"></i>
                                                    @lang('Admin Reply')
                                                </div>
                                                @if($comment->admin_reply)
                                                    <div class="reel-reply-preview">{{ $comment->admin_reply }}</div>
                                                    <div class="reel-reply-meta">
                                                        <div>@lang('Replied by'): <strong>{{ $comment->adminReplier?->username ?? __('Admin') }}</strong></div>
                                                        @if($comment->replied_at)
                                                            <div>{{ showDateTime($comment->replied_at) }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="reel-reply-empty">@lang('No reply yet')</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $comment->status ? 'badge--success' : 'badge--danger' }}">
                                                {{ $comment->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ showDateTime($comment->created_at) }}</td>
                                        <td>
                                            <a href="{{ route('admin.reels.comments.status', $comment->id) }}" class="btn btn-sm btn--secondary">
                                                <i class="las la-sync"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn--info js-open-reply-modal"
                                                data-action="{{ route('admin.reels.comments.reply', $comment->id) }}"
                                                data-comment="{{ e($comment->comment) }}"
                                                data-reply="{{ e($comment->admin_reply ?? '') }}"
                                                data-user="{{ e($comment->user?->fullname ?? $comment->user?->username ?? __('Deleted user')) }}"
                                                data-reel="{{ e($comment->reel?->title ?? __('Deleted reel')) }}">
                                                <i class="las la-reply"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Delete this comment?')" data-action="{{ route('admin.reels.comments.delete', $comment->id) }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($comments->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($comments) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">@lang('Reply to Comment')</h5>
                        <div class="small text-muted" id="replyModalMeta"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="replyModalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">@lang('User Comment')</label>
                            <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e5eaf2; white-space: pre-wrap;" id="replyModalComment"></div>
                        </div>
                        <div>
                            <label class="form-label">@lang('Admin Reply')</label>
                            <textarea name="admin_reply" id="replyModalTextarea" rows="6" class="form-control" placeholder="@lang('Write a reply...')"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Save Reply')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function () {
            'use strict';

            const replyModal = document.getElementById('replyModal');
            const replyModalForm = document.getElementById('replyModalForm');
            const replyModalTextarea = document.getElementById('replyModalTextarea');
            const replyModalComment = document.getElementById('replyModalComment');
            const replyModalMeta = document.getElementById('replyModalMeta');
            const modal = replyModal ? new bootstrap.Modal(replyModal) : null;

            document.querySelectorAll('.js-open-reply-modal').forEach(function (button) {
                button.addEventListener('click', function () {
                    replyModalForm.action = button.dataset.action;
                    replyModalTextarea.value = button.dataset.reply || '';
                    replyModalComment.textContent = button.dataset.comment || '';
                    replyModalMeta.textContent = [button.dataset.user, button.dataset.reel].filter(Boolean).join(' • ');
                    modal.show();
                    setTimeout(function () {
                        replyModalTextarea.focus();
                    }, 200);
                });
            });
        })();
    </script>
@endpush
