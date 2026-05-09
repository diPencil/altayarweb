@extends($activeTemplate.'layouts.employee.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="base--card radius--20">
            <form action="{{route('employee.kyc.submit')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row gy-3">
                    <div class="col-sm-12">
                        <x-custom-form identifier="act" identifierValue="agent_kyc"></x-custom-form>
                    </div>
                   
                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn--base btn--lg pills">@lang('Save')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


