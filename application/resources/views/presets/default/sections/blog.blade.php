@php
    $blog = getContent('blog.content', true);
    $blogs = getContent('blog.element', false, 3);
@endphp

<section class="news-section section--bg py-100  z--1">
    <div class="container">

        @if (request()->route()->uri != '/blog')
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section-content mb-50">
                        <div class="title-wrap">
                            <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0" dir="auto">
                                {{ getLangContent($blog->data_values, 'title') }}</h6>
                            <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp"
                                data-wow-delay="0.2s" dir="auto">
                                {{ getLangContent($blog->data_values, 'heading') }}</h2>
                            <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                                data-wow-delay="0.3s" dir="auto">
                                {{ getLangContent($blog->data_values, 'sub_heading') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif



        <div class="row justify-content-center gy-4">

            @include($activeTemplate . 'components.blog')
        </div>
    </div>
</section>
