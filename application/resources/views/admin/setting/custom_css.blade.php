@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6>@lang('Warning: Please do it carefully. This might break the design.')</h6>
            </div>
            <form action="" method="post">
                @csrf
                <div class="card-body">
                    <div class="form-group custom-css">
                        <textarea class="customCss" rows="20" name="css">{{ $file_content }}</textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('style')
<style>
    .customCss {
    background-color: black;
    color: white;
    font-size: 15px !important;
}
</style>
@endpush
