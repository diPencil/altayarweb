@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('SI')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Full Name')</th>
                                    <th>@lang('Seats')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Phone')</th>
                                    <th>@lang('Payment')</th>
                                    <th>@lang('Action')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tourBookings as $item)
                                    <tr>
                                        <td data-label="@lang('SI')">
                                            <span class="fw-bold">{{ $loop->iteration }}</span>
                                        </td>
                                        <td data-label="@lang('Username')">
                                            <span class="fw-bold">
                                                <a href="{{route('admin.users.detail',$item->user->id)}}">
                                                {{ $item->user->username }}
                                            </a>
                                            </span>
                                        </td>
                                        <td data-label="@lang('Full Name')">
                                            <span class="fw-bold">
                                                {{ $item->user->fullname }}
                                            </span>
                                        </td>
                                   
                                        <td data-label="@lang('Take Seat')">
                                            <span class="fw-bold">{{ $item->seat }}</span>
                                        </td>
                                        <td data-label="@lang('Price')">
                                            <span class="fw-bold">{{ $general->cur_sym . $item->price }}</span>
                                        </td>
                                        
                                        <td class="text-center" data-label="@lang('Email')">
                                            {{ $item->user->email }}
                                        </td>
                                        <td class="text-center" data-label="@lang('Phone')">
                                            {{ $item->user->mobile }}
                                        </td>
                                       
                                        <td data-label="@lang('Payment Status')">
                                            @php
                                                echo $item->statusPaymentBadge();
                                            @endphp
                                        </td>
                                        
                                        <td data-label="@lang('Action')">
                                            <a class="btn btn--sm btn--primary detailBtn" title="@lang('View Details')"
                                                href="{{route('admin.tour.package.booking.details',$item->id)}}">
                                                <i class="la la-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td data-label="@lang('Booking Table')" class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($tourBookings->hasPages())
                    <div class="card-footer py-4">
                        {{ $tourBookings->links() }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <form action="" method="GET" class="form-inline">
            <div class="input-group justify-content-end">
                <input type="text" name="search" class="form-control" placeholder="@lang('Search by user name')"
                    value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush
