@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-5">
                                <label class="form-label">@lang('Search')</label>
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Search by title or source')">
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
                                <button class="btn btn--primary w-100" type="submit">@lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1">@lang('Reels Library')</h4>
                <p class="mb-0 text-muted">@lang('Manage vertical video reels, captions, media and priority order.')</p>
            </div>
            <a href="{{ route('admin.reels.create') }}" class="btn btn--primary">
                <i class="las la-plus me-1"></i>@lang('Add Reel')
            </a>
        </div>

        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Media')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Connection')</th>
                                    <th>@lang('Stats')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reels as $reel)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <video muted playsinline preload="metadata" style="width: 52px; height: 78px; object-fit: cover; border-radius: 10px; background: #0f172a; flex-shrink: 0;">
                                                    <source src="{{ $reel->video_url }}" type="video/mp4">
                                                </video>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $reel->title }}</div>
                                            <small class="text-muted">{{ $reel->source_name ?: __('No source') }}</small>
                                        </td>
                                        <td>
                                            @if($reel->tourPackage)
                                                <a href="{{ route('tour.package.details', [slug($reel->tourPackage->title), $reel->tourPackage->id]) }}" target="_blank">{{ __($reel->tourPackage->title) }}</a>
                                            @elseif($reel->link_url)
                                                <a href="{{ $reel->link_url }}" target="_blank">{{ $reel->link_url }}</a>
                                            @else
                                                <span class="text-muted">@lang('No related page')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span><i class="las la-eye"></i> {{ $reel->views_count }}</span>
                                                <span><i class="las la-heart"></i> {{ $reel->likes_count }}</span>
                                                <span><i class="las la-bookmark"></i> {{ $reel->saves_count }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $reel->sort_order }}</td>
                                        <td>{!! $reel->statusBadge() !!}</td>
                                        <td>
                                            <a href="{{ route('admin.reels.status.change', $reel->id) }}" class="btn btn-sm btn--secondary">
                                                <i class="las la-sync"></i>
                                            </a>
                                            <a href="{{ route('admin.reels.edit', $reel->id) }}" class="btn btn-sm btn--primary">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Delete this reel?')" data-action="{{ route('admin.reels.delete', $reel->id) }}">
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
                @if($reels->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($reels) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
