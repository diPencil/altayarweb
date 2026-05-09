@php
    $topDestinationContent = getContent('top_destination.content', true);
    // Read a bounded set of locations so the homepage does not scan the full table.
    $allLocations = App\Models\Location::query()
        ->where('status', 1)
        ->select(['id', 'location', 'latitude', 'longitude', 'image'])
        ->orderBy('id')
        ->limit(25)
        ->get();
    
    $filteredDestinations = [];
    $seenCountries = [];
    
    foreach ($allLocations as $location) {
        // Skip Saudi Arabia
        if (stripos($location->location, 'Saudi Arabia') !== false || stripos($location->location, 'السعودية') !== false || stripos($location->location, 'KSA') !== false) {
            continue;
        }
        
        // Extract country (assume format "City, Country" or just "Country")
        $parts = explode(',', $location->location);
        $country = trim(end($parts));
        
        // If we haven't picked a city for this country yet, pick this one
        if (!isset($seenCountries[$country])) {
            $filteredDestinations[] = $location;
            $seenCountries[$country] = true;
        }
    }
    
    // Take a maximum of 5 cards for the home page layout
    $topDestinations = array_slice($filteredDestinations, 0, 5);
@endphp

<section class="location--section py-100 position-relative overflow-hidden">
    <div class="bg--element position-absolute" style="z-index: 0; pointer-events: none;">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/shape-1.png') }}" alt="image">
    </div>
    <div class="bg--element-two position-absolute" style="z-index: 0; pointer-events: none;">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg2.png') }}" alt="image">
    </div>

    <div class="container" style="position: relative; z-index: 2;">

        <div class="row align-items-center gy-4 mb-50">
            <div class="col-lg-8 col-md-8">
                <div class="section-content text-start text-center-sm">
                    <div class="title-wrap">
                        <h6 class="heading third--font fs--32 fw--700 text--base mb-0" dir="auto">
                            {{ getLangContent($topDestinationContent->data_values, 'title') }}</h6>
                        <h2 class="title mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0.2s" dir="auto">
                            {{ getLangContent($topDestinationContent->data_values, 'heading') }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp fs-16 fw--400 mb-0"
                            data-wow-delay="0.3s" dir="auto">
                            {{ getLangContent($topDestinationContent->data_values, 'sub_heading') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="btn--wrap text-start text-md-end text-center-sm wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                    <a href="{{ route('public.travel.index', ['section' => 'destinations']) }}" class="btn btn--base btn--lg pills px-5">
                        @if(session('lang') == 'ar')
                            استكشف المزيد
                            <i class="fa-solid fa-arrow-left ms-2"></i>
                        @else
                            Explore More
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="location--card__wrap d-flex gap--20 flex-wrap justify-content-center">
                    @foreach ($topDestinations ?? [] as $item)
                        @include($activeTemplate . 'partials.destination-card', [
                            'item' => $item,
                            'href' => route('browse') . '?lati=' . $item->latitude . '&longi=' . $item->longitude,
                            'active' => $loop->iteration == 3,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
<style>
    @media (max-width: 767px) {
        .text-center-sm {
            text-align: center !important;
        }
        .text-center-sm .title-wrap {
            text-align: center !important;
        }
    }
</style>
@endpush
