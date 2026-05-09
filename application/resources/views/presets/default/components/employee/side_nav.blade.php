<div class="sidebar-menu">
    <span class="sidebar-menu__close"><i class="las la-times"></i></span>
    <div class="logo-wrapper px-3">
        <a href="{{route('home')}}" class="normal-logo" id="normal-logo">  <img width="150" src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="{{ config('app.name') }}">
        </a>
    </div>

    <ul class="sidebar-menu-list">

        <li class="sidebar-menu-list__item">
            <a href="{{route('employee.home')}}" class="sidebar-menu-list__link {{ Route::is('employee.home') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-gauge-high"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>

        @if (employeeUserCan('users'))
        <li class="sidebar-menu-list__item">
            <a href="{{route('employee.users')}}" class="sidebar-menu-list__link {{ Route::is('employee.users') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-users"></i></span>
                <span class="text">@lang('Users')</span>
            </a>
        </li>
        @endif



        @if (employeeMenuCan('tour_packages'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.tour.package.my.list') ||isActiveRoute('employee.tour.package.create')||isActiveRoute('employee.tour.package.pending') || isActiveRoute('employee.tour.package.active') || isActiveRoute('employee.tour.package.running') || isActiveRoute('employee.tour.package.expired') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-plane"></i></span>
                <span class="text">@lang('Tour Package')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.tour.package.my.list') ||isActiveRoute('employee.tour.package.create')||isActiveRoute('employee.tour.package.pending') ||isActiveRoute('employee.tour.package.active')  ||isActiveRoute('employee.tour.package.running') ||isActiveRoute('employee.tour.package.expired') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.tour.package.my.list') }}" class="sidebar-submenu-list__link {{ Route::is('employee.tour.package.my.list') ? 'active' : '' }}">@lang('All List')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.tour.package.create') }}" class="sidebar-submenu-list__link {{ Route::is('employee.tour.package.create') ? 'active' : '' }}">@lang('Create Tour')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.tour.package.active') }}" class="sidebar-submenu-list__link {{ Route::is('employee.tour.package.active') ? 'active' : '' }}">@lang('Active')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.tour.package.running') }}" class="sidebar-submenu-list__link {{ Route::is('employee.tour.package.running') ? 'active' : '' }}">@lang('Running')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.tour.package.expired') }}" class="sidebar-submenu-list__link {{ Route::is('employee.tour.package.expired') ? 'active' : '' }}">@lang('Expired')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif



        @if (employeeMenuCan('listings'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.listing.index') || isActiveRoute('employee.listing.create') || isActiveRoute('employee.listing.edit') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-tags"></i></span>
                <span class="text">@lang('Listing Offers')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.listing.index') || isActiveRoute('employee.listing.create') || isActiveRoute('employee.listing.edit') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.listing.index') }}" class="sidebar-submenu-list__link {{ Route::is('employee.listing.index') ? 'active' : '' }}">@lang('All Offers')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.listing.create') }}" class="sidebar-submenu-list__link {{ Route::is('employee.listing.create') ? 'active' : '' }}">@lang('Create Offer')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif





        @if (employeeMenuCan('bookings'))
        <li class="sidebar-menu-list__item">
            <a href="{{route('employee.tour.package.booking.my.list')}}" class="sidebar-menu-list__link {{ Route::is('employee.tour.package.booking.my.list') ? 'active' : '' }}">
                <span class="icon">

                    <i class="fa-solid fa-calendar-days"></i>
                </span>
                <span class="text">@lang('Package Bookings')</span>
            </a>
        </li>
        @endif

        @if (employeeMenuCan('popup_ads'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.popup-ads.index') || isActiveRoute('employee.popup-ads.create') || isActiveRoute('employee.popup-ads.edit') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-bullhorn"></i></span>
                <span class="text">@lang('Popup Ads')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.popup-ads.index') || isActiveRoute('employee.popup-ads.create') || isActiveRoute('employee.popup-ads.edit') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.popup-ads.index') }}" class="sidebar-submenu-list__link {{ Route::is('employee.popup-ads.index') ? 'active' : '' }}">@lang('Personal Offers')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.popup-ads.create') }}" class="sidebar-submenu-list__link {{ Route::is('employee.popup-ads.create') ? 'active' : '' }}">@lang('Create Offer')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if (employeeMenuCan('kyc'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.kyc.form')||isActiveRoute('employee.kyc.data')  ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-shield-halved"></i></span>
                <span class="text">@lang('KYC')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.kyc.form')||isActiveRoute('employee.kyc.data')  ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    @if (auth('employee')->user()-> kv != 1)

                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('employee.kyc.form')}}" class="sidebar-submenu-list__link {{ Route::is('employee.kyc.form') ? 'active' : '' }}">@lang('KYC Form')</a>
                    </li>
                    @endif
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('employee.kyc.data')}}" class="sidebar-submenu-list__link {{ Route::is('employee.kyc.data') ? 'active' : '' }}">@lang('KYC Data')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if (employeeMenuCan('withdraw'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.withdraw') || isActiveRoute('employee.withdraw.history') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-money-check-dollar"></i></span>
                <span class="text">@lang('Withdraw')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.withdraw') || isActiveRoute('employee.withdraw.history') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.withdraw') }}" class="sidebar-submenu-list__link {{ Route::is('employee.withdraw') ? 'active' : '' }}">@lang('Withdraw Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.withdraw.history') }}" class="sidebar-submenu-list__link {{ Route::is('employee.withdraw.history') ? 'active' : '' }}">@lang('Withdraw Log')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if (employeeMenuCan('payments'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.e.payment') || isActiveRoute('employee.transactions') || isActiveRoute('employee.deposit.history') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-credit-card"></i></span>
                <span class="text">@lang('Payments')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.e.payment') || isActiveRoute('employee.transactions') || isActiveRoute('employee.deposit.history') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.e.payment') }}" class="sidebar-submenu-list__link {{ Route::is('employee.e.payment') ? 'active' : '' }}">@lang('E-Payment')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.transactions') }}" class="sidebar-submenu-list__link {{ Route::is('employee.transactions') ? 'active' : '' }}">@lang('Transactions')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.deposit.history') }}" class="sidebar-submenu-list__link {{ Route::is('employee.deposit.history') ? 'active' : '' }}">@lang('Payment Log')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if (employeeMenuCan('tickets'))
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('employee.ticket')||isActiveRoute('employee.ticket.open') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-headset"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('employee.ticket')||isActiveRoute('employee.ticket.open') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.ticket') }}" class="sidebar-submenu-list__link {{ Route::is('employee.ticket') ? 'active' : '' }}">@lang('My Tickets')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('employee.ticket.open') }}" class="sidebar-submenu-list__link {{ Route::is('employee.ticket.open') ? 'active' : '' }}">@lang('New Ticket')</a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

    </ul>
</div>
