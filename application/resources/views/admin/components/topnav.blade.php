@php
    $languages = App\Models\Language::where('code', '!=', 'es')->get();
    $currentLang = $languages->firstWhere('code', session('lang', 'en')) ?? $languages->first();
@endphp
<!-- navbar-wrapper start -->
<nav class="navbar-wrapper">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Search Options...')">
            <i class="fas fa-search text--primary"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button type="button" class="btn btn-sm btn--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ getImage(getFilePath('language') . '/' . @$currentLang->icon, getFileSize('language')) }}" alt="@lang('Icon')" style="width: 20px; height: 20px; border-radius: 50%;"> 
                    <span class="d-none d-sm-inline">{{ __(@$currentLang->name) }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-end">
                    @foreach ($languages as $language)
                        <li>
                            <a class="dropdown-menu__item d-flex align-items-center px-3 py-2 lang-change" href="javascript:void(0)" data-lang="{{ $language->code }}">
                                <img src="{{ getImage(getFilePath('language') . '/' . $language->icon, getFileSize('language')) }}" alt="@lang('Icon')" style="width: 20px; height: 20px; border-radius: 50%; margin-inline-end: 10px;">
                                <span class="dropdown-menu__caption">{{ __($language->name) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li>
                <a title="@lang('Visit Site')" href="{{ route('home') }}" target="_blank"
                    class="btn btn-sm btn--primary"><i class="fas fa-globe-americas"></i></a>
            </li>

            <li class="dropdown">
                <button type="button" class="primary--layer" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-bell text--primary"></i>
                    @if ($adminNotificationCount > 0)
                        <div class="new-not white"></div>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-end" id="adminNotificationDropdown">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Notification')</span>
                        <p id="adminNotificationHeaderText">
                            @if ($adminNotificationCount > 0)
                                @lang('You have') {{ $adminNotificationCount }} @lang('unread notification')
                            @else
                                @lang('No unread notification found')
                            @endif
                        </p>
                    </div>
                    <div class="dropdown-menu__body" id="adminNotificationDropdownBody">
                        @foreach ($adminNotifications as $notification)
                            <a href="{{ route('admin.notification.read', $notification->id) }}"
                                class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--green b-radius--rounded"><img
                                            src="{{ getImage(getFilePath('userProfile') . '/' . $notification->user?->image, getFileSize('userProfile')) }}"
                                            alt="@lang('Profile Image')"></div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">{{ __($notification->title) }}</h6>
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div><!-- navbar-notifi end -->
                            </a>
                        @endforeach
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('admin.notifications') }}" class="view-all-message" id="adminNotificationViewAllLink">@lang('View all
                                                    notification')</a>
                    </div>
                </div>
            </li>


            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage('assets/admin/images/profile/' . auth()->guard('admin')->user()->image) }}"
                                alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->guard('admin')->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-angle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-end">
                    <a href="{{ route('admin.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>
                    <a href="{{ route('admin.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <!-- <i class="dropdown-menu__icon las la-sign-out-alt"></i> -->
                        <i class="dropdown-menu__icon las la-chevron-circle-right"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->

        @push('script')
            <script>
                (function ($) {
                    'use strict';

                    const pollUrl = "{{ route('admin.notifications.live') }}";
                    const pollInterval = 15000;
                    const dropdownBody = document.getElementById('adminNotificationDropdownBody');
                    const headerText = document.getElementById('adminNotificationHeaderText');
                    const viewAllLink = document.getElementById('adminNotificationViewAllLink');
                    const bellButton = document.querySelector('.navbar-wrapper .primary--layer');

                    function escapeHtml(value) {
                        return String(value ?? '')
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;');
                    }

                    function renderNotifications(items, count, emptyMessage, viewAllUrl) {
                        if (!dropdownBody || !headerText) {
                            return;
                        }

                        headerText.textContent = count > 0
                            ? `{{ __('You have') }} ${count} {{ __('unread notification') }}`
                            : emptyMessage;

                        if (viewAllLink && viewAllUrl) {
                            viewAllLink.setAttribute('href', viewAllUrl);
                        }

                        const badge = document.querySelector('.navbar-wrapper .new-not.white');
                        if (badge) {
                            badge.style.display = count > 0 ? '' : 'none';
                        } else if (count > 0 && bellButton) {
                            const dot = document.createElement('div');
                            dot.className = 'new-not white';
                            bellButton.appendChild(dot);
                        }

                        if (!items.length) {
                            dropdownBody.innerHTML = `<div class="dropdown-menu__item text-center text-muted py-3">${escapeHtml(emptyMessage)}</div>`;
                            return;
                        }

                        dropdownBody.innerHTML = items.map((item) => `
                            <a href="${escapeHtml(item.read_url)}" class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--green b-radius--rounded">
                                        <img src="${escapeHtml(item.image_url)}" alt="{{ __('Profile Image') }}">
                                    </div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">${escapeHtml(item.title)}</h6>
                                        <span class="time"><i class="far fa-clock"></i> ${escapeHtml(item.created_at)}</span>
                                    </div>
                                </div>
                            </a>
                        `).join('');
                    }

                    async function refreshNotifications() {
                        try {
                            const response = await fetch(pollUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) {
                                return;
                            }

                            const payload = await response.json();
                            if (!payload?.success) {
                                return;
                            }

                            renderNotifications(payload.notifications || [], Number(payload.count || 0), payload.empty_message || '{{ __('No unread notification found') }}', payload.view_all_url || '{{ route('admin.notifications') }}');
                        } catch (error) {
                            console.error(error);
                        }
                    }

                    refreshNotifications();
                    window.setInterval(refreshNotifications, pollInterval);
                })(jQuery);
            </script>
        @endpush
