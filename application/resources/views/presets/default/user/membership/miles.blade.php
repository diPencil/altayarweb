@extends($activeTemplate . 'layouts.user.master')

@section('content')
    {{-- Header Banner Card --}}
    <div class="row gy-4 mb-4">
        <div class="col-12">
            <div class="base--card radius--20 p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h4 class="mb-1 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-plane-departure text--base"></i>
                            @lang('Miles Balance & History')
                        </h4>
                        <p class="text-muted mb-0">@lang('Track your available mileage balance, lifetime earnings, deductions, and complete transaction ledger.')</p>
                    </div>
                    @if($currentMembership)
                        <span class="badge bg--success px-3 py-2 fs--14">
                            <i class="fa-solid fa-crown me-1"></i>
                            @lang('Current Plan'): {{ is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Three Core Metrics Cards --}}
    <div class="row gy-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="base--card radius--20 h-100 membership-stat-card border-top border-4 border-primary">
                <span class="text-muted text-uppercase fs--12 fw-semibold">@lang('Current Available Miles')</span>
                <h3 class="mb-2 mt-1 text--primary fw-bold">{{ number_format($availableMiles) }}</h3>
                <p class="text-muted mb-0 fs--14">@lang('Your active, spendable mileage balance for rewards and bookings.')</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="base--card radius--20 h-100 membership-stat-card border-top border-4 border-success">
                <span class="text-muted text-uppercase fs--12 fw-semibold">@lang('Total Miles Earned')</span>
                <h3 class="mb-2 mt-1 text--success fw-bold">+{{ number_format($totalEarned) }}</h3>
                <p class="text-muted mb-0 fs--14">@lang('Lifetime gross miles accumulated from promotions, travel, and perks.')</p>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="base--card radius--20 h-100 membership-stat-card border-top border-4 border-danger">
                <span class="text-muted text-uppercase fs--12 fw-semibold">@lang('Total Miles Deducted')</span>
                <h3 class="mb-2 mt-1 text--danger fw-bold">-{{ number_format($totalDeducted) }}</h3>
                <p class="text-muted mb-0 fs--14">@lang('Lifetime gross miles redeemed, deducted, or utilized.')</p>
            </div>
        </div>
    </div>

    {{-- Transaction History Table --}}
    <div class="row gy-4">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 border-bottom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text--base"></i>
                        @lang('Miles Transactions Ledger')
                    </h5>
                    <span class="text-muted fs--14">
                        @lang('Showing authoritative ledger entries')
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('TRX')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Balance After')</th>
                                <th>@lang('Source & Reference')</th>
                                <th>@lang('Description')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $row)
                                <tr>
                                    <td data-label="@lang('TRX')">
                                        <span class="fw-bold">{{ $row->trx }}</span>
                                        @if($row->is_reversed)
                                            <span class="badge bg--dark text-white ms-1">@lang('Reversed')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Date')">
                                        {{ showDateTime($row->created_at, 'd M Y, h:i A') }}
                                        <small class="text-muted d-block">{{ diffForHumans($row->created_at) }}</small>
                                    </td>
                                    <td data-label="@lang('Type')">
                                        @if($row->is_reversal)
                                            <span class="badge bg--warning text-dark">@lang('Reversal')</span>
                                        @elseif($row->type === 'credit')
                                            <span class="badge bg--success text-white">@lang('Credit (+)')</span>
                                        @else
                                            <span class="badge bg--danger text-white">@lang('Debit (-)')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Amount')">
                                        <span class="fw-bold {{ $row->direction === 1 ? 'text--success' : 'text--danger' }}">
                                            {{ $row->direction === 1 ? '+' : '-' }}{{ number_format($row->miles) }}
                                        </span>
                                    </td>
                                    <td data-label="@lang('Balance After')">
                                        <span class="fw-bold">{{ number_format($row->balance_after) }}</span>
                                    </td>
                                    <td data-label="@lang('Source & Reference')">
                                        <span class="badge bg--light text-dark">{{ ucfirst($row->source) }}</span>
                                        @if($row->reference_id)
                                            <small class="text-muted d-block">@lang('Ref'): {{ $row->reference_id }}</small>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Description')">
                                        <span>{{ $row->remark }}</span>
                                        @if($row->is_reversal && $row->reversalOf)
                                            <small class="text-muted d-block">@lang('Reversal of'): {{ $row->reversalOf->trx }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center text-muted py-5">
                                        <div class="empty-state">
                                            <i class="fa-solid fa-plane-slash fs--40 mb-3 text-muted d-block"></i>
                                            <h6>@lang('No mileage transactions found')</h6>
                                            <p class="text-muted mb-0 fs--14">@lang('When miles are credited or redeemed, they will appear here in your ledger.')</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer py-4 border-top">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
