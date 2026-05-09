@extends($activeTemplate.'layouts.employee.master')
@section('content')
<div class="row mb-4 justify-content-center">
    <div class="col-lg-6">
        <div class="base--card">
            <form action="{{route('employee.withdraw.submit')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    @php
                    echo $withdraw->method->description;
                    @endphp
                </div>
                <x-custom-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}"></x-custom-form>
                @if(Employee()->ts)
                <div class="form-group">
                    <label>@lang('Google Authenticator Code')</label>
                    <input type="text" name="authenticator_code" class="form-control form--control" required>
                </div>
                @endif
                <div class="form-group text-end">
                    <button type="submit" class="btn btn--base btn--lg w-100 pills">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

