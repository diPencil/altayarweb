@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row justify-content-center gy-4">
    <div class="col-lg-12">
        <div class="tour-card radius--20 position-relative bg--white">
            <h5 class="mb-20">@lang('Service Booking Details')</h5>
            <ul class="list-group mb-3 gap--12">
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Type'):
                    <span class="fw--500">{{ str_replace('_', ' ', ucfirst($bookingDetails->booking_type)) }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Title'):
                    <span class="fw--500">{{ $bookingDetails->title }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Reference'):
                    <span class="fw--500">{{ $bookingDetails->reference_no ?? __('-') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Booking Date'):
                    <span class="fw--500">{{ $bookingDetails->booking_date ? showDateTime($bookingDetails->booking_date) : __('-') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Service Date'):
                    <span class="fw--500">{{ $bookingDetails->service_date ? showDateTime($bookingDetails->service_date) : __('-') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Travel Time'):
                    <span class="fw--500">{{ $bookingDetails->service_time ?? __('-') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Amount'):
                    <span class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails->amount) }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Status'):
                    <span class="fw--500">@php echo $bookingDetails->statusBadge(); @endphp</span>
                </li>
                @if($bookingDetails->notes)
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Notes'):
                        <span class="fw--500 text-end">{{ $bookingDetails->notes }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
