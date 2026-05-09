@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <form action="" method="GET">
                <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                            placeholder="@lang('Search by user name')">
                        <button type="submit" class="input-group-text bg--base text-white border-0"><i
                                class="las la-search"></i></button>
                    </div>
                </div>
            </form>
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Full Name')</th>
                            <th>@lang('Take Seat')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Phone')</th>
                            <th>@lang('Payment Status')</th>
                            <th>@lang('Action')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tourBookings as $item)
                            <tr>
                                <td data-label="@lang('SI')"><span>{{ $loop->iteration }}</span></td>

                                <td class="text-center" data-label="@lang('Full Name')">
                                    {{ $item->user->fullname }}
                                </td>

                                <td class="text-center" data-label="@lang('Take Seat')">
                                    {{ $item->seat }}
                                </td>

                                <td class="text-center" data-label="@lang('Price')">
                                    {{ $general->cur_sym . $item->price }}
                                </td>

                                <td class="text-center" data-label="@lang('Email')">
                                    {{ $item->user->email }}
                                </td>
                                <td data-label="@lang('Phone')">
                                    {{ $item->user->mobile }}
                                </td>


                                <td data-label="@lang('Payment Status')">
                                    @php
                                        echo ($item->statusPaymentBadge())
                                    @endphp
                                </td>
                                <td data-label="@lang('Action')">
                                    @if (employeeUserCan('booking_details'))
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('User List')"href="{{ route('employee.tour.package.booking.details', $item->id) }}">
                                        <i class="la la-eye"></i>
                                    </a>
                                    @endif
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

    @if ($tourBookings->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $tourBookings->links() }}
            </div>
        </div>
    @endif
@endsection


