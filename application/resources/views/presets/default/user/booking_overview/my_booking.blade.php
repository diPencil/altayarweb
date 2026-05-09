@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <form action="" method="GET">
            <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                <div class="input-group">
                    <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}" placeholder="@lang('Search by title or type')">
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
                            <th>@lang('Reference')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Status')</th>
                            <th class="text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td data-label="@lang('SI')">{{ $loop->iteration }}</td>
                                <td data-label="@lang('Type')">
                                    {{ $booking->kind_label }}
                                    @if($booking->legacy_import)
                                        <span class="badge badge--info" style="font-size: 10px;">@lang('Legacy')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Reference')">{{ $booking->reference_no ?? __('-') }}</td>
                                <td data-label="@lang('Title')">{{ $booking->title }}</td>
                                <td data-label="@lang('Date')">{{ ($booking->service_date ?? $booking->booking_date) ? showDateTime($booking->service_date ?? $booking->booking_date, 'd M Y') : __('-') }}</td>
                                <td data-label="@lang('Amount')">{{ $general->cur_sym }}{{ showAmount($booking->amount) }}</td>
                                <td data-label="@lang('Status')">{!! $booking->status_html !!}</td>
                                <td class="text-center" data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')" href="{{ $booking->details_url }}">
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

@include($activeTemplate . 'components.pagination_controls', ['data' => $bookings])
@endsection
