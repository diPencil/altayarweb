@php
    $aboutemployeeContent = getContent('about_employee.content', true);
    $aboutemployeeElements = getContent('about_employee.element', false);
@endphp

<section class="about--employee py-100">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-6">
                <div class="employee-thumbs position-relative">
                    <div class="main-thumb radius--20 overflow-hidden shadow-lg">
                        <img class="fit--img" src="{{ getImage(getFilePath('aboutemployee') . '/' . @$aboutemployeeContent->data_values->image_one) }}" alt="image">
                    </div>
                    <div class="sub-thumbs d-flex gap-3 mt-3">
                         <div class="sub-thumb radius--10 overflow-hidden shadow-sm flex-grow-1">
                            <img class="fit--img" src="{{ getImage(getFilePath('aboutemployee') . '/' . @$aboutemployeeContent->data_values->image_two) }}" alt="image">
                         </div>
                         <div class="sub-thumb radius--10 overflow-hidden shadow-sm flex-grow-1">
                            <img class="fit--img" src="{{ getImage(getFilePath('aboutemployee') . '/' . @$aboutemployeeContent->data_values->image_three) }}" alt="image">
                         </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-content">
                    <h6 class="text--base fw--bold">{{ __(@$aboutemployeeContent->data_values->title) }}</h6>
                    <h2 class="title mb-3">{{ __(@$aboutemployeeContent->data_values->heading) }}</h2>
                    <p class="description mb-4">{{ __(@$aboutemployeeContent->data_values->description) }}</p>

                    <div class="employee-accordion">
                        @foreach($aboutemployeeElements as $item)
                            <div class="accordion-item bg--light p-3 radius--10 mb-3 border">
                                <div class="accordion-header d-flex align-items-center gap-3">
                                    <div class="icon text--base">
                                        @php echo @$item->data_values->icon @endphp
                                    </div>
                                    <h6 class="mb-0">{{ __(@$item->data_values->title) }}</h6>
                                </div>
                                <div class="accordion-body mt-2">
                                    <p class="fs--14 text-muted mb-0">{{ __(@$item->data_values->description) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
