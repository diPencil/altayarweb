@extends('admin.layouts.app')

@section('panel')
    <style>
        .chat-list-status--open {
            background: #16a34a;
            color: #fff !important;
        }

        .chat-list-status--closed {
            background: #ef4444;
            color: #fff !important;
        }
    </style>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input selectAll">
                                </th>
                                <th>@lang('Customer')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Unread')</th>
                                <th>@lang('Last Message')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input chatCheckbox" value="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold text--muted">{{ $item->name ?? __('Guest') }}</div>
                                        <div class="text-muted small">{{ $item->email }}</div>
                                    </td>
                                    <td>
                                        @if($item->status === 'closed')
                                            <span class="badge chat-list-status--closed">@lang('Closed')</span>
                                        @else
                                            <span class="badge chat-list-status--open">@lang('Open')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->unread_admin_count > 0)
                                            <span class="badge badge--primary">{{ $item->unread_admin_count }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ strLimit(optional($item->latestMessage)->message ?? '-', 35) }}
                                    </td>
                                    <td>
                                        {{ $item->last_message_at ? showDateTime($item->last_message_at, 'd M Y, h:i A') : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('admin.chat-assistant.view', $item->id) }}" class="btn btn-sm btn--primary" title="@lang('View')" aria-label="@lang('View')">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn--danger confirmationBtn"
                                                title="@lang('Delete')"
                                                aria-label="@lang('Delete')"
                                                data-question="@lang('Delete this conversation?')"
                                                data-action="{{ route('admin.chat-assistant.delete', $item->id) }}">
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
                    </table>
                </div>
            </div>
            @if ($items->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($items) }}
                </div>
            @endif
        </div>
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
            <form action="{{ route('admin.chat-assistant.bulk.delete') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-danger">@lang('Are you sure you want to permanently delete the selected chat conversations?')</p>
                    <p>@lang('This action cannot be undone. Related messages will also be deleted.')</p>
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
        const $chatCheckboxes = $('.chatCheckbox');
        const $bulkDeleteBtn = $('.bulkDeleteBtn');
        const $selectedCount = $('.selected-count');
        const $bulkDeleteModal = $('#bulkDeleteModal');
        const $deleteConfirmInput = $('.deleteConfirmInput');
        const $confirmDeleteBtn = $('.confirmDeleteBtn');

        function updateBulkButton() {
            const count = $('.chatCheckbox:checked').length;
            $selectedCount.text(count);
            if (count > 0) {
                $bulkDeleteBtn.removeClass('d-none');
            } else {
                $bulkDeleteBtn.addClass('d-none');
                $selectAll.prop('checked', false);
            }
        }

        $selectAll.on('change', function () {
            $chatCheckboxes.prop('checked', $(this).prop('checked'));
            updateBulkButton();
        });

        $(document).on('change', '.chatCheckbox', function () {
            if ($('.chatCheckbox:checked').length == $chatCheckboxes.length) {
                $selectAll.prop('checked', true);
            } else {
                $selectAll.prop('checked', false);
            }
            updateBulkButton();
        });

        $bulkDeleteBtn.on('click', function () {
            const selectedIds = $('.chatCheckbox:checked').map(function () {
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
