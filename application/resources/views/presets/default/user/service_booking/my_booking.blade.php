@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a class="btn btn--base {{ empty($type) ? 'active' : '' }}" href="{{ route('user.service.booking.my.list') }}">@lang('All')</a>
            @foreach($allowedTypes as $allowedType)
                <a class="btn btn--base {{ $type === $allowedType ? 'active' : '' }}" href="{{ route('user.service.booking.my.list', ['type' => $allowedType]) }}">
                    {{ str_replace('_', ' ', ucfirst($allowedType)) }}
                </a>
            @endforeach
        </div>
        <form action="" method="GET">
            <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                <div class="input-group">
                    <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}" placeholder="@lang('Search by title or reference')">
                    <button type="submit" class="input-group-text bg--base text-white"><i class="las la-search"></i></button>
                </div>
            </div>
        </form>
        <div class="base--card radius--20">
            <div class="table-responsive">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Reference')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Time')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Status')</th>
                            <th class="text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceBookings as $booking)
                            <tr>
                                <td data-label="@lang('SI')">{{ $loop->iteration }}</td>
                                <td data-label="@lang('Type')">{{ str_replace('_', ' ', ucfirst($booking->booking_type)) }}</td>
                                <td data-label="@lang('Title')">{{ $booking->title }}</td>
                                <td data-label="@lang('Reference')">{{ $booking->reference_no ?? __('-') }}</td>
                                <td data-label="@lang('Date')">{{ $booking->booking_date ? showDateTime($booking->booking_date) : __('-') }}</td>
                                <td data-label="@lang('Time')">{{ $booking->service_time ?? __('-') }}</td>
                                <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($booking->amount) }}</td>
                                <td data-label="@lang('Status')">@php echo $booking->statusBadge(); @endphp</td>
                                <td class="text-center" data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')" href="{{ route('user.service.booking.details', $booking->id) }}">
                                        <i class="la la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include($activeTemplate . 'components.pagination_controls', ['data' => $serviceBookings])
@endsection
