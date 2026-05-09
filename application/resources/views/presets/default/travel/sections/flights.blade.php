<div class="flights-section py-4">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h3 fw-bold mb-4">@lang('Best Airlines We Work With')</h2>
            <div class="airline-slick-slider">
                @php
                    $airlines = [
                        ['name' => __('Emirates'), 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Emirates_logo.svg/1200px-Emirates_logo.svg.png', 'local' => 'assets/images/airlines/emirates.png', 'desc' => __('Fly Better with Emirates.')],
                        ['name' => __('Qatar Airways'), 'logo' => 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7f/Qatar_Airways_Logo.svg/1200px-Qatar_Airways_Logo.svg.png', 'local' => 'assets/images/airlines/qatar-airways.webp', 'desc' => __('Going Places Together.')],
                        ['name' => __('Singapore Airlines'), 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Singapore_Airlines_Logo.svg/1200px-Singapore_Airlines_Logo.svg.png', 'local' => 'assets/images/airlines/singapore-airlines.png', 'desc' => __('A Great Way to Fly.')],
                        ['name' => __('Turkish Airlines'), 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Turkish_Airlines_logo.svg/1200px-Turkish_Airlines_logo.svg.png', 'local' => 'assets/images/airlines/turkish-airlines.png', 'desc' => __('Widen Your World.')],
                        ['name' => __('Etihad Airways'), 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Etihad_Airways_Logo.svg/1200px-Etihad_Airways_Logo.svg.png', 'local' => 'assets/images/airlines/Etihad-Airways.png', 'desc' => __('From Abu Dhabi to the World.')],
                    ];
                @endphp
                @foreach($airlines as $airline)
                <div class="px-2">
                        <div class="airline-card rounded-4 p-4 border shadow-sm bg-white text-center h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 200px; transition: all 0.3s ease;">
                            <div class="airline-card__logo mb-3" style="height: 80px; display: flex; align-items: center; justify-content: center; width: 100%;">
                                @php
                                    $imgSrc = asset($airline['local']);
                                    $parts = preg_split('/\s+/', trim($airline['name']));
                                    $initials = '';
                                    foreach ($parts as $p) {
                                        $initials .= mb_substr($p, 0, 1);
                                        if (mb_strlen($initials) >= 2) break;
                                    }
                                    $initials = strtoupper($initials ?: mb_substr($airline['name'],0,2));
                                @endphp

                                <img src="{{ $imgSrc }}" alt="{{ $airline['name'] }}" 
                                     style="max-height: 60px; max-width: 100%; height: auto; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05)); object-fit: contain;" 
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <div class="logo-fallback" style="display:none; align-items:center; justify-content:center; height:60px; width:100%;">
                                    <div style="background:#f6f7f9; border-radius:8px; padding:6px 12px; color:#222; font-weight:700; font-size:18px; letter-spacing:1px;">
                                        {{ $initials }}
                                    </div>
                                </div>
                            </div>
                            <h4 class="h6 fw-bold mb-1">{{ $airline['name'] }}</h4>
                            <p class="text-muted ultra-small mb-0">{{ $airline['desc'] }}</p>
                        </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row justify-content-center pt-4">
        <div class="col-lg-10">
            <div class="booking-form-wrapper bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                <h3 class="fw-bold mb-4 text-center">@lang('Flight Ticket Booking')</h3>
                <form action="{{ route('service.booking.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="flight">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('From (Origin)')</label>
                                <input type="text" name="origin" class="form-control rounded-pill px-4" style="height: 48px;" placeholder="@lang('Departure City')" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('To (Destination)')</label>
                                <input type="text" name="destination" class="form-control rounded-pill px-4" style="height: 48px;" placeholder="@lang('Arrival City')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Departure Date')</label>
                                <input type="date" name="service_date" class="form-control rounded-pill px-4" style="height: 48px;" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Return Date (Optional)')</label>
                                <input type="date" name="service_end_date" class="form-control rounded-pill px-4" style="height: 48px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold small">@lang('Class')</label>
                                <select name="class" class="form-select rounded-pill px-4" style="height: 48px;">
                                    <option value="economy">@lang('Economy')</option>
                                    <option value="business">@lang('Business')</option>
                                    <option value="first">@lang('First Class')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn--base btn-lg rounded-pill px-5 fw-bold">@lang('Book Now')</button>
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
        $('.airline-slick-slider').slick({
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
                        slidesToShow: 2
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
