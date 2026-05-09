@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ isset($plan) ? route('admin.membership.plans.update', $plan->id) : route('admin.membership.plans.store') }}" method="POST" enctype="multipart/form-data" class="card b-radius--10">
                @csrf
                <div class="card-body p-4 p-lg-5">
                    <div class="row gy-4">
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Plan Name')</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" class="form-control" required>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Plan Name (Arabic)')</label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $plan->name_ar ?? '') }}" class="form-control" dir="rtl">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Plan Price')</label>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $plan->price ?? 0) }}" class="form-control" required>
                            <small class="text-muted">@lang('Used to determine upgrade and downgrade changes.')</small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description')</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description', $plan->description ?? '') }}</textarea>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description (Arabic)')</label>
                            <textarea name="description_ar" rows="4" class="form-control" dir="rtl">{{ old('description_ar', $plan->description_ar ?? '') }}</textarea>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Plan Image')</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            @if(!empty($plan?->image_file))
                                <small class="text-muted d-block mt-2">@lang('Current image:') {{ $plan->image_file }}</small>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Plan Cover')</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                            @if(!empty($plan?->cover_image))
                                <small class="text-muted d-block mt-2">@lang('Current cover:') {{ $plan->cover_image }}</small>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('PDF Document')</label>
                            <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                            @if(!empty($plan?->pdf_file))
                                <small class="text-muted d-block mt-2">@lang('Current file:') {{ $plan->pdf_file }}</small>
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Duration in Days')</label>
                            <input type="number" min="0" name="duration_days" value="{{ old('duration_days', $plan->duration_days ?? 30) }}" class="form-control">
                            <small class="text-muted">@lang('Use 0 for lifetime membership.')</small>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Bonus Points')</label>
                            <input type="number" min="0" name="bonus_points" value="{{ old('bonus_points', $plan->bonus_points ?? 0) }}" class="form-control">
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Features')</label>
                            <div id="benefit-fields" class="d-grid gap-2">
                                @php
                                    $benefits = old('benefits', $plan->benefits ?? ['']);
                                @endphp
                                @foreach($benefits as $benefit)
                                    <input type="text" name="benefits[]" value="{{ $benefit }}" class="form-control" placeholder="@lang('Feature item')">
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn--secondary mt-3" id="add-benefit">@lang('Add Feature')</button>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Features (Arabic)')</label>
                            <div id="benefit-fields-ar" class="d-grid gap-2">
                                @php
                                    $benefitsAr = old('benefits_ar', $plan->benefits_ar ?? ['']);
                                @endphp
                                @foreach($benefitsAr as $benefit)
                                    <input type="text" name="benefits_ar[]" value="{{ $benefit }}" class="form-control" dir="rtl" placeholder="@lang('Feature item')">
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn--secondary mt-3" id="add-benefit-ar">@lang('Add Feature (Arabic)')</button>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="planStatus" @checked(old('status', $plan->status ?? 1))>
                                <label class="form-check-label" for="planStatus">@lang('Active plan')</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.membership.plans') }}" class="btn btn--dark">@lang('Cancel')</a>
                    <button type="submit" class="btn btn--primary">{{ isset($plan) ? __('Update Plan') : __('Save Plan') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function () {
        'use strict';
        const container = document.getElementById('benefit-fields');
        const containerAr = document.getElementById('benefit-fields-ar');
        const button = document.getElementById('add-benefit');
        const buttonAr = document.getElementById('add-benefit-ar');
        if (button && container) {
            button.addEventListener('click', function () {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'benefits[]';
                input.className = 'form-control';
                input.placeholder = "@lang('Feature item')";
                container.appendChild(input);
            });
        }

        if (buttonAr && containerAr) {
            buttonAr.addEventListener('click', function () {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'benefits_ar[]';
                input.className = 'form-control';
                input.setAttribute('dir', 'rtl');
                input.placeholder = "@lang('Feature item')";
                containerAr.appendChild(input);
            });
        }
    })();
</script>
@endpush
