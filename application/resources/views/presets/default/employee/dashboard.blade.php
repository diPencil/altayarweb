@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-3 align-items-center">
        <div class="col-lg-12">
            @if (auth('employee')->user()->kv == 0)
                <div class="alert alert-warning radius--20">
                    <div class="kyc-noty d-flex justify-content-between align-items-center" role="alert">
                        <h5 class="alert-heading mb-0">@lang('KYC Verification required')</h5>
                        <hr>
                        <p class="mb-0">
                            @if (employeePageCan('kyc'))
                                <a href="{{ route('employee.kyc.form') }}" class="btn btn--base btn--md pills">
                                    @lang('Click Here to Verify')
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
            @elseif(auth('employee')->user()->kv == 2)
                <div class="alert alert-warning radius--20">
                    <div class="kyc-noty kyc-noty-pending d-flex justify-content-between align-items-center" role="alert">
                        <h5 class="alert-heading mb-0">@lang('KYC Verification pending')</h5>
                        <hr>
                        <p class="mb-0">
                            @if (employeePageCan('kyc'))
                                <a href="{{ route('employee.kyc.data') }}" class="btn btn--base btn--md pills">
                                    @lang('See KYC Data')
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row gy-4 pb-4" id="sortable-container">
        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard1">
            <a class="d-block" href="{{ route('employee.users') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Users Assign')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_assigned_users'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard2">
            <a class="d-block" href="{{ route('employee.tour.package.my.list') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Tour Package')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_tour_package'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-plane"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard3">
            <a class="d-block" href="{{ route('employee.listing.index') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Listing Offers')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_listing_offers'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard4">
            <a class="d-block" href="{{ route('employee.ticket') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Tickets')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_support_ticker'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard5">
            <a class="d-block" href="{{ route('employee.ticket.open') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Open Tickets')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_open_support_ticker'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard6">
            <a class="d-block" href="{{ route('employee.popup-ads.index') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Popup Ads')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_popup_ads'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('employee.transactions') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Total Transactions')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_transaction'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard8">
            <a class="d-block" href="{{ route('employee.deposit.history') }}">
                <div class="wizard-card d-flex flex-row justify-content-between align-items-start gap--12">
                    <div class="wizard-card__text d-flex flex-column align-items-start flex-grow-1 min-w-0 gap-2">
                        <h6 class="title fw--400 fs--16 mb-0 align-self-stretch text-start">@lang('Payment Logs')</h6>
                        <div class="amount-wrap align-self-stretch">
                            <h6 class="amount mb-0 fs--24 text-start">{{ $widget['total_payment_logs'] }}</h6>
                        </div>
                    </div>
                    <div class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1 flex-shrink-0">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if (employeeDashboardCan('charts'))
        <div class="row gy-4 pb-4">
            <div class="col-lg-6">
                <div class="base--card radius--20">
                    <h6>@lang('Monthly Tour Booked Chart')</h6>
                    <div id="tourPackageChart"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="base--card radius--20">
                    <h6>@lang('Monthly Withdrawals Chart')</h6>
                    <div id="depositChart"></div>
                </div>
            </div>
        </div>
    @endif

    @if (employeeDashboardCan('recent_bookings'))
        <div class="row gy-4 pb-4">
            <div class="col-lg-12">
                <div class="base--card radius--20">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('SI')</th>
                                <th>@lang('Image')</th>
                                <th>@lang('Tour Package Title')</th>
                                <th>@lang('Tour Date')</th>
                                <th>@lang('Tour End')</th>
                                <th>@lang('Total seats')</th>
                                <th>@lang('Available seats')</th>
                                <th>@lang('Tour Status')</th>
                                <th>@lang('Booking Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myBooked as $item)
                                <tr>
                                    <td data-label="@lang('SI')"><span>{{ $loop->iteration }}</span></td>
                                    <td data-label="@lang('tourPackageImage')">
                                        <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $item->TourPackagePrimaryImage->image) }}" alt="@lang('image')" class="rounded img-thumb" style="width: 60px;height:60px;">
                                    </td>
                                    <td class="text-center" data-label="@lang('Tour Package Title')">{{ __($item->title) }}</td>
                                    <td class="text-center" data-label="@lang('Tour Date')">{{ showDateTime($item->tour_start) }}</td>
                                    <td class="text-center" data-label="@lang('Tour Date')">{{ showDateTime($item->tour_end) }}</td>
                                    <td class="text-center" data-label="@lang('Total seats')">{{ $item->person_capability }}</td>
                                    <td class="text-center" data-label="@lang('Available seats')">{{ $item->person_capability - $item->booking_person }}</td>
                                    <td class="text-center" data-label="@lang('Tour Status')">@php echo ($item->statusBadge($item->status)) @endphp</td>
                                    <td class="text-center" data-label="@lang('Status')">@php echo ($item->tourPositionBadge()) @endphp</td>
                                    <td data-label="@lang('Action')">
                                        <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')" href="{{ route('tour.package.details', [slug($item->displayTitle()), $item->id]) }}"><i class="la la-eye"></i></a>
                                        <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('User List')" href="{{ route('employee.tour.package.booking.user.list', $item->id) }}"><i class="la la-users"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">@lang('No data found')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($myBooked->hasPages())
        <div class="row">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $myBooked->links() }}
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
    <script>
        (function () {
            "use strict";
            var depositOptions = {
                chart: { type: 'area', stacked: false, height: '310px' },
                stroke: { width: [0, 3], curve: 'smooth' },
                plotOptions: { bar: { columnWidth: '50%' } },
                colors: ['#00adad', '#67BAA7'],
                series: [{
                    name: '@lang('Withdrawals')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode(collect($withdrawalsChart['values'])->map(fn($val) => (float) $val)); ?>')
                }],
                fill: { opacity: [0.85, 1] },
                labels: JSON.parse('<?php echo json_encode($withdrawalsChart['labels']); ?>'),
                markers: { size: 0 },
                xaxis: { type: 'text' },
                yaxis: { min: 0 },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: function(y) { return typeof y !== "undefined" ? "$ " + y.toFixed(0) : y; } }
                },
                legend: { labels: { useSeriesColors: true }, markers: { customHTML: [function() { return '' }, function() { return '' }] } }
            };
            new ApexCharts(document.querySelector("#depositChart"), depositOptions).render();

            var tourPackageOptions = {
                chart: { type: 'area', stacked: false, height: '310px' },
                stroke: { width: [0, 3], curve: 'smooth' },
                plotOptions: { bar: { columnWidth: '50%' } },
                colors: ['#00adad', '#67BAA7'],
                series: [{
                    name: '@lang('Tour Package')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode(collect($tourPackageChart['values'])->map(fn($val) => (float) $val)); ?>')
                }],
                labels: JSON.parse('<?php echo json_encode($tourPackageChart['labels']); ?>') ?? ['No Data'],
                markers: { size: 0, showNullDataPoints: false },
                fill: { opacity: [0.85, 1] },
                xaxis: { type: 'text' },
                yaxis: { min: 0 },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: function(y) { return typeof y !== "undefined" ? "$ " + y.toFixed(0) : y; } }
                },
                legend: { labels: { useSeriesColors: true }, markers: { customHTML: [function() { return '' }, function() { return '' }] } }
            };
            new ApexCharts(document.querySelector("#tourPackageChart"), tourPackageOptions).render();
        })();
    </script>
@endpush


