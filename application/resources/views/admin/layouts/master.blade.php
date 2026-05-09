<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" @if(is_rtl()) dir="rtl" @endif>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $general->siteName($pageTitle ?? '') }}</title>

    <link rel="shortcut icon" type="image/png" href="{{getImage(getFilePath('logoIcon') .'/favicon.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Maven+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('includes.rtl-assets')

    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap-toggle.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/line-awesome.min.css')}}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{asset('assets/admin/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/admin.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/custom-style.css')}}">

    



    @stack('style')
</head>

<body>
    @yield('content')




    <script src="{{asset('assets/common/js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{asset('assets/common/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/jquery.slimscroll.min.js')}}"></script>



    @include('includes.notify')
    @stack('script-lib')


    <script src="{{asset('assets/admin/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/admin.js')}}"></script>
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>
    {{-- LOAD EDITOR --}}
    <script>
        "use strict";
        document.querySelectorAll('.trumEdit').forEach(element => {
            ClassicEditor
                .create(element)
                .then(editor => {
                    window.editor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>

    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.lang-change', function() {
                const lang = $(this).data('lang');
                window.location.href = "{{ route('home') }}/change/" + lang;
            });
        })(jQuery);
    </script>


    @stack('script')



</body>

</html>
