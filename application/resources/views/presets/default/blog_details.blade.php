@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="blog-details--section section--bg py-100">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-lg-8">
                    <div class="blog-details">

                        <div class="blog-item">
                            <div class="blog-item--thumb">
                                <img class="fit--img"
                                    src="{{ getImage(getFilePath('frontend') . '/blog' . '/' . ($blog->data_values->blog_image ?? 'default.jpg')) }}"
                                    alt="@lang('blog-img')">
                            </div>
                            <div class="blog-item--content pt-3">
                                <ul class="text-list d-flex gap--16">
                                    <li class="text-list__item">
                                        <div class="text-list__item-icon d-flex align-items-center gap--8">
                                            <i class="fas fa-calendar-alt"></i>
                                            <p> {{ showDateTime($blog->created_at, 'd M Y') }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="blog-details--content">
                            <h3 class="blog-details--title">{{ getLangContent($blog->data_values, 'title') }}</h3>
                          
                                <blockquote >{{ getLangContent($blog->data_values, 'quote') }}</blockquote>


                                @php
                                    echo getLangContent($blog->data_values, 'description');
                                @endphp
                          

                            <div class="blog-details--share mt-4 d-flex align-items-center flex-wrap mb-5">
                                <h5 class="social-share--title mb-0 me-sm-3 me-1 d-inline-block">@lang('Share This'):</h5>
                                <ul class="social-list blog-details d-flex gap--12">
                                    <li class="social-list--item">
                                        <a href="https://www.facebook.com/share.php?u={{ Request::url() }}&title={{ slug($blog->data_values->title) }}"
                                            class="social-list__link d-flex justify-content-center align-items-center"><i
                                                class="fab fa-facebook-f"></i></a>
                                    </li>
                                    <li class="social-list--item"><a
                                            href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url() }}&title={{ slug($blog->data_values->title) }}&source=behands"
                                            class="social-list__link d-flex justify-content-center align-items-center"><i
                                                class="fab fa-linkedin-in"></i></a></li>
                                    <li class="social-list--item"><a
                                            href="https://twitter.com/intent/tweet?status={{ slug($blog->data_values->title) }}+{{ Request::url() }}"
                                            class="social-list__link d-flex justify-content-center align-items-center"><i
                                                class="fab fa-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <!--  Blog Details Sidebar Start  -->
                    <div class="blog-sidebar-wrapper">




                            <div class="row justify-content-end mb-4">
                                <div class="col-lg-12 col-md-12">
                                    <form action="{{ route('blog') }}" method="GET">
                                        <div class="search--input  position-relative">
                                            <div class="input--group search--input d-flex flex-nowrap position-relative">
                                                <input type="text" class="form--control bg--white" id="searchValue"
                                                    value="{{ request()->search }}" placeholder="@lang('Search by title')"
                                                    name="search">
                                                <button class="search-btn"><i
                                                        class="fa-solid fa-magnifying-glass"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        <div class="blog-sidebar base--card radius--12">
                            <h5 class="blog-sidebar--title fs--24 fw--600 d-inline-block position-relative">
                                @lang('Latest Topics')</h5>
                            @foreach ($latests as $item)
                                <div class="latest-blog d-flex flex-wrap">
                                    <div class="latest-blog--thumb">
                                        <a
                                            href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id]) }}">
                                            <img src="{{ getImage(getFilePath('frontend') . '/blog' . '/' . 'thumb_' . ($item->data_values->blog_image ?? 'default.jpg')) }}"
                                                alt="@lang('blog-image')"></a>
                                    </div>
                                    <div class="latest-blog--content">
                                        <a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id]) }}"
                                            class="latest-blog--title fs--16 fw--600 w-100">
                                            @if (strlen(__($item->data_values->title)) > 30)
                                                {{ substr(__($item->data_values->title), 0, 30) . '...' }}
                                            @else
                                                {{ __($item->data_values->title) }}
                                            @endif
                                        </a>

                                        <span class="latest-blog--date fs--14">{{ showDateTime($item->created_at) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Blog Details Sidebar End-->
                </div>
            </div>
        </div>
    </section>
@push('style')
    <style>
        [dir="rtl"] .latest-blog--thumb {
            margin-left: 15px;
            margin-right: 0;
        }
        [dir="rtl"] .latest-blog--content {
            text-align: right;
        }
    </style>
@endpush
@endsection
