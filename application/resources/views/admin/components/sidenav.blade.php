<div class="sidebar">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img
                    src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-chart-line"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>
                <li class="sidebar__menu-header">@lang('Users Management')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.users.*') }}">
                    <a href="{{ route('admin.users.active') }}" class="nav-link ">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('All Users')</span>
                        @if ($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0)
                            <div class="blob white"></div>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.employees.*') }}">
                    <a href="{{ route('admin.employees.active') }}" class="nav-link position-relative">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('All Employees')</span>
                        @if ($employeeBannedUsersCount > 0 || $employeeEmailUnverifiedUsersCount > 0 || $employeeMobileUnverifiedUsersCount > 0)
                            <div class="blob white"></div>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.membership.*') }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-id-card"></i>
                        <span class="menu-title">@lang('Membership')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.membership.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.membership.plans') }}">
                                <a href="{{ route('admin.membership.plans') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Membership Plans')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.membership.subscriptions') }}">
                                <a href="{{ route('admin.membership.subscriptions') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Subscriptions')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.membership.points') }}">
                                <a href="{{ route('admin.membership.points') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Points')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.membership.cashback') }}">
                                <a href="{{ route('admin.membership.cashback') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Cashback')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.privilege.cards.*') }}">
                                <a href="{{ route('admin.privilege.cards.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Privilege Cards')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.membership.benefits.*') }}">
                                <a href="{{ route('admin.membership.benefits.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Benefits / Additions')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar__menu-header">@lang('Booking Management')</li>
                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.service.booking.*', 'admin.tour.package.booking.*']) }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-calendar-check"></i>
                        <span class="menu-title">@lang('Bookings')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.service.booking.*', 'admin.tour.package.booking.*'], 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.service.booking.index') }}">
                                <a href="{{ route('admin.service.booking.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Bookings List')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.service.booking.hotels') }}">
                                <a href="{{ route('admin.service.booking.hotels') }}" class="nav-link">
                                    <i class="menu-icon las la-hotel"></i>
                                    <span class="menu-title">@lang('Hotels Requests')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.service.booking.flights') }}">
                                <a href="{{ route('admin.service.booking.flights') }}" class="nav-link">
                                    <i class="menu-icon las la-plane-departure"></i>
                                    <span class="menu-title">@lang('Flights Requests')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.service.booking.transportation') }}">
                                <a href="{{ route('admin.service.booking.transportation') }}" class="nav-link">
                                    <i class="menu-icon las la-car"></i>
                                    <span class="menu-title">@lang('Transport Requests')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.service.booking.create') }}">
                                <a href="{{ route('admin.service.booking.create') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Add Booking')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.booking.pending') }}">
                                <a href="{{ route('admin.tour.package.booking.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Pending Bookings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.booking.approved') }}">
                                <a href="{{ route('admin.tour.package.booking.approved') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Approved Bookings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.booking.canceled') }}">
                                <a href="{{ route('admin.tour.package.booking.canceled') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Canceled Bookings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.booking.employee.index') }}">
                                <a href="{{ route('admin.tour.package.booking.employee.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Employee Bookings')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.tour.package.*') }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-box"></i>
                        <span class="menu-title">@lang('Tour Packages')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.tour.package.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.booking.index') }}">
                                <a href="{{ route('admin.tour.package.booking.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Packages List')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.index') }}">
                                <a href="{{ route('admin.tour.package.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('All Tour Packages')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.create') }}">
                                <a href="{{ route('admin.tour.package.create') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Add New Tour')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.category.index') }}">
                                <a href="{{ route('admin.category.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Categories')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.location.index') }}">
                                <a href="{{ route('admin.location.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Locations')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.pending') }}">
                                <a href="{{ route('admin.tour.package.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Pending Tours')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.active') }}">
                                <a href="{{ route('admin.tour.package.active') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Active Tours')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.running') }}">
                                <a href="{{ route('admin.tour.package.running') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Running Tours')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.cancelled') }}">
                                <a href="{{ route('admin.tour.package.cancelled') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Cancelled Tours')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.expired') }}">
                                <a href="{{ route('admin.tour.package.expired') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Expired Tours')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.my.list') }}">
                                <a href="{{ route('admin.tour.package.my.list') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('My Tour Packages')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.tour.package.all.employee') }}">
                                <a href="{{ route('admin.tour.package.all.employee') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Employee Tour Packages')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.listing.*') }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-list-ul"></i>
                        <span class="menu-title">@lang('Listing Offers')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.listing.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.listing.index') }}">
                                <a href="{{ route('admin.listing.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Offers List')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.listing.type.index') }}">
                                <a href="{{ route('admin.listing.type.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Offers types')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar__menu-header">@lang('Reels Management')</li>
                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.reels.*') }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-film"></i>
                        <span class="menu-title">@lang('Reels')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.reels.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.reels.index') }}">
                                <a href="{{ route('admin.reels.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('All Reels')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.reels.comments') }}">
                                <a href="{{ route('admin.reels.comments') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Comments')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>



                <li class="sidebar__menu-header">@lang('Financial Logs')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.deposit.list') }}">
                    <a href="{{ route('admin.deposit.list') }}" class="nav-link ">
                        <i class="menu-icon las la-history"></i>
                        <span class="menu-title">@lang('Payments History')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }}">
                    <a href="{{ route('admin.deposit.pending') }}" class="nav-link ">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">@lang('Pending')</span>
                        @if (0 < $pendingDepositsCount)
                            <div class="blob white">
                            </div>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.*') }}">
                    <a href="{{ route('admin.withdraw.pending') }}" class="nav-link ">
                        <i class="menu-icon las la la-credit-card"></i>
                        <span class="menu-title">@lang('Withdrawals')</span>
                        @if (0 < $pendingWithdrawCount)
                            <div class="blob white">
                            </div>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive(['admin.report.transaction', 'admin.report.transaction.search']) }}">
                    <a href="{{ route('admin.report.transaction') }}" class="nav-link">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Transaction Logs')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.invoice.index') }}">
                    <a href="{{ route('admin.invoice.index') }}" class="nav-link ">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('Invoices Management')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.wallet.*') }}">
                    <a href="{{ route('admin.wallet.index') }}" class="nav-link">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">@lang('Customer Wallets')</span>
                    </a>
                </li>
                <!-- <li class="sidebar-menu-item {{ menuActive('admin.withdraw.method.index') }}">
                    <a href="{{ route('admin.withdraw.method.index') }}" class="nav-link ">
                        <i class="menu-icon las la-dollar-sign"></i>
                        <span class="menu-title">@lang('Withdrawal Methods')</span>
                    </a>
                </li> -->

                <li class="sidebar__menu-header">@lang('Report')</li>
                <li
                    class="sidebar-menu-item {{ menuActive(['admin.report.login.history','admin.employee.report.login.history', 'admin.report.login.ipHistory','admin.employee.report.login.ipHistory']) }}">
                    <a href="{{ route('admin.report.login.history') }}" class="nav-link">
                        <i class="menu-icon las la-sign-in-alt"></i>
                        <span class="menu-title">@lang('Login Activities')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive(['admin.report.notification.history','admin.employee.report.notification.history']) }}">
                    <a href="{{ route('admin.report.notification.history') }}" class="nav-link">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Notifications')</span>
                    </a>
                </li>
                <li class="sidebar__menu-header">@lang('Help Desk')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.chat-assistant.*') }}">
                    <a href="{{ route('admin.chat-assistant.index') }}" class="nav-link">
                        <i class="menu-icon las la-comments"></i>
                        <span class="menu-title">@lang('Live Chat')</span>
                        @if ($pendingChatConversationsCount > 0)
                            <div class="blob white"></div>
                        @endif
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.ticket.*') }}">
                    <a href="{{ route('admin.ticket.pending') }}" class="nav-link ">
                        <i class="menu-icon las la la-life-ring"></i>
                        <span class="menu-title">@lang('User Ticket')</span>
                        @if (0 < $pendingTicketCount)
                            <div class="blob white">
                            </div>
                        @endif
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('admin.employee.ticket.*') }}">
                    <a href="{{ route('admin.employee.ticket.pending') }}" class="nav-link position-relative">
                        <i class="menu-icon las la la-life-ring"></i>
                        <span class="menu-title">@lang('Employee Tickets')</span>
                        @if (0 < $employeePendingTicketCount)
                            <div class="blob white">
                            </div>
                        @endif
                    </a>
                </li>

                <li class="sidebar__menu-header">@lang('Marketing')</li>
                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive('admin.popup-ads.*') }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-bullhorn"></i>
                        <span class="menu-title">@lang('Popup Ads')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.popup-ads.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.popup-ads.index') }}">
                                <a href="{{ route('admin.popup-ads.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Popup Ads')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.popup-ads.create') }}">
                                <a href="{{ route('admin.popup-ads.create') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Create Popup Ad')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <li class="sidebar__menu-header">@lang('KYC Management')</li>

                <li class="sidebar-menu-item {{ menuActive(['admin.kyc.setting']) }}">
                    <a href="{{ route('admin.kyc.setting') }}" class="nav-link">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('User KYC')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive(['admin.employee.kyc.setting']) }}">
                    <a href="{{ route('admin.employee.kyc.setting') }}" class="nav-link">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Employee KYC')</span>
                    </a>
                </li>
                <li class="sidebar__menu-header">@lang('Content Management')</li>

                <li class="sidebar-menu-item {{ menuActive('admin.frontend.manage.*') }}">
                    <a href="{{ route('admin.frontend.manage.pages') }}" class="nav-link ">
                        <i class="menu-icon la la-pager"></i>
                        <span class="menu-title">@lang('Pages')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.frontend.sections*', 3) }}">
                        <i class="menu-icon la la-grip-horizontal"></i>
                        <span class="menu-title">@lang('Sections')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.frontend.sections*', 2) }} ">
                        <ul>
                            @php
                                $sections = getPageSections(true);
                                $categories = [];
                                foreach ($sections as $k => $secs) {
                                    if (isset($secs['builder']) && $secs['builder']) {
                                        $cat = isset($secs['category']) ? $secs['category'] : 'General';
                                        $categories[$cat][$k] = $secs;
                                    }
                                }
                                $lastSegment = collect(request()->segments())->last();
                            @endphp

                            @foreach ($categories as $catName => $catSections)
                                <li class="sidebar-menu-item sidebar-dropdown">
                                    <a href="javascript:void(0)" class="{{ in_array($lastSegment, array_keys($catSections)) ? 'side-menu--open' : '' }}">
                                        <i class="menu-icon las la-folder"></i>
                                        <span class="menu-title">{{ __($catName) }}</span>
                                    </a>
                                    <div class="sidebar-submenu {{ in_array($lastSegment, array_keys($catSections)) ? 'sidebar-submenu__open' : '' }} ">
                                        <ul>
                                            @foreach ($catSections as $k => $secs)
                                                <li class="sidebar-menu-item  @if ($lastSegment == $k) active @endif ">
                                                    <a href="{{ route('admin.frontend.sections', $k) }}" class="nav-link">
                                                        <i class="menu-icon las la-caret-right"></i>
                                                        <span class="menu-title">{{ __($secs['name']) }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown {{ menuActive(['admin.setting.*', 'admin.language.*', 'admin.extensions.*', 'admin.seo*', 'admin.gateway.*']) }}">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title">@lang('General Settings')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.setting.*', 'admin.language.*', 'admin.extensions.*', 'admin.seo*', 'admin.gateway.*'], 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                                <a href="{{ route('admin.setting.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Global Settings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.*') }}">
                                <a href="{{ route('admin.gateway.automatic.index') }}" class="nav-link ">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Payment Gateways')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.logo.icon') }}">
                                <a href="{{ route('admin.setting.logo.icon') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Logo & Favicon')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                                <a href="{{ route('admin.language.manage') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Language')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item sidebar-dropdown">
                                <a href="javascript:void(0)" class="{{ menuActive('admin.setting.notification*', 3) }}">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Email & Notification')</span>
                                </a>
                                <div class="sidebar-submenu {{ menuActive('admin.setting.notification*', 2) }} ">
                                    <ul>
                                        <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.templates') }} ">
                                            <a href="{{ route('admin.setting.notification.templates') }}" class="nav-link">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('All Templates')</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.global') }} ">
                                            <a href="{{ route('admin.setting.notification.global') }}" class="nav-link">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Global Template')</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.email') }} ">
                                            <a href="{{ route('admin.setting.notification.email') }}" class="nav-link">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('Email Config')</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.sms') }} ">
                                            <a href="{{ route('admin.setting.notification.sms') }}" class="nav-link">
                                                <i class="menu-icon las la-dot-circle"></i>
                                                <span class="menu-title">@lang('SMS Config')</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.setting.socialite.credentials') }}">
                                <a href="{{ route('admin.setting.socialite.credentials') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Social Credentials')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                                <a href="{{ route('admin.extensions.index') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Plugins')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                                <a href="{{ route('admin.seo') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('SEO')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.cookie') }}">
                                <a href="{{ route('admin.setting.cookie') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('GDPR Policy')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.custom.css') }}">
                                <a href="{{ route('admin.setting.custom.css') }}" class="nav-link">
                                    <i class="menu-icon las la-caret-right"></i>
                                    <span class="menu-title">@lang('Custom CSS')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.clear.cache') }}" class="nav-link">
                        <i class="menu-icon las la-broom"></i>
                        <span class="menu-title">@lang('Clear Cache')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="menu-icon las la-code-branch"></i>
                        <span class="menu-title">@lang('Altayar Panel') 1.0.1</span>
                    </a>
                </li>



            </ul>
        </div>
    </div>

    <div class="bgg--element position-absolute">
        <img class="position-absolute" src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image" >
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        'use strict';
        $(document).ready(function() {
            var activeItem = $('.sidebar-menu-item.active').last();
            if (activeItem.length) {
                var menuWrapper = $('#sidebar__menuWrapper');
                var scrollAmount = activeItem.offset().top - menuWrapper.offset().top + menuWrapper.scrollTop() - 150;
                menuWrapper.animate({
                    scrollTop: scrollAmount
                }, 500);
            }
        });
    </script>
@endpush
