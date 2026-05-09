@php
    $popularTourContent = getContent('popular_tour.content', true);
    $tourPackagesQuery = App\Models\TourPackage::with('TourPackagePrimaryImage', 'wishlists')
        ->whereIn('status', [1, 2, 3]);

    $tourPackages = (clone $tourPackagesQuery)
        ->orderByDesc('view')
        ->orderByDesc('id')
        ->limit(6)
        ->get();

    if ($tourPackages->isEmpty()) {
        $tourPackages = (clone $tourPackagesQuery)
            ->active()
            ->orderByDesc('id')
            ->limit(6)
            ->get();
    }
@endphp
<section class="feature--section position-relative py-100">
    <div class="bg--element position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/dash-line.png') }}" alt="image">
    </div>

    <div class="bg--element-two position-absolute top_image_bounce">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg3.png') }}" alt="image">
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($popularTourContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp">
                            {{ __($popularTourContent->data_values->heading) }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($popularTourContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center gy-4">
            @forelse ($tourPackages as $item)
    @php
        $primaryImage = optional($item->TourPackagePrimaryImage)->image;
    @endphp
    <div class="col-md-4 col-sm-6">
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
                                {{ strLimit($location, 32) }}
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
                    <h6 class="tour-card__title fs--20 fw--600" title="{{ $item->title }}">
                        {{ __(strLimit($item->displayTitle(), 32)) }}
                    </h6>
                </a>

                <div class="tour-card__price-wrap d-flex justify-content-between align-items-center gap--16 flex-wrap">
                    <div class="tour-card__price">
                        <h6 class="fs--20 fw--600 mb-0 body--font">{{ $general->cur_sym }}{{ $item->price }}<span
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
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ url('/more-travel/packages') }}" class="btn btn--base btn--lg pills px-4">
                    {{ is_rtl() ? 'عرض كل الباقات' : 'View All Packages' }}
                    <i class="fa-solid fa-arrow-right-long ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    function addToWishlist(element) {
        "use strict";

        var isAddingToWishlist = false;
        var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        if (!isAddingToWishlist && isLoggedIn) {
            isAddingToWishlist = true;
            var tourPackageId = $(element).data('tour_package_id');
            var url = $(element).data('url');

            $.ajax({
                url: url,
                type: 'get',
                data: {
                    tourPackageId: tourPackageId,
                },
                complete: function() {
                    isAddingToWishlist = false;
                },
                success: function(response) {
                    if (response.hasOwnProperty('message')) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        var heartIcon = $(element).find('i');
                        if (response.message.includes('added')) {
                            heartIcon.removeClass('far fa-heart').addClass('fas fa-heart text--base');
                        } else if (response.message.includes('removed')) {
                            heartIcon.removeClass('fas fa-heart text--base').addClass('far fa-heart');
                        }
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: response.error
                        });
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Error occurred while updating the wishlist.';
                    Toast.fire({
                        icon: 'error',
                        title: errorMessage
                    });
                }
            });
        } else if (!isLoggedIn) {
            var errorMessage = 'Please log in to manage your wishlist.';
            Toast.fire({
                icon: 'warning',
                title: errorMessage
            });
        }
    }
</script>
