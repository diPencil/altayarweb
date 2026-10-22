@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive admin-table-responsive">
                    <table class="table table--light style--two custom-feedback-table">
                        <thead>
                            <tr>
                                <th class="col-check">
                                    <input type="checkbox" class="form-check-input selectAll" title="@lang('Select All')">
                                </th>
                                <th class="col-sl">@lang('SL')</th>
                                <th class="col-name">@lang('Name')</th>
                                <th class="col-profession">@lang('Profession')</th>
                                <th class="col-city">@lang('City')</th>
                                <th class="col-comment">@lang('Comment')</th>
                                <th class="col-rating">@lang('Rating')</th>
                                <th class="col-status">@lang('Status')</th>
                                <th class="col-action">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($feedback as $item)
                                <tr>
                                    <td class="col-check text-center align-middle">
                                        <input type="checkbox" class="form-check-input feedbackCheckbox" value="{{ $item->id }}">
                                    </td>
                                    <td class="col-sl text-center align-middle">{{ $feedback->firstItem() + $loop->index }}</td>
                                    <td class="col-name align-middle" dir="auto" title="{{ $item->name }}">{{ $item->name }}</td>
                                    <td class="col-profession align-middle" dir="auto" title="{{ $item->profession }}">{{ $item->profession }}</td>
                                    <td class="col-city align-middle" dir="auto" title="{{ $item->city }}">{{ $item->city }}</td>
                                    <td class="col-comment align-middle">
                                        <div class="comment-text-box" dir="auto" title="{{ $item->comment }}">
                                            {{ $item->comment }}
                                        </div>
                                        @if(mb_strlen($item->comment) > 60)
                                            <a href="javascript:void(0)" class="viewFullCommentBtn text--primary small d-inline-block mt-1" 
                                               data-name="{{ $item->name }}" 
                                               data-profession="{{ $item->profession }}"
                                               data-rating="{{ $item->rating }}"
                                               data-comment="{{ $item->comment }}">
                                                <i class="las la-eye"></i> @lang('Read more')
                                            </a>
                                        @endif
                                    </td>
                                    <td class="col-rating align-middle text-center">
                                        <span class="badge badge--dark px-2 py-1">{{ $item->rating }} / 5 ★</span>
                                    </td>
                                    <td class="col-status align-middle text-center">
                                        <span class="badge {{ $item->is_approved ? 'badge--success' : 'badge--warning' }}">
                                            {{ $item->is_approved ? __('Approved') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td class="col-action align-middle text-end">
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

{{-- View Full Comment Modal --}}
<div id="viewFullCommentModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-comment-alt text--primary me-1"></i> <span id="vfcAuthor"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span id="vfcProfession" class="badge badge--dark"></span>
                    <span id="vfcRating" class="badge badge--warning"></span>
                </div>
                <div class="p-3 bg-light rounded" style="max-height: 350px; overflow-y: auto;">
                    <p id="vfcComment" class="mb-0 text-dark" style="white-space: pre-wrap; word-break: break-word; line-height: 1.6; font-size: 14px;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
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

    /* Fix table layout and column spill */
    .admin-table-responsive table.custom-feedback-table {
        table-layout: fixed !important;
        width: 100% !important;
        min-width: 1050px !important;
    }

    .custom-feedback-table th, 
    .custom-feedback-table td {
        vertical-align: middle !important;
    }

    /* Explicit column widths */
    .custom-feedback-table .col-check { width: 45px !important; text-align: center; }
    .custom-feedback-table .col-sl { width: 55px !important; text-align: center; }
    .custom-feedback-table .col-name { width: 130px !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap !important; }
    .custom-feedback-table .col-profession { width: 120px !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap !important; }
    .custom-feedback-table .col-city { width: 100px !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap !important; }
    
    /* Comment column: strictly wrap and clamp, never overflow */
    .custom-feedback-table .col-comment {
        width: 320px !important;
        max-width: 320px !important;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
    }

    .comment-text-box {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.45;
        max-height: 2.9em;
        font-size: 13px;
        color: #555;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .custom-feedback-table .col-rating { 
        width: 95px !important; 
        white-space: nowrap !important; 
        text-align: center !important; 
    }
    
    .custom-feedback-table .col-status { 
        width: 105px !important; 
        white-space: nowrap !important; 
        text-align: center !important; 
    }
    
    .custom-feedback-table .col-action { 
        width: 130px !important; 
        white-space: nowrap !important; 
        text-align: right !important; 
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

        // View full comment modal handler
        $('.viewFullCommentBtn').on('click', function () {
            const name = $(this).data('name');
            const profession = $(this).data('profession') || '';
            const rating = $(this).data('rating');
            const comment = $(this).data('comment');

            $('#vfcAuthor').text(name);
            if (profession) {
                $('#vfcProfession').text(profession).show();
            } else {
                $('#vfcProfession').hide();
            }
            $('#vfcRating').text(rating + ' / 5 ★');
            $('#vfcComment').text(comment);

            $('#viewFullCommentModal').modal('show');
        });

    })(jQuery);
</script>
@endpush
