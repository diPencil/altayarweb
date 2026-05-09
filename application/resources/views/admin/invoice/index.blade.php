@extends('admin.layouts.app')
@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
        <form action="" method="GET" class="d-flex flex-wrap gap-2">
            <div class="input-group w-auto">
                <input type="text" name="search" class="form-control" placeholder="@lang('Invoice # / Username')" value="{{ request()->search }}">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
        <a href="{{ route('admin.invoice.create') }}" class="btn btn--primary"><i class="fas fa-plus"></i> @lang('Add New')</a>
    </div>
@endpush
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Invoice Number')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Booking Type')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Issue Date')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td>
                                    <div class="user--info">
                                        <span class="fw-bold d-block">{{ $invoice->user->fullname }}</span>
                                        <small> <a href="{{ route('admin.users.detail', $invoice->user_id) }}"><span>@</span>{{ $invoice->user->username }}</a> </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $type = str_replace(['App\Models\\', 'Booking'], ['', ' Booking'], $invoice->booking_type);
                                        if(!$type) { $type = 'Manual / Custom'; }
                                    @endphp
                                    <span class="badge badge--primary" style="white-space: normal; text-align: left; display: inline-block; padding: 5px 10px; min-width: 100px;">{{ __($type) }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $general->cur_sym }}{{ showAmount($invoice->total_amount) }}</span>
                                </td>
                                <td>
                                    {{ showDateTime($invoice->issue_date, 'Y-m-d') }}
                                </td>
                                <td>
                                    @if($invoice->status == 1)
                                        <span class="badge badge--success">@lang('Paid')</span>
                                    @elseif($invoice->status == 2)
                                        <span class="badge badge--danger">@lang('Cancelled')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="button--group">
                                        <a href="{{ route('invoice.show', $invoice->invoice_number) }}" class="btn btn-sm btn--outline-primary" target="_blank" title="@lang('View')">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.invoice.edit', $invoice->id) }}" class="btn btn-sm btn--outline-info" title="@lang('Edit')">
                                            <i class="las la-pen"></i>
                                        </a>
                                        <button class="btn btn-sm btn--outline-warning editStatusBtn" 
                                            data-id="{{ $invoice->id }}" 
                                            data-status="{{ $invoice->status }}"
                                            data-paid="{{ $invoice->paid_amount }}"
                                            data-amount="{{ $invoice->total_amount }}"
                                            title="@lang('Update Status')">
                                            <i class="las la-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($invoices->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($invoices) }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div id="statusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Invoice Status')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.invoice.status.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select name="status" class="form-control" required>
                                <option value="0">@lang('Pending')</option>
                                <option value="1">@lang('Paid')</option>
                                <option value="2">@lang('Cancelled')</option>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label>@lang('Paid Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input type="number" step="any" name="paid_amount" class="form-control">
                            </div>
                            <small class="text-muted">@lang('Total Invoice Amount'): {{ $general->cur_sym }}<span id="total_amt"></span></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        $('.editStatusBtn').on('click', function () {
            var modal = $('#statusModal');
            var data = $(this).data();
            modal.find('input[name=id]').val(data.id);
            modal.find('select[name=status]').val(data.status);
            modal.find('input[name=paid_amount]').val(data.paid);
            modal.find('#total_amt').text(data.amount);
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
