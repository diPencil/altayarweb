@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-md-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th>@lang('Extension')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extensions as $extension)
                            <tr>
                                <td>
                                    <div class="user">
                                        <div class="thumb"><img
                                                src="{{ getImage(getFilePath('extensions') .'/'. $extension->image,getFileSize('extensions')) }}"
                                                alt="{{ __($extension->name) }}" class="plugin_bg"></div>
                                        <span class="name">{{ __($extension->name) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($extension->status == 1)
                                    <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                    <span class="badge badge--warning">@lang('Disabled')</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="button--group">
                                        <button title="@lang('Edit')" type="button"
                                            class="btn btn-sm btn--primary ms-1 mb-2 editBtn"
                                            data-name="{{ __($extension->name) }}"
                                            data-shortcode="{{ e(json_encode($extension->shortcode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}"
                                            data-action="{{ route('admin.extensions.update', $extension->id) }}">
                                            <i class="la la-pen"></i>
                                        </button>
                                        @if($extension->status == 0)
                                        <button title="@lang('Enable')" type="button"
                                            class="btn btn-sm btn--success ms-1 mb-2 confirmationBtn"
                                            data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                            data-question="@lang('Are you sure to enable this extension?')">
                                            <i class="la la-check-circle"></i>
                                        </button>
                                        @else
                                        <button title="@lang('Disable')" type="button"
                                            class="btn btn-sm btn--danger mb-2 confirmationBtn"
                                            data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                            data-question="@lang('Are you sure to disable this extension?')">
                                            <i class="la la-ban"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- EDIT METHOD MODAL --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Update Extension'): <span class="extension-name"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('Script')</label>
                        <div class="col-md-12">
                            <textarea name="script" class="form-control" required rows="8"
                                placeholder="@lang('Paste your script with proper key')">{{ old('script') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global" id="editBtn">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
<x-confirmation-modal></x-confirmation-modal>
@endsection


@push('breadcrumb-plugins')

<div class="d-flex flex-wrap justify-content-end">
    <div class="d-inline">
        <div class="input-group justify-content-end">
            <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
            <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
        </div>
    </div>
</div>
@endpush


@push('script')
<script>
    (function ($) {
        "use strict";

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showModal(modal) {
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal[0]).show();
                return;
            }

            if (typeof modal.modal === 'function') {
                modal.modal('show');
                return;
            }

            modal.addClass('show').css('display', 'block');
        }

        $(document).on('click', '.editBtn', function () {
            var modal = $('#editModal');
            var shortcode = $(this).attr('data-shortcode');
            var extensionName = $(this).data('name') || '';

            try {
                shortcode = typeof shortcode === 'string' ? JSON.parse(shortcode) : shortcode;
            } catch (error) {
                shortcode = {};
            }

            modal.find('.extension-name').text(extensionName);
            modal.find('form').attr('action', $(this).data('action'));

            var html = '';
            if (!shortcode || typeof shortcode !== 'object') {
                shortcode = {};
            }

            $.each(shortcode, function (key, item) {
                var fieldType = item.type || ((item.value || '').length > 120 ? 'textarea' : 'input');
                if (fieldType === 'textarea') {
                    html += `<div class="form-group">
                        <label class="col-md-12 control-label fw-bold">${escapeHtml(item.title)}</label>
                        <div class="col-md-12">
                            <textarea name="${key}" class="form-control" rows="6" placeholder="--" required>${escapeHtml(item.value)}</textarea>
                        </div>
                    </div>`;
                    return;
                }

                html += `<div class="form-group">
                        <label class="col-md-12 control-label fw-bold">${escapeHtml(item.title)}</label>
                        <div class="col-md-12">
                            <input name="${key}" class="form-control" placeholder="--" value="${escapeHtml(item.value)}" required>
                        </div>
                    </div>`;
            })

            if (!html) {
                html = `<div class="alert alert-warning mb-0">
                    @lang('Unable to load this extension settings. Please refresh the page and try again.')
                </div>`;
            }

            if (!Object.keys(shortcode).length && extensionName.toLowerCase() === 'feliz ai') {
                html = `
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('Enable Assistant')</label>
                        <div class="col-md-12">
                            <input name="enabled" class="form-control" placeholder="--" value="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('AI Brand')</label>
                        <div class="col-md-12">
                            <input name="provider" class="form-control" placeholder="--" value="feliz_ai" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('API Key')</label>
                        <div class="col-md-12">
                            <textarea name="api_key" class="form-control" rows="3" placeholder="--" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('Model')</label>
                        <div class="col-md-12">
                            <input name="model" class="form-control" placeholder="--" value="feliz-ai" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('System Prompt')</label>
                        <div class="col-md-12">
                            <textarea name="system_prompt" class="form-control" rows="8" placeholder="--" required>You're an AI travel sales and support assistant. Be short, helpful, and conversion-focused.</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-12 control-label fw-bold">@lang('Static Knowledge')</label>
                        <div class="col-md-12">
                            <textarea name="static_knowledge" class="form-control" rows="4" placeholder="--" required>Travel offers, memberships, cashback, booking help, and human handover.</textarea>
                        </div>
                    </div>
                `;
            }

            modal.find('.modal-body').html(html);

                showModal(modal);
        });

        $(document).on('click', '.helpBtn', function () {
            var modal = $('#helpModal');
            var path = "{{ asset(getFilePath('extensions')) }}";
            modal.find('.modal-body').html(`<div class="mb-2">${$(this).data('description')}</div>`);
            if ($(this).data('support') != 'na') {
                modal.find('.modal-body').append(`<img src="${path}/${$(this).data('support')}">`);
            }
            showModal(modal);
        });

    })(jQuery);

</script>
@endpush
