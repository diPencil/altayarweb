@extends($activeTemplate.'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-6">
            @if(!Employee()->ts)
                <div class="base--card">
                    <h5>@lang('Add Your Account')</h5>

                    <div class="card-body">
                        <p class="mb-3">
                            @lang('Use the QR code or setup key on your Google Authenticator app to add your account. ')</p>

                        <div class="form-group mx-auto text-center">
                            <img class="mx-auto" src="{{$qrCodeUrl}}">
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Setup Key')</label>
                            <div class="input-group">
                                <input type="text" name="key" value="{{$secret}}" class="form-control form--control referralURL"
                                    readonly>
                                <button type="button" class="input-group-text bg--base text-white copytext" id="copyBoard"><i
                                        class="fa fa-copy"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-6">
            @if(Employee()->ts)
                <div class="base--card">
                    <h5>@lang('Disable 2FA Security')</h5>
                    <form action="{{route('user.twofactor.disable')}}" method="POST">
                        @csrf
                        <input type="hidden" name="key" value="{{$secret}}">
                        <div class="form-group">
                            <label class="form--label">@lang('Google Authenticatior OTP')</label>
                            <input type="text" class="form-control form--control" name="code" required>
                        </div>
                        <button type="submit" class="btn btn--base btn--lg mt-3 w-100">@lang('Submit')</button>
                    </form>
                </div>
            @else
                <div class="base--card">
                    <h5>@lang('Enable 2FA Security')</h5>
                    <form action="{{ route('user.twofactor.enable') }}" method="POST">
                            @csrf
                            <input type="hidden" name="key" value="{{$secret}}">
                            <div class="form-group">
                                <label class="form--label">@lang('Google Authenticatior OTP')</label>
                                <input type="text" class="form-control form--control" name="code" required>
                            </div>
                            <button type="submit" class="btn btn--base btn--lg mt-2 w-100 pills">@lang('Submit')</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
    <style>
        .copied::after {
            background-color: #{{ $general->base_color }};
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('#copyBoard').on('click', function () {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush

