@if(is_rtl())
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ $rtlCss ?? asset('assets/presets/default/css/rtl.css') }}" rel="stylesheet">
@else
    <link href="{{ $bootstrapCss ?? asset('assets/common/css/bootstrap.min.css') }}" rel="stylesheet">
@endif
