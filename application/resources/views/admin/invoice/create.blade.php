@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form action="{{ route('admin.invoice.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Select User')</label>
                                    <select name="user_id" id="user_id" class="form-control select2-basic" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->fullname }} ({{ $user->username }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Quick Add from Bookings')</label>
                                    <select id="booking_select" class="form-control select2-basic" disabled>
                                        <option value="">@lang('Select User First')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('Issue Date')</label>
                                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>@lang('Paid Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                        <input type="number" step="any" name="paid_amount" class="form-control" placeholder="0.00">
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
                                    <!-- First Item Row -->
                                    <div class="item-row border-bottom pb-3 mb-3">
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <label>@lang('Item Name / Service')</label>
                                                <input type="text" name="items[0][name]" class="form-control" placeholder="e.g. Flight, Hotel Name" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Price')</label>
                                                <input type="number" step="any" name="items[0][unit_price]" class="form-control price-calc" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label>@lang('Qty')</label>
                                                <input type="number" name="items[0][qty]" class="form-control price-calc" value="1" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label>@lang('Guests')</label>
                                                <input type="number" name="items[0][guests]" class="form-control" value="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Check-In')</label>
                                                <input type="date" name="items[0][check_in]" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label>@lang('Check-Out')</label>
                                                <div class="input-group">
                                                    <input type="date" name="items[0][check_out]" class="form-control">
                                                    <button type="button" class="btn btn--danger remove-item"><i class="la la-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-3 justify-content-end">
                                    <div class="col-md-4 text-end">
                                        <h5>@lang('Total Amount'): {{ $general->cur_sym }}<span id="grand-total">0.00</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Notes')</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Create Invoice')</button>
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
        let itemIndex = 1;
        let bookingsData = [];

        $('#user_id').on('change', function() {
            let userId = $(this).val();
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
        });

        $('#booking_select').on('change', function() {
            let index = $(this).val();
            if (index !== "") {
                addItemFromBooking(bookingsData[index]);
                $(this).val("").trigger('change.select2');
            }
        });

        function addItemFromBooking(booking) {
            // If the first row is empty, fill it. Otherwise, add a new row.
            let firstRow = $('.item-row').first();
            let firstNameInput = firstRow.find('input[name*="[name]"]');
            
            if (firstNameInput.val() === "") {
                fillRow(firstRow, booking);
            } else {
                addNewRow(booking);
            }
            calculateTotal();
        }

        function fillRow(row, booking) {
            row.find('input[name*="[name]"]').val(booking.title + ' (' + booking.reference + ')');
            row.find('input[name*="[unit_price]"]').val(booking.price);
            row.find('input[name*="[qty]"]').val(1);
            row.find('input[name*="[guests]"]').val(booking.guests || 1);
            if (booking.check_in) row.find('input[name*="[check_in]"]').val(booking.check_in);
            if (booking.check_out) row.find('input[name*="[check_out]"]').val(booking.check_out);
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
