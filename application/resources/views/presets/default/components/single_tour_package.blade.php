@forelse ($tourPackages as $item)
    @php
        $packagePriceText = $item->priceRangeText() ?: ($item->displayCurrencySymbol() . showAmount(showTourPackageCalculateDiscount($item->price, $item->discount)));
        $primaryImage = optional($item->TourPackagePrimaryImage)->image;
    @endphp
    <div
        class="col-md-4 col-sm-6">
        <div class="tour-card radius--20 position-relative bg--white">
            <div class="tour-card__thumb">
                <a href="{{ route('tour.package.details', [slug($item->displayTitle()), $item->id]) }}">
                    <img class="fit--img"
                        src="{{ $primaryImage ? getImage(getFilePath('tourPackageImage') . '/thumb_' . $primaryImage) : asset('assets/images/general/default.png') }}"
                        alt="Tour Image">
                </a>
            </div>
            @if ($item->discount)
                <span class="tour-card__tag position-absolute text--white fw--500">
                    -{{ discountShowAmount($item->discount) }}% </span>
            @endif


            <button
                class="tour-card__favbtn position-absolute d-flex justify-content-center align-items-center wishlist-btn"
                data-tour_package_id="{{ $item->id }}" data-url="{{ route('user.wishlist.added') }}"
                onclick="addToWishlist(this)">
                @php echo isWishlist($item) @endphp
            </button>

            <div class="tour-card__content">
                <div class="tour-card__location">
                    <ul class="d-flex justify-content-between align-items-start gap--20">
                        <li>
                            @php
                                $locationParts = array_filter([$item->city, $item->state, $item->country]);
                                $location = implode(', ', $locationParts);
                            @endphp
                            @if($location)
                            <p title="{{ $location }}" class="fs--14">
                                <i class="fa-regular fa-compass"></i>
                                {{ strLimit($location, 18) }}
                            </p>
                        @endif
                        </li>
                        <li class="flex-shrink-0">
                            <p class="fs--14"><i class="fa-regular fa-clock"></i>
                                {{ tourVacationCount($item->tour_start, $item->tour_end) }} @lang('Days')
                            </p>
                        </li>
                    </ul>
                </div>

                <div class="tour-card__star d-flex align-items-center">
                    <ul class="d-flex">
                        @php echo calculateIndividualRating($item->average_rating) @endphp
                    </ul>
                    <p class="fs--14">({{ $item->average_rating }})</p>
                </div>

                <a href="{{ route('tour.package.details', [slug($item->displayTitle()), $item->id]) }}">
                    <h6 class="tour-card__title fs--20 fw--600" title="{{ $item->displayTitle() }}">
                        {{ __(strLimit($item->displayTitle(), 20)) }}
                    </h6>
                </a>

                <div class="tour-card__price-wrap d-flex justify-content-between align-items-center gap--16 flex-wrap">
                    <div class="tour-card__price">
                        <h6 class="fs--20 fw--600 mb-0 body--font">{{ $packagePriceText }}<span
                                class="text--black7 fs--16">/@lang('package')</span></h6>
                    </div>

                    <div class="tour-card__btn-wrap">
                        <a href="{{ route('tour.package.details', [slug($item->displayTitle()), $item->id]) }}"
                            class="text--base"><i class="fa-solid fa-arrow-right-long"></i> @lang('Book')</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@empty
    <p class="text-center">@lang('No data found')</p>
@endforelse

<div class="mt-4">
    @if (!request()->ajax() && Route::is('browse') && $tourPackages->hasPages())
        @if ($tourPackages->hasPages())
            <div class="row mt-3">
                <div class="col-lg-12 justify-content-end d-flex">
                    {{ $tourPackages->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
