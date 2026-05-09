@php
    $href = $href ?? '#';
    $activeClass = !empty($active) ? 'active' : '';
@endphp

<a href="{{ $href }}"
    class="location__card radius--20 overflow-hidden position-relative {{ $activeClass }}">
    <div class="location__card-thumb position-relative w--100 h--100">
        <img class="fit--img" src="{{ getImage(getFilePath('location') . '/' . $item->image) }}" loading="lazy" decoding="async"
            alt="{{ $item->displayName() }}">
    </div>

    <div class="location__card-content position-absolute w--100 d-flex justify-content-between align-items-center">
        <div class="content">
            <h6 class="title text--white fs--24 mb-1">
                <i class="fa-solid fa-location-dot"></i>
                {{ $item->displayName() }}
            </h6>
            @if($item->count)
                <p class="text--white mb-1 fw-bold small" style="opacity: 0.9;">{{ $item->displayCountLabel() }}</p>
            @endif
            <p class="text--white custom-location mb-0 small" style="opacity: 0.7;">{{ $item->displayLocation() }}</p>
        </div>
        <span class="btn circle"><i class="fa-solid fa-arrow-up-long"></i></span>
    </div>
</a>