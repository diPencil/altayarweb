@php
    $cookie = App\Models\Frontend::where('data_keys','cookie.data')->first();
@endphp
@if ($cookie && (int) ($cookie->data_values->status ?? 0) === 1 && !\Cookie::get('gdpr_cookie'))
    <!-- cookies dark version start -->
    <div class="d-flex justify-content-center">
        <div class="cookies-card hide text-center">
            <p class="cookies-card__content">{{ $cookie->data_values->short_desc }} <a class="text--base"
                    href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a> 
                    <a href="javascript:void(0)"
                    class="btn btn--base btn--sm border-none policy ms-4">@lang('Accept')</a></p>
        </div>
    </div>
    <!-- cookies dark version end -->
@endif
