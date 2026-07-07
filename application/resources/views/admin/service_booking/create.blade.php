@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Add Booking')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.service.booking.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Client')</label>
                                <select name="user_id" class="form-control select2-basic" required>
                                    <option value="" data-balance="0">@lang('Select Client')</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-balance="{{ $user->balance }}">{{ $user->username }} - {{ $user->firstname }} {{ $user->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Booking Type')</label>
                                <select name="booking_type" class="form-control" required>
                                    <option value="tour">@lang('Trip / Tour')</option>
                                    <option value="flight">@lang('Flight')</option>
                                    <option value="stay">@lang('Stay / Accommodation')</option>
                                    <option value="coupon">@lang('Discount Coupon')</option>
                                    <option value="restaurant">@lang('Restaurant')</option>
                                    <option value="cafe">@lang('Cafe')</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">@lang('Title / Service Name')</label>
                                <input type="text" name="title" class="form-control" placeholder="@lang('e.g. Dubai Flight, Sea View Hotel, Dinner Reservation')" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Reference No.')</label>
                                <input type="text" name="reference_no" class="form-control" placeholder="@lang('Optional')">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Booking Date')</label>
                                <input type="date" name="booking_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Service Date')</label>
                                <input type="date" name="service_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('End Date')</label>
                                <input type="date" name="service_end_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Amount')</label>
                                <input type="number" step="any" min="0" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control" required>
                                    <option value="0">@lang('Pending')</option>
                                    <option value="1">@lang('Confirmed')</option>
                                    <option value="2">@lang('Completed')</option>
                                    <option value="3">@lang('Canceled')</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="walletDeductionDiv" style="display:none;">
                                <label class="form-label" for="deductWallet">
                                    <input type="checkbox" name="deduct_wallet" id="deductWallet" value="1" class="me-1">
                                    @lang('Deduct from Wallet') (<span id="currentWalletBalance" class="fw-bold"></span>)
                                </label>
                                <div id="deductAmountDiv" style="display:none;">
                                    <input type="number" step="any" min="0" name="deduct_amount" class="form-control" placeholder="@lang('Amount to deduct')">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Notes')</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="@lang('Optional details about the booking')"></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn--primary">@lang('Save Booking')</button>
                            </div>
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
            'use strict';
            $('.select2-basic').select2();

            const userSelect = $('select[name="user_id"]');
            const statusSelect = $('select[name="status"]');
            const walletDiv = $('#walletDeductionDiv');
            const balanceSpan = $('#currentWalletBalance');
            const deductWalletCb = $('#deductWallet');
            const deductAmountDiv = $('#deductAmountDiv');

            deductWalletCb.on('change', function() {
                if ($(this).is(':checked')) {
                    deductAmountDiv.slideDown();
                } else {
                    deductAmountDiv.slideUp();
                }
            });

            function toggleWalletDeduction() {
                const selectedUser = userSelect.find('option:selected');
                const status = statusSelect.val();

                if (status == 1 && selectedUser.val()) {
                    const balance = parseFloat(selectedUser.data('balance') || 0).toFixed(2);
                    balanceSpan.text(balance);
                    walletDiv.slideDown();
                } else {
                    walletDiv.slideUp();
                    deductWalletCb.prop('checked', false).trigger('change');
                }
            }

            userSelect.on('change', toggleWalletDeduction);
            statusSelect.on('change', toggleWalletDeduction);
        })(jQuery);
    </script>
@endpush
