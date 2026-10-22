@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive admin-table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" class="form-check-input selectAll" title="@lang('Select All')">
                                </th>
                                <th style="width: 50px;">@lang('SL')</th>
                                <th style="min-width: 120px;">@lang('Name')</th>
                                <th style="min-width: 110px;">@lang('Profession')</th>
                                <th style="min-width: 90px;">@lang('City')</th>
                                <th style="min-width: 260px; max-width: 420px;">@lang('Comment')</th>
                                <th style="min-width: 90px; white-space: nowrap;">@lang('Rating')</th>
                                <th style="min-width: 100px; white-space: nowrap;">@lang('Status')</th>
                                <th style="min-width: 120px; white-space: nowrap; text-align: end;">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedback as $item)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input feedbackCheckbox" value="{{ $item->id }}">
                                    </td>
                                    <td class="align-middle">{{ $feedback->firstItem() + $loop->index }}</td>
                                    <td class="align-middle" dir="auto">{{ $item->name }}</td>
                                    <td class="align-middle" dir="auto">{{ $item->profession }}</td>
                                    <td class="align-middle" dir="auto">{{ $item->city }}</td>
                                    <td class="align-middle" style="min-width: 260px; max-width: 420px; white-space: normal; overflow-wrap: anywhere;">
                                        <div dir="auto">{{ $item->comment }}</div>
                                    </td>
                                    <td class="align-middle" style="white-space: nowrap;">{{ $item->rating }} / 5</td>
                                    <td class="align-middle" style="white-space: nowrap;">
                                        <span class="badge {{ $item->is_approved ? 'badge--success' : 'badge--warning' }}">
                                            {{ $item->is_approved ? __('Approved') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td class="align-middle" style="white-space: nowrap;">
                                        <div class="d-flex gap-2 justify-content-end align-items-center">
                                            @unless($item->is_approved)
                                                <form method="POST" action="{{ route('admin.client-feedback.approve', $item->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--success" title="@lang('Approve')" aria-label="@lang('Approve')">
                                                        <i class="las la-check"></i>
                                                    </button>
                                                </form>
                                            @endunless
                                            <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.client-feedback.edit', $item->id) }}" title="@lang('Edit')" aria-label="@lang('Edit')">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.client-feedback.delete', $item->id) }}" onsubmit="return confirm(@js(__('Delete this feedback permanently?')))" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger" title="@lang('Delete')" aria-label="@lang('Delete')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-muted text-center py-4">@lang('No feedback yet')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($feedback->hasPages())
                <div class="card-footer py-4">{{ paginateLinks($feedback) }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Delete Confirmation Modal --}}
<div id="bulkDeleteModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="las la-exclamation-triangle me-1"></i> @lang('Delete Selected Feedback')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.client-feedback.bulk-delete') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-2">@lang('Are you sure you want to permanently delete the selected feedback item(s)?')</p>
                    <p class="text-muted small">@lang('Selected count:') <strong class="selected-count text--danger">0</strong></p>
                    <div class="selected-ids-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--danger"><i class="las la-trash me-1"></i> @lang('Delete Permanently')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Approve Confirmation Modal --}}
<div id="bulkApproveModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success"><i class="las la-check-circle me-1"></i> @lang('Approve Selected Feedback')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.client-feedback.bulk-approve') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-2">@lang('Are you sure you want to approve the selected feedback item(s) for public display?')</p>
                    <p class="text-muted small">@lang('Selected count:') <strong class="selected-count text--success">0</strong></p>
                    <div class="selected-ids-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--success"><i class="las la-check me-1"></i> @lang('Approve')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
        <button type="button" class="btn btn--success bulkApproveBtn d-none">
            <i class="las la-check-circle"></i> @lang('Approve Selected') (<span class="selected-count">0</span>)
        </button>
        <button type="button" class="btn btn--danger bulkDeleteBtn d-none">
            <i class="las la-trash"></i> @lang('Delete Selected') (<span class="selected-count">0</span>)
        </button>
    </div>
@endpush

@push('style')
<style>
    .feedbackCheckbox, .selectAll {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";

        const $selectAll = $('.selectAll');
        const $feedbackCheckboxes = $('.feedbackCheckbox');
        const $bulkDeleteBtn = $('.bulkDeleteBtn');
        const $bulkApproveBtn = $('.bulkApproveBtn');
        const $selectedCount = $('.selected-count');
        const $bulkDeleteModal = $('#bulkDeleteModal');
        const $bulkApproveModal = $('#bulkApproveModal');

        function updateBulkButtons() {
            const count = $('.feedbackCheckbox:checked').length;
            $selectedCount.text(count);
            if (count > 0) {
                $bulkDeleteBtn.removeClass('d-none');
                $bulkApproveBtn.removeClass('d-none');
            } else {
                $bulkDeleteBtn.addClass('d-none');
                $bulkApproveBtn.addClass('d-none');
                $selectAll.prop('checked', false);
            }
        }

        $selectAll.on('change', function () {
            $feedbackCheckboxes.prop('checked', $(this).prop('checked'));
            updateBulkButtons();
        });

        $(document).on('change', '.feedbackCheckbox', function () {
            if ($('.feedbackCheckbox:checked').length === $feedbackCheckboxes.length && $feedbackCheckboxes.length > 0) {
                $selectAll.prop('checked', true);
            } else {
                $selectAll.prop('checked', false);
            }
            updateBulkButtons();
        });

        $bulkDeleteBtn.on('click', function () {
            const selectedIds = $('.feedbackCheckbox:checked').map(function () {
                return $(this).val();
            }).get();

            let html = '';
            selectedIds.forEach(id => {
                html += `<input type="hidden" name="ids[]" value="${id}">`;
            });

            $bulkDeleteModal.find('.selected-ids-container').html(html);
            $bulkDeleteModal.modal('show');
        });

        $bulkApproveBtn.on('click', function () {
            const selectedIds = $('.feedbackCheckbox:checked').map(function () {
                return $(this).val();
            }).get();

            let html = '';
            selectedIds.forEach(id => {
                html += `<input type="hidden" name="ids[]" value="${id}">`;
            });

            $bulkApproveModal.find('.selected-ids-container').html(html);
            $bulkApproveModal.modal('show');
        });

    })(jQuery);
</script>
@endpush
