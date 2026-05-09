@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'components.hero', [
        'heroTitle' => __('Ready to Explore?'),
        'heroHeading' => __('Unforgettable Tour Packages'),
        'heroSubHeading' => __('Discover our handpicked experiences tailored for you.'),
    ])
    <section class="explore-section section--bg py-100">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-12 mb-3 d-flex justify-content-between align-items-center gap--16 flex-wrap">
                    <div class="filter-btn--wrap">
                        <button class="text--base fw--600 fs--20"><i class="fa-solid fa-sliders"></i> @lang('Filter')</button>
                    </div>

                    <div class="item-btn--wrap d-flex align-items-center gap--12 flex-wrap">

                        <i class="fa-solid fa-circle-notch fa-spin d-none" id="spinn"></i>

                        <a href="{{ route('browse') }}"
                            class="btn btn-outline--base pills {{ request()->search == '' ? 'active' : '' }}">@lang('All')</a>
                        <a href="{{ route('browse') }}?search=new"
                            class="btn btn-outline--base pills {{ request()->search == 'new' ? 'active' : '' }}">@lang('New Item')</a>
                        <a href="{{ route('browse') }}?search=rating"
                            class="btn btn-outline--base pills {{ request()->search == 'rating' ? 'active' : '' }}">@lang('Best Rated')</a>
                        <a href="{{ route('browse') }}?search=trending"
                            class="btn btn-outline--base pills {{ request()->search == 'trending' ? 'active' : '' }}">@lang('Trend')</a>
                    </div>
                </div>


                <div class="explore-item--wrap">
                    <div class="row justify-content-center mb-2 gy-4">

                        <div class="col-md-3 position-relative">
                            <div class="filter--box base--radius position-sticky">
                                @include($activeTemplate . 'components.left_side_search')
                            </div>
                        </div>


                        <div class="col-lg-9">
                            <div class="row gy-4 tour-content">
                                @include($activeTemplate . 'components.single_tour_package')
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @if ($sections != null)
        @foreach (json_decode($sections) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            'use strict'
            $(".datepicker-active").datepicker({
                minDate: new Date(),
                timepicker: true,
                timeFormat: ', hh:ii aa'

            });

        });
    </script>
    <script>
        // rating set
        $(document).ready(function() {
            'use strict'

            var initialRating = parseInt($('#rating').val());
            if (initialRating > 0) {
                updateStars(initialRating);
            }

            $('.rating-stars i').on('click', function() {
                var rating = parseInt($(this).data('rating'));
                $('#rating').val(rating);
                updateStars(rating);
            });

            $('#rating').on('input', function() {
                var rating = $(this).val();
                updateStars(rating);
            });

            function updateStars(rating) {
                var stars = $('.rating-stars i');
                stars.removeClass('fas').addClass('far');
                stars.each(function(index) {
                    if (index < rating) {
                        $(this).removeClass('far').addClass('fas');
                    }
                });
            }

        });
        // end rating set
    </script>


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
@endpush
