@php
    $languages = App\Models\Language::where('code', '!=', 'es')->get();
    $currentLang = $languages->firstWhere('code', session('lang', 'en')) ?? $languages->first();

    $navigationMenu = [
        [
            'id' => 'home-menu',
            'label' => 'Home',
            'url' => route('home'),
            'active' => request()->routeIs('home'),
        ],
        [
            'id' => 'epayment-menu',
            'label' => 'E-Payment',
            'translate' => false,
            'url' => 'https://app.fawaterk.com/ec/altayarvip-e-payment',
            'target' => '_blank',
            'rel' => 'noopener noreferrer',
            'active' => false,
        ],
        [
            'id' => 'travelers-guide-menu',
            'label' => "Traveler's Guide",
            'url' => 'javascript:void(0)',
            'children' => [
                        [
                            'id' => 'limited-offers-menu',
                            'label' => 'Limited Offers',
                            'url' => route('public.offers.index', ['category' => 'limited']),
                            'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['limited', 'all'], true),
                            'children' => [
                                [
                                    'id' => 'ramadan-offers-menu',
                                    'label' => __('offers_nav.yearly'),
                                    'url' => route('public.offers.index', ['category' => 'yearly']),
                                    'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['yearly'], true),
                                ],
                                [
                                    'id' => 'weekend-offers-menu',
                                    'label' => 'Weekend Offers',
                                    'url' => route('public.offers.index', ['category' => 'weekend']),
                                    'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['weekend'], true),
                                ],
                                [
                                    'id' => 'spa-beauty-offers-menu',
                                    'label' => __('offers_nav.spa'),
                                    'url' => route('public.offers.index', ['category' => 'spa-beauty']),
                                    'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['spa', 'spa-beauty'], true),
                                ],
                                [
                                    'id' => 'coupons-menu',
                                    'label' => 'Coupons',
                                    'url' => route('public.offers.index', ['category' => 'coupons']),
                                    'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['coupons'], true),
                                ],
                                [
                                    'id' => 'vouchers-menu',
                                    'label' => 'Vouchers',
                                    'url' => route('public.offers.index', ['category' => 'vouchers']),
                                    'active' => request()->routeIs('public.offers.index') && in_array(request()->route('category'), ['vouchers'], true),
                                ],
                            ],
                        ],
                [
                    'id' => 'more-travel-menu',
                    'label' => 'More Travel',
                    'url' => route('public.travel.index'),
                    'active' => request()->routeIs('public.travel.index') && request()->route('section') === null,
                    'children' => [
                        [
                            'id' => 'packages-menu',
                            'label' => 'Tour Packages',
                            'url' => route('public.travel.index', ['section' => 'packages']),
                            'active' => request()->routeIs('public.travel.index') && request()->route('section') === 'packages',
                        ],
                        [
                            'id' => 'destinations-menu',
                            'label' => 'Destinations',
                            'url' => route('public.travel.index', ['section' => 'destinations']),
                            'active' => request()->routeIs('public.travel.index') && request()->route('section') === 'destinations',
                        ],
                        [
                            'id' => 'hotels-menu',
                            'label' => 'Hotels',
                            'url' => route('public.travel.index', ['section' => 'hotels']),
                            'active' => request()->routeIs('public.travel.index') && request()->route('section') === 'hotels',
                        ],
                        [
                            'id' => 'flights-menu',
                            'label' => 'Flights',
                            'url' => route('public.travel.index', ['section' => 'flights']),
                            'active' => request()->routeIs('public.travel.index') && request()->route('section') === 'flights',
                        ],
                        [
                            'id' => 'transportation-menu',
                            'label' => 'Transportation',
                            'url' => route('public.travel.index', ['section' => 'transportation']),
                            'active' => request()->routeIs('public.travel.index') && request()->route('section') === 'transportation',
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'categories-menu',
            'label' => 'Categories',
            'url' => 'javascript:void(0)',
            'children' => [
                [
                    'id' => 'membership-card-menu',
                    'label' => 'Membership Card',
                    'url' => route('public.membership.card'),
                    'active' => request()->routeIs('public.membership.card'),
                ],
                [
                    'id' => 'membership-details-menu',
                    'label' => 'Membership Details',
                    'url' => route('public.membership.details'),
                    'active' => request()->routeIs('public.membership.details') || request()->routeIs('public.membership.details.show'),
                ],
                [
                    'id' => 'privilege-card-menu',
                    'label' => 'Privilege Card',
                    'url' => route('public.privilege.cards.index'),
                    'active' => request()->routeIs('public.privilege.cards.index'),
                ],
                [
                    'id' => 'engine-screen-menu',
                    'label' => 'Engine Screen',
                    'url' => route('public.engine.screen'),
                    'active' => request()->routeIs('public.engine.screen'),
                ],
            ],
        ],
        [
            'id' => 'company-menu',
            'label' => 'About Company',
            'url' => 'javascript:void(0)',
            'children' => [
                [
                    'id' => 'about-us-menu',
                    'label' => 'About Us',
                    'url' => route('pages', ['about']),
                    'active' => Request::is('about'),
                ],
                [
                    'id' => 'contact-us-menu',
                    'label' => 'Contact Us',
                    'url' => route('contact'),
                    'active' => request()->routeIs('contact'),
                ],
                [
                    'id' => 'news-updates-menu',
                    'label' => 'News & Updates',
                    'url' => route('blog'),
                    'active' => request()->routeIs('blog'),
                ],
                [
                    'id' => 'client-feedback-menu',
                    'label' => 'Client Feedback',
                    'url' => route('public.client.feedback'),
                    'active' => request()->routeIs('public.client.feedback'),
                ],
                [
                    'id' => 'reels-menu',
                    'label' => 'Reels',
                    'url' => route('reels.index'),
                    'active' => request()->routeIs('reels.index'),
                ],
                [
                    'id' => 'policy-terms-menu',
                    'label' => 'Policy & Terms',
                    'url' => route('policy.index'),
                    'active' => request()->routeIs('policy.index') || request()->routeIs('policy.pages') || request()->routeIs('policy.terms') || request()->routeIs('policy.website'),
                ],
            ],
        ],
    ];

@endphp


<style>
    @media (max-width: 768px) {
        .language-box > .dropdown-toggle .language-box__label {
            display: none !important;
        }

        .offcanvas-body .user-info .user-thumb {
            width: 72px !important;
            height: 72px !important;
            border-radius: 50% !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
        }

        .offcanvas-body .user-info .user-thumb img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
        }
    }
</style>


<!-- Header Start -->
<div class="header-main-area">
    <div class="header" id="header">
        <div class="container-fluid position-relative">
            <div class="header-wrapper">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <!-- ham menu -->
                    <i class="fa-sharp fa-solid fa-bars-staggered ham__menu" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"></i>

                    <!-- logo -->
                    <div class="logo-wrapper">
                        <a href="{{ route('home') }}" class="normal-logo" id="normal-logo">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                alt="{{ config('app.name') }}" width="150" height="35">
                        </a>
                    </div>
                    <!-- / logo -->

                    <div class="menu--wrap d-flex align-items-center gap--72 flex-grow-1">
                        <div class="menu-list-wrapper">
                            <ul class="main-menu" id="primary-navigation">
                                @foreach ($navigationMenu as $item)
                                    @include($activeTemplate . 'components.header.menu_item', ['item' => $item, 'mode' => 'desktop', 'depth' => 0])
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <ul class="login-lng d-flex align-items-center gap-4">
                    <li class="language">
                        <div class="language-box">
                                                    <button class="dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <img class="flag--img" src="{{ getImage(getFilePath('language') . '/' . $currentLang->icon, getFileSize('language')) }}" alt="@lang('Icon')"><span class="language-box__label d-none d-md-inline">{{ __($currentLang->name) }}</span>
                          </button>
                          <ul class="dropdown-menu lng--dropdown">
                            @foreach ($languages as $language)
                              <li>
                                <a class="dropdown-item lang-change @if (Session::get('lang') === $language->code) selected @endif" href="javascript:void(0)"
                                   data-lang="{{ $language->code }}">
                                  <img class="flag--img" src="{{ getImage(getFilePath('language') . '/' . $language->icon, getFileSize('language')) }}" alt="@lang('Icon')">
                                  {{ __($language->name) }}
                                </a>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </li>

                    <li class="loin-btn--wrap">
                        @auth
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('user.home') }}">@lang('Dashboard') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endauth
                        @auth('employee')
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('employee.home') }}">@lang('Dashboard') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endauth
                        @if (!(auth()->id() || employeeId()))
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('user.login') }}">@lang('Sign In') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Header section End -->

