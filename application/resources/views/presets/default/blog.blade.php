@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="news-section section--bg py-100">
        <div class="container">
            <div class="row justify-content-center gy-4">
                @include($activeTemplate . 'components.blog')
            </div>
            @if ($blogs->hasPages())
                <div class="row mt-3">
                    <div class="col-lg-12 justify-content-end d-flex">
                        {{ $blogs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if ($sections->secs != null)
    @foreach (json_decode($sections->secs) as $sec)
        @include($activeTemplate . 'sections.' . $sec)
    @endforeach
@endif
@endsection
