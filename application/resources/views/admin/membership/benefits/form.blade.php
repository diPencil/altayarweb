@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($benefit) ? route('admin.membership.benefits.update', $benefit->id) : route('admin.membership.benefits.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('User')</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">@lang('Select user')</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected((isset($benefit) && $benefit->user_id == $user->id) || ($selectedUserId == $user->id))>{{ $user->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">@lang('Membership Plan')</label>
                                <select name="membership_plan_id" class="form-control">
                                    <option value="">@lang('None')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected(isset($benefit) && $benefit->membership_plan_id == $plan->id)>{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div id="benefit-container">
                            <h5 class="mb-3">@lang('Benefit Items')</h5>
                            
                            @if(isset($benefit))
                                {{-- Edit Mode: Single Item --}}
                                <div class="benefit-item border rounded p-3 mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">@lang('Benefit Name')</label>
                                            <input type="text" name="title" class="form-control" value="{{ $benefit->title }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">@lang('Description')</label>
                                            <textarea name="description" class="form-control">{{ $benefit->description }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">@lang('Total Quantity')</label>
                                            <input type="number" min="0" name="total_quantity" class="form-control" value="{{ $benefit->total_quantity }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">@lang('Used Quantity')</label>
                                            <input type="number" min="0" name="used_quantity" class="form-control" value="{{ $benefit->used_quantity }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">@lang('Unit')</label>
                                            <input type="text" name="unit" class="form-control" value="{{ $benefit->unit }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">@lang('Starts At')</label>
                                            <input type="date" name="starts_at" class="form-control" value="{{ $benefit->starts_at ? $benefit->starts_at->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">@lang('Expires At')</label>
                                            <input type="date" name="expires_at" class="form-control" value="{{ $benefit->expires_at ? $benefit->expires_at->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">@lang('Status')</label>
                                            <select name="status" class="form-control">
                                                <option value="1" @selected($benefit->status == 1)>@lang('Active')</option>
                                                <option value="0" @selected($benefit->status == 0)>@lang('Inactive')</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">@lang('Notes')</label>
                                            <input type="text" name="notes" class="form-control" value="{{ $benefit->notes }}">
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Create Mode: Repeater --}}
                                <div id="empty-benefit-message" class="text-center p-5 border rounded bg--light mb-3">
                                    <i class="las la-info-circle la-3x text--info mb-2"></i>
                                    <p class="text-muted mb-0">@lang('No benefit items added yet. Click Add Benefit to start.')</p>
                                </div>
                                <div class="benefit-repeater"></div>
                                <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn--success add-benefit"><i class="las la-plus"></i> @lang('Add Benefit')</button>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button class="btn btn--primary h-45 w-100" type="submit">@lang('Save')</button>
                        </div>
                    </form>
                </div>

@push('script')
<script>
    (function ($) {
        "use strict";
        
        let index = 0;

        $('.add-benefit').on('click', function () {
            let html = `
                <div class="benefit-item border rounded p-3 mb-3 position-relative">
                    <button type="button" class="btn btn-sm btn--danger remove-benefit position-absolute" style="top: 10px; right: 10px;"><i class="las la-times"></i></button>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">@lang('Benefit Name')</label>
                            <input type="text" name="benefits[${index}][title]" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Description')</label>
                            <textarea name="benefits[${index}][description]" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Total Quantity')</label>
                            <input type="number" min="0" name="benefits[${index}][total_quantity]" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Used Quantity')</label>
                            <input type="number" min="0" name="benefits[${index}][used_quantity]" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Unit')</label>
                            <input type="text" name="benefits[${index}][unit]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Starts At')</label>
                            <input type="date" name="benefits[${index}][starts_at]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Expires At')</label>
                            <input type="date" name="benefits[${index}][expires_at]" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Status')</label>
                            <select name="benefits[${index}][status]" class="form-control">
                                <option value="1">@lang('Active')</option>
                                <option value="0">@lang('Inactive')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Notes')</label>
                            <input type="text" name="benefits[${index}][notes]" class="form-control">
                        </div>
                    </div>
                </div>
            `;
            $('.benefit-repeater').append(html);
            index++;
            updateUI();
        });

        $(document).on('click', '.remove-benefit', function () {
            $(this).closest('.benefit-item').remove();
            updateUI();
        });

        function updateUI() {
            let count = $('.benefit-item').length;
            if (count > 0) {
                $('#empty-benefit-message').hide();
                $('.add-benefit').html('<i class="las la-plus"></i> @lang('Add Another Benefit')');
            } else {
                $('#empty-benefit-message').show();
                $('.add-benefit').html('<i class="las la-plus"></i> @lang('Add Benefit')');
            }
        }
    })(jQuery);
</script>
@endpush
            </div>
        </div>
    </div>
@endsection
