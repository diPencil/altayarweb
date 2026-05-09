@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-12">
            <div class="base--card radius--20">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">@lang('Available Membership Plans')</h5>
                        <p class="text-muted mb-0">@lang('Compare benefits, bonuses and duration before subscribing.')</p>
                    </div>
                </div>
                <div class="row gy-4">
                    @forelse($plans as $plan)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm radius--20 {{ $currentMembership && $currentMembership->membership_plan_id == $plan->id ? 'border-success' : '' }}" style="background: linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #eef6ff 100%); border: 1px solid #e5e7eb !important; box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05) !important;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                        <div class="d-flex align-items-center gap-3 min-w-0">
                                            <div class="flex-shrink-0">
                                                @if($plan->image_file)
                                                    <img src="{{ asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file) }}" alt="{{ is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name }}" style="width: 52px; height: 52px; object-fit: contain; background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 4px;">
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center text-muted" style="width: 52px; height: 52px; border-radius: 14px; font-size: 10px; background: #fff; border: 1px solid #e5e7eb;">
                                                        @lang('No image')
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h5 class="mb-1 text-truncate">{{ is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name }}</h5>
                                                <p class="text-muted mb-0">{{ $plan->duration_days ? $plan->duration_days . ' ' . __('Days') : __('Lifetime') }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                                            <span class="badge bg--primary">{{ $general->cur_sym }}{{ showAmount($plan->price) }}</span>
                                            <span class="badge badge--success">+{{ $plan->bonus_points }} @lang('Points')</span>
                                            @if($currentMembership && $currentMembership->membership_plan_id == $plan->id)
                                                <span class="badge bg--success">@lang('Subscribed')</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-muted">{{ is_rtl() && $plan->description_ar ? $plan->description_ar : $plan->description }}</p>
                                    <ul class="list-unstyled d-grid gap-2 mb-4">
                                        @foreach((is_rtl() && !empty($plan->benefits_ar) ? $plan->benefits_ar : ($plan->benefits ?? [])) as $benefit)
                                            <li><i class="las la-check text--success me-1"></i>{{ $benefit }}</li>
                                        @endforeach
                                    </ul>
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        @if($plan->pdf_file)
                                            @if($currentMembership && $currentMembership->membership_plan_id == $plan->id)
                                                <a class="btn btn-sm btn-outline--base" href="{{ route('user.membership.download', $plan->id) }}">
                                                    <i class="fa-solid fa-file-pdf me-1"></i> @lang('Download PDF')
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline--base restricted-download" type="button">
                                                    <i class="fa-solid fa-file-pdf me-1"></i> @lang('Download PDF')
                                                </button>
                                            @endif
                                        @endif
                                        @if(! ($currentMembership && $currentMembership->membership_plan_id == $plan->id))
                                            <form action="{{ route('user.membership.subscribe') }}" method="POST" class="ms-auto">
                                                @csrf
                                                <input type="hidden" name="membership_plan_id" value="{{ $plan->id }}">
                                                <button class="btn btn--base" type="submit">@lang('Subscribe')</button>
                                            </form>
                                        @else
                                            <span class="ms-auto text-success fw-semibold">@lang('Current Plan')</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">@lang('No membership plans available right now.')</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        $('.restricted-download').on('click', function () {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("Access Restricted") }}',
                text: '{{ __("You must be subscribed to this membership plan or upgrade your current plan to download this PDF.") }}',
                confirmButtonText: '{{ __("OK") }}',
                confirmButtonColor: '#2257bf'
            });
        });
    })(jQuery);
</script>
@endpush
