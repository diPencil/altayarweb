@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="payment-status-section py-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        @if($status == 'success')
                            <div class="status-icon mb-4 text-success">
                                <i class="fas fa-check-circle fa-5x"></i>
                            </div>
                            <h2 class="mb-3">@lang('Payment Completed')</h2>
                        @else
                            <div class="status-icon mb-4 text-danger">
                                <i class="fas fa-times-circle fa-5x"></i>
                            </div>
                            <h2 class="mb-3">@lang('Payment Error')</h2>
                        @endif

                        <p class="lead mb-4">{{ $message }}</p>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i> @lang('Back to Home')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
    .payment-status-section {
        background-color: #f8f9fa;
        min-height: 60vh;
        display: flex;
        align-items: center;
    }
    .status-icon i {
        animation: scaleIn 0.5s ease-out;
    }
    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
@endpush
