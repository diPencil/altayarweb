@extends($activeTemplate.'layouts.frontend')
@section('content')

@php
    $hubVals = $sections->data_values ?? null;
    $hubBadge = policy_hub_cms_field($hubVals, 'title');
    $hubHeading = policy_hub_cms_field($hubVals, 'heading');
    $hubSub = policy_hub_cms_field($hubVals, 'sub_heading');
@endphp

<section class="policy-index-section py-100 section--bg">
    <div class="container">
        <div class="text-center mb-5">
            <span class="subtitle text--base bg--white px-3 py-1 radius--5 mb-2 d-inline-block">
                {{ $hubBadge !== '' ? $hubBadge : __('policy_hub.badge') }}
            </span>
            <h2 class="section-title fs--48 fw--700 mb-3">{{ $hubHeading !== '' ? $hubHeading : __('policy_hub.heading') }}</h2>
            <p class="mx-auto" style="max-width: 700px;">
                {{ $hubSub !== '' ? $hubSub : __('policy_hub.sub') }}
            </p>
        </div>

        <div class="row gy-4 justify-content-center">
            @foreach($policies as $policy)
            @php
                $cardHeading = policy_public_heading($policy);
                $cardEyebrow = policy_card_eyebrow($policy);
                $imageName = policy_is_website_policy_card($policy) ? 'Website Policy.jpg' : 'Terms And Conditions.jpg';
                $policyPageUrl = policy_detail_url($policy);
            @endphp
            <div class="col-lg-6 col-md-12">
                <div class="card policy-card border-0 radius--20 overflow-hidden shadow-sm h-100">
                    <div class="policy-thumb position-relative" style="height: 350px;">
                        <img src="{{ asset($activeTemplateTrue . 'images/' . $imageName) }}" 
                             alt="{{ $cardHeading }}" class="w-100 h-100 object-fit-cover">
                        
                        <div class="policy-content-overlay position-absolute bottom-0 start-0 w-100 p-4">
                            <div class="bg-white p-4 radius--15 position-relative shadow card-content-box">
                                <h4 class="title border-bottom-dashed pb-2 mb-3">
                                    <a href="{{ $policyPageUrl }}" class="text--base">
                                        @lang('Read Details')
                                    </a>
                                </h4>
                                @if($cardEyebrow)
                                    <p class="text--base small fw-500 mb-2">{{ $cardEyebrow }}</p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 fw--700 text-dark">{{ $cardHeading }}</h5>
                                    <a href="{{ $policyPageUrl }}" 
                                       class="btn btn--base rounded-circle d-flex align-items-center justify-content-center" 
                                       style="width: 45px; height: 45px;">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                                <div class="extra-content">
                                    <p class="text-muted small mb-0">
                                        @if (policy_is_website_policy_card($policy))
                                            {{ Str::limit(__('Website Policy card summary'), 160) }}
                                        @else
                                            {{ Str::limit(__('policy_hub.terms_card_summary'), 160) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('style')
<style>
    .policy-card {
        transition: all 0.4s ease-in-out;
        cursor: pointer;
    }
    .card-content-box {
        transition: all 0.4s ease-in-out;
    }
    .extra-content {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: all 0.4s ease-in-out;
    }
    .policy-card:hover .extra-content {
        max-height: 200px;
        opacity: 1;
        margin-top: 10px;
    }
    .border-bottom-dashed {
        border-bottom: 1px dashed #ddd;
    }
    .policy-content-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
        transition: all 0.4s ease-in-out;
    }
    .subtitle {
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>
@endpush
