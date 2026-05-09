<!doctype html>
<html lang="{{ config('app.locale') }}" @if(is_rtl()) dir="rtl" @endif itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('includes.seo')
    <!-- Bootstrap CSS -->
    @include('includes.rtl-assets')
    <link href="{{ asset('assets/common/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/common/css/line-awesome.min.css')}}">

    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/slick.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/splitting.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/main.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/custom.css')}}">

    @stack('style-lib')
    @stack('style')

    <link rel="stylesheet"
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}">
</head>
<body>

    @include($activeTemplate . 'components.loader')
    <section class="dashboard-section">
        <div class="dashboard">
            @include($activeTemplate . 'components.employee.side_nav')
            <div class="dashboard-container-wrap">
                <div class="dashboard-body">
                    <div class="container-fluid">
                        @include($activeTemplate . 'components.employee.top_header')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/splitting.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{asset('assets/admin/js/select2.min.js')}}"></script>

    <script src="{{ asset($activeTemplateTrue . 'js/gsap.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/gsap-scroll-trigger.js') }}"></script>
    
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')
    @stack('script')
    @include('includes.plugins')
    @include('includes.notify')


    <script>
        (function ($) {

            "use strict";

            $(".langSel").on("change", function () {
                window.location.href = "{{route('home')}}/change/" + $(this).val();
            });

            $(document).on('click', '.lang-change', function() {
                const lang = $(this).data('lang');
                window.location.href = "{{ route('home') }}/change/" + lang;
            });

            $('.policy').on('click', function () {
                $.get('{{route('cookie.accept')}}', function (response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function () {
                $('.cookies-card').removeClass('hide')
            }, 2000);

        })(jQuery);
    </script>

</body>
</html>
