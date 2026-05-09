@php
    $counterElements = getContent('counter.element', false, 4);
@endphp

<section class="counter-section py-70 bg--base position-relative">
    <div class="bg--rotate"></div>

    <span class="bg--element-one position-absolute left_image_bounce">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/flyimg2.png') }}" alt="image">
    </span>

    <span class="bg--element-two position-absolute zoomInOut">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/flyimg3.png') }}" alt="image">
    </span>

    <span class="bg--element-three position-absolute top_image_bounce">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/flyimg4.png') }}" alt="image">
    </span>


    <div class="container">
        <div class="row gy-4">
            @foreach ($counterElements ?? [] as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="count-card">
                        <div class="count-number-wraper">
                            <span class="amount odometer text--white" data-count="{{$item->data_values->counter_number}}"></span>
                            <h6 class="amount fs--36 fw--500 text--white">{{$item->data_values->counter_text}}</h6>
                        </div>
                        <p class="subtitle fs--22 text-center text--white">{{__($item->data_values->counter_heading)}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
