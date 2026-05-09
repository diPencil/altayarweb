@extends('admin.layouts.app')
@section('panel')
    @include('admin.components.tabs.booking')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('SI')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Tour Name')</th>
                                    <th>@lang('Tour Date')</th>
                                    <th>@lang('Tour Status')</th>
                                    <th>@lang('Booking Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookingTourPackages as $item)
                                    <tr>
                                        @php $primaryImage = optional($item->TourPackagePrimaryImage)->image; @endphp
                                        <td data-label="@lang('SI')"><span class="fw-bold">{{ $loop->iteration }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tour.package.details', [slug($item->title), $item->id]) }}">
                                                <img src="{{ $primaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $primaryImage) : asset('assets/images/general/default.png') }}"
                                                    alt="@lang('image')" class="rounded img-thumb"
                                                    style="width: 60px;height:60px;">
                                            </a>

                                        </td>
                                        <td data-label="@lang('Tour Name')">
                                            <span class="fw-bold">{{ __(strLimit($item->title, 35)) }}</span>
                                        </td>
                                        <td data-label="@lang('Tour Date')">
                                            <span class="fw-bold">{{ showDateTime($item->tour_start) }}</span>
                                        </td>


                                        <td class="text-center" data-label="@lang('Status')">
                                            @php echo $item->statusBadge($item->status) @endphp
                                        </td>

                                        <td class="text-center" data-label="@lang('Booking Status')">
                                            @php echo ($item->adminTourPositionBadge()) @endphp
                                        </td>

                                        <td data-label="@lang('Action')">
                                            <a class="btn btn--sm btn--primary detailBtn" title="@lang('Details')"
                                                href="{{ route('tour.package.details', [slug($item->title), $item->id]) }}">
                                                <i class="la la-eye"></i>
                                            </a>


                                            @if ($item->user_type == 'admin')
                                                <a class="btn btn--sm btn--primary detailBtn" title="@lang('User List')"
                                                    href="{{ route('admin.tour.package.booking.user.list', $item->id) }}">
                                                    <i class="la la-users"></i>
                                                </a>
                                            @else
                                                <a class="btn btn--sm btn--primary detailBtn" title="@lang('User List')"
                                                    href="{{ route('admin.tour.package.booking.agent.user.list', $item->id) }}">
                                                    <i class="la la-users"></i>
                                                </a>
                                            @endif

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
                @if ($bookingTourPackages->hasPages())
                    <div class="card-footer py-4">
                        {{ $bookingTourPackages->links() }}
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
                <input type="text" name="search" class="form-control " placeholder="@lang('Search by tour name')"
                    value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush
