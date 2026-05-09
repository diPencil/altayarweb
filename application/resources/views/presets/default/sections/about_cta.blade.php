@php
    $aboutCtaContent = getContent('about_cta.content', true);
    $aboutCtaElements = getContent('about_cta.element', false);
@endphp

<section class="about--cta py-80">
    <div class="container">
        <div class="cta-wrapper bg--base p-5 radius--20 position-relative overflow-hidden shadow-lg">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <div class="cta-content text--white">
                        <h2 class="title text--white mb-3">{{ __(@$aboutCtaContent->data_values->heading) }}</h2>
                        <p class="description mb-4 opacity-75">{{ __(@$aboutCtaContent->data_values->description) }}</p>
                        @if(@$aboutCtaContent->data_values->button_text)
                            <a href="{{ @$aboutCtaContent->data_values->button_url }}" class="btn btn--white text--base fw-bold">
                                {{ __(@$aboutCtaContent->data_values->button_text) }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row gy-3">
                        @foreach($aboutCtaElements as $item)
                            <div class="col-sm-6">
                                <div class="cta-feature-box d-flex align-items-center gap-3 bg--white p-3 radius--10">
                                    <div class="icon text--base fs--20">
                                        @php echo @$item->data_values->icon @endphp
                                    </div>
                                    <h6 class="mb-0 fs--15">{{ __(@$item->data_values->title) }}</h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
