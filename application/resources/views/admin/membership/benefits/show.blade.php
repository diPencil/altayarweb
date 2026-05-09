@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
                    <div class="button-group ms-auto">
                        <a href="{{ route('admin.membership.benefits.index') }}" class="btn btn--dark">
                            <i class="la la-reply"></i> @lang('Back')
                        </a>
                        <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#addBenefitModal">
                            <i class="la la-plus"></i> @lang('Add')
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Benefit')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Used')</th>
                                    <th>@lang('Rem.')</th>
                                    <th>@lang('Unit')</th>
                                    <th>@lang('Starts')</th>
                                    <th>@lang('Expires')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($benefits as $benefit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $benefit->title }}</td>
                                        <td>
                                            @if($benefit->description)
                                                <span title="{{ $benefit->description }}">{{ strLimit($benefit->description, 25) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $benefit->total_quantity }}</td>
                                        <td>{{ $benefit->used_quantity }}</td>
                                        <td>{{ $benefit->remaining_quantity }}</td>
                                        <td>{{ $benefit->unit ?? '-' }}</td>
                                        <td>{{ $benefit->starts_at ? showDateTime($benefit->starts_at, 'd M Y') : '-' }}</td>
                                        <td>{{ $benefit->expires_at ? showDateTime($benefit->expires_at, 'd M Y') : '-' }}</td>
                                        <td>
                                            @if($benefit->status == 1)
                                                <span class="badge bg--success text-white">@lang('Active')</span>
                                            @else
                                                <span class="badge bg--danger text-white">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button-group">
                                                <a href="{{ route('admin.membership.benefits.edit', $benefit->id) }}" class="btn btn-sm btn--primary" title="@lang('Edit')">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn--danger confirmationBtn" 
                                                        title="@lang('Delete')"
                                                        data-question="@lang('Are you sure you want to delete this membership benefit? This action cannot be undone.')" 
                                                        data-action="{{ route('admin.membership.benefits.delete', $benefit->id) }}">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No benefits assigned to this user')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Benefit Modal --}}
    <div class="modal fade" id="addBenefitModal" tabindex="-1" role="dialog" aria-labelledby="addBenefitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBenefitModalLabel">@lang('Add Benefits for') {{ $user->username }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.membership.benefits.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="redirect_to_user" value="1">
                    
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Membership Plan')</label>
                                <select name="membership_plan_id" class="form-control">
                                    <option value="">@lang('None')</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="modal-benefit-container">
                            <h6 class="mb-3">@lang('Benefit Items')</h6>
                            
                            <div id="modal-empty-message" class="text-center p-4 border rounded bg--light mb-3">
                                <i class="las la-info-circle la-2x text--info mb-2"></i>
                                <p class="text-muted mb-0">@lang('No benefit items added yet. Click Add Benefit to start.')</p>
                            </div>
                            
                            <div class="modal-benefit-repeater"></div>
                            
                            <div class="text-start mt-2">
                                <button type="button" class="btn btn-sm btn--success modal-add-benefit"><i class="las la-plus"></i> @lang('Add Benefit')</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Save Benefits')</button>
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
        
        let index = 0;

        $('.modal-add-benefit').on('click', function () {
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
            $('.modal-benefit-repeater').append(html);
            index++;
            updateModalUI();
        });

        $(document).on('click', '.remove-benefit', function () {
            $(this).closest('.benefit-item').remove();
            updateModalUI();
        });

        function updateModalUI() {
            let count = $('.modal-benefit-repeater .benefit-item').length;
            if (count > 0) {
                $('#modal-empty-message').hide();
                $('.modal-add-benefit').html('<i class="las la-plus"></i> @lang('Add Another Benefit')');
            } else {
                $('#modal-empty-message').show();
                $('.modal-add-benefit').html('<i class="las la-plus"></i> @lang('Add Benefit')');
            }
        }
    })(jQuery);
</script>
@endpush
