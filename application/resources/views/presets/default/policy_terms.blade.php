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
                        <h1 class="policy-terms-doc-title">{{ __('policy_terms.doc_title') }}</h1>

                        <h2 class="policy-terms-section-head">{{ __('policy_terms.intro_head') }}</h2>
                        <p>{{ __('policy_terms.welcome') }}</p>
                        <p>{{ __('policy_terms.intro_p1') }}</p>
                        <p>{{ __('policy_terms.intro_p2') }}</p>
                        <p>{{ __('policy_terms.intro_p3') }}</p>

                        <h2 class="policy-terms-section-head">{{ __('policy_terms.use_head') }}</h2>
                        <ul class="policy-terms-ul">
                            <li>{{ __('policy_terms.use_li1') }}</li>
                            <li>{{ __('policy_terms.use_li2') }}</li>
                            <li>{{ __('policy_terms.use_li3') }}</li>
                        </ul>

                        <h2 class="policy-terms-section-head">{{ __('policy_terms.svc_head') }}</h2>
                        <ol class="policy-terms-ol">
                            @for ($i = 1; $i <= 24; $i++)
                                @php $k = 'svc_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT); @endphp
                                <li>{!! __('policy_terms.' . $k) !!}</li>
                            @endfor
                        </ol>

                        <figure class="policy-terms-thanks">
                            <span class="policy-terms-quote" aria-hidden="true">&ldquo;</span>
                            <blockquote class="policy-terms-thanks-text">
                                <p>{{ __('policy_terms.thanks') }}</p>
                            </blockquote>
                        </figure>
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

    .policy-terms-page .policy-terms-doc.wyg ol.policy-terms-ol {
        margin: 0 0 1.5rem;
        padding-inline-start: 1.75rem;
        list-style-type: decimal;
        list-style-position: outside;
    }

    .policy-terms-page .policy-terms-doc.wyg ol.policy-terms-ol li {
        margin-bottom: 0.95rem;
        line-height: 1.7;
        padding-inline-start: 0.65rem;
        font-size: 17px;
        color: hsl(var(--black) / 0.88);
    }

    .policy-terms-page .policy-terms-doc.wyg a {
        color: hsl(var(--base));
        font-weight: 500;
        word-break: break-word;
    }

    .policy-terms-page .policy-terms-doc.wyg a:hover {
        color: hsl(var(--base-two));
    }

    .policy-terms-thanks {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin: 2.5rem 0 0;
        padding: 1.75rem 0 0;
        border-top: 1px dashed hsl(var(--base) / 0.35);
    }

    [dir="rtl"] .policy-terms-thanks {
        flex-direction: row-reverse;
    }

    .policy-terms-quote {
        flex-shrink: 0;
        font-family: var(--heading-font);
        font-size: clamp(2.75rem, 6vw, 3.75rem);
        line-height: 0.82;
        font-weight: 700;
        color: hsl(var(--base));
        opacity: 0.9;
    }

    [dir="rtl"] .policy-terms-quote {
        transform: scaleX(-1);
    }

    .policy-terms-thanks-text {
        margin: 0;
        padding: 0.35rem 0 0;
        border: 0;
    }

    .policy-terms-thanks-text p {
        margin: 0 !important;
        font-family: var(--body-font);
        font-weight: 600;
        font-style: italic;
        font-size: 1.05rem;
        line-height: 1.65;
        color: hsl(var(--heading-color));
    }

    @media screen and (max-width: 575px) {
        .policy-terms-page .policy-terms-doc.wyg ol.policy-terms-ol {
            padding-inline-start: 1.35rem;
        }
    }
</style>
@endpush
