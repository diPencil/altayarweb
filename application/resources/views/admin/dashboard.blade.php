@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 mb-4">
        <div class="col-xl-6">
            <div class="card p-3 rounded-3 h-100">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
                    <h6 class="mb-0 fw-semibold">@lang('Core Operations')</h6>
                </div>
                <div class="row g-0">
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-users"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.users.all') }}"></a>
                                <h5>{{ $widget['total_users'] }}</h5>
                                <span>@lang('Total Users')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-user-check"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.users.active') }}"></a>
                                <h5>{{ $widget['verified_users'] }}</h5>
                                <span>@lang('Active Users')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-users"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.employees.all') }}"></a>
                                <h5>{{ $widget['total_Employees'] }}</h5>
                                <span>@lang('Total Employees')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-file-invoice-dollar"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.invoice.index') }}"></a>
                                <h5>{{ $widget['total_invoices'] }}</h5>
                                <span>@lang('Total Invoices')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-ticket-alt"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.ticket') }}"></a>
                                <h5>{{ $widget['total_tickets'] }}</h5>
                                <span>@lang('Total Tickets')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-comments"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.chat-assistant.index') }}"></a>
                                <h5>{{ $widget['live_chats'] }}</h5>
                                <span>@lang('Live Chats')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card p-3 rounded-3 h-100">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
                    <h6 class="mb-0 fw-semibold">@lang('Business Side')</h6>
                </div>
                <div class="row g-0">
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-plane"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.tour.package.index') }}"></a>
                                <h5>{{ $widget['total_tour_package'] }}</h5>
                                <span>@lang('Tour Packages')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-calendar-alt"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.service.booking.index') }}"></a>
                                <h5>{{ $widget['total_bookings'] }}</h5>
                                <span>@lang('All Bookings')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-tags"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.listing.index') }}"></a>
                                <h5>{{ $widget['listing_offers'] }}</h5>
                                <span>@lang('Listing Offers')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-bullhorn"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.popup-ads.index') }}"></a>
                                <h5>{{ $widget['popup_ads'] }}</h5>
                                <span>@lang('Popup Ads')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-comment-dots"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.reels.comments') }}"></a>
                                <h5>{{ $widget['reel_comments'] }}</h5>
                                <span>@lang('Reel Comments')</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                        <div class="dashboard-widget">
                            <div class="dashboard-widget__icon">
                                <i class="dashboard-card-icon las la-spinner"></i>
                            </div>
                            <div class="dashboard-widget__content">
                                <a title="@lang('View all')" class="dashboard-widget-link" href="{{ route('admin.deposit.pending') }}"></a>
                                <h5>{{ $deposit['total_deposit_pending'] }}</h5>
                                <span>@lang('Pending Deposits')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="row gy-4">
                <div class="col-sm-3">
                    <a href="{{ route('admin.membership.subscriptions') }}">
                        <div class="card prod-p-card  background-pattern-white bg--primary">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">@lang('Total Memberships')</h6>
                                        <h3 class="m-b-0 text-white">{{ $membership['total_subscriptions'] }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-id-card text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.membership.subscriptions') }}">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">@lang('Active Memberships')</h6>
                                        <h3 class="m-b-0">{{ $membership['active_subscriptions'] }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.membership.subscriptions') }}">
                        <div class="card prod-p-card background-pattern-white bg--primary">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">@lang('Expiring Soon')</h6>
                                        <h3 class="m-b-0 text-white">{{ $membership['expiring_soon'] }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-hourglass-half text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.membership.plans') }}">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">@lang('Active Plans')</h6>
                                        <h3 class="m-b-0">{{ $membership['active_plans'] }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-layer-group"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>

    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="card-title mb-0">@lang('Recent Bookings')</h5>
                        <a href="{{ route('admin.service.booking.index') }}" class="btn btn-sm btn--primary text-white">@lang('View All')</a>
                    </div>
                    <div class="table-responsive admin-table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Booking Date')</th>
                                    <th>@lang('Service Date')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->user?->username ?? __('-') }}</td>
                                        <td>{{ str_replace('_', ' ', ucfirst($booking->booking_type)) }}</td>
                                        <td>{{ __(strLimit($booking->title, 28)) }}</td>
                                        <td>{{ $booking->booking_date ? showDateTime($booking->booking_date) : __('-') }}</td>
                                        <td>{{ $booking->service_date ? showDateTime($booking->service_date) : __('-') }}</td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($booking->amount) }}</td>
                                        <td>{!! $booking->status_html !!}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">@lang('No bookings found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="card-title mb-0">@lang('Recent Payments')</h5>
                        <a href="{{ route('admin.deposit.list') }}" class="btn btn-sm btn--primary text-white">@lang('View All')</a>
                    </div>
                    <div class="table-responsive admin-table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway')</th>
                                    <th>@lang('Transaction')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Created at')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td>{{ __($payment->gateway?->name ?? __('-')) }}</td>
                                        <td>{{ $payment->trx }}</td>
                                        <td>
                                            <a class="text-muted" href="{{ route('admin.users.detail', $payment->user_id) }}">
                                                {{ $payment->user?->fullname ?? __('-') }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong title="@lang('Amount with charge')">
                                                {{ showAmount($payment->amount + $payment->charge) }} {{ __($general->cur_text) }}
                                            </strong>
                                        </td>
                                        <td>
                                            <strong>{{ showAmount($payment->final_amo) }} {{ __($payment->method_currency) }}</strong>
                                        </td>
                                        <td>{{ showDateTime($payment->created_at) }}</td>
                                        <td>
                                            <span class="d-inline-flex align-items-center justify-content-center">
                                                @php echo $payment->statusBadge @endphp
                                            </span>
                                        </td>
                                        <td>
                                            <a title="@lang('Details')" href="{{ route('admin.deposit.details', $payment->id) }}" class="btn btn-sm btn--primary ms-1">
                                                <i class="la la-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No payments found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Monthly Payments & Withdraw Report') (@lang('This year'))</h5>
                    <div id="account-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="row gy-4 mb-4">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Daily Logins Users') (@lang('Last 10 days'))</h5>
                            <div id="login-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Daily Logins Employees') (@lang('Last 10 days'))</h5>
                            <div id="Employee-login-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>

    <script>
        "use strict";
        // [ account-chart ] start
        (function() {
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '268px'
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
                colors: ['#39bff9', '#39bff9'],
                series: [{
                    name: '@lang('Withdrawals')',
                    type: 'column',
                    data: JSON.parse('<?php echo json_encode($withdrawalsChart['values']); ?>')
                }, {
                    name: '@lang('Deposits')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode($depositsChart['values']); ?>')
                }],
                fill: {
                    opacity: [0.85, 1],
                },
                labels: JSON.parse('<?php echo json_encode($depositsChart['labels']); ?>'),
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'text'
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "$ " + y.toFixed(0);
                            }
                            return y;

                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: true
                    },
                    markers: {
                        customHTML: [
                            function() {
                                return ''
                            },
                            function() {
                                return ''
                            }
                        ]
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#account-chart"),
                options
            );
            chart.render();
        })();

        // [ User login-chart ] start
        (function() {
            var options = {
                series: [{
                    name: "User Count",
                    data: JSON.parse('<?php echo json_encode($userLogins['values']); ?>')
                }],
                chart: {
                    height: '310px',
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: ['#39bff9'],
                labels: JSON.parse('<?php echo json_encode($userLogins['labels']); ?>'),
                xaxis: {
                    type: 'date',
                },
                yaxis: {
                    opposite: true
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart = new ApexCharts(document.querySelector("#login-chart"), options);
            chart.render();
        })();

        // [ Employee login-chart ] start
        (function() {
            var options = {
                series: [{
                    name: "Employee Count",
                    data: JSON.parse('<?php echo json_encode($EmployeeLogins['values']); ?>')
                }],
                chart: {
                    height: '310px',
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: ['#39bff9'],
                labels: JSON.parse('<?php echo json_encode($EmployeeLogins['labels']); ?>'),
                xaxis: {
                    type: 'date',
                },
                yaxis: {
                    opposite: true
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart = new ApexCharts(document.querySelector("#Employee-login-chart"), options);
            chart.render();
        })();
    </script>
@endpush
