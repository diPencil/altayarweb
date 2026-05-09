<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" @if(is_rtl()) dir="rtl" @endif>
<head>
	<title>{{ $pageTitle }}</title>
	@include('includes.rtl-assets')
	<style>
		@if(is_rtl())
		body {
			direction: rtl;
			text-align: right;
			font-family: 'Tajawal', sans-serif;
		}

		.info{
			margin-top: 40px;
		    margin-left: 40px;
		    margin-bottom: 25px;
		}

		[dir="rtl"] .info{
			margin-left: 0;
			margin-right: 40px;
		}
		@endif
		p{
			margin: 0;
			margin-bottom: 10px;
		}
		h4{
			margin: 0;
			margin-bottom: 10px;
		}
	</style>
</head>
<body>
	@php echo $email->message @endphp
</body>
</html>
