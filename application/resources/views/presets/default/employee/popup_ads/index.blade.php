@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="dashboard-body__bar d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1">@lang('Personal Offers')</h4>
            <p class="mb-0 text-muted">@lang('Create popup offers that appear only for selected customers.')</p>
        </div>
        <a href="{{ route('employee.popup-ads.create') }}" class="btn btn--base"><i class="las la-plus"></i> @lang('Create Offer')</a>
    </div>

    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Offer')</th>
                            <th>@lang('Customers')</th>
                            <th>@lang('Views')</th>
                            <th>@lang('Clicks')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td data-label="@lang('Offer')">
                                    <strong>{{ $ad->name }}</strong>
                                    <div class="text-muted">{{ $ad->localizedTitle() }}</div>
                                </td>
                                <td data-label="@lang('Customers')">{{ count($ad->target_user_ids ?? []) }}</td>
                                <td data-label="@lang('Views')">{{ $ad->impressions_count }}</td>
                                <td data-label="@lang('Clicks')">{{ $ad->clicks_count }}</td>
                                <td data-label="@lang('Status')">
                                    <form method="POST" action="{{ route('employee.popup-ads.status', $ad) }}">
                                        @csrf
                                        <button class="badge border-0 {{ $ad->status ? 'badge--success' : 'badge--danger' }}">{{ $ad->status ? __('Active') : __('Inactive') }}</button>
                                    </form>
                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('employee.popup-ads.edit', $ad) }}" class="btn btn-sm btn--base"><i class="las la-edit"></i></a>
                                    <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Delete this offer?')" data-action="{{ route('employee.popup-ads.delete', $ad) }}"><i class="las la-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($ads->hasPages())
            <div class="card-footer">{{ $ads->links() }}</div>
        @endif
    </div>
@endsection
