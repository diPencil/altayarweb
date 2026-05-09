@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-4">
            <div class="base--card radius--20 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Membership Status')</span>
                <h4 class="mb-2 mt-1">{{ $currentMembership ? __('Active Membership') : __('No Active Membership') }}</h4>
                @if($currentMembership)
                    <p class="mb-1">{{ is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name }}</p>
                    <p class="text-muted mb-0">{{ showDateTime($currentMembership->start_date, 'd M Y') }} - {{ $currentMembership->end_date ? showDateTime($currentMembership->end_date, 'd M Y') : __('Lifetime') }}</p>
                @else
                    <p class="text-muted mb-0">@lang('Choose a plan to unlock rewards, member-only perks and PDF details.')</p>
                @endif
                <a href="{{ route('user.membership.summary') }}" class="btn btn--base btn-sm mt-3">@lang('Plan Summary')</a>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="base--card radius--20 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Points Balance')</span>
                <h4 class="mb-2 mt-1 membership-stat-card__metric">{{ $user->membership_points_balance }}</h4>
                <p class="text-muted mb-0">@lang('Earned from bookings and membership subscriptions.')</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="base--card radius--20 h-100 membership-stat-card">
                <span class="text-muted text-uppercase fs--12">@lang('Cashback Balance')</span>
                <h4 class="mb-2 mt-1 membership-stat-card__metric">{{ $general->cur_sym }}{{ showAmount($user->cashback_balance) }}</h4>
                <p class="text-muted mb-0">@lang('Can be reused on future payments.')</p>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="base--card radius--20">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">@lang('Points History')</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Type')</th>
                                <th>@lang('Points')</th>
                                <th>@lang('Balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pointTransactions as $row)
                                <tr>
                                    <td data-label="@lang('Type')">{{ match (strtolower((string) $row->type)) {
                                        'earned' => __('Earned'),
                                        'used' => __('Used'),
                                        default => __(ucfirst((string) $row->type)),
                                    } }}</td>
                                    <td data-label="@lang('Points')">{{ $row->points }}</td>
                                    <td data-label="@lang('Balance')">{{ $row->balance_after }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="100%" class="text-center text-muted">@lang('No point history found.')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pointTransactions->links() }}
            </div>
        </div>
        <div class="col-lg-6">
            <div class="base--card radius--20">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">@lang('Cashback History')</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashbackTransactions as $row)
                                <tr>
                                    <td data-label="@lang('Type')">{{ match (strtolower((string) $row->type)) {
                                        'earned' => __('Earned'),
                                        'used' => __('Used'),
                                        default => __(ucfirst((string) $row->type)),
                                    } }}</td>
                                    <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($row->amount) }}</td>
                                    <td data-label="@lang('Balance')">{{ $general->cur_sym }}{{ showAmount($row->balance_after) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="100%" class="text-center text-muted">@lang('No cashback history found.')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $cashbackTransactions->links() }}
            </div>
        </div>
    </div>
@endsection
