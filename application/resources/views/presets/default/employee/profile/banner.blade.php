
@php
    $employeeProfile = $employee ?? $Employee ?? null;
@endphp

<div class="owner--profile bg--img base--radius mb-40">
    <div class="profile--banner radius--16 overflow-hidden">
        <img class="fit--img"
            src="{{ getImage(getFilePath('coverImage') . '/' . ($employeeProfile?->cover_image ?? ''), getFileSize('coverImage')) }}"
            alt="@lang('Image')">
    </div>
    <div class="profile--info d-flex flex-column justify-content-center align-items-center gap--16 mb-4">
        <div class="thumb--wrap radius--50 overflow-hidden">
            <img class="fit--img radius--50 overflow-hidden"
                src="{{ getImage(getFilePath('EmployeeProfile') . '/' . ($employeeProfile?->image ?? ''), getFileSize('EmployeeProfile')) }}"
                alt="@lang('Image')">
        </div>
        <h6 class="name fs--26 fw--700 mb-0">{{ $employeeProfile?->fullname ?? '' }}</h6>
        <p class="">@lang('Member since at') {{ showDateTime($employeeProfile?->created_at, 'M Y') }}</p>
    </div>

    <div class="auth--badge d-flex align-items-center justify-content-center gap--8 flex-wrap mb-4">
        <!-- Facebook Share Button -->
        <a href="https://www.facebook.com/share.php?u={{ route('seller.profile', ['id' => $employeeProfile?->id, 'slug' => slug($employeeProfile?->username ?? '')]) }}&title={{ slug($employeeProfile?->name ?? '') }}" class="btn btn--dark" target="_blank">
            <i class="fab fa-facebook-f"></i>
        </a>

        <!-- X (Twitter) Share Button -->
        <a href="https://twitter.com/intent/tweet?text={{ slug($employeeProfile?->name ?? '') }}&url={{ route('seller.profile', ['id' => $employeeProfile?->id, 'slug' => slug($employeeProfile?->username ?? '')]) }}" class="btn btn--dark" target="_blank">
            <i class="fab fa-twitter"></i>
        </a>

        <!-- Pinterest Share Button -->
        <a href="https://pinterest.com/pin/create/button/?url={{ route('seller.profile', ['id' => $employeeProfile?->id, 'slug' => slug($employeeProfile?->username ?? '')]) }}&media={{ getImage(getFilePath('EmployeeProfile') . '/' . ($employeeProfile?->image ?? ''), getFileSize('EmployeeProfile')) }}&description={{ slug($employeeProfile?->name ?? '') }}" class="btn btn--dark" target="_blank">
            <i class="fab fa-pinterest-p"></i>
        </a>
    </div>

    <ul class="profile-menu--wrap d-flex justify-content-center align-items-center flex-wrap gap--40">
        <li class="item"> <a class="active" href="javascript:void(0)" data-route="{{ route('seller.artwork.filter') }}"  id="artwork-link">@lang('Artwork')</a></li>
        <li class="item"> <a href="javascript:void(0)" data-route="{{ route('seller.collection.filter') }}" id="collection-link">@lang('Collection')</a></li>
    </ul>

</div>


