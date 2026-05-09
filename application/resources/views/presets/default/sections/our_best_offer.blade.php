@php
    $ourBestOfferContent = getContent('our_best_offer.content', true);
    $offerValues = $ourBestOfferContent->data_values;
    $leftImage = is_rtl() && !empty($offerValues->left_image_ar)
        ? $offerValues->left_image_ar
        : ($offerValues->left_image ?? null);
    $rightImage = is_rtl() && !empty($offerValues->right_image_ar)
        ? $offerValues->right_image_ar
        : ($offerValues->right_image ?? null);
@endphp

<section class="offer--section section--bg py-100 position-relative">


    <div class="bg--element-two position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg3.png') }}" alt="image">
    </div>

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($ourBestOfferContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp">
                            {{ __($ourBestOfferContent->data_values->heading) }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($ourBestOfferContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="row gy-4">
            <div class="col-xxl-7 col-lg-6">
                <div class="offer-card card--one radius--20 position-relative overflow--hidden">
                    <div class="offer-card__thumb radius--20 w--100 h--100 bg--base">
                        <img class="fit--img"
                            src="{{ getImage(getFilePath('ourBestOffer') . '/' . $leftImage) }}" loading="lazy" decoding="async"
                            alt="image">
                    </div>
                    <div class="offer-card__content position-absolute">
                        <p class="sub--title text--white third--font fw--700">
                            {{ __($ourBestOfferContent->data_values->left_discount_title) }}</p>
                        <h6 class="title fw--700 text--white">{{ __($ourBestOfferContent->data_values->left_heading) }}
                        </h6>
                        <a href="{{$ourBestOfferContent->data_values->left_button_url}}"
                            class="btn btn--dark btn--lg pills">{{ __($ourBestOfferContent->data_values->left_button_name) }}</a>
                    </div>
                </div>
            </div>

            <div class="col-xxl-5 col-lg-6">
                <div class="offer-card card--two bg--base-two  radius--20 position-relative overflow--hidden">
                    <div class="offer-card__thumb w--100 h--100">
                        <img class="fit--img"
                            src="{{ getImage(getFilePath('ourBestOffer') . '/' . $rightImage) }}" loading="lazy" decoding="async"
                            alt="image">
                    </div>
                    <div class="offer-card__content position-absolute">
                        <p class="sub--title text-end text--white third--font fw--700">
                            {{ __($ourBestOfferContent->data_values->right_discount_title) }}</p>
                        <h6 class="title fw--700 text-end text--white">
                            {{ __($ourBestOfferContent->data_values->right_heading) }}</h6>
                        <div class="btn--wrap float-end">
                            <a href="{{ $ourBestOfferContent->data_values->right_button_url }}"
                                class="btn btn--dark btn--lg pills">{{ __($ourBestOfferContent->data_values->right_button_name) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
