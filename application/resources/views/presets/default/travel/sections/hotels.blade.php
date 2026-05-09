<div class="hotels-section py-4">
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 fw-bold mb-0">@lang('World\'s Most Luxurious Escapes')</h2>
                <a href="#" class="text--base fw-bold text-decoration-none small">@lang('View All Destinations')</a>
            </div>
            
            <div class="hotel-slick-slider">
                @php
                    $hotels = [
                        ['name' => __('Burj Al Arab Jumeirah'), 'location' => __('Dubai, UAE'), 'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.5', 'rating_text' => __('Exceptional'), 'reviews' => '12,450'],
                        ['name' => __('The Plaza Hotel'), 'location' => __('New York, USA'), 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.1', 'rating_text' => __('Wonderful'), 'reviews' => '8,200'],
                        ['name' => __('Marina Bay Sands'), 'location' => __('Singapore'), 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.3', 'rating_text' => __('Exceptional'), 'reviews' => '45,000'],
                        ['name' => __('Ritz Paris'), 'location' => __('Paris, France'), 'image' => 'https://images.unsplash.com/photo-1541976844346-f18aeac57b06?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.7', 'rating_text' => __('Exceptional'), 'reviews' => '3,100'],
                        ['name' => __('Atlantis The Royal'), 'location' => __('Dubai, UAE'), 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.4', 'rating_text' => __('Wonderful'), 'reviews' => '5,420'],
                        ['name' => __('Mandarin Oriental'), 'location' => __('Bangkok, Thailand'), 'image' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.6', 'rating_text' => __('Exceptional'), 'reviews' => '6,700'],
                        ['name' => __('Four Seasons George V'), 'location' => __('Paris, France'), 'image' => 'https://images.unsplash.com/photo-1549468057-5b7fa1a41d7a?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.8', 'rating_text' => __('Exceptional'), 'reviews' => '2,900'],
                        ['name' => __('The Savoy'), 'location' => __('London, UK'), 'image' => 'https://images.unsplash.com/photo-1521783988139-89397d761dce?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.2', 'rating_text' => __('Wonderful'), 'reviews' => '4,500'],
                        ['name' => __('Aman Tokyo'), 'location' => __('Tokyo, Japan'), 'image' => 'https://images.unsplash.com/photo-1503174971373-b1f69850bded?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.5', 'rating_text' => __('Exceptional'), 'reviews' => '1,200'],
                        ['name' => __('Armani Hotel'), 'location' => __('Dubai, UAE'), 'image' => 'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.0', 'rating_text' => __('Wonderful'), 'reviews' => '3,800'],
                        ['name' => __('Belmond Hotel Cipriani'), 'location' => __('Venice, Italy'), 'image' => 'https://images.unsplash.com/photo-1535827841776-24afc1e255ac?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.4', 'rating_text' => __('Exceptional'), 'reviews' => '1,800'],
                        ['name' => __('Claridge\'s'), 'location' => __('London, UK'), 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.6', 'rating_text' => __('Exceptional'), 'reviews' => '2,500'],
                        ['name' => __('Hotel du Cap-Eden-Roc'), 'location' => __('Antibes, France'), 'image' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.7', 'rating_text' => __('Exceptional'), 'reviews' => '950'],
                        ['name' => __('Raffles Singapore'), 'location' => __('Singapore'), 'image' => 'https://images.unsplash.com/photo-1596436889106-be35e843f974?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.5', 'rating_text' => __('Exceptional'), 'reviews' => '5,600'],
                        ['name' => __('Taj Lake Palace'), 'location' => __('Udaipur, India'), 'image' => 'https://images.unsplash.com/photo-1590073844006-33379778ae09?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.3', 'rating_text' => __('Wonderful'), 'reviews' => '4,200'],
                        ['name' => __('Waldorf Astoria'), 'location' => __('Ithaafushi, Maldives'), 'image' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.8', 'rating_text' => __('Exceptional'), 'reviews' => '2,100'],
                        ['name' => __('Rosewood Hong Kong'), 'location' => __('Hong Kong'), 'image' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.4', 'rating_text' => __('Exceptional'), 'reviews' => '1,500'],
                        ['name' => __('Villa d\'Este'), 'location' => __('Lake Como, Italy'), 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.6', 'rating_text' => __('Exceptional'), 'reviews' => '1,300'],
                        ['name' => __('The Beverly Hills Hotel'), 'location' => __('California, USA'), 'image' => 'https://images.unsplash.com/photo-1560662105-57f8ad6ae2d1?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.2', 'rating_text' => __('Wonderful'), 'reviews' => '6,800'],
                        ['name' => __('Badrutt\'s Palace'), 'location' => __('St. Moritz, Switzerland'), 'image' => 'https://images.unsplash.com/photo-1551632436-cbf8dd35adfa?auto=format&fit=crop&q=80&w=1600', 'rating' => '9.5', 'rating_text' => __('Exceptional'), 'reviews' => '2,400'],
                    ];
                @endphp
                @foreach($hotels as $hotel)
                <div class="px-2">
                    <div class="booking-hotel-card rounded-3 overflow-hidden border-0 shadow-sm bg-white h-100 position-relative">
                        <button class="wishlist-btn position-absolute top-0 end-0 m-3 border-0 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; z-index: 2;">
                            <i class="lar la-heart fs-5"></i>
                        </button>
                        
                        <div class="card-media ratio ratio-1x1 overflow-hidden">
                            <img src="{{ $hotel['image'] }}" alt="{{ $hotel['name'] }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        
                        <div class="card-body p-3 d-flex flex-column">
                            <div class="hotel-type-stars d-flex align-items-center gap-1 mb-1">
                                <span class="text-muted" style="font-size: 0.65rem;">@lang('Resort')</span>
                                <div class="stars-wrap text-warning" style="font-size: 1rem;">
                                    <i class="las la-star"></i><i class="las la-star"></i><i class="las la-star"></i><i class="las la-star"></i><i class="las la-star"></i>
                                </div>
                                <span class="d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-1 ms-1" style="width: 14px; height: 14px; font-size: 0.6rem;">
                                    <i class="las la-thumbs-up"></i>
                                </span>
                            </div>
                            <h5 class="hotel-title fw-bold mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.2; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $hotel['name'] }}</h5>
                            <p class="hotel-location text-muted mb-2" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 0;">{{ $hotel['location'] }}</p>
                            
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-1 px-1 py-1 fw-bold" style="font-size: 0.7rem; background-color: #003580 !important;">{{ $hotel['rating'] }}</span>
                                    <div class="rating-info">
                                        <p class="mb-0 fw-bold text-dark" style="font-size: 0.7rem; line-height: 1;">{{ $hotel['rating_text'] }}</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ $hotel['reviews'] }} @lang('reviews')</p>
                                    </div>
                                </div>
                                <a href="#booking-form" class="btn btn-sm btn--base rounded-pill fw-bold px-4 py-1 shadow-sm small" style="font-size: 0.75rem; min-width: 80px;">
                                    @lang('Book')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row justify-content-center pt-4" id="booking-form">
        <div class="col-lg-10">
            <div class="booking-form-wrapper bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                <h3 class="fw-bold mb-4 text-center">@lang('Custom Hotel Booking Request')</h3>
                <form action="{{ route('service.booking.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="hotel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Hotel Name / Preference')</label>
                                <input type="text" name="title" class="form-control rounded-pill px-4" style="height: 48px;" placeholder="@lang('e.g. Burj Al Arab, The Plaza')" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Destination City')</label>
                                <input type="text" name="destination" class="form-control rounded-pill px-4" style="height: 48px;" placeholder="@lang('Where do you want to stay?')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Check-in Date')</label>
                                <input type="date" name="service_date" class="form-control rounded-pill px-4" style="height: 48px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Check-out Date')</label>
                                <input type="date" name="service_end_date" class="form-control rounded-pill px-4" style="height: 48px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Guests')</label>
                                <input type="number" name="guests" min="1" class="form-control rounded-pill px-4" style="height: 48px;" value="1">
                            </div>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn--base btn-lg rounded-pill px-5 fw-bold">@lang('Submit Request')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function(){
        // Smooth scroll for Book buttons
        $('a[href="#booking-form"]').on('click', function(e) {
            e.preventDefault();
            const target = $('#booking-form');
            if (target.length) {
                const headerHeight = $('.header').outerHeight() || 80;
                const offset = target.offset().top - headerHeight - 30; // 30px extra padding
                $('html, body').animate({
                    scrollTop: offset
                }, 800);
            }
        });

        $('.hotel-slick-slider').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            rtl: {{ is_rtl() ? 'true' : 'false' }},
            arrows: true,
            prevArrow: '<button type="button" class="slick-prev"><i class="las la-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="las la-angle-right"></i></button>',
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2.2
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1.2
                    }
                }
            ]
        });
    });
</script>
@endpush
