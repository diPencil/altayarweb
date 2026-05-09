@php
    $categories = App\Models\Category::where('status',1)->latest()->inRandomOrder()->take(6)->get();
@endphp
<section class="feature--section section--bg py-100 z--1">
    <div class="container">
        <div class="row justify-content-center gy-4">
            @foreach ($categories as $category )
            <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="{{route('category.artwork',$category->id)}}" class="categorey--card__one  position-relative base--radius d-flex flex-column justify-content-center align-items-center">
                    <div class="thumb--wrap">
                        <img src="{{ getImage(getFilePath('category') . '/' . $category->image)}}"  alt="@lang('image')">
                    </div>
                    <div class="content--wrap">
                        <h6 class="mb-0">{{__($category->name)}}</h6>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
