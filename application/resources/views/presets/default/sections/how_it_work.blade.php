@php
    $howItWorkContent = getContent('how_it_work.content', true);
    $howItWorkElements = getContent('how_it_work.element', false, 3,true);
@endphp

<section class="how-it--work section--bg py-100 position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0" dir="auto">{{ getLangContent($howItWorkContent->data_values, 'title') }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp"
                            data-wow-delay="0.2s" dir="auto">{{ getLangContent($howItWorkContent->data_values, 'heading') }}</h2>
                            <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400" data-wow-delay="0.3s" dir="auto">{{ getLangContent($howItWorkContent->data_values, 'sub_heading') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center justify-content-lg-between gy-4 position-relative">
            <div class="air--line position-absolute">
                <img class="w--100 h-auto" src="{{asset($activeTemplateTrue . 'images/shape/air-line.png')}}" alt="image">
            </div>

            @foreach ($howItWorkElements ??[]  as $item)

            <div class="col-lg-4 col-md-6">
                <div class="how-work--box d-flex flex-column justify-content-center align-items-center position-relative">
                    <div class="icon-wrap d-flex justify-content-center align-items-center flex-shrink-0 position-relative">
                        @php
                            echo ($item->data_values->icon)
                        @endphp

                        <span class="steps position-absolute fs--14 fw--500 text--white">@lang('Step') 0{{$loop->iteration}}</span>
                    </div>
                    <div class="content-wrap">
                        <h6 class="title fs--24 text-center fw--600 mb-1" dir="auto">{{ getLangContent($item->data_values, 'title') }}</h6>
                        <p class="text-center" dir="auto">{{ getLangContent($item->data_values, 'description') }}</p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>
