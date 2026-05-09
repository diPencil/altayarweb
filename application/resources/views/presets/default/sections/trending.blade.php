@php
    $trending = getContent('trending.content', true);
    $artworks = App\Models\Artwork::with(['category', 'agent'])
    ->active()
    ->whereHas('orderItems', fn ($query) => 
        $query->where('quantity', '>', 0)
              ->where('created_at', '>=', now()->subDays(30))
    )
    ->withCount(['orderItems as total_quantity' => fn ($query) => 
        $query->where('created_at', '>=', now()->subDays(30))
              ->selectRaw('sum(quantity)')
    ])
    ->orderByDesc('total_quantity')
    ->take(8)
    ->get();
@endphp
<section class="category--section  py-100  z--1">
    <div class="container">
        <div class="row gy-5 mb-60">
            <div class="col-xl-6 col-lg-6 col-md-8">
                <div class="section-content-4 position-relative d-flex justify-content-start flex-column align-items-start">
                    <span class="strok--heading">{{__($trending->data_values->heading)}}</span>
                    <h6 class="title heading--title wow animate__animated animate__fadeInUp text-start fs--40 fw--700 splite-text mb-0" data-splitting data-wow-delay="0.2s">{{__($trending->data_values->heading)}}</h6>
                </div>
            </div>
            @if($artworks->count() > 0)
            <div class="col-lg-6 col-md-4 d-none d-md-block">
                <div class="btn--wrap text-start text-md-end">
                    <a href="{{route('artwork')}}" class="btn btn-outline--base btn--lg">@lang('See More')</a>
                </div>
            </div>
            @endif
        </div>
        <div class="row gy-4 justify-content-center">
            @include($activeTemplate.'components.artwork')
        </div>
    </div>
</section>
