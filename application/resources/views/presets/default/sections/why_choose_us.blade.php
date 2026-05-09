@php
    $whyChooseUsContent = getContent('why_choose_us.content', true);
    $whyChooseUsElements = getContent('why_choose_us.element', false,3,true);

@endphp
<section class="why-choose section--bg py-100 position-relative">
    
    <div class="element position-absolute">
        <img src="{{asset($activeTemplateTrue . 'images/shape/whychoose-bg2.png')}}" alt="image">
    </div>

    <div class="element--two position-absolute">
        <img src="{{asset($activeTemplateTrue . 'images/shape/tree5.png')}}" alt="image">
    </div>

    <div class="element--three position-absolute">
        <img src="{{asset($activeTemplateTrue . 'images/shape/dash-line.png')}}" alt="image">
    </div>

    <div class="bg--element position-absolute">
        <img src="{{asset($activeTemplateTrue . 'images/shape/element-8.png')}}" alt="image">
    </div>



    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-6 col-md-12 why-choose__text">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-content mb-30">
                            <div class="title-wrap">
                                <div class="row justify-content-between">
                                    <div class="col-xl-10 col-lg-12">
                                        <h6 class="heading third--font fs--32 fw--700 text--base mb-0" dir="auto">{{ getLangContent($whyChooseUsContent->data_values, 'title') }}</h6>
                                        <h2 class="title mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp"
                                            data-wow-delay="0.2s" dir="auto">{{ getLangContent($whyChooseUsContent->data_values, 'heading') }}</h2>
                                        <p class="subtitle wow animate__animated animate__fadeInUp"
                                            data-wow-delay="0.3s" dir="auto">{{ getLangContent($whyChooseUsContent->data_values, 'sub_heading') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row gy-4 mb-5">
                    @foreach ($whyChooseUsElements as $item)

                    <div class="col-xxl-8 col-lg-12 col-md-6">
                        <div class="service-key--box d-flex flex-column gap--8">
                            <div class="d-flex align-items-center gap--8 why-choose__feature-head">
                                <div
                                    class="icon--wrap d-flex justify-content-center align-items-center bg--base radius--50 flex-shrink-0">
                                    <i class="fa-solid fa-check text--white"></i>
                                </div>
                                <div class="title--wrap">
                                    <h6 class="title fs--20 fw--700 mb-0" dir="auto">{{ getLangContent($item->data_values, 'title') }}</h6>
                                </div>
                            </div>
                            <div class="content-wrap">

                                <p class="description" dir="auto">{{ getLangContent($item->data_values, 'description') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6 col-md-12 why-choose__visual">
                <div class="right--thumb__wrap d-flex justify-content-end position-relative">

                    <div class="thumb-3 position-absolute">
                        <img src="{{getImage(getFilePath('whyChooseUs').'/'.$whyChooseUsContent->data_values->image_one)}}" alt="image">
                    </div>
                    <div class="thumb-2 radius--20 overflow-hidden position-absolute">
                        <img class="fit--img" src="{{getImage(getFilePath('whyChooseUs').'/'.$whyChooseUsContent->data_values->image_two)}}" alt="image">
                    </div>
                    <div class="thumb-1 radius--20 overflow-hidden">
                        <img class="fit--img" src="{{getImage(getFilePath('whyChooseUs').'/'.$whyChooseUsContent->data_values->image_three)}}" alt="image">
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
