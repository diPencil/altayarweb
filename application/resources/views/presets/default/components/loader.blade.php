@php
    $preloaderArabic = session('lang') === 'ar';
@endphp
    <!--==================== Preloader Start ====================-->
    <div id="preloader">
        <div id="text" class="preloader-text {{ $preloaderArabic ? 'preloader-text--ar' : 'preloader-text--en' }}">
            @if ($preloaderArabic)
                <p>الطيار</p>
                <p>في</p>
                <p>اي</p>
                <p class="active">بي</p>
            @else
                <p>A</p>
                <p>L</p>
                <p>T</p>
                <p>A</p>
                <p>Y</p>
                <p>A</p>
                <p>R</p>
                <p>V</p>
                <p>I</p>
                <p class="active">P</p>
            @endif
        </div>
    </div>

    <div class="sidebar-overlay"></div>

    <!--==================== Preloader End ====================-->
