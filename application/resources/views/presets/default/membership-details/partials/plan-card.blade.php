@php
    $isRtl = is_rtl();
    $planName = $isRtl && $plan->name_ar ? $plan->name_ar : $plan->name;
    $planDescription = $isRtl && $plan->description_ar ? $plan->description_ar : $plan->description;
    
    // Explicitly prioritize cover_image for the 6 cards as requested
    $planImage = null;
    if ($plan->cover_image) {
        $planImage = asset(getFilePath('membershipPlanCover') . '/' . $plan->cover_image);
    } elseif ($plan->image_file) {
        $planImage = asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file);
    }
    
    $planUrl = route('public.membership.details.show', $plan->id);
@endphp

<a href="{{ $planUrl }}" class="membership-plan-card base--card radius--24 h-100 d-flex flex-column text-decoration-none">
    <div class="membership-plan-card__media">
        @if($planImage)
            <img src="{{ $planImage }}" alt="{{ $planName }}">
        @else
            <div class="membership-plan-card__placeholder">
                <i class="las la-crown"></i>
            </div>
        @endif
    </div>

    <div class="membership-plan-card__body d-flex flex-column flex-grow-1">
        <h5 class="membership-plan-card__title mb-2">{{ $planName }}</h5>
        <p class="membership-plan-card__subtitle mb-0">{{ $planDescription }}</p>

        <div class="mt-auto pt-4 d-flex align-items-center justify-content-between gap-3">
            <span class="membership-plan-card__cta-label">@lang('View Details')</span>
            <span class="membership-plan-card__cta-icon"><i class="las la-arrow-right"></i></span>
        </div>
    </div>
</a>
