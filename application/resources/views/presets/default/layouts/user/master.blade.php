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
    <link rel="stylesheet" href="{{asset('assets/common/css/line-awesome.min.css')}}" >
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/magnific-popup.css') }}">
    
    <link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/datepicker.min.css')}}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/splitting.css') }}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/main.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue . 'css/custom.css')}}">


    @stack('style-lib')
    @stack('style')

    <link rel="stylesheet"
        href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}">
</head>
<body>

    @include($activeTemplate . 'components.loader')

    @php
        $isUser = auth()->check() && !auth('employee')->check();
        $showActivationOverlay = $isUser && !auth()->user()->hasActiveMembership() && !session('admin_impersonating');
    @endphp

    @if($showActivationOverlay)
        <style>
            body {
                overflow: hidden !important;
            }
            .full-page-lock {
                position: fixed;
                inset: 0;
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.4);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            .dashboard-section {
                filter: blur(15px);
                pointer-events: none;
                user-select: none;
            }
            .activation-card {
                background: white;
                padding: 2.5rem 2.5rem 3.5rem;
                border-radius: 24px;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
                text-align: center;
                max-width: 580px;
                width: 90%;
                border: 1px solid rgba(0, 0, 0, 0.05);
                animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
                position: relative;
                z-index: 100000;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .activation-card .logo-container {
                margin-bottom: 2.25rem !important;
                display: flex;
                justify-content: center;
            }
            .activation-card .logo-container img {
                max-width: 180px;
                height: auto;
            }
            .activation-card h3 {
                font-weight: 800;
                color: #1a1a1a;
                margin-top: 0 !important;
                margin-bottom: 0.75rem !important;
                font-size: 1.85rem;
                line-height: 1.2;
            }
            .activation-card p {
                font-size: 1.1rem;
                color: #4a4a4a;
                line-height: 1.5;
                margin-bottom: 0.5rem !important;
            }
            .activation-card .arabic-text {
                font-family: 'Cairo', sans-serif;
                font-size: 1.2rem;
                color: #333;
                margin: 0.5rem 0 1.5rem !important;
                font-weight: 600;
                line-height: 1.6;
            }
            .activation-card .btn--base {
                font-size: 1.1rem;
                font-weight: 600;
                padding: 12px 35px;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            /* Keep Chatbot Visible */
            .ai-chat-assistant-shell, 
            #tawkchat-container, 
            iframe[title="chat widget"] {
                z-index: 100001 !important;
                filter: none !important;
                pointer-events: auto !important;
            }
        </style>

        <div class="full-page-lock">
            <div class="activation-card">
                <div class="logo-container">
                    <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="{{ config('app.name') }}">
                </div>
                <h3>@lang('Account Activation Required')</h3>
                <p>@lang('Please contact the admin to activate your account according to the required membership.')</p>
                <div class="arabic-text" dir="rtl">برجاء التواصل مع الإدارة حتى يتم تفعيل حسابك حسب العضوية المطلوبة.</div>
                <a href="{{ route('contact') }}" class="btn btn--base pills">
                    <i class="las la-headset"></i> @lang('Contact for Activation')
                </a>
            </div>
        </div>
    @endif

    <section class="dashboard-section">
        <div class="dashboard">
            @include($activeTemplate . 'components.user.side_nav')
            <div class="dashboard-container-wrap">
                <div class="dashboard-body">
                    <div class="container-fluid">
                        @include($activeTemplate . 'components.user.top_header')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/datepicker.min.js')}}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/splitting.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.magnific-popup.min.js') }}"></script>

    <script src="{{ asset($activeTemplateTrue . 'js/gsap.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/gsap-scroll-trigger.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.appear.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/odometer.min.js') }}"></script>
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
