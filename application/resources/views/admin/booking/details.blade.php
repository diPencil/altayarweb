@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="base--card z--1">
                <h5 class="mb-20">@lang('Tour Details')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Tour Package Title'):
                        <span class="fw-bold">{{ __($bookingDetails?->tour_package->title) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Category'):
                        <span class="fw-bold">{{ __($bookingDetails?->tour_package?->category->name) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Tour Owner'):
                        <span class="fw-bold">{{ __(ucfirst($bookingDetails?->owner_type)) }}</span>
                    </li>
                    @if ($bookingDetails?->owner_type == 'agent')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Owner Email'):
                            <span class="fw-bold">{{ __(ucfirst($bookingDetails?->agent->email)) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Owner Phone'):
                            <span class="fw-bold">{{ __(ucfirst($bookingDetails?->agent->mobile)) }}</span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Persons'):
                        <span class="fw-bold">{{ __($bookingDetails?->tour_package?->person_capability) }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Price'):
                        <span
                            class="fw-bold badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails?->tour_package?->price) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Discount'):
                        <span class="fw-bold badge badge--success">{{ $bookingDetails?->tour_package?->discount }}%</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Start Date'):
                        <span
                            class="fw-bold">{{ showDateTime($bookingDetails?->tour_package?->tour_start, 'd-m-Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('End Date'):
                        <span
                            class="fw-bold">{{ showDateTime($bookingDetails?->tour_package->tour_end, 'd-m-Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Stay Day/Nights'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->day_nights }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('City'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->city }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('State'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->state }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Zip Code'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->zip_code }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Country'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->country }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Address'):
                        <span class="fw-bold">{{ $bookingDetails?->tour_package->address }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Tour Status'):
                        @php
                                echo $bookingDetails?->tour_package->statusBadge(
                                    $bookingDetails?->tour_package->status,
                                );
                            @endphp
                    </li>
                </ul>

            </div>
        </div>


        <div class="col-lg-6">
            <div class="base--card z--1">
                <h5 class="mb-20">@lang('Booking Details')</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Full Name'):
                        <span class="fw-bold">{{ $bookingDetails->user?->fullname }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Email'):
                        <span class="fw-bold">{{ $bookingDetails->user?->email }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Phone'):
                        <span class="fw-bold">{{ $bookingDetails->user?->mobile }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Number of Seats'):
                        <span class="fw-bold">{{ $bookingDetails->seat }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Price'):
                        <span
                            class="fw-bold badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails->price) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Get Discount'):
                        <span class="fw-bold badge badge--success">{{ $bookingDetails->discount ?? 0 }}%</span>
                    </li>
                    @if ($bookingDetails?->user_proposal_date != $bookingDetails?->tour_package?->tour_start)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('User Proposal Date'):
                            <span
                                class="fw-bold">{{ showDateTime($bookingDetails?->user_proposal_date, 'd-m-Y h:i A') }}</span>
                        </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment Status'):
                        <span class="fw-bold">@php
                            echo $bookingDetails->statusPaymentBadge();
                        @endphp</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
