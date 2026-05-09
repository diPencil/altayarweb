@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <h5 class="mb-2">@lang('KYC Data')</h5>
                        <p class="text-muted mb-0">
                            @if ($employee->kyc_data)
                                @lang('These are the details you already submitted for KYC review.')
                            @else
                                @lang('This page shows the employee KYC fields that are configured by the admin. Submit the form first, then your saved data will appear here.')
                            @endif
                        </p>
                    </div>

                    @if ($employee->kyc_data)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>@lang('Field Name')</th>
                                        <th>@lang('Value')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->kyc_data as $val)
                                        @continue(!$val->value)
                                        <tr>
                                            <td>{{ __($val->name) }}</td>
                                            <td>
                                                @if ($val->type == 'checkbox')
                                                    {{ implode(', ', $val->value) }}
                                                @elseif ($val->type == 'file')
                                                    <a href="{{ route('employee.attachment.download', encrypt(getFilePath('verify') . '/' . $val->value)) }}">
                                                        <i class="fas fa-download"></i> @lang('Download Attachment')
                                                    </a>
                                                @else
                                                    {{ __($val->value) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="border rounded-3 p-3 bg--light">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                        <div>
                                            <h6 class="mb-1">@lang('Configured KYC Fields')</h6>
                                            <small class="text-muted">@lang('These are the fields the employee should submit in the KYC form.')</small>
                                        </div>
                                        <span class="badge bg--primary">{{ $form ? count((array) $form->form_data) : 0 }} @lang('Fields')</span>
                                    </div>

                                    @if($form && $form->form_data)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('Field Name')</th>
                                                        <th>@lang('Type')</th>
                                                        <th>@lang('Required')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($form->form_data as $field)
                                                        <tr>
                                                            <td>{{ __($field->name) }}</td>
                                                            <td>{{ __($field->type) }}</td>
                                                            <td>{{ $field->is_required == 'required' ? __('Yes') : __('No') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            @lang('No KYC fields are configured yet. Please contact admin.')
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="border rounded-3 p-3 text-center">
                                    <h5 class="mb-2">@lang('KYC data not found')</h5>
                                    <p class="text-muted mb-0">@lang('Once you submit the employee KYC form, your saved data will appear here for review.') </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


