@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="{{ route('admin.invoice.update', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Select User')</label>
                                    <select name="user_id" id="user_id" class="form-control select2-basic" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected($invoice->user_id == $user->id)>{{ $user->fullname }} ({{ $user->username }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Quick Add from Bookings')</label>
                                    <select id="booking_select" class="form-control select2-basic">
                                        <option value="">@lang('Select a Booking to Add')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('Issue Date')</label>
                                    <input type="date" name="issue_date" class="form-control" value="{{ $invoice->issue_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('Paid Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                        <input type="number" step="any" name="paid_amount" class="form-control" value="{{ getAmount($invoice->paid_amount) }}" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border--primary mt-3">
                            <div class="card-header bg--primary d-flex justify-content-between align-items-center">
                                <h5 class="text-white">@lang('Invoice Items')</h5>
                                <button type="button" class="btn btn-sm btn-outline-light add-item"><i class="la la-plus"></i> @lang('Add Service/Item')</button>
                            </div>
                            <div class="card-body">
                                <div class="items-container">
                                    @foreach($invoice->items as $index => $item)
                                    <div class="item-row border-bottom pb-3 mb-3">
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <label>@lang('Item Name / Service')</label>
                                                <input type="text" name="items[{{ $index }}][name]" class="form-control" value="{{ $item->item_name }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Price')</label>
                                                <input type="number" step="any" name="items[{{ $index }}][unit_price]" class="form-control price-calc" value="{{ getAmount($item->unit_price) }}" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label>@lang('Qty')</label>
                                                <input type="number" name="items[{{ $index }}][qty]" class="form-control price-calc" value="{{ $item->qty }}" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label>@lang('Guests')</label>
                                                <input type="number" name="items[{{ $index }}][guests]" class="form-control" value="{{ $item->guests }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Check-In')</label>
                                                <input type="date" name="items[{{ $index }}][check_in]" class="form-control" value="{{ $item->check_in }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Check-Out')</label>
                                                <div class="input-group">
                                                    <input type="date" name="items[{{ $index }}][check_out]" class="form-control" value="{{ $item->check_out }}">
                                                    <button type="button" class="btn btn--danger remove-item"><i class="la la-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <hr>
                                <div class="row mt-3 justify-content-end">
                                    <div class="col-md-4 text-end">
                                        <h5>@lang('Total Amount'): {{ $general->cur_sym }}<span id="grand-total">{{ getAmount($invoice->total_amount) }}</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Notes')</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ $invoice->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Invoice')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        let itemIndex = {{ $invoice->items->count() }};
        let bookingsData = [];

        function loadBookings(userId) {
            let bookingSelect = $('#booking_select');
            if (userId) {
                bookingSelect.prop('disabled', false).html('<option value="">@lang("Loading Bookings...")</option>');
                $.get("{{ route('admin.invoice.getBookings') }}", { user_id: userId }, function(data) {
                    bookingsData = data.bookings;
                    let html = '<option value="">@lang("Select a Booking to Add")</option>';
                    bookingsData.forEach((booking, index) => {
                        html += `<option value="${index}">${booking.reference} - ${booking.title} (${booking.price} {{ $general->cur_text }})</option>`;
                    });
                    bookingSelect.html(html);
                });
            } else {
                bookingSelect.prop('disabled', true).html('<option value="">@lang("Select User First")</option>');
            }
        }

        // Initialize bookings for current user
        loadBookings($('#user_id').val());

        $('#user_id').on('change', function() {
            loadBookings($(this).val());
        });

        $('#booking_select').on('change', function() {
            let index = $(this).val();
            if (index !== "") {
                addItemFromBooking(bookingsData[index]);
                $(this).val("").trigger('change.select2');
            }
        });

        function addItemFromBooking(booking) {
            addNewRow(booking);
            calculateTotal();
        }

        function addNewRow(booking = null) {
            let html = `
                <div class="item-row border-bottom pb-3 mb-3">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <label>@lang('Item Name / Service')</label>
                            <input type="text" name="items[${itemIndex}][name]" class="form-control" value="${booking ? booking.title + ' (' + booking.reference + ')' : ''}" required>
                        </div>
                        <div class="col-md-2">
                            <label>@lang('Price')</label>
                            <input type="number" step="any" name="items[${itemIndex}][unit_price]" class="form-control price-calc" value="${booking ? booking.price : '0.00'}" required>
                        </div>
                        <div class="col-md-1">
                            <label>@lang('Qty')</label>
                            <input type="number" name="items[${itemIndex}][qty]" class="form-control price-calc" value="1" required>
                        </div>
                        <div class="col-md-1">
                            <label>@lang('Guests')</label>
                            <input type="number" name="items[${itemIndex}][guests]" class="form-control" value="${booking ? (booking.guests || 1) : 1}">
                        </div>
                        <div class="col-md-2">
                            <label>@lang('Check-In')</label>
                            <input type="date" name="items[${itemIndex}][check_in]" class="form-control" value="${(booking && booking.check_in) ? booking.check_in : ''}">
                        </div>
                        <div class="col-md-2">
                            <label>@lang('Check-Out')</label>
                            <div class="input-group">
                                <input type="date" name="items[${itemIndex}][check_out]" class="form-control" value="${(booking && booking.check_out) ? booking.check_out : ''}">
                                <button type="button" class="btn btn--danger remove-item"><i class="la la-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('.items-container').append(html);
            itemIndex++;
        }

        $('.add-item').on('click', function () {
            addNewRow();
            calculateTotal();
        });

        $(document).on('click', '.remove-item', function () {
            $(this).closest('.item-row').remove();
            calculateTotal();
        });

        $(document).on('input', '.price-calc', function () {
            calculateTotal();
        });

        function calculateTotal() {
            let grandTotal = 0;
            $('.item-row').each(function () {
                let price = parseFloat($(this).find('input[name*="[unit_price]"]').val()) || 0;
                let qty = parseInt($(this).find('input[name*="[qty]"]').val()) || 0;
                grandTotal += price * qty;
            });
            $('#grand-total').text(grandTotal.toFixed(2));
        }
    })(jQuery);
</script>
@endpush
