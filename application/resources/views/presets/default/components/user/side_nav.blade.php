<div class="sidebar-menu">
    <span class="sidebar-menu__close"><i class="las la-times"></i></span>
    <div class="logo-wrapper px-3">
        <a href="{{route('home')}}" class="normal-logo" id="normal-logo">  <img width="150" src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="{{ config('app.name') }}">
        </a>
    </div>

    <ul class="sidebar-menu-list">
        <li class="sidebar-menu-list__item">
            <a href="{{route('user.home')}}" class="sidebar-menu-list__link {{ Route::is('user.home') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-border-all"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('user.deposit') || isActiveRoute('user.transactions') || isActiveRoute('user.deposit.history') || isActiveRoute('user.invoice.list') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-credit-card"></i></span>
                <span class="text">@lang('Payments')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('user.deposit') || isActiveRoute('user.transactions') || isActiveRoute('user.deposit.history') || isActiveRoute('user.invoice.list') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.deposit') }}" class="sidebar-submenu-list__link {{ Route::is('user.deposit') ? 'active' : '' }}">@lang('E-Payment')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.transactions')}}" class="sidebar-submenu-list__link {{ Route::is('user.transactions') ? 'active' : '' }}">@lang('Transactions')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.deposit.history')}}" class="sidebar-submenu-list__link {{ Route::is('user.deposit.history') ? 'active' : '' }}">@lang('Payment Log')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.invoice.list')}}" class="sidebar-submenu-list__link {{ Route::is('user.invoice.list') ? 'active' : '' }}">@lang('Invoices')</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('user.tour.package.booking.all.list')||isActiveRoute('user.tour.package.booking.my.list')||isActiveRoute('user.tour.package.booking.pending') ||   isActiveRoute('user.tour.package.booking.approved')|| isActiveRoute('user.tour.package.booking.cancel') || isActiveRoute('user.tour.package.booking.details') || isActiveRoute('user.service.booking.my.list') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-cart-shopping"></i></span>
                <span class="text">@lang('Bookings')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('user.tour.package.booking.all.list')||isActiveRoute('user.tour.package.booking.my.list')||isActiveRoute('user.tour.package.booking.pending') || isActiveRoute('user.tour.package.booking.approved')|| isActiveRoute('user.tour.package.booking.cancel') || isActiveRoute('user.tour.package.booking.details') || isActiveRoute('user.service.booking.my.list') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.all.list')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.all.list') ? 'active' : '' }}">@lang('Booking List')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.my.list')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.my.list') ? 'active' : '' }}">@lang('Tour Packages')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.service.booking.my.list')}}" class="sidebar-submenu-list__link {{ Route::is('user.service.booking.my.list') ? 'active' : '' }}">@lang('My Bookings')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.pending')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.pending') ? 'active' : '' }}">@lang('Processing')</a>
                    </li>

                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.approved')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.approved') ? 'active' : '' }}">@lang('Approved')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.canceled')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.canceled') ? 'active' : '' }}">@lang('Canceled')</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('user.membership.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-id-card"></i></span>
                <span class="text">@lang('Membership')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('user.membership.*') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.membership.index') }}" class="sidebar-submenu-list__link {{ Route::is('user.membership.index') ? 'active' : '' }}">@lang('Membership Center')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.membership.card') }}" class="sidebar-submenu-list__link {{ Route::is('user.membership.card') ? 'active' : '' }}">@lang('Member Card')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.membership.benefits') }}" class="sidebar-submenu-list__link {{ Route::is('user.membership.benefits') ? 'active' : '' }}">@lang('Membership Benefits')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.membership.summary') }}" class="sidebar-submenu-list__link {{ Route::is('user.membership.summary') ? 'active' : '' }}">@lang('Plan Summary')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('user.membership.plans') }}" class="sidebar-submenu-list__link {{ Route::is('user.membership.plans') ? 'active' : '' }}">@lang('Available Plans')</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="sidebar-menu-list__item">            <a href="{{ route('user.wallet.index') }}" class="sidebar-menu-list__link {{ Route::is('user.wallet.index') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-wallet"></i></span>
                <span class="text">@lang('My Wallet')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">            <a href="{{route('user.get.wishlist')}}" class="sidebar-menu-list__link {{ Route::is('user.get.wishlist') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-heart"></i></span>
                <span class="text">@lang('Wishlists')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a href="{{ route('user.reels.library') }}" class="sidebar-menu-list__link {{ Route::is('user.reels.library') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-clapperboard"></i></span>
                <span class="text">@lang('Reels Library')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('ticket')||isActiveRoute('ticket.open') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-headset"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('ticket')||isActiveRoute('ticket.open') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket') }}" class="sidebar-submenu-list__link {{ Route::is('ticket') ? 'active' : '' }}">@lang('My Tickets')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket.open') }}" class="sidebar-submenu-list__link {{ Route::is('ticket.open') ? 'active' : '' }}">@lang('New Ticket')</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="sidebar-menu-list__item">
            <a href="{{ route('user.clear.cache') }}" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-broom"></i></span>
                <span class="text">@lang('Clear Cache')</span>
            </a>
        </li>
    </ul>

    @php
        $sidebarMembership = auth()->user()?->currentMembership;
    @endphp

    <div class="sidebar-membership-card">
        <div class="sidebar-membership-card__link">
            <div class="sidebar-membership-card__content align-items-start">
                <div class="sidebar-membership-card__text">
                    @php
                        $sidebarMembershipPlanImage = $sidebarMembership?->plan?->image_file ? asset(getFilePath('membershipPlanImage') . '/' . $sidebarMembership->plan->image_file) : null;
                    @endphp
                    <span class="icon d-inline-flex align-items-center justify-content-center overflow-hidden" style="width: 56px; height: 56px; border-radius: 18px; background: #fff; border: 1px solid rgba(91, 156, 249, 0.18);">
                        @if($sidebarMembershipPlanImage)
                            <img src="{{ $sidebarMembershipPlanImage }}" alt="{{ $sidebarMembership?->plan?->name ?? __('Membership') }}" style="width: 100%; height: 100%; object-fit: contain; padding: 6px;">
                        @else
                            <i class="fa-solid fa-id-card"></i>
                        @endif
                    </span>
                    <div class="sidebar-membership-card__titles">
                        <div class="sidebar-membership-card__title-row">
                            <span class="sidebar-membership-card__title">{{ $sidebarMembership?->member_code ?? __('No Active Membership') }}</span>
                            <span class="badge rounded-pill sidebar-membership-card__badge {{ $sidebarMembership ? 'is-active' : 'is-inactive' }}">
                                {{ $sidebarMembership ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                        <span class="sidebar-membership-card__subtitle">
                            {{ $sidebarMembership ? (is_rtl() && $sidebarMembership->plan?->name_ar ? $sidebarMembership->plan?->name_ar : $sidebarMembership->plan?->name) : __('No Active Membership') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="sidebar-membership-card__validity-block mt-3 pt-3">
                <span class="sidebar-membership-card__subtitle d-block mb-1">@lang('Validity')</span>
                <strong class="text-dark sidebar-membership-card__validity d-block">
                    {{ $sidebarMembership ? showDateTime($sidebarMembership->start_date, 'd M Y') . ' - ' . ($sidebarMembership->end_date ? showDateTime($sidebarMembership->end_date, 'd M Y') : __('Lifetime')) : __('-') }}
                </strong>
            </div>
        </div>
    </div>

</div>
