@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg--primary d-flex justify-content-between">
                <h5 class="text-white mb-0 d-flex align-items-center gap-2">
                    <span class="text-white">{{ __($kycFormName) }}</span>
                    <span class="text-white" role="button" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __($kycDescription ?? '') }}">
                        <i class="las la-question-circle"></i>
                    </span>
                </h5>
                <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i
                        class="la la-fw la-plus"></i>@lang('Add New')</button>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    @csrf
                    <div class="row addedField">
                        @if($form)
                            @foreach($form->form_data as $formData)
                                <div class="col-md-4">
                                    <div class="card border mb-3" id="{{ $loop->index }}">
                                        <input type="hidden" name="form_generator[is_required][]"
                                            value="{{ $formData->is_required }}">
                                        <input type="hidden" name="form_generator[extensions][]"
                                            value="{{ $formData->extensions }}">
                                        <input type="hidden" name="form_generator[options][]"
                                            value="{{ implode(',',$formData->options) }}">

                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>@lang('Label')</label>
                                                <input type="text" name="form_generator[form_label][]" class="form-control"
                                                    value="{{ $formData->name }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('Type')</label>
                                                <input type="text" name="form_generator[form_type][]" class="form-control"
                                                    value="{{ $formData->type }}" readonly>
                                            </div>
                                            @php
                                            $jsonData = json_encode([
                                            'type'=>$formData->type,
                                            'is_required'=>$formData->is_required,
                                            'label'=>$formData->name,
                                            'extensions'=>explode(',',$formData->extensions) ?? 'null',
                                            'options'=>$formData->options,
                                            'old_id'=>'',
                                            ]);
                                            @endphp
                                            <div class="btn-group w-100">
                                                <button type="button" class="btn btn--primary editFormData"
                                                    data-form_item="{{ $jsonData }}" data-update_id="{{ $loop->index }}"><i
                                                        class="las la-pen"></i></button>
                                                <button type="button" class="btn btn--danger removeFormData"><i
                                                        class="las la-times"></i></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="border rounded-3 p-4 bg-white text-center">
                                    <div class="fw-semibold mb-2">@lang('No employee KYC fields yet')</div>
                                    <div class="text-muted">@lang('Click Add New to create the first employee verification field.') </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </form>
            </div>
        </div>
    </div>
</div>
<x-form-generator></x-form-generator>
@endsection


@push('script')
<script>
    "use strict"
    var formGenerator = new FormGenerator();
    formGenerator.totalField = {{ $form ? count((array) $form -> form_data) : 0 }}

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
        new bootstrap.Tooltip(element);
    });
</script>

<script src="{{ asset('assets/common/js/form_actions.js') }}"></script>
@endpush
