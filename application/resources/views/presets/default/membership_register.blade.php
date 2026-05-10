@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="membership-register-section py-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-heading text-center mb-50">
                    <h2 class="section-heading__title">{{ __("Join the Altayar VIP Family") }}</h2>
                    <p class="section-heading__desc">{{ __("Registration is your first step to getting an exceptional travel experience and managing your bookings with ease and security.") }}</p>
                </div>

                <div class="row gy-4">
                    <div class="col-md-3">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-user-check fa-2x text--base"></i>
                            </div>
                            <h5 class="benefit-card__title">{{ __("Account Management") }}</h5>
                            <p class="benefit-card__desc small">{{ __("Private dashboard to manage your data and travel preferences.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-history fa-2x text--base"></i>
                            </div>
                            <h5 class="benefit-card__title">{{ __("Booking History") }}</h5>
                            <p class="benefit-card__desc small">{{ __("Quick access to all your past and future bookings in one place.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-heart fa-2x text--base"></i>
                            </div>
                            <h5 class="benefit-card__title">{{ __("Wishlist") }}</h5>
                            <p class="benefit-card__desc small">{{ __("Save your favorite offers and destinations to return to them later.") }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-card text-center p-4 h-100 shadow-sm border-radius-10">
                            <div class="benefit-card__icon mb-3">
                                <i class="fas fa-shield-alt fa-2x text--base"></i>
                            </div>
                            <h5 class="benefit-card__title">{{ __("Security and Speed") }}</h5>
                            <p class="benefit-card__desc small">{{ __("Complete payments and bookings with super speed with full protection for your data.") }}</p>
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
        transition: all 0.3s ease;
        border: 1px solid #f1f1f1;
    }
    .benefit-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
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
    .small {
        font-size: 0.85rem;
    }
</style>
@endsection
