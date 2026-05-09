@php
    $aboutIncludesContent = getContent('about_includes.content', true);
    $aboutIncludesElements = getContent('about_includes.element', false);
@endphp

<section class="about--includes py-100 bg--light">
    <div class="container">
        <div class="row text-center mb-5 justify-content-center">
            <div class="col-lg-8">
                <h6 class="text--base fw--bold">{{ __(@$aboutIncludesContent->data_values->title) }}</h6>
                <h2 class="title">{{ __(@$aboutIncludesContent->data_values->heading) }}</h2>
            </div>
        </div>
        <div class="row gy-4">
            @foreach($aboutIncludesElements as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="include-box d-flex align-items-center gap-3 bg--white p-4 radius--10 shadow-sm border h-100">
                        <div class="icon text--base fs--30">
                            @php echo @$item->data_values->icon @endphp
                        </div>
                        <div class="content">
                            <h6 class="title mb-1 fs--16">{{ __(@$item->data_values->title) }}</h6>
                            <p class="description fs--12 text-muted mb-0">{{ __(@$item->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
