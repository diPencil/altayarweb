@php
    $aboutOfferContent = getContent('about_offer.content', true);
@endphp

<section class="about--offer py-100">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="offer-thumb radius--20 overflow-hidden shadow-lg">
                    <img class="fit--img" src="{{ getImage(getFilePath('aboutOffer') . '/' . @$aboutOfferContent->data_values->image) }}" alt="image">
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1">
                <div class="section-content">
                    <h6 class="text--base fw--bold">{{ __(@$aboutOfferContent->data_values->title) }}</h6>
                    <h2 class="title mb-4">{{ __(@$aboutOfferContent->data_values->heading) }}</h2>
                    <div class="description mb-4">
                        @php echo @$aboutOfferContent->data_values->description @endphp
                    </div>
                    <div class="btn-wrap d-flex flex-wrap gap-3">
                         @if(@$aboutOfferContent->data_values->button_one_text)
                            <a href="{{ @$aboutOfferContent->data_values->button_one_url }}" class="btn btn--base">
                                {{ __(@$aboutOfferContent->data_values->button_one_text) }}
                            </a>
                        @endif
                        @if(@$aboutOfferContent->data_values->button_two_text)
                            <a href="{{ @$aboutOfferContent->data_values->button_two_url }}" class="btn btn--outline-base">
                                {{ __(@$aboutOfferContent->data_values->button_two_text) }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
