@extends('admin.layouts.app')
@section('panel')
    @php
        $currentSort = $sort ?? '';
        $currentDirection = $direction ?? 'desc';
        $currentFilter = request('legacy_filter', $filter ?? 'all');

        $sortUrl = function (string $column) use ($currentSort, $currentDirection, $currentFilter) {
            $query = request()->query();
            unset($query['page']);

            if ($currentSort === $column) {
                $query['sort'] = $column;
                $query['direction'] = $currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $query['sort'] = $column;
                $query['direction'] = 'asc';
            }

            $query['legacy_filter'] = $currentFilter;

            return request()->url() . '?' . http_build_query($query);
        };

        $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
            if ($currentSort !== $column) {
                return '<i class="la la-sort text-white-50"></i>';
            }

            return $currentDirection === 'asc'
                ? '<i class="la la-sort-up text-white"></i>'
                : '<i class="la la-sort-down text-white"></i>';
        };
    @endphp
    <style>
        .sortable-header-link,
        .booking-sort-link {
            color: #fff !important;
            text-decoration: none !important;
        }

        .sortable-header-link:hover,
        .sortable-header-link:focus,
        .booking-sort-link:hover,
        .booking-sort-link:focus {
            color: #fff !important;
            text-decoration: none !important;
        }

        .sortable-header-link i,
        .sortable-header-link svg,
        .booking-sort-link i,
        .booking-sort-link svg {
            color: #fff !important;
        }

        /* Stronger scoped override for booking table header colors */
        .booking-list-table thead th,
        .booking-list-table thead th a,
        .booking-list-table thead th a span,
        .booking-list-table thead th a i,
        .booking-list-table thead th a svg,
        .booking-list-table thead th .booking-sort-link,
        .booking-list-table thead th .sortable-header-link {
            color: #ffffff !important;
            fill: #ffffff !important;
            stroke: #ffffff !important;
            text-decoration: none !important;
        }

        .booking-list-table thead th a:hover,
        .booking-list-table thead th a:focus,
        .booking-list-table thead th a:hover span,
        .booking-list-table thead th a:focus span,
        .booking-list-table thead th a:hover i,
        .booking-list-table thead th a:focus i {
            color: #ffffff !important;
            fill: #ffffff !important;
            stroke: #ffffff !important;
            text-decoration: none !important;
        }
    </style>
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <h5 class="card-title mb-0">@lang('Tour Package Bookings')</h5>
                    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap ms-auto">
                        <form action="" method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-lg-nowrap mb-0">
                            <select name="legacy_filter" class="form-control bg--white"
                                style="width: clamp(230px, 18vw, 260px);" onchange="this.form.submit()">
                                <option value="all" @selected($currentFilter === 'all')>@lang('All bookings')</option>
                                <option value="additional_legacy" @selected($currentFilter === 'additional_legacy')>@lang('Additional legacy bookings only')</option>
                                <option value="future_dated" @selected($currentFilter === 'future_dated')>@lang('Future-dated bookings')</option>
                                <option value="manual_review_excluded" @selected($currentFilter === 'manual_review_excluded')>@lang('Manual-review excluded rows')</option>
                                <option value="repaired_legacy" @selected($currentFilter === 'repaired_legacy')>@lang('Repaired legacy rows')</option>
                            </select>
                            <div class="d-flex align-items-center flex-nowrap gap-2">
                                <input type="text" name="search" class="form-control bg--white"
                                    style="width: clamp(340px, 30vw, 420px);"
                                    placeholder="@lang('Search by client, title, reference, type...')"
                                    value="{{ $search ?? '' }}">
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">
                                <button class="btn btn--primary flex-shrink-0" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <a href="{{ route('admin.service.booking.create') }}" class="btn btn--primary flex-shrink-0">@lang('Add Booking')</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0 booking-list-table">
                            <thead>
                                <tr>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('client') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Client')</span>{!! $sortIcon('client') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('type') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Type')</span>{!! $sortIcon('type') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('title') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Title')</span>{!! $sortIcon('title') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('reference') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Reference')</span>{!! $sortIcon('reference') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('dates') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Dates')</span>{!! $sortIcon('dates') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('amount') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Amount')</span>{!! $sortIcon('amount') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">
                                        <a href="{{ $sortUrl('status') }}" class="sortable-header-link booking-sort-link text-white d-inline-flex align-items-center gap-1" style="color:#fff !important;">
                                            <span>@lang('Status')</span>{!! $sortIcon('status') !!}
                                        </a>
                                    </th>
                                    <th class="text-white">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->user?->username }}</td>
                                        <td>
                                            @if ($booking->source === 'tour')
                                                @lang('Tour Package')
                                            @else
                                                {{ $booking->display_type ?? __(Str::headline(str_replace('_', ' ', (string) $booking->booking_type))) }}
                                            @endif
                                        </td>
                                        <td class="wrap-column" title="{{ __($booking->title) }}">{{ Str::limit(__($booking->title), 35) }}</td>
                                        <td>{{ $booking->reference_no ?? __('-') }}</td>
                                        <td>
                                            {{ $booking->date_primary ? showDateTime($booking->date_primary, 'd M Y') : __('-') }}
                                            @if($booking->service_date || $booking->service_end_date)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $booking->service_date ? showDateTime($booking->service_date, 'd M Y') : __('-') }}
                                                    @if($booking->service_end_date)
                                                        - {{ showDateTime($booking->service_end_date, 'd M Y') }}
                                                    @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($booking->amount) }}</td>
                                        <td>{!! $booking->status_html !!}</td>
                                        <td>
                                            @if($booking->source === 'tour')
                                                <a href="{{ $booking->detail_url }}" class="btn btn-sm btn--primary">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn--primary edit-booking-btn"
                                                    data-bs-toggle="modal" data-bs-target="#bookingEditModal"
                                                    data-id="{{ $booking->id }}"
                                                    data-user_id="{{ $booking->user?->id }}"
                                                    data-booking_type="{{ $booking->booking_type }}"
                                                    data-title="{{ $booking->title }}"
                                                    data-reference_no="{{ $booking->reference_no }}"
                                                    data-booking_date="{{ $booking->booking_date?->format('Y-m-d') }}"
                                                    data-service_date="{{ $booking->service_date?->format('Y-m-d') }}"
                                                    data-service_end_date="{{ $booking->service_end_date?->format('Y-m-d') }}"
                                                    data-amount="{{ $booking->amount }}"
                                                    data-status="{{ $booking->status }}"
                                                    data-notes="{{ $booking->notes }}">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this booking?')"
                                                        data-action="{{ route('admin.service.booking.delete', $booking->id) }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            @endif
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
                @if($bookings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($bookings) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookingEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit Booking')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" id="bookingEditForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Client')</label>
                                <select name="user_id" class="form-control select2-basic" id="edit_user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->username }} - {{ $user->firstname }} {{ $user->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Booking Type')</label>
                                <select name="booking_type" class="form-control" id="edit_booking_type" required>
                                    <option value="tour">@lang('Tour Package')</option>
                                    <option value="flight">@lang('Flight')</option>
                                    <option value="stay">@lang('Stay / Accommodation')</option>
                                    <option value="coupon">@lang('Discount Coupon')</option>
                                    <option value="restaurant">@lang('Restaurant')</option>
                                    <option value="cafe">@lang('Cafe')</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">@lang('Title / Service Name')</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Reference No.')</label>
                                <input type="text" name="reference_no" id="edit_reference_no" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Booking Date')</label>
                                <input type="date" name="booking_date" id="edit_booking_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Service Date')</label>
                                <input type="date" name="service_date" id="edit_service_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('End Date')</label>
                                <input type="date" name="service_end_date" id="edit_service_end_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Amount')</label>
                                <input type="number" step="any" min="0" name="amount" id="edit_amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="0">@lang('Pending')</option>
                                    <option value="1">@lang('Confirmed')</option>
                                    <option value="2">@lang('Completed')</option>
                                    <option value="3">@lang('Canceled')</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Notes')</label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary">@lang('Update Booking')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            'use strict';
            $('.select2-basic').select2();
            $(document).on('click', '.edit-booking-btn', function () {
                const button = $(this);
                const updateUrl = '{{ route('admin.service.booking.update', ['id' => 'BOOKING_ID']) }}';
                $('#bookingEditForm').attr('action', updateUrl.replace('BOOKING_ID', button.data('id')));
                $('#edit_user_id').val(button.data('user_id')).trigger('change');
                $('#edit_booking_type').val(button.data('booking_type'));
                $('#edit_title').val(button.data('title'));
                $('#edit_reference_no').val(button.data('reference_no'));
                $('#edit_booking_date').val(button.data('booking_date'));
                $('#edit_service_date').val(button.data('service_date'));
                $('#edit_service_end_date').val(button.data('service_end_date'));
                $('#edit_amount').val(button.data('amount'));
                $('#edit_status').val(button.data('status'));
                $('#edit_notes').val(button.data('notes'));
            });
        })(jQuery);
    </script>
@endpush
