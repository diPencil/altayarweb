@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="membership-intro-section py-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-heading text-center mb-50">
                    <h2 class="section-heading__title">{{ __("Membership Benefits at Altayar VIP") }}</h2>
                    <p class="section-heading__desc">{{ __("Join a world of luxury and exclusive benefits designed specifically to meet your needs and aspirations on every trip.") }}</p>
                </div>

                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-percent fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Exclusive Discounts") }}</h4>
                            <p class="benefit-card__desc">{{ __("Enjoy discounts of up to 50% on the best hotels and flights around the world.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-crown fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Priority Booking") }}</h4>
                            <p class="benefit-card__desc">{{ __("Be the first to book limited offers and most requested destinations with priority customer service.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-wallet fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Points and Rewards") }}</h4>
                            <p class="benefit-card__desc">{{ __("Collect points with every booking and exchange them for free trips or special extra services.") }}</p>
                        </div>
                    </div>
                </div>

                <div class="intro-cta text-center mt-60">
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('user.register') }}" class="btn btn--base btn--lg pills">
                            {{ __("Register Account") }} <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </a>
                        <a href="{{ route('public.membership.card') }}" class="btn btn--base btn--lg pills">
                            {{ __("Check Memberships") }} <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .benefit-card {
        background: #fff;
        transition: transform 0.3s ease;
        border: 1px solid #eee;
    }
    .benefit-card:hover {
        transform: translateY(-10px);
        border-color: var(--base-color);
    }
    .text--base {
        color: var(--base-color);
    }
    .mt-60 {
        margin-top: 60px;
    }
    .py-80 {
        padding-top: 80px;
        padding-bottom: 80px;
    }
</style>
@endsection
