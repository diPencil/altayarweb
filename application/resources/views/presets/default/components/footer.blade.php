@php
    $importantLinks = getContent('footer_important_links.element', false, null, true);
    $companyLinks = getContent('footer_company_links.element', false, null, true);
    $contact = getContent('contact_us.content', true);
    $socialIcons = getContent('social_icon.element', false);
    $policyPages = getContent('policy_pages.element', false, null, true);

    $footerDescRaw = getLangContent($contact?->data_values, 'short_details') ?: __('Footer tagline short');
    $footerDesc = $footerDescRaw;
    if (! str_contains($footerDesc, "\n")) {
        $footerDesc = preg_replace('/\s+يقدم\s+/u', "\nيقدم ", $footerDesc, 1);
        if (! str_contains($footerDesc, "\n")) {
            $footerDesc = preg_replace('/\s+نقدم\s+/u', "\nنقدم ", $footerDesc, 1);
        }
    }
@endphp
<!-- Footer Start Here — overflow--hidden removed so decorative clouds beside the logo are not clipped (body already uses overflow-x: hidden). -->
<footer class="footer-area z--1">

    <div class="bg--element position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg2.png') }}" alt="image">
    </div>

    <div class="bg--element-three position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/dash-line.png') }}" alt="image">
    </div>


    <div class="cloud--group__one position-absolute">
        <img class="cloud-one position-relative left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud1.png') }}" alt="image">
        <img class="cloud-two position-absolute left_image_bounce-2"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud2.png') }}" alt="image">
        <img class="cloud-three position-absolute left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud3.png') }}" alt="image">
        <img class="cloud-four position-absolute left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud6.png') }}" alt="image">
        <img class="cloud-five position-absolute left_image_bounce-2"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud7.png') }}" alt="image">
    </div>

    <div class="airplane--two">
        <div class="thumb--wrap">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/ballon.png') }}" alt="image">
        </div>
    </div>


    <div class="footer-top py-100">
        <div class="container">
            <div class="row gy-4 justify-content-start">
                <div class="col-xl-4 col-sm-6">
                    <div class="footer-item">
                        <div class="footer-item--logo">
                            <a href="{{ route('home') }}" class="footer-logo-normal" id="footer-logo-normal">
                                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                    alt="">
                            </a>
                        </div>
                        <p class="footer-item--desc footer-brand-desc">{{ $footerDesc }}</p>

                        <ul class="social-list z--9 position-relative">
                            @foreach ($socialIcons as $item)
                            <li>
                                <a href="{{ $item->data_values->url }}" class="social-list__link icon-wrapper">
                                    <div class="icon {{$loop->iteration == 2 ? '': ''}}">@php echo $item->data_values->social_icon; @endphp</div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Important Links')</h5>
                        <ul class="footer-menu">
                            <li class="menu--item">
                                <a href="{{ route('public.membership.details') }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('Our Club Member') }}
                                </a>
                            </li>
                            <li class="menu--item">
                                <a href="{{ route('public.travel.index') }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('Discover Needs') }}
                                </a>
                            </li>
                            <li class="menu--item">
                                <a href="{{ route('public.offers.index', ['category' => 'limited']) }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('Limited Offers') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Company Link')</h5>
                        <ul class="footer-menu">
                            <li class="menu--item">
                                <a href="{{ route('blog') }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('News & Updates') }}
                                </a>
                            </li>
                            <li class="menu--item">
                                <a href="{{ route('pages', ['about']) }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('About AltayarVIP') }}
                                </a>
                            </li>
                            <li class="menu--item">
                                <a href="{{ route('contact') }}" class="menu--link">
                                    <i class="fa-solid fa-arrow-right-long"></i>{{ __('Contact Our Expert') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>



                <div class="col-xl-4 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Stay Updated')</h5>
                        <p class="footer-item--desc">@lang('Join Our Travel Tribe – Get the Best Deals & Destination Ideas Delivered to Your Inbox!')</p>
                        <div class="subscribe-box mb-3">

                            <form action="{{ route('subscribe') }}" method="POST">
                                @csrf
                                <input class="form--control footer-input" type="email" name="email"
                                    placeholder="@lang('Email Address')">
                                <button class="sub-btn" type="submit"><i
                                        class="fa-regular fa-paper-plane"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- Footer Top End-->

    <!-- bottom Footer -->
    <div class="bottom-footer pt-4 pb-3">
        <div class="container">
            <div class="row text-center gy-2">
                <div class="col-lg-12">
                    <div class="bottom-footer-text d-flex flex-wrap gap--12 justify-content-center justify-content-sm-between">
                        <div class="mb-0">@php echo @$contact->data_values->website_footer; @endphp</div>
                        <div class="d-flex flex-wrap gap--12 align-items-center">
                            <a href="{{ url('/our-privacy') }}">{{ __('Policy & Terms') }}</a>
                            <a href="{{ url('/cookie-policy') }}">{{ __('Cookies Policy') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</footer>
<!-- ==================== Footer End Here ==================== -->
<style>
    @media (max-width: 768px) {
        .scroll-top {
            display: none !important;
        }
    }
</style>
<div class="scroll-top">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 197.514;">
        </path>
    </svg>
</div>
