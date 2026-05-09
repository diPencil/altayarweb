@php
    $mobileApp = getContent('mobile_app.content', true);
    
    // Default values
    $badgeText = $mobileApp->data_values->badge_text ?? __('Coming Soon');
    $title = $mobileApp->data_values->title ?? __('AltayarVIP Mobile App Coming Soon');
    $description = $mobileApp->data_values->description ?? __('Manage your bookings, memberships, benefits, wallet, and travel services from one easy mobile app.');
    $appStoreUrl = $mobileApp->data_values->app_store_url ?? '#';
    $googlePlayUrl = $mobileApp->data_values->google_play_url ?? '#';
    
    $status = $mobileApp->data_values->section_status ?? 'active';
    if ($status == 'inactive') {
        return;
    }

    // Mobile Image (Uploaded or null)
    $mobileImage = null;
    if (isset($mobileApp->data_values->mobile_image) && !empty($mobileApp->data_values->mobile_image)) {
        $mobileImage = getImage('assets/images/frontend/mobile_app/' . $mobileApp->data_values->mobile_image, '600x800');
    }

    // Store Badges (Uploaded or Fallback)
    $appStoreBadge = null;
    if (isset($mobileApp->data_values->app_store_badge) && !empty($mobileApp->data_values->app_store_badge)) {
        $appStoreBadge = getImage('assets/images/frontend/mobile_app/' . $mobileApp->data_values->app_store_badge, '180x60');
    } else {
        // Fallback to found file
        $appStoreBadge = asset('application/public/assets/mobile-app/app.png');
    }

    $googlePlayBadge = null;
    if (isset($mobileApp->data_values->google_play_badge) && !empty($mobileApp->data_values->google_play_badge)) {
        $googlePlayBadge = getImage('assets/images/frontend/mobile_app/' . $mobileApp->data_values->google_play_badge, '180x60');
    } else {
        // Fallback to found file
        $googlePlayBadge = asset('application/public/assets/mobile-app/google.png');
    }
@endphp

<section class="mobile-app-section py-40 position-relative overflow-hidden">
    <div class="container">
        <div class="mobile-app-banner-v9 wow animate__animated animate__fadeInUp">
            {{-- Subtle Decorative Pattern Overlay --}}
            <div class="banner-pattern-v9"></div>
            
            <div class="row align-items-center g-0 h-100 min-h-banner-v9">
                {{-- Left: Image or Fallback Mockup --}}
                <div class="col-lg-4 d-none d-lg-block">
                    @if($mobileImage)
                        <div class="uploaded-image-v9">
                            <img src="{{ $mobileImage }}" alt="@lang('Mobile App')">
                        </div>
                    @else
                        <div class="overlapping-phones-v9">
                            <div class="phone-item-v9 phone-back">
                                <div class="phone-frame-v9">
                                    <div class="phone-screen-v9">
                                        <div class="skeleton-v9 s-banner"></div>
                                        <div class="skeleton-v9 s-line"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="phone-item-v9 phone-front">
                                <div class="phone-frame-v9">
                                    <div class="phone-screen-v9">
                                        <div class="skeleton-v9 s-banner"></div>
                                        <div class="skeleton-v9 s-line"></div>
                                        <div class="skeleton-v9 s-line w-75"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Center: Content (Widened to prevent title break) --}}
                <div class="col-lg-5 col-12">
                    <div class="app-content-v9 p-4 p-lg-2">
                        <h6 class="app-badge-v9 third--font mb-2">
                            {{ __($badgeText) }}
                        </h6>
                        <h2 class="app-title-v9 mb-3">
                            {{ __($title) }}
                        </h2>
                        <p class="app-desc-v9 mb-0">
                            {{ __($description) }}
                        </p>
                    </div>
                </div>

                {{-- Right: Store BADGE IMAGES --}}
                <div class="col-lg-3 col-12">
                    <div class="store-badges-v9">
                        {{-- Google Play Badge --}}
                        <a href="{{ $googlePlayUrl }}" class="badge-link-v9" target="_blank">
                            <img src="{{ $googlePlayBadge }}" alt="Google Play" class="badge-img-v9">
                        </a>

                        {{-- App Store Badge --}}
                        <a href="{{ $appStoreUrl }}" class="badge-link-v9" target="_blank">
                            <img src="{{ $appStoreBadge }}" alt="App Store" class="badge-img-v9">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
