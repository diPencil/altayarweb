@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-4">
            <div class="base--card radius--20 p-4 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Current Plan')</span>
                <h4 class="mb-2 mt-1">{{ $currentMembership ? (is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name) : __('No Active Membership') }}</h4>
                <p class="mb-1 text-muted">@lang('Price'): {{ $currentMembership?->plan ? $general->cur_sym . showAmount($currentMembership->plan->price) : __('-') }}</p>
                <p class="mb-0 text-muted">@lang('Validity'): {{ $currentMembership ? showDateTime($currentMembership->start_date, 'd M Y') . ' - ' . ($currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime')) : __('-') }}</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="base--card radius--20 p-4 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Total Changes')</span>
                <h4 class="mb-2 mt-1 membership-stat-card__metric">{{ $histories->total() }}</h4>
                <p class="mb-0 text-muted">@lang('Upgrade, downgrade and renewal history.')</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="base--card radius--20 p-4 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Latest Update')</span>
                @php
                    $typeLabel = match($latestHistory?->change_type) {
                        'subscribe' => __('Subscribe'),
                        'upgrade' => __('Upgrade'),
                        'downgrade' => __('Downgrade'),
                        'renewal' => __('Renewal'),
                        'removal' => __('Removed by Admin'),
                        default => __('No history')
                    };
                @endphp
                <h4 class="mb-2 mt-1">{{ $typeLabel }}</h4>
                <p class="mb-0 text-muted">{{ $latestHistory?->created_at ? showDateTime($latestHistory->created_at, 'd M Y') : __('-') }}</p>
            </div>
        </div>
    </div>

    <div class="base--card radius--20 p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-1">@lang('Plan Summary')</h5>
                <p class="text-muted mb-0">@lang('Detailed membership changes approved by admin or made by subscription.')</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table--responsive--lg">
                <thead>
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('From')</th>
                        <th>@lang('To')</th>
                        <th>@lang('Type')</th>
                        <th>@lang('Price Difference')</th>
                        <th>@lang('By')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $row)
                        <tr>
                            <td data-label="@lang('Date')">{{ showDateTime($row->created_at, 'd M Y') }}</td>
                            <td data-label="@lang('From')">{{ $row->previousPlan ? (is_rtl() && $row->previousPlan?->name_ar ? $row->previousPlan?->name_ar : $row->previousPlan?->name) : __('-') }}</td>
                            <td data-label="@lang('To')">{{ $row->newPlan ? (is_rtl() && $row->newPlan?->name_ar ? $row->newPlan?->name_ar : $row->newPlan?->name) : __('-') }}</td>
                            <td data-label="@lang('Type')">
                                @php
                                    $badgeClass = match($row->change_type) {
                                        'upgrade' => 'badge--success',
                                        'downgrade' => 'badge--danger',
                                        'removal' => 'badge--dark',
                                        'subscribe' => 'badge--primary',
                                        'renewal' => 'badge--info',
                                        default => 'badge--warning'
                                    };
                                    $rowTypeLabel = match($row->change_type) {
                                        'subscribe' => __('Subscribe'),
                                        'upgrade' => __('Upgrade'),
                                        'downgrade' => __('Downgrade'),
                                        'renewal' => __('Renewal'),
                                        'removal' => __('Removed by Admin'),
                                        default => __(ucfirst((string)$row->change_type))
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $rowTypeLabel }}
                                </span>
                            </td>
                            <td data-label="@lang('Price Difference')">{{ $general->cur_sym }}{{ showAmount($row->price_difference) }}</td>
                            <td data-label="@lang('By')">
                                {{ $row->admin ? __($row->admin->name) : ($row->created_by_user_id ? __('User') : __('System')) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center text-muted">@lang('No membership changes found.')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($histories->hasPages())
            <div class="mt-3">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
@endsection
