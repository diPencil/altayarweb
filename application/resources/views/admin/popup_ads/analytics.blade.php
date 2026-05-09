@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <h5 class="mb-1">{{ $popupAd->name }}</h5>
                        <p class="mb-0 text-muted">@lang('Popup ad performance and latest viewer events.')</p>
                    </div>
                    <a href="{{ route('admin.popup-ads.edit', $popupAd) }}" class="btn btn--primary"><i class="las la-edit"></i> @lang('Edit')</a>
                </div>
            </div>
        </div>
        @foreach([
            __('Views') => $popupAd->impressions_count,
            __('Unique Views') => $popupAd->unique_impressions_count,
            __('Clicks') => $popupAd->clicks_count,
            __('Closes') => $popupAd->closes_count,
        ] as $label => $value)
            <div class="col-sm-6 col-xl-3">
                <div class="card b-radius--10">
                    <div class="card-body">
                        <span class="text-muted">{{ $label }}</span>
                        <h3 class="mb-0">{{ $value }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Event')</th>
                                    <th>@lang('Viewer')</th>
                                    <th>@lang('URL')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td><span class="badge badge--info">{{ __(ucfirst($event->event_type)) }}</span></td>
                                        <td>{{ __(ucfirst($event->viewer_type)) }} @if($event->viewer_id)#{{ $event->viewer_id }}@endif</td>
                                        <td class="text-break">{{ $event->url }}</td>
                                        <td>{{ showDateTime($event->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($events->hasPages())
                    <div class="card-footer">{{ $events->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.popup-ads.index') }}" class="btn btn-sm btn--dark"><i class="las la-arrow-left"></i> @lang('Back')</a>
@endpush
