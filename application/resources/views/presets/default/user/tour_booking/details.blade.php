@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row justify-content-center gy-4">

        <div class="col-lg-12">
            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6">
                    <div class="tour-card radius--20 position-relative bg--white">
                        <div class="tour-card__thumb">
                            <a href="{{ route('tour.package.details', [slug($bookingDetails?->tour_package->displayTitle()), $bookingDetails?->tour_package->id]) }}">
                                <img class="fit--img"
                                    src="{{ getImage(getFilePath('tourPackageImage') . '/thumb_' . $bookingDetails?->tour_package->TourPackagePrimaryImage->image) }}"
                                    alt="Tour Image">
                            </a>
                        </div>
                        @if ($bookingDetails?->tour_package->discount)
                            <span class="tour-card__tag position-absolute text--white fw--500">
                                -{{ discountShowAmount($bookingDetails?->tour_package->discount) }}% </span>
                        @endif


                        <button
                            class="tour-card__favbtn position-absolute d-flex justify-content-center align-items-center wishlist-btn"
                            data-tour_package_id="{{ $bookingDetails?->tour_package->id }}" data-url="{{ route('user.wishlist.added') }}"
                            onclick="addToWishlist(this)">
                            @php echo isWishlist($bookingDetails?->tour_package) @endphp

                        </button>

                        <div class="tour-card__content">
                            <div class="tour-card__location">
                                <ul class="d-flex justify-content-between align-items-start gap--20">
                                    <li>
                                        <p class="fs--14"><i class="fa-regular fa-compass"></i>
                                            {{ strLimit(($bookingDetails?->tour_package->city ? $bookingDetails?->tour_package->city . ', ' : '') . ($bookingDetails?->tour_package->country ? $bookingDetails?->tour_package->country . ', ' : '') . ($bookingDetails?->tour_package->state ? $bookingDetails?->tour_package->state . ', ' : ''), 35) }}
                                        </p>
                                    </li>
                                    <li class="flex-shrink-0">
                                        <p class="fs--14"><i class="fa-regular fa-clock"></i>
                                            {{ tourVacationCount($bookingDetails?->tour_package->tour_start, $bookingDetails?->tour_package->tour_end) }} @lang('Days')
                                        </p>
                                    </li>
                                </ul>
                            </div>

                            <div class="tour-card__star d-flex align-items-center">
                                <ul class="d-flex">
                                    @php echo calculateIndividualRating($bookingDetails?->tour_package->average_rating) @endphp
                                </ul>
                                <p class="fs--14">({{ $bookingDetails?->tour_package->average_rating }})</p>
                            </div>

                            <a href="{{ route('tour.package.details', [slug($bookingDetails?->tour_package->displayTitle()), $bookingDetails?->tour_package->id]) }}">
                                <h6 class="tour-card__title fs--20 fw--600">
                                    {{ __(strLimit($bookingDetails?->tour_package->displayTitle(), 25)) }}
                                </h6>
                            </a>

                            <div class="tour-card__price-wrap d-flex justify-content-between align-items-center gap--16 flex-wrap">
                                <div class="tour-card__price">
                                    <h6 class="fs--20 fw--600 mb-0 body--font">{{ $bookingDetails?->tour_package->displayCurrencySymbol() }}{{ showAmount($bookingDetails?->tour_package->price) }}<span
                                            class="text--black7 fs--16">/@lang('package')</span></h6>
                                </div>

                                <div class="tour-card__btn-wrap">
                                    <a href="{{ route('tour.package.details', [slug($bookingDetails?->tour_package->displayTitle()), $bookingDetails?->tour_package->id]) }}" class="text--base"><i class="fa-solid fa-arrow-right-long"></i> @lang('View Details')</a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="tour-card radius--20 position-relative bg--white">
                        <h5 class="mb-20">@lang('Booking Details')</h5>
                        <ul class="list-group mb-3 gap--12">
                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Full Name'):
                                <span class="fw--500">{{ $bookingDetails->user?->fullname }}</span>
                            </li>

                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Email'):
                                <span class="fw--500">{{ $bookingDetails->user?->email }}</span>
                            </li>

                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Phone'):
                                <span class="fw--500">{{ $bookingDetails->user?->mobile }}</span>
                            </li>

                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Number of Seats'):
                                <span class="fw--500">{{ $bookingDetails->seat }}</span>
                            </li>

                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Total Price'):
                                <span
                                    class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails->price) }}</span>
                            </li>
                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Get Discount'):
                                <span class="fw--500 badge badge--success">{{ $bookingDetails->discount ?? 0 }}%</span>
                            </li>
                            @if ($bookingDetails?->user_proposal_date != $bookingDetails?->tour_package?->tour_start)
                                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                    @lang('User Proposal Date'):
                                    <span
                                        class="fw--500">{{ showDateTime($bookingDetails?->user_proposal_date, 'd-m-Y h:i A') }}</span>
                                </li>
                            @endif

                            <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                                @lang('Payment Status'):
                                <span class="fw--500">@php
                                    echo $bookingDetails->statusPaymentBadge();
                                @endphp</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
