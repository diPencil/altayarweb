@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="packages-intro-section py-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-heading text-center mb-50">
                    <h2 class="section-heading__title">{{ __("Permanent Travel Packages & VIP Membership") }}</h2>
                    <p class="section-heading__desc">{{ __("Explore our permanent and continuous offers throughout the year, and know why Altayar VIP membership is your best and most saving option for your next trips.") }}</p>
                </div>

                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-globe fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Global Destinations") }}</h4>
                            <p class="benefit-card__desc">{{ __("Permanent travel packages for more than 100 destinations around the world at competitive prices that do not change.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-gem fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Membership Option") }}</h4>
                            <p class="benefit-card__desc">{{ __("Subscribing to membership provides you with extra features and instant discounts that make your trip economical and comfortable.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-headset fa-3x text--base"></i>
                            </div>
                            <h4 class="benefit-card__title">{{ __("Continuous Support") }}</h4>
                            <p class="benefit-card__desc">{{ __("Dedicated customer service for VIP members to help you plan and modify your bookings at any time.") }}</p>
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
