@extends($activeTemplate . 'layouts.frontend')
@section('content')

@php
    $docLang = is_rtl() ? 'ar' : 'en';
    $docDir = is_rtl() ? 'rtl' : 'ltr';
@endphp

<section class="cookie-section policy-terms-page section--bg py-100 position-relative">
    <div class="bg--thumb position-absolute one">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="">
    </div>

    <div class="container-fluid px-3 px-sm-4 px-lg-5">
        <div class="row justify-content-center g-0">
            <div class="col-12">
                <div class="coockie-wrap policy-terms-panel bg-white radius--20 shadow-sm">
                    <article class="wyg policy-terms-doc" lang="{{ $docLang }}" dir="{{ $docDir }}">
                        <h1 class="policy-terms-doc-title">{{ __('policy_website.doc_title') }}</h1>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.dear_head') }}</h2>
                        <p>{{ __('policy_website.dear_p1') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.what_head') }}</h2>
                        <p>{{ __('policy_website.what_p1') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.find_head') }}</h2>
                        <p>{{ __('policy_website.find_p1') }}</p>
                        <ul class="policy-terms-ul">
                            @for ($i = 1; $i <= 7; $i++)
                                <li>{{ __('policy_website.find_li' . $i) }}</li>
                            @endfor
                        </ul>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.price_head') }}</h2>
                        <ul class="policy-terms-ul">
                            @for ($i = 1; $i <= 4; $i++)
                                <li>{{ __('policy_website.price_li' . $i) }}</li>
                            @endfor
                        </ul>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.club_head') }}</h2>
                        <p>{!! __('policy_website.club_p1') !!}</p>
                        <p>{{ __('policy_website.club_p2') }}</p>
                        <p>{{ __('policy_website.club_p3') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.rights_head') }}</h2>
                        <p>{{ __('policy_website.rights_p1') }}</p>
                        <p>{{ __('policy_website.rights_p2') }}</p>
                        <p>{{ __('policy_website.rights_p3') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.embed_head') }}</h2>
                        <p>{{ __('policy_website.embed_p1') }}</p>
                        <p>{{ __('policy_website.embed_p2') }}</p>
                        <p>{{ __('policy_website.embed_p3') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.share_head') }}</h2>
                        <p>{{ __('policy_website.share_p1') }}</p>
                        <p>{{ __('policy_website.share_p2') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.retain_head') }}</h2>
                        <p>{{ __('policy_website.retain_p1') }}</p>
                        <p>{{ __('policy_website.retain_p2') }}</p>
                        <p>{{ __('policy_website.retain_p3') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.sent_head') }}</h2>
                        <p>{{ __('policy_website.sent_p1') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.comments_head') }}</h2>
                        <p>{{ __('policy_website.comments_p1') }}</p>
                        <p>{{ __('policy_website.comments_p2') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.media_head') }}</h2>
                        <p>{{ __('policy_website.media_p1') }}</p>
                        <p>{{ __('policy_website.media_p2') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_website.cookies_head') }}</h2>
                        <p>{{ __('policy_website.cookies_p1') }}</p>
                        <ul class="policy-terms-ul">
                            @for ($i = 1; $i <= 4; $i++)
                                <li>{!! __('policy_website.cookies_li' . $i) !!}</li>
                            @endfor
                        </ul>
                        <p>{{ __('policy_website.cookies_p2') }}</p>

                        <p class="policy-website-note mt-4 mb-0"><strong>{{ __('policy_website.note_label') }}</strong> {{ __('policy_website.note_p') }}</p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
    .policy-terms-panel {
        padding: clamp(1.25rem, 3vw, 2.75rem) clamp(1rem, 4vw, 3.5rem);
        border: 1px solid hsl(var(--black) / 0.06);
    }

    .policy-terms-doc {
        text-align: start;
        max-width: none;
    }

    .policy-terms-doc-title {
        font-family: var(--heading-font);
        font-size: clamp(1.35rem, 2.2vw, 1.75rem);
        font-weight: 700;
        color: hsl(var(--heading-color));
        margin: 0 0 1.5rem;
        padding-bottom: 0.85rem;
        line-height: 1.3;
        border-bottom: 3px solid hsl(var(--base));
        text-decoration: none;
    }

    .policy-terms-page .policy-terms-doc.wyg h2.policy-terms-section-head {
        font-family: var(--heading-font);
        font-size: clamp(1.05rem, 1.35vw, 1.2rem);
        font-weight: 600;
        color: hsl(var(--heading-color));
        margin: 2rem 0 1rem;
        padding: 0.35rem 0 0.35rem 0.85rem;
        border-inline-start: 4px solid hsl(var(--base));
        line-height: 1.45;
    }

    .policy-terms-page .policy-terms-doc.wyg p {
        margin-bottom: 1rem;
        line-height: 1.8;
        color: hsl(var(--black) / 0.88);
    }

    .policy-terms-page .policy-website-note {
        padding: 1rem 1.15rem;
        background: hsl(var(--base) / 0.06);
        border-radius: 10px;
        border-inline-start: 4px solid hsl(var(--base));
        font-size: 0.95rem;
        line-height: 1.65;
    }

    .policy-terms-page .policy-terms-doc.wyg ul.policy-terms-ul {
        margin: 0 0 1.5rem;
        padding-inline-start: 1.25rem;
        list-style-type: disc;
    }

    .policy-terms-page .policy-terms-doc.wyg ul.policy-terms-ul li {
        margin-bottom: 0.85rem;
        line-height: 1.75;
        padding-inline-start: 0.35rem;
        font-size: 17px;
    }

    .policy-terms-page .policy-terms-doc.wyg a {
        color: hsl(var(--base));
        font-weight: 500;
        word-break: break-word;
    }

    .policy-terms-page .policy-terms-doc.wyg a:hover {
        color: hsl(var(--base-two));
    }

    @media screen and (max-width: 575px) {
        .policy-terms-page .policy-terms-doc.wyg ul.policy-terms-ul {
            padding-inline-start: 1.1rem;
        }
    }
</style>
@endpush
