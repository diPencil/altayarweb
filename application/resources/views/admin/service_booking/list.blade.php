@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Client')</th>
                                    <th>@lang('Service')</th>
                                    <th>@lang('Date Submitted')</th>
                                    <th>@lang('Requested Date')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $booking->user?->username }}</span><br>
                                            <small>{{ $booking->user?->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge--dark">{{ ucfirst($booking->booking_type) }}</span><br>
                                            {{ __($booking->title) }}
                                        </td>
                                        <td>{{ showDateTime($booking->created_at, 'd M Y') }}</td>
                                        <td>
                                            {{ showDateTime($booking->service_date, 'd M Y') }}
                                            @if($booking->service_end_date)
                                                - {{ showDateTime($booking->service_end_date, 'd M Y') }}
                                            @endif
                                            @if($booking->service_time)
                                                <br><small class="text-muted">{{ $booking->service_time }}</small>
                                            @endif
                                        </td>
                                        <td>{!! $booking->statusBadge() !!}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn--primary view-details"
                                                data-id="{{ $booking->id }}"
                                                data-notes="{{ $booking->notes }}"
                                                data-status="{{ $booking->status }}">
                                                <i class="las la-eye"></i> @lang('Details')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No requests found') }}</td>
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

    {{-- Details Modal --}}
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Request Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" id="statusUpdateForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('User Requirements')</label>
                            <div id="request_notes" class="p-3 bg-light rounded border" style="white-space: pre-wrap;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">@lang('Update Status')</label>
                            <select name="status" id="request_status" class="form-control" required>
                                <option value="0">@lang('Pending / Under Review')</option>
                                <option value="1">@lang('Accept / Confirmed')</option>
                                <option value="3">@lang('Reject / Canceled')</option>
                                <option value="2">@lang('Completed')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100">@lang('Save Changes')</button>
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
            $(document).on('click', '.view-details', function () {
                const btn = $(this);
                const id = btn.data('id');
                const notes = btn.data('notes');
                const status = btn.data('status');
                
                $('#request_notes').text(notes);
                $('#request_status').val(status);
                
                const url = "{{ route('admin.service.booking.status.update', ':id') }}".replace(':id', id);
                $('#statusUpdateForm').attr('action', url);
                
                $('#detailsModal').modal('show');
            });
        })(jQuery);
    </script>
@endpush