<!-- Sidebar mobile menu wrap Start-->
<div class="offcanvas @if(is_rtl()) offcanvas-end @else offcanvas-start @endif text-bg-light" tabindex="-1" id="offcanvasExample">
    <div class="offcanvas-header">
        <div class="logo">
            <div class="align-items-center d-flex">
                <div class="logo-wrapper">
                    <a href="{{ route('home') }}" class="normal-logo" id="offcanvas-logo-normal">
                        <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                            alt="{{ config('app.name') }}" width="150" height="35">
                    </a>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="user-info">
            @auth
                <div class="user-thumb">
                    <a href="{{ route('user.home') }}">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image) }}" alt="user-thumb">
                    </a>
                </div>
                <a href="{{ route('user.home') }}">
                    <h4>{{ __(auth()->user()->username) }}</h4>
                </a>
            @endauth
            @auth('employee')

                <div class="user-thumb">
                    <a href="{{ route('employee.home') }}">
                        <img src="{{ getImage(getFilePath('employeeProfile') . '/' . auth('employee')->user()->image) }}" alt="user-thumb">
                    </a>
                </div>
                <a href="{{ route('employee.home') }}">
                    <h4>{{ auth('employee')->user()->username }}</h4>
                </a>
            @endauth
        </div>
        <ul class="side-Nav">
            @foreach ($navigationMenu as $item)
                @include($activeTemplate . 'components.header.menu_item', ['item' => $item, 'mode' => 'mobile', 'depth' => 0])
            @endforeach
            <li>
                @auth
                    <a class="login-btn" href="{{ route('user.home') }}">@lang('Dashboard') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endauth
                @auth('employee')
                    <a class="login-btn" href="{{ route('employee.home') }}">@lang('Dashboard') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endauth
                @if (!(auth()->id() || employeeId()))
                    <a class="login-btn" href="{{ route('user.login') }}">@lang('Sign In') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endif
            </li>
        </ul>
    </div>
</div>
