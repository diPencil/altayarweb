@extends($activeTemplate . 'layouts.frontend')
@section('content')
<section class="py-5">
    <div class="container py-2 py-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="base--card p-4 p-xl-5" style="border-radius: 28px; box-shadow: 0 22px 60px rgba(15, 23, 42, .10);">
                    <div class="text-center mb-4">
                        <h2 class="mb-3 fw-bold text-dark">
                            {{ __('Pay Online') }}
                        </h2>
                        <p class="lead text-muted mx-auto" style="max-width: 700px; font-size: 1.1rem; line-height: 1.6;">
                            @if(session('lang') == 'ar' || is_rtl())
                                يمكنك إتمام الدفع بأمان من خلال فواتيرك. إذا لم تظهر صفحة الدفع، برجاء استخدام الزر بالأسفل لفتحها مباشرة.
                            @else
                                You can complete your payment securely through Fawaterk. If the payment page does not load, please use the button below to open it directly.
                            @endif
                        </p>
                        
                        <div class="mt-3 mb-4">
                            <a href="https://app.fawaterk.com/ec/altayarvip-e-payment" target="_blank" class="btn btn--base btn--lg pills">
                                <i class="fa-solid fa-square-arrow-up-right me-1"></i>
                                @if(session('lang') == 'ar' || is_rtl())
                                    فتح صفحة الدفع الآمنة
                                @else
                                    Open secure payment page
                                  @endif
                            </a>
                        </div>
                    </div>

                    <div class="iframe-container position-relative mb-4" style="border-radius: 16px; overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.12); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);">
                        <iframe src="https://app.fawaterk.com/ec/altayarvip-e-payment" 
                                title="AltayarVIP Fawaterk Payment Page"
                                style="width: 100%; min-height: 750px; border: none; display: block;" 
                                allowfullscreen>
                        </iframe>
                    </div>

                    <div class="text-center mt-3">
                        <p class="text-muted small">
                            @if(session('lang') == 'ar' || is_rtl())
                                إذا لم تظهر استمارة الدفع، افتحها مباشرة من زر الدفع الآمن.
                            @else
                                If the payment form does not appear, open it directly from the secure payment button.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
