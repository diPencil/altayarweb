<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" @if(is_rtl()) dir="rtl" @endif>
<head>
    <meta charset="UTF-8">
    @include('includes.rtl-assets')
    <title>{{$general->site_name}}</title>
</head>

<body>
<form action="{{$data->url}}" method="{{$data->method}}" id="auto_submit">
    @foreach($data->val as $k=> $v)
        <input type="hidden" name="{{$k}}" value="{{$v}}"/>
    @endforeach
</form>
<script>
	"use strict";
    document.getElementById("auto_submit").submit();
</script>
</body>

</html>