<style>
    /* Section Main */
    .mobile-app-section { background-color: transparent; }

    /* Banner - Compact size */
    .mobile-app-banner-v9 {
        background: linear-gradient(135deg, #2266cc 0%, #39bff9 100%);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(34, 102, 204, 0.2);
    }

    .min-h-banner-v9 { min-height: 300px; }

    /* Subtle Pattern Overlay */
    .banner-pattern-v9 {
        position: absolute;
        top: 0; right: 0; width: 100%; height: 100%;
        pointer-events: none; z-index: 1;
        background-image: url('{{ asset('assets/presets/default/images/shape/element-10.png') }}');
        background-repeat: no-repeat;
        background-position: center right;
        background-size: 420px auto; 
        opacity: 0.10;
    }

    /* Left Side Images */
    .uploaded-image-v9 {
        height: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        padding-left: 20px;
        position: relative;
        bottom: -20px;
    }
    .uploaded-image-v9 img {
        max-height: 360px;
        max-width: 100%;
        width: auto;
        object-fit: contain;
        z-index: 4;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
    }

    /* Fallback Mockup */
    .overlapping-phones-v9 {
        position: relative;
        height: 100%; width: 100%;
        display: flex; align-items: flex-end;
        margin-left: 30px;
    }
    .phone-item-v9 { position: absolute; bottom: -50px; }
    .phone-back { left: 0; transform: scale(0.85); z-index: 2; opacity: 0.6; }
    .phone-front { left: 60px; z-index: 3; }
    .phone-frame-v9 {
        width: 145px; height: 290px;
        background: #0f172a; border-radius: 22px; padding: 7px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .phone-screen-v9 {
        width: 100%; height: 100%;
        background: #fff; border-radius: 17px; overflow: hidden; padding: 10px;
    }
    .skeleton-v9 { background: #f1f5f9; border-radius: 4px; margin-bottom: 8px; }
    .s-banner { height: 50px; background: #eaf8ff; }
    .s-line { height: 7px; }

    /* Content Styling */
    .app-content-v9 { position: relative; z-index: 5; color: #fff; }
    
    .app-badge-v9 {
        font-size: 32px !important;
        font-weight: 700 !important;
        line-height: 1.2;
        margin-bottom: 10px;
        display: block;
        color: #ff9f1c !important; /* Orange Color */
    }

    .app-title-v9 {
        font-size: 46px; /* Slightly Reduced for Better Two-Line Fit */
        font-weight: 800;
        line-height: 1.1;
        color: #ffffff;
        margin-bottom: 15px;
    }
    .app-desc-v9 {
        font-size: 1rem;
        color: rgba(255,255,255,0.95);
        line-height: 1.4;
        max-width: 90%;
    }

    /* Store Badge Images Styling */
    .store-badges-v9 {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: nowrap; /* Force Side-by-Side on Desktop */
        gap: 8px;
        padding: 20px 0;
        padding-right: 60px; /* Shift Inward toward center */
        z-index: 5;
        position: relative;
    }

    .badge-link-v9 {
        display: block;
        transition: transform 0.3s ease;
        line-height: 0;
        flex-shrink: 0; /* Prevent shrinking */
    }
    .badge-link-v9:hover {
        transform: translateY(-3px);
    }

    .badge-img-v9 {
        height: 48px;
        width: auto;
        display: block;
        object-fit: contain;
    }

    /* Tablet and Mobile Scaling */
    @media (max-width: 1200px) {
        .app-title-v9 { font-size: 38px; }
        .app-badge-v9 { font-size: 28px !important; }
        .store-badges-v9 { padding-right: 30px; } /* Slightly less inward on smaller desktop */
    }

    @media (max-width: 991px) {
        .min-h-banner-v9 { min-height: auto; padding: 50px 0; }
        .app-content-v9 { text-align: center; margin-bottom: 30px; }
        .app-desc-v9 { max-width: 100%; }
        .store-badges-v9 { 
            justify-content: center; 
            padding: 20px; 
            padding-right: 0; /* Reset for center alignment */
            flex-wrap: wrap; /* Allow wrap on mobile */
        }
        .uploaded-image-v9 { justify-content: center; padding-left: 0; bottom: 0; margin-bottom: 20px; }
    }

    @media (max-width: 575px) {
        .app-title-v9 { font-size: 30px; }
        .app-badge-v9 { font-size: 24px !important; }
        .badge-img-v9 { height: 42px; }
        .store-badges-v9 { flex-direction: row; gap: 10px; }
    }

    /* ==================================================
       RTL (Arabic) Specific Adjustments
       ================================================== */
    
    [dir="rtl"] .store-badges-v9 {
        padding-right: 0;
        padding-left: 60px; /* Shift inward from left edge in RTL */
    }

    [dir="rtl"] .overlapping-phones-v9 {
        margin-left: 0;
        margin-right: 30px;
    }

    [dir="rtl"] .uploaded-image-v9 {
        padding-left: 0;
        padding-right: 20px;
        justify-content: flex-end; /* Align to the outer right edge in RTL */
    }

    /* RTL Responsive Scaling */
    @media (max-width: 1200px) {
        [dir="rtl"] .store-badges-v9 { 
            padding-left: 30px; 
            padding-right: 0;
        }
    }

    @media (max-width: 991px) {
        [dir="rtl"] .store-badges-v9 {
            padding-left: 0;
            padding-right: 0;
            justify-content: center;
        }
        [dir="rtl"] .overlapping-phones-v9 {
            margin-right: 0;
        }
        [dir="rtl"] .uploaded-image-v9 {
            padding-right: 0;
            justify-content: center;
        }
    }
</style>
@endpush
