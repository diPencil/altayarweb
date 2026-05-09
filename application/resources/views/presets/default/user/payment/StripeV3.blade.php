
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" @if(is_rtl()) dir="rtl" @endif>
<head>
    <meta charset="UTF-8">
    @include('includes.rtl-assets')
    <title>@lang('Deposit with Stripe')</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
@php
    $publishable_key = $data->StripeJSAcc->publishable_key;
    $sessionId = $data->session->id;

@endphp

<script>
    "use strict";
    var stripe = Stripe('{{$publishable_key}}');
        stripe.redirectToCheckout({
        sessionId: '{{$sessionId}}'
    });
</script>
</body>
</html>
