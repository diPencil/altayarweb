@extends($activeTemplate . 'layouts.employee.master')
@section('content')
<div class="row justify-content-center gy-4">
    <div class="col-lg-6">

        <div class="tour-card radius--20 position-relative bg--white">
            <div class="tour-card__thumb">
                <a href="">
                    <img class="fit--img"
                        src="{{ getImage(getFilePath('tourPackageImage') . '/' . $bookingDetails->tour_package->TourPackagePrimaryImage->image) }}"
                        alt="Tour Image">
                </a>
            </div>

            <div class="tour-card__content">
                <ul class="list-group border-0 gap--12">
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Package Title'):
                        <span class="fw--500">{{ __($bookingDetails?->tour_package->title) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Category'):
                        <span class="fw--500">{{ __($bookingDetails?->tour_package?->category->name) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Owner'):
                        <span class="fw--500">{{ __(ucfirst($bookingDetails?->owner_type)) }}</span>
                    </li>

                    @if ($bookingDetails?->owner_type == 'Employee')
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Email'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->Employee->email)) }}</span>
                        </li>

                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Phone'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->Employee->mobile)) }}</span>
                        </li>
                    @else
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Email'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->admin->email)) }}</span>
                        </li>
                    @endif
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Total Persons'):
                        <span class="fw--500">{{ __($bookingDetails?->tour_package?->person_capability) }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Price'):
                        <span
                            class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails?->tour_package?->price) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Discount'):
                        <span
                            class="fw--500 badge badge--success">{{ $bookingDetails?->tour_package?->discount }}%</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Start Date'):
                        <span
                            class="fw--500">{{ showDateTime($bookingDetails?->tour_package?->tour_start, 'd-m-Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('End Date'):
                        <span
                            class="fw--500">{{ showDateTime($bookingDetails?->tour_package->tour_end, 'd-m-Y h:i A') }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Stay Day/Nights'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->day_nights }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('City'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->city }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('State'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->state }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Zip Code'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->zip_code }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Country'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->country }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Address'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package->address }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
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
    </div>


        <div class="col-lg-6">
            <div class="tour-card radius--20 position-relative bg--white">
                <h5 class="mb-20">@lang('Booking Details')</h5>
                <ul class="list-group mb-3 gap--12">
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Full Name'):
                        <span class="fw--500">{{ $bookingDetails->user?->fullname }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Email'):
                        <span class="fw--500">{{ $bookingDetails->user?->email }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Phone'):
                        <span class="fw--500">{{ $bookingDetails->user?->mobile }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Number of Seats'):
                        <span class="fw--500">{{ $bookingDetails->seat }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Total Price'):
                        <span
                            class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails->price) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Get Discount'):
                        <span class="fw--500 badge badge--success">{{ $bookingDetails->discount ?? 0 }}%</span>
                    </li>
                    @if ($bookingDetails?->user_proposal_date != $bookingDetails?->tour_package?->tour_start)
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('User Proposal Date'):
                            <span
                                class="fw--500">{{ showDateTime($bookingDetails?->user_proposal_date, 'd-m-Y h:i A') }}</span>
                        </li>
                    @endif

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Payment Status'):
                        <span class="fw--500">@php
                            echo $bookingDetails->statusPaymentBadge();
                        @endphp</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection


