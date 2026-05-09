@php
    $membershipContent = getContent('membership_section.content', true);
    $plans = App\Models\MembershipPlan::where('status', 1)->orderBy('id', 'asc')->get();
    $planPlaceholder = asset($activeTemplateTrue . 'images/shape/flyimg3.png');
@endphp

<section class="membership-section py-100 bg--white position-relative">
    <div class="container">
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-9">
                <div class="section-content text-start">
                    <h6
                        class="heading third--font text-start fs--32 fw--700 text--base mb-0 wow animate__animated animate__fadeInUp"
                        data-wow-delay="0.05s">
                        @lang('Recent memberships')
                    </h6>
                    <h2 class="section-main-title title mb-0 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                        data-splitting data-wow-delay="0.2s">@lang('Join Our Amazing System')</h2>
                    <p class="section-subtitle subtitle mt-2 fs--16 fw--400 wow animate__animated animate__fadeInUp"
                        data-wow-delay="0.3s">
                        @lang('Upgrade your travel experience with our premium membership plans.')<br>
                        @lang('Unlock exclusive rewards, priority support, and special discounts tailored just for you.')
                    </p>
                </div>
            </div>
            <div class="col-md-3 {{ is_rtl() ? 'text-md-start' : 'text-md-end' }}">
                <div class="wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                    <a href="{{ route('public.membership.card') }}" class="btn btn--base btn--lg pills px-4">
                        @lang('Explore Our Points System')
                    </a>
                </div>
            </div>
        </div>

        <div class="membership-slider wow animate__animated animate__fadeInUp" data-wow-delay="0.5s">
            @forelse ($plans as $plan)
                @php
                    $isRtl = is_rtl();
                    $planName = $isRtl && $plan->name_ar ? $plan->name_ar : $plan->name;
                    if ($isRtl && empty($plan->name_ar) && $plan->name && preg_match('/vip/i', $plan->name)) {
                        $planName = 'الطيار في اي بي';
                    }
                    $planImage = null;
                    if ($plan->cover_image) {
                        $planImage = asset(getFilePath('membershipPlanCover') . '/' . $plan->cover_image);
                    } elseif ($plan->image_file) {
                        $planImage = asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file);
                    }
                    $planImage = $planImage ?? $planPlaceholder;
                @endphp
                <div class="slider-item">
                    <div class="custom-plan-card radius--20 overflow-hidden h-100 bg--white border">
                        <div class="card-image ratio ratio-4x3">
                            <img class="w-100 h-100 object-fit-cover" src="{{ $planImage }}"
                                alt="{{ $planName }}">
                        </div>
                        <div class="card-footer-content">
                            <div class="info">
                                <span class="label">@lang('MEMBERSHIP')</span>
                                <h5 class="name mb-0">{{ $planName }}</h5>
                            </div>
                            <a href="{{ route('public.membership.details.show', $plan->id) }}"
                                class="arrow-btn flex-shrink-0">
                                <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center w-100 mb-0 text-muted">@lang('No membership plans available right now.')</p>
            @endforelse
        </div>
    </div>
</section>

@push('style')
    <style>
        .section-main-title {
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 800;
            color: #222;
            margin-top: 0.35rem;
        }

        .membership-slider {
            margin: 2rem -12px 0;
        }

        .membership-slider .slider-item {
            padding: 0 12px;
        }

        .custom-plan-card {
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.06);
            border-color: #eee !important;
        }

        .card-footer-content {
            padding: 1.1rem 1.15rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            background: #fff;
        }

        .card-footer-content .label {
            display: block;
            font-size: 11px;
            color: #999;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .card-footer-content .name {
            margin: 0.15rem 0 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: #222;
        }

        .arrow-btn {
            width: 46px;
            height: 46px;
            background: #f0f4f8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 1.35rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .arrow-btn:hover {
            background: #2266cc;
            color: #fff;
        }

        .membership-slider .slick-dots {
            bottom: -44px;
        }

        .membership-slider .slick-dots li button:before {
            color: #2266cc;
            opacity: 0.35;
        }

        .membership-slider .slick-dots li.slick-active button:before {
            opacity: 1;
        }
    </style>
@endpush

@push('script')
    <script>
        (function() {
            $(document).ready(function() {
                var $slider = $('.membership-slider');
                if ($slider.length && $slider.find('.slider-item').length) {
                    $slider.slick({
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true,
                        arrows: false,
                        autoplay: true,
                        autoplaySpeed: 4000,
                        rtl: {{ is_rtl() ? 'true' : 'false' }},
                        responsive: [{
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 2,
                                }
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 1,
                                }
                            }
                        ]
                    });
                }
            });
        })();
    </script>
@endpush
