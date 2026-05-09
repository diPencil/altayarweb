@extends($activeTemplate . 'layouts.auth')
@section('content')
    <section class="login-section position-relative ">

        <div class="bg--thumb-one position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-10.png') }}" alt="image">
        </div>

        <div class="bg--thumb-two position-absolute">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image">
        </div>

        <div class="row mx-0 justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="base--card custom--card">
                    <div class="card-body">
                        <h3 class="text-center text-danger">@lang('You are banned')</h3>
                        <p class="fw-bold mb-1">@lang('Reason'):</p>
                        <p>{{ $user->ban_reason }}</p>
                    </div>
                </div>
            </div>
    </section>
@endsection
