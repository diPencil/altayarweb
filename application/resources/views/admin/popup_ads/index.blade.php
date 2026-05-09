@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">@lang('Popup Ads')</h5>
                        <p class="mb-0 text-muted">@lang('Create targeted popup campaigns for website pages and dashboards.')</p>
                    </div>
                    <a href="{{ route('admin.popup-ads.create') }}" class="btn btn--primary">
                        <i class="las la-plus me-1"></i>@lang('Add Popup Ad')
                    </a>
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
                                    <th>@lang('Campaign')</th>
                                    <th>@lang('Placement')</th>
                                    <th>@lang('Audience')</th>
                                    <th>@lang('Views')</th>
                                    <th>@lang('Clicks')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $ad->name }}</div>
                                            <small class="text-muted">{{ $ad->localizedTitle() ?: __('No title') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge--info">{{ __(App\Models\PopupAd::PLACEMENTS[$ad->placement] ?? $ad->placement) }}</span>
                                            <div><small class="text-muted">{{ __(App\Models\PopupAd::SIZES[$ad->size] ?? $ad->size) }}</small></div>
                                        </td>
                                        <td>{{ __(App\Models\PopupAd::AUDIENCES[$ad->audience_type] ?? $ad->audience_type) }}</td>
                                        <td>
                                            <strong>{{ $ad->impressions_count }}</strong>
                                            <div><small class="text-muted">@lang('unique') {{ $ad->unique_impressions_count }}</small></div>
                                        </td>
                                        <td>{{ $ad->clicks_count }}</td>
                                        <td>
                                            <form action="{{ route('admin.popup-ads.status', $ad) }}" method="POST">
                                                @csrf
                                                <button class="badge border-0 {{ $ad->status ? 'badge--success' : 'badge--danger' }}" type="submit">
                                                    {{ $ad->status ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.popup-ads.analytics', $ad) }}" class="btn btn-sm btn--info" title="@lang('Analytics')">
                                                    <i class="las la-chart-bar"></i>
                                                </a>
                                                <a href="{{ route('admin.popup-ads.edit', $ad) }}" class="btn btn-sm btn--primary" title="@lang('Edit')">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn--danger confirmationBtn"
                                                    data-question="@lang('Delete this popup ad?')"
                                                    data-action="{{ route('admin.popup-ads.delete', $ad) }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
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
                @if($ads->hasPages())
                    <div class="card-footer">{{ $ads->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <form method="GET" class="d-inline">
            <div class="input-group justify-content-end">
                <select name="status" class="form-control bg--white">
                    <option value="">@lang('All Status')</option>
                    <option value="1" @selected(request('status') === '1')>@lang('Active')</option>
                    <option value="0" @selected(request('status') === '0')>@lang('Inactive')</option>
                </select>
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search popup ads')" value="{{ request('search') }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush
