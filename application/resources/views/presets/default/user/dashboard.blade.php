@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div>
        <div class="row gy-4 pb-4">
            <div class="col-12">
                <div
                    class="dashboard-welcome-card base--card radius--20 p-4 p-lg-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <span class="badge badge--primary mb-2">@lang('Welcome back')</span>
                        <h4 class="mb-2">{{ auth()->user()->fullname }}</h4>
                        <p class="mb-0 text-muted">@lang('Here is a quick look at your bookings, membership, and account activity.')</p>
                    </div>
                    @php
                        $userMembership = auth()->user()->currentMembership;
                        $membershipPlan = $userMembership ? $userMembership->plan : null;
                    @endphp
                    @if ($membershipPlan && $membershipPlan->image_file)
                        <div class="membership-avatar rounded-circle border overflow-hidden"
                            style="width: 70px; height: 70px; background: #fff; border: 2px solid #eee;">
                            <img src="{{ getImage(getFilePath('membershipPlanImage') . '/' . $membershipPlan->image_file) }}"
                                alt="membership" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 64px; height: 64px; background: rgba(91, 156, 249, 0.12); color: var(--base-color); font-size: 1.5rem;">
                            <i class="las la-user"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row gy-4 pb-4 sortable-container" id="sortable-container">
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard1">
                <a class="d-block" href="{{ route('user.tour.package.booking.all.list') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('All Bookings')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_all_bookings'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard2">
                <a class="d-block" href="{{ route('user.invoice.list') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Invoices')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_invoices'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard3">
                <a class="d-block" href="{{ route('user.tour.package.booking.pending') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Pending Bookings')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_pending_tour_package'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard4">
                <a class="d-block" href="{{ route('user.tour.package.booking.approved') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Approved Bookings')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_approved_tour_package'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard5">
                <a class="d-block" href="{{ route('user.reels.library') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Saved Reels')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['saved_reels'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-film"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard6">
                <a class="d-block" href="{{ route('ticket.open') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Active Ticket')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_active_support_ticker'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
                <a class="d-block" href="{{ route('ticket') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Ticket')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_support_ticker'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard8">
                <a class="d-block" href="{{ route('ticket.open') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Open Ticket')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_open_support_ticker'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard9">
                <a class="d-block" href="{{ route('user.transactions') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Transactions')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_transaction'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard10">
                <a class="d-block" href="{{ route('user.transactions') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Wallet Balance')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ gs()->cur_sym }}{{ showAmount($widget['balance']) }} </h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard11">
                <a class="d-block" href="{{ route('user.membership.index') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Loyalty Points')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">{{ $widget['membership_points'] }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard12">
                <a class="d-block" href="{{ route('user.membership.index') }}">
                    <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                        <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                            <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Cashback')</h6>
                            <div class="amount-wrap align-self-stretch">
                                <h6 class="amount mb-0 fs--24 text-start">
                                    {{ $general->cur_sym }}{{ showAmount($widget['cashback_balance']) }}</h6>
                            </div>
                        </div>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row gy-4 pb-4">
            <div class="col-lg-6">
                <div class="base--card radius--20">
                    <h6>@lang('Monthly Tour Booking Chart')</h6>
                    <div id="tourPackageChart"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="base--card radius--20">
                    <h6>@lang('Monthly Payments Chart')</h6>
                    <div id="depositChart"></div>
                </div>
            </div>
        </div>
        <div class="row gy-4 pb-4">
            <div class="col-lg-12">
                <div class="base--card radius--20">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('SI')</th>
                                <th>@lang('Tour Packages')</th>
                                <th>@lang('Tour Date')</th>
                                <th>@lang('Stay Day & Nights')</th>
                                <th>@lang('Total Price')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myBookings as $item)
                                <tr>
                                    <td data-label="@lang('SI')"><span class="">{{ $loop->iteration }}</span>
                                    </td>

                                    <td class="text-center" data-label="@lang('Tour Packages')">
                                        {{ __($item->tour_package->title) }}
                                    </td>
                                    <td class="text-center" data-label="@lang('Tour Date')">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ showDateTime($item->tour_package->tour_start) }}
                                    </td>
                                    <td class="text-center" data-label="@lang('Stay Day & Nights')">
                                        {{ __($item->tour_package->day_nights) }}
                                    </td>
                                    <td class="text-center" data-label="@lang('Price')">
                                        {{ $general->cur_sym }}{{ showAmount($item->price) }}
                                    </td>

                                    <td class="text-center" data-label="@lang('Discount')">
                                        {{ $general->cur_sym }}{{ showAmount($item->discount) }}
                                    </td>

                                    <td class="text-center" data-label="@lang('Status')">
                                        @php echo $item->statusBadge($item->status) @endphp
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')"
                                            href="{{ route('tour.package.details', [slug($item->tour_package->title), $item->tour_package->id]) }}">
                                            <i class="la la-eye"></i>
                                        </a>


                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td data-label="@lang('Tour Table')" class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if ($myBookings->hasPages())
            <div class="row mx-xxl-5 mx-lg-0 my-4">
                <div class="col-lg-12 justify-content-end d-flex">
                    {{ $myBookings->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
    <script>
        // tourPackageChart
        (function() {
            "use strict";
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '310px'
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
               colors: ['#39bff9', '#39bff9a6'],
                series: [{
                    name: '@lang('Tour Package')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode($tourPackageChart['values']); ?>')
                }],
                fill: {
                    opacity: [0.85, 1],
                },
                labels: JSON.parse('<?php echo json_encode($tourPackageChart['labels']); ?>'),
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
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#tourPackageChart"),
                options
            );
            chart.render();
        })();

        (function() {
            "use strict";
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '310px'
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
                colors: ['#39bff9', '#39bff9a6'],
                series: [{
                    name: '@lang('Payments Chart')',
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
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#depositChart"),
                options
            );
            chart.render();
        })();
    </script>
@endpush
