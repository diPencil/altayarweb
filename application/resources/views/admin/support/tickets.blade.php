@extends('admin.layouts.app')

@section('panel')
@include('admin.components.tabs.ticket')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input selectAll">
                                </th>
                                <th>@lang('Subject')</th>
                                <th>@lang('Opened By')</th>
                                <th>@lang('Assigned To')</th>
                                <th>@lang('Priority')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input ticketCheckbox" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('admin.ticket.view', $item->id) }}" class="fw-bold text--muted">
                                        @lang('Ticket')#{{ $item->ticket }} - {{ strLimit($item->subject,30) }} </a>
                                </td>

                                <td>
                                    @if($item->user_id)
                                    <a href="{{ route('admin.users.detail', $item->user_id) }}">
                                        {{$item->user->fullname}}</a>
                                    @else
                                    <p class="fw-bold"> {{$item->name}}</p>
                                    @endif
                                </td>
                                <td>
                                    @if($item->agent_id && $item->employee)
                                        <a href="{{ route('admin.employees.detail', $item->agent_id) }}">{{ $item->employee->fullname }}</a>
                                    @else
                                        <span class="text-muted">@lang('Unassigned')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->priority == 1)
                                    <span class="badge badge--dark">@lang('Low')</span>
                                    @elseif($item->priority == 2)
                                    <span class="badge  badge--warning">@lang('Medium')</span>
                                    @elseif($item->priority == 3)
                                    <span class="badge badge--danger">@lang('High')</span>
                                    @endif
                                </td>
                                <td>
                                    @php echo $item->statusBadge; @endphp
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a title="@lang('Details')" href="{{ route('admin.ticket.view', $item->id) }}"
                                            class="btn btn-sm btn--primary">
                                            <i class="las la-eye text--shadow"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn--danger confirmationBtn"
                                            title="@lang('Delete')"
                                            aria-label="@lang('Delete')"
                                            data-question="@lang('Delete this ticket?')"
                                            data-action="{{ route('admin.ticket.delete_ticket', $item->id) }}">
                                            <i class="las la-trash"></i>
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
            @if ($items->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($items) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn--danger bulkDeleteBtn d-none">
        <i class="la la-trash"></i> @lang('Delete Selected') (<span class="selected-count">0</span>)
    </button>
@endpush

<div id="bulkDeleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Bulk Delete Confirmation')</h5>
                <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.ticket.bulk_delete_tickets') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-danger">@lang('Are you sure you want to permanently delete the selected support tickets?')</p>
                    <p>@lang('This action cannot be undone. Related messages and attachments will also be deleted.')</p>
                    <div class="form-group mt-3">
                        <label>@lang('Please type "DELETE" to confirm:')</label>
                        <input type="text" class="form-control deleteConfirmInput" placeholder="DELETE" autocomplete="off">
                    </div>
                    <div class="selected-ids-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--danger confirmDeleteBtn" disabled>@lang('Yes, Delete All')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    (function ($) {
        "use strict";

        const $selectAll = $('.selectAll');
        const $ticketCheckboxes = $('.ticketCheckbox');
        const $bulkDeleteBtn = $('.bulkDeleteBtn');
        const $selectedCount = $('.selected-count');
        const $bulkDeleteModal = $('#bulkDeleteModal');
        const $deleteConfirmInput = $('.deleteConfirmInput');
        const $confirmDeleteBtn = $('.confirmDeleteBtn');

        function updateBulkButton() {
            const count = $('.ticketCheckbox:checked').length;
            $selectedCount.text(count);
            if (count > 0) {
                $bulkDeleteBtn.removeClass('d-none');
            } else {
                $bulkDeleteBtn.addClass('d-none');
                $selectAll.prop('checked', false);
            }
        }

        $selectAll.on('change', function () {
            $ticketCheckboxes.prop('checked', $(this).prop('checked'));
            updateBulkButton();
        });

        $(document).on('change', '.ticketCheckbox', function () {
            if ($('.ticketCheckbox:checked').length == $ticketCheckboxes.length) {
                $selectAll.prop('checked', true);
            } else {
                $selectAll.prop('checked', false);
            }
            updateBulkButton();
        });

        $bulkDeleteBtn.on('click', function () {
            const selectedIds = $('.ticketCheckbox:checked').map(function () {
                return $(this).val();
            }).get();

            let html = '';
            selectedIds.forEach(id => {
                html += `<input type="hidden" name="ids[]" value="${id}">`;
            });

            $('.selected-ids-container').html(html);
            $deleteConfirmInput.val('');
            $confirmDeleteBtn.prop('disabled', true);
            $bulkDeleteModal.modal('show');
        });

        $deleteConfirmInput.on('input', function () {
            if ($(this).val().toUpperCase() === 'DELETE') {
                $confirmDeleteBtn.prop('disabled', false);
            } else {
                $confirmDeleteBtn.prop('disabled', true);
            }
        });

    })(jQuery);
</script>
@endpush
@endsection
