@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="privilege-section section--bg py-100">
        <div class="container">
            <div class="row align-items-end gy-4 mb-4">
                <div class="col-lg-7 text-start">
                    <span class="offers-hero__eyebrow">@lang('Privilege Cards')</span>
                    <h1 class="offers-hero__title mb-2">@lang('Unlock Exclusive Travel Privileges')</h1>
                    <p class="text-muted mb-3 fs--18">
                        @lang('Our privilege cards are designed to provide you with elite access, significant savings, and premium services across our global travel network. Whether you are looking for airport lounge access, hotel upgrades, or exclusive concierge services, our cards offer a gateway to a seamless and luxurious travel experience.')
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="badge badge--light text-dark shadow-sm px-3 py-2"><i class="las la-check-circle text--success"></i> @lang('Global Access')</span>
                        <span class="badge badge--light text-dark shadow-sm px-3 py-2"><i class="las la-check-circle text--success"></i> @lang('Premium Savings')</span>
                        <span class="badge badge--light text-dark shadow-sm px-3 py-2"><i class="las la-check-circle text--success"></i> @lang('Priority Support')</span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="base--card radius--20 p-4 shadow-sm border-0 bg--primary-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg--primary text-white p-3 rounded-circle">
                                <i class="las la-percentage fs--30"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-dark">@lang('Special Discount')</h5>
                                <p class="mb-0 text-muted fs--14">@lang('Get up to 25% off on available club memberships when using privilege cards.')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($featuredCards->count())
                <div class="row gy-4 mb-4">
                    @foreach($featuredCards as $featuredCard)
                        <div class="col-lg-4 col-md-6">
                            <div class="privilege-feature-card base--card radius--20 h-100 overflow-hidden">
                                <div class="privilege-feature-card__media">
                                    @if($featuredCard->image_file)
                                        <img src="{{ asset(getFilePath('privilegeCardImage') . '/' . $featuredCard->image_file) }}" alt="{{ $featuredCard->name }}">
                                    @else
                                        <div class="privilege-feature-card__empty">@lang('No image')</div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <span class="badge badge--primary">@lang('Featured')</span>
                                        <span class="badge badge--success">{{ $general->cur_sym }}{{ showAmount($featuredCard->price) }}</span>
                                    </div>
                                    <h5 class="mb-2">{{ is_rtl() && $featuredCard->name_ar ? $featuredCard->name_ar : $featuredCard->name }}</h5>
                                    <p class="text-muted mb-3">{{ is_rtl() && $featuredCard->subtitle_ar ? $featuredCard->subtitle_ar : $featuredCard->subtitle }}</p>
                                    @if($featuredCard->pdf_file)
                                        <a href="{{ asset(getFilePath('privilegeCardPdf') . '/' . $featuredCard->pdf_file) }}" class="btn btn--light btn-sm pills">@lang('Download PDF')</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row gy-4">
                @forelse($cards as $card)
                    <div class="col-lg-4 col-md-6">
                        <div class="base--card radius--20 privilege-card-item h-100">
                            <div class="privilege-card-item__media mb-3">
                                @if($card->image_file)
                                    <img src="{{ asset(getFilePath('privilegeCardImage') . '/' . $card->image_file) }}" alt="{{ $card->name }}">
                                @else
                                    <div class="privilege-card-item__empty">@lang('No image')</div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ is_rtl() && $card->name_ar ? $card->name_ar : $card->name }}</h5>
                                    <p class="text-muted mb-0">{{ is_rtl() && $card->subtitle_ar ? $card->subtitle_ar : $card->subtitle }}</p>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    @if($card->is_featured)
                                        <span class="badge badge--success mb-2">@lang('Featured')</span>
                                    @endif
                                    <div class="fs--20 fw--700 text--base">{{ $general->cur_sym }}{{ showAmount($card->price) }}</div>
                                    @if($card->original_price)
                                        <small class="text-muted text-decoration-line-through">{{ $general->cur_sym }}{{ showAmount($card->original_price) }}</small>
                                    @endif
                                </div>
                            </div>

                            @if($card->description)
                                <p class="text-muted mb-3">{{ is_rtl() && $card->description_ar ? $card->description_ar : $card->description }}</p>
                            @endif

                            @if(($card->benefits ?? []) || ($card->features ?? []))
                                <div class="row g-3 mb-3">
                                    @if($card->benefits)
                                        <div class="col-12">
                                            <h6 class="fs--14 text-uppercase text-muted mb-2">@lang('Benefits')</h6>
                                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                                @foreach($card->benefits as $benefit)
                                                    <li><i class="las la-check text--success me-1"></i>{{ $benefit }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if($card->features)
                                        <div class="col-12">
                                            <h6 class="fs--14 text-uppercase text-muted mb-2">@lang('Features')</h6>
                                            <ul class="list-unstyled d-grid gap-2 mb-0">
                                                @foreach($card->features as $feature)
                                                    <li><i class="las la-star text--base me-1"></i>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @if($card->pdf_file)
                                    <a href="{{ asset(getFilePath('privilegeCardPdf') . '/' . $card->pdf_file) }}" class="btn btn--light btn-sm">@lang('Download PDF')</a>
                                @endif
                                <a href="{{ route('contact') }}" class="btn btn--base btn-sm">@lang('Request Access')</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">@lang('No privilege cards available right now.')</div>
                @endforelse
            </div>

            @if(method_exists($cards, 'hasPages') && $cards->hasPages())
                <div class="row mt-4">
                    <div class="col-lg-12 d-flex justify-content-end">
                        {{ $cards->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('style')
    <style>
        .privilege-feature-card,
        .privilege-card-item {
            border: 1px solid rgba(15, 23, 42, .08);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .privilege-feature-card:hover,
        .privilege-card-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12) !important;
        }

        .privilege-feature-card__media,
        .privilege-card-item__media {
            min-height: 220px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .privilege-feature-card__media img,
        .privilege-card-item__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .privilege-feature-card__empty,
        .privilege-card-item__empty {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            background: linear-gradient(135deg, #eef2f7 0%, #f7f8fb 100%);
        }
    </style>
@endpush
