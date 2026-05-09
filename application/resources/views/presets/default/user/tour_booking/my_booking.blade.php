@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <form action="" method="GET">
            <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                <div class="input-group">
                    <input type="text" name="search" class="form--control form-control bg--white " value="{{ request()->search }}" placeholder="@lang('Search by tour package title or reference.')">
                    <button type="submit" class="input-group-text bg--base text-white "><i class="las la-search"></i></button>
                </div>
            </div>
        </form>
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('SI')</th>
                    <th>@lang('Reference')</th>
                    <th>@lang('Tour Packages')</th>
                    <th>@lang('Tour Date')</th>
                    <th>@lang('Stay Day & Nights')</th>
                    <th>@lang('Total Price')</th>
                    <th>@lang('Discount')</th>
                    <th>@lang('Tour Status')</th>
                    <th class="text-center">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tourBookingList as $item)
                    <tr>
                        <td data-label="@lang('SI')"><span class="">{{ $loop->iteration }}</span></td>
                        <td class="text-center" data-label="@lang('Reference')">{{ $item->reference_no ?? __('-') }}</td>
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

                        <td class="text-center" data-label="@lang('Tour Status')">
                            @php echo $item->statusBadge($item->status) @endphp
                        </td>
                        <td class="text-center" data-label="@lang('Action')">
                            <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')"
                                href="{{ route('tour.package.details', [slug($item->tour_package->title), $item->tour_package->id]) }}">
                                <i class="la la-link"></i>
                            </a>


                            <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Tour Details')"
                                href="{{ route('user.tour.package.booking.details',$item->id) }}">
                                <i class="la la-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td data-label="@lang('Tour Table')" class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>

@include($activeTemplate . 'components.pagination_controls', ['data' => $tourBookingList])
@endsection
