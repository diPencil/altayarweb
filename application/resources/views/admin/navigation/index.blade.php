@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Menu Item')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('URL')</th>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orderedItems as $row)
                                    @php($item = $row['item'])
                                    <tr>
                                        <td data-label="@lang('Menu Item')">
                                            <div class="d-flex align-items-center gap-2" style="padding-inline-start: {{ $row['depth'] * 22 }}px">
                                                @if ($row['depth'])
                                                    <i class="las la-level-up-alt text-muted" style="transform: rotate(90deg)"></i>
                                                @endif
                                                <div class="text-start">
                                                    <strong>{{ $item->name }}</strong>
                                                    @if ($item->name_ar)
                                                        <div class="small text-muted" dir="rtl">{{ $item->name_ar }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="@lang('Type')">
                                            <span class="badge {{ $item->kind === 'listing_types' ? 'badge--primary' : 'badge--dark' }}">
                                                {{ $item->kind_label }}
                                            </span>
                                        </td>
                                        <td data-label="@lang('URL')">
                                            @if ($item->kind === 'link')
                                                <span class="text-break">{{ $item->url }}</span>
                                                @if ($item->target_blank)
                                                    <i class="las la-external-link-alt ms-1" title="@lang('Opens in a new tab')"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">@lang('Generated dropdown')</span>
                                            @endif
                                        </td>
                                        <td data-label="@lang('Order')">{{ $item->sort_order }}</td>
                                        <td data-label="@lang('Status')">{!! $item->statusBadge() !!}</td>
                                        <td data-label="@lang('Action')">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button" class="btn btn-sm btn--primary edit-item"
                                                    data-item="{{ base64_encode(json_encode($item->only(['id', 'parent_id', 'name', 'name_ar', 'kind', 'url', 'sort_order', 'target_blank', 'status']))) }}"
                                                    title="@lang('Edit')">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn--warning confirmationBtn"
                                                    data-question="@lang('Are you sure to change this status?')"
                                                    data-action="{{ route('admin.navigation.status', $item) }}"
                                                    title="@lang('Change Status')">
                                                    <i class="las la-sync-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn--danger confirmationBtn"
                                                    data-question="@lang('Delete this menu item and all of its children?')"
                                                    data-action="{{ route('admin.navigation.delete', $item) }}"
                                                    title="@lang('Delete')">
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
            </div>
        </div>
    </div>

    <div class="modal fade" id="menuItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="menuItemForm" action="{{ route('admin.navigation.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" data-add-title="@lang('Add Menu Item')" data-edit-title="@lang('Edit Menu Item')">
                            @lang('Add Menu Item')
                        </h5>
                        <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal" aria-label="@lang('Close')">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" maxlength="120" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name (Arabic)')</label>
                                    <input type="text" class="form-control" name="name_ar" maxlength="120" dir="rtl">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Type')</label>
                                    <select class="form-select" name="kind" required>
                                        <option value="link">@lang('Link')</option>
                                        <option value="group">@lang('Dropdown')</option>
                                        <option value="listing_types">@lang('Dynamic Listing Types')</option>
                                    </select>
                                    <small class="form-text text-muted">@lang('Dynamic Listing Types automatically includes every active offer type.')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Parent Item')</label>
                                    <select class="form-select" name="parent_id">
                                        <option value="">@lang('Top Level')</option>
                                        @foreach ($parentOptions as $option)
                                            <option value="{{ $option['item']->id }}">
                                                {{ str_repeat('— ', $option['depth']) }}{{ $option['item']->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8 menu-url-field">
                                <div class="form-group">
                                    <label>@lang('URL')</label>
                                    <input type="text" class="form-control" name="url" maxlength="500" placeholder="/contact or https://example.com">
                                    <small class="form-text text-muted">@lang('Use a path beginning with / for this website, or a complete https:// URL.')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Order')</label>
                                    <input type="number" class="form-control" name="sort_order" min="0" max="9999" value="0" required>
                                </div>
                            </div>
                            <div class="col-md-6 menu-target-field">
                                <div class="form-group">
                                    <label class="d-flex align-items-center gap-2 mb-0">
                                        <input type="hidden" name="target_blank" value="0">
                                        <input type="checkbox" name="target_blank" value="1">
                                        <span>@lang('Open in a new tab')</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="d-flex align-items-center gap-2 mb-0">
                                        <input type="hidden" name="status" value="0">
                                        <input type="checkbox" name="status" value="1" checked>
                                        <span>@lang('Active')</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--primary add-item">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        'use strict';

        const modal = $('#menuItemModal');
        const form = $('#menuItemForm');
        const storeUrl = @json(route('admin.navigation.store'));
        const updateUrlTemplate = @json(route('admin.navigation.update', ['navigationItem' => '__ID__']));

        function updateTypeFields() {
            const isLink = form.find('[name=kind]').val() === 'link';
            form.find('.menu-url-field, .menu-target-field').toggle(isLink);
            form.find('[name=url]').prop('required', isLink);
        }

        $('.add-item').on('click', function () {
            form.attr('action', storeUrl)[0].reset();
            form.find('[name=parent_id] option').prop('disabled', false);
            form.find('[name=status]').filter('[type=checkbox]').prop('checked', true);
            form.find('.modal-title').text(form.find('.modal-title').data('add-title'));
            updateTypeFields();
            modal.modal('show');
        });

        $('.edit-item').on('click', function () {
            const item = JSON.parse(atob($(this).attr('data-item')));
            form.attr('action', updateUrlTemplate.replace('__ID__', item.id))[0].reset();
            form.find('[name=name]').val(item.name);
            form.find('[name=name_ar]').val(item.name_ar || '');
            form.find('[name=kind]').val(item.kind);
            form.find('[name=url]').val(item.url || '');
            form.find('[name=parent_id]').val(item.parent_id || '');
            form.find('[name=sort_order]').val(item.sort_order);
            form.find('[name=target_blank]').filter('[type=checkbox]').prop('checked', Boolean(item.target_blank));
            form.find('[name=status]').filter('[type=checkbox]').prop('checked', Boolean(item.status));
            form.find('[name=parent_id] option').prop('disabled', false);
            form.find('[name=parent_id] option[value="' + item.id + '"]').prop('disabled', true);
            form.find('.modal-title').text(form.find('.modal-title').data('edit-title'));
            updateTypeFields();
            modal.modal('show');
        });

        form.find('[name=kind]').on('change', updateTypeFields);
    </script>
@endpush
