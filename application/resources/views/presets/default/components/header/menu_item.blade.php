@php
    $mode = $mode ?? 'desktop';
    $depth = $depth ?? 0;
    $hasChildren = !empty($item['children']);
    $isActive = !empty($item['active']);
    $isExpanded = !empty($item['expanded']);
    $submenuId = $item['id'] ?? 'menu-item-' . $depth;
    $label = __($item['label']);
    $url = $item['url'] ?? '#';
@endphp

@if ($mode === 'desktop')
    @if ($depth === 0)
        <li class="main-menu__item {{ $hasChildren ? 'has-dropdown' : '' }} {{ $isExpanded ? 'is-open' : '' }}" data-menu-item="{{ $submenuId }}">
            @if ($hasChildren)
                <button type="button" class="main-menu-link main-menu-trigger {{ $isActive ? 'active' : '' }}" data-menu-toggle aria-expanded="{{ $isExpanded ? 'true' : 'false' }}" aria-controls="{{ $submenuId }}">
                    <span>{{ $label }}</span>
                    <i class="fa-solid fa-chevron-down menu-caret"></i>
                </button>
                <ul class="main-menu__dropdown {{ $isExpanded ? 'is-open' : '' }}" id="{{ $submenuId }}" data-submenu data-menu-level="root">
                    @foreach ($item['children'] as $child)
                        @include($activeTemplate . 'components.header.menu_item', ['item' => $child, 'mode' => 'desktop', 'depth' => 1])
                    @endforeach
                </ul>
            @else
                <a class="main-menu-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">{{ $label }}</a>
            @endif
        </li>
    @else
        <li class="main-menu__subitem {{ $hasChildren ? 'has-flyout' : '' }} {{ $isExpanded ? 'is-open' : '' }}" data-menu-item="{{ $submenuId }}">
            @if ($hasChildren)
                <button type="button" class="main-menu-submenu-trigger {{ $isActive ? 'active' : '' }}" data-menu-toggle aria-expanded="{{ $isExpanded ? 'true' : 'false' }}" aria-controls="{{ $submenuId }}">
                    <span>{{ $label }}</span>
                    <i class="fa-solid fa-chevron-right menu-caret"></i>
                </button>
                <ul class="main-menu__dropdown main-menu__dropdown--nested {{ $isExpanded ? 'is-open' : '' }}" id="{{ $submenuId }}" data-submenu data-menu-level="nested">
                    @foreach ($item['children'] as $child)
                        @include($activeTemplate . 'components.header.menu_item', ['item' => $child, 'mode' => 'desktop', 'depth' => $depth + 1])
                    @endforeach
                </ul>
            @else
                <a class="main-menu-sublink {{ $isActive ? 'active' : '' }}" href="{{ $url }}">{{ $label }}</a>
            @endif
        </li>
    @endif
@else
    @if ($hasChildren)
        <li class="side-Nav__item has-collapse {{ $isExpanded ? 'is-open' : '' }}">
            <button type="button" class="side-Nav__trigger {{ $isActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#{{ $submenuId }}" aria-expanded="{{ $isExpanded ? 'true' : 'false' }}" aria-controls="{{ $submenuId }}">
                <span>{{ $label }}</span>
                <i class="fa-solid fa-chevron-down side-Nav__caret"></i>
            </button>
            <div class="collapse side-Nav__collapse {{ $isExpanded ? 'show' : '' }}" id="{{ $submenuId }}">
                <ul class="side-Nav__submenu">
                    @foreach ($item['children'] as $child)
                        @include($activeTemplate . 'components.header.menu_item', ['item' => $child, 'mode' => 'mobile', 'depth' => $depth + 1])
                    @endforeach
                </ul>
            </div>
        </li>
    @else
        <li class="side-Nav__item">
            <a class="side-Nav__link {{ $isActive ? 'active' : '' }}" href="{{ $url }}" aria-current="{{ $isActive ? 'page' : 'false' }}">{{ $label }}</a>
        </li>
    @endif
@endif
