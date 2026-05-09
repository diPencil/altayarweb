@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $heroTitle = getLangContent($aboutMeContent->data_values, 'heading') ?: __($pageTitle);
        $heroSubtitle = getLangContent($aboutMeContent->data_values, 'sub_heading');
        $heroTag = getLangContent($aboutMeContent->data_values, 'title') ?: __('Why Choose Us');
        $heroRightImage = !empty($aboutMeContent->data_values->right_image)
            ? getImage(getFilePath('aboutMe') . '/' . $aboutMeContent->data_values->right_image)
            : asset('assets/presets/default/images/Live-travel-control-center.jpg');
        $heroLeftImage = !empty($aboutMeContent->data_values->left_image)
            ? getImage(getFilePath('aboutMe') . '/' . $aboutMeContent->data_values->left_image)
            : asset('assets/presets/default/images/membership-details.jpg');
        $heroMiddleImage = !empty($aboutMeContent->data_values->middle_image)
            ? getImage(getFilePath('aboutMe') . '/' . $aboutMeContent->data_values->middle_image)
            : asset('assets/presets/default/images/premium-membership.jpg');
        $aboutCards = [
            ['title' => __('Our Mission'), 'description' => __('We have chosen for you the best tourist places, We offer a variety of recreational places to suit your tastes.')],
            ['title' => __('Principles'), 'description' => __('We have comfortable capabilities to satisfy all our customers on trips whether it is a long or short trip, so our concern is your safety.')],
            ['title' => __('Our Vision'), 'description' => __('Choosing the right places for customers and satisfying them in an easy and simple way, as we book many tours all over the world.')],
            ['title' => __('Travel Guide'), 'description' => __('Our travel guides aim to provide you with the best and most up-to-date information on major travel destinations around the world.')],
        ];
        $aboutFaqItems = [
            [
                'question' => __('How do I start booking with your platform?'),
                'answer' => __('You can begin by browsing the available travel options, choosing the service that fits your trip, and then moving to the next step from the same page. The flow is designed to stay simple so you do not need to jump between several sites just to finish one booking.')
            ],
            [
                'question' => __('Can I compare different services before I choose?'),
                'answer' => __('Yes. The platform is built to help users compare plans, travel options, and support choices in a clear way. The goal is to make the decision easier, not to hide important details behind a crowded layout.')
            ],
            [
                'question' => __('Do you support both Arabic and English content?'),
                'answer' => __('Yes. We keep the experience bilingual so the interface feels natural for Arabic readers and still works well for English users. The copy is written to be easy to scan, easy to understand, and consistent across both languages.')
            ],
            [
                'question' => __('What happens if I need help after booking?'),
                'answer' => __('Our support flow is part of the experience, not an extra step. If you need help after booking, you can reach the team through the available contact channels and continue your trip with clearer guidance.')
            ],
            [
                'question' => __('Are membership benefits connected to my travel activity?'),
                'answer' => __('Yes. The membership experience is designed to add value to the travel journey, whether through better support, clearer access, or reward-focused features. It helps keep the traveler within one connected system instead of separating booking and membership into different places.')
            ],
            [
                'question' => __('How can I contact your team quickly?'),
                'answer' => __('You can use the Contact Now button, join the membership area, or reach the support team directly through the WhatsApp contact card in the hero section. Every path is meant to shorten the time between a question and a real answer.')
            ],
        ];
    @endphp

    <section class="about-hero py-100">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="about-hero__eyebrow" dir="auto">{{ $heroTag }}</span>
                    <h1 class="about-hero__title" dir="auto">{{ $heroTitle }}</h1>
                    <p class="about-hero__lead" dir="auto">{{ $heroSubtitle }}</p>

                    <div class="about-hero__actions">
                        <a href="{{ route('contact') }}" class="btn btn--base btn-lg pills">@lang('Contact Now')</a>
                        <a href="{{ route('public.membership.details') }}" class="btn btn--light btn-lg pills about-hero__club-btn">@lang('Join Our Club')</a>
                    </div>

                    <div class="about-hero__contact mt-4">
                        <div class="about-hero__contact-inner">
                            <div class="about-hero__contact-row">
                                <div class="about-hero__contact-icon"><i class="las la-headset"></i></div>
                                <div class="about-hero__contact-body">
                                    <strong class="d-block mb-1">@lang('Ready to travel?')</strong>
                                    <p class="mb-0 text-muted">@lang('Chat with our experts') @lang('and start your journey with a professional support team.')</p>
                                </div>
                            </div>
                            <a class="btn btn--base btn-sm pills about-hero__contact-btn" href="https://api.whatsapp.com/send?phone=966574734062" target="_blank" rel="noopener">@lang('Chat with our experts')</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="about-hero__visual">
                        <div class="about-hero__visual-main">
                            <img src="{{ $heroMiddleImage }}" alt="{{ $heroTitle }}">
                        </div>
                        <div class="about-hero__visual-small about-hero__visual-small--one">
                            <img src="{{ $heroLeftImage }}" alt="{{ $heroTitle }}">
                        </div>
                        <div class="about-hero__visual-small about-hero__visual-small--two">
                            <img src="{{ $heroRightImage }}" alt="{{ $heroTitle }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-storyline py-100">
        <div class="container">
            <div class="row align-items-stretch g-5 about-storyline__row">
                <div class="col-lg-6 h-100">
                    <div class="about-storyline__content-card">
                        <span class="about-section__eyebrow">@lang('Our Story')</span>
                        <h2 class="about-section__title mb-3">@lang('Built to make every trip feel calm, premium, and easy to trust')</h2>
                        <p class="about-section__lead mb-3">@lang('We designed this experience to remove the friction that usually comes with planning a trip. Instead of bouncing between separate tools, travelers get one connected place to explore, compare, and move forward with confidence.')</p>
                        <p class="about-section__lead mb-0">@lang('The focus is not only convenience. It is clarity, support, and a sense of control from the first search to the final confirmation, so the page feels like a guided journey rather than a static form.')</p>

                        <div class="about-storyline__list mt-4">
                            <div class="about-storyline__item">
                                <i class="las la-check-circle"></i>
                                <div>
                                    <strong>@lang('One clear journey')</strong>
                                    <p>@lang('Search, booking, and support stay connected in one simple flow.')</p>
                                </div>
                            </div>
                            <div class="about-storyline__item">
                                <i class="las la-check-circle"></i>
                                <div>
                                    <strong>@lang('Content that explains')</strong>
                                    <p>@lang('Short, useful copy keeps the page clear and reassuring.')</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 h-100">
                    <div class="about-storyline__image about-storyline__image--framed">
                        <img src="{{ asset('assets/presets/default/images/our-story.jpg') }}" alt="{{ $heroTitle }}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-focus py-100 section--bg">
        <div class="container-fluid px-3 px-lg-5">
            <div class="about-focus__layout">
                <div class="about-focus__intro">
                    <span class="about-section__eyebrow">@lang('What we focus on')</span>
                    <h2 class="about-section__title mb-3">@lang('A fuller story, not just a prettier layout')</h2>
                    <p class="about-section__lead mb-4">@lang('We keep the narrative clear, premium, and easy to scan so the page reads like a structured travel story instead of a random set of sections.')</p>
                </div>

                <div class="about-focus__track-wrap">
                    <div class="about-focus__track">
                        <div class="about-focus__track-line"></div>

                        <div class="about-focus__track-node about-focus__track-node--top">
                            <div class="about-focus__year">@lang('01')</div>
                            <div class="about-focus__node-card">
                                <h5>@lang('Guided booking')</h5>
                                <p>@lang('Every call to action points to a next step, so users always know where to go next.')</p>
                            </div>
                        </div>

                        <div class="about-focus__track-node about-focus__track-node--center">
                            <div class="about-focus__year">@lang('02')</div>
                            <div class="about-focus__node-card">
                                <h5>@lang('Trust and clarity')</h5>
                                <p>@lang('We combine short highlights with longer explanations so the page works for quick readers and detailed readers alike.')</p>
                            </div>
                        </div>

                        <div class="about-focus__track-node about-focus__track-node--bottom">
                            <div class="about-focus__year">@lang('03')</div>
                            <div class="about-focus__node-card about-focus__node-card--accent">
                                <h5>@lang('Travel with confidence')</h5>
                                <p>@lang('From the first glance to the final confirmation, the page is meant to reassure the traveler and explain the service without clutter.')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-operation py-100 section--bg">
        <div class="container">
            <div class="row align-items-end mb-4 g-3">
                <div class="col-lg-8">
                    <span class="about-section__eyebrow">@lang('Discover Our Operation')</span>
                    <h2 class="about-section__title mb-2">@lang('A global search engine for companies and individuals')</h2>
                    <p class="about-section__lead mb-0">@lang('A global search engine for companies and individuals in cooperation with one of the largest global search engine industry companies that allows you to book everything you need during your travel.')</p>
                </div>
            </div>

            <div class="row g-4 align-items-stretch">
                @foreach($aboutCards as $card)
                    <div class="col-lg-3 col-md-6">
                        <div class="about-feature-card h-100">
                            <div class="about-feature-card__icon"><i class="las la-plane"></i></div>
                            <h5>{{ $card['title'] }}</h5>
                            <p>{{ $card['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-stats py-100">
        <div class="container">
            <div class="about-banner">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="about-section__eyebrow">@lang('Join the largest and best points system that gives you various packages!')</span>
                        <h2 class="about-banner__title">@lang('Join the largest and best points system that gives you various packages!')</h2>
                        <p class="about-banner__lead">@lang('Our membership and booking experience is designed to keep everything in one place, from the first search to the final confirmation.')</p>
                        <a href="{{ route('public.membership.details') }}" class="btn btn--base pills">@lang('Check Now')</a>
                    </div>
                    <div class="col-lg-5">
                        <div class="about-package-grid">
                            <div class="about-package-card"><i class="las la-hotel"></i><span>@lang('Hotel Reservations')</span></div>
                            <div class="about-package-card"><i class="las la-plane"></i><span>@lang('Free Flight Tickets')</span></div>
                            <div class="about-package-card"><i class="las la-tags"></i><span>@lang('Discount Vouchers')</span></div>
                            <div class="about-package-card"><i class="las la-notes-medical"></i><span>@lang('Medical Tourism')</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-story py-100 section--bg">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-story__media">
                        <img src="{{ asset('assets/presets/default/images/Great-Opportunity-For-Adventure-&-Travels.jpg') }}" alt="{{ $heroTitle }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="about-section__eyebrow">@lang('Use Of The Website')</span>
                    <h2 class="about-section__title mb-3">@lang('Great Opportunity For Adventure & Travels')</h2>
                    <p class="about-section__lead mb-3">@lang('At AltayarVIP, we believe that every journey should be an adventure without the stress of logistics. Our platform provides a comprehensive gateway where you can book everything related to your travel—from flights and luxury hotels to exclusive local tours—all through one unified interface. Why jump between dozens of tabs when you can manage your entire itinerary in one place?')</p>
                    <p class="about-section__lead mb-4">@lang('Experience real-time confirmations and instant documentation. There is no longer a need to wait days for a reservation number or a manual invoice. Our automated system allows you to complete payments securely and receive your confirmation vouchers and technical invoices immediately. We focus on the details so you can focus on the memories.')</p>

                    <div class="about-rate-grid">
                        <div class="about-rate-card">
                            <strong>93%</strong>
                            <span>@lang('Selected Clients')</span>
                        </div>
                        <div class="about-rate-card">
                            <strong>97%</strong>
                            <span>@lang('Success Rate')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-features py-100">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center">
                    <span class="about-section__eyebrow">@lang('The site also includes everything you need from')</span>
                    <h2 class="about-section__title">@lang('The site also includes everything you need from')</h2>
                </div>
            </div>

            <div class="row g-4 align-items-stretch about-features__grid">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"><div class="about-mini-card flex-fill w-100"><span class="about-mini-card__icon"><i class="las la-suitcase"></i></span><h5>@lang('Booking System')</h5><p>@lang('Book for you many categories of hotels.')</p></div></div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"><div class="about-mini-card flex-fill w-100"><span class="about-mini-card__icon"><i class="las la-headset"></i></span><h5>@lang('Customer Care')</h5><p>@lang('Available 24/7, to help you by inquiry.')</p></div></div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"><div class="about-mini-card flex-fill w-100"><span class="about-mini-card__icon"><i class="las la-map-marked-alt"></i></span><h5>@lang('Localwide')</h5><p>@lang('Integrated entertainment services.')</p></div></div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"><div class="about-mini-card flex-fill w-100"><span class="about-mini-card__icon"><i class="las la-globe"></i></span><h5>@lang('Worldwide')</h5><p>@lang('Various travel programs and tours.')</p></div></div>
            </div>
        </div>
    </section>

    <section class="about-agents py-100 section--bg">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-agents__gallery">
                        <img src="{{ asset('assets/presets/default/images/membership-details.jpg') }}" alt="{{ $heroTitle }}">
                        <img src="{{ asset('assets/presets/default/images/premium-membership.jpg') }}" alt="{{ $heroTitle }}">
                        <img src="{{ asset('assets/presets/default/images/Live-travel-control-center.jpg') }}" alt="{{ $heroTitle }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="about-section__eyebrow">@lang('Tourism Operations')</span>
                    <h2 class="about-section__title mb-3">@lang('Our Agent System')</h2>
                    <div class="about-agent-list">
                        <div class="about-agent-list__item"><i class="las la-check-circle"></i><div><h5>@lang('Low Price & Friendly')</h5><p>@lang('Get the lowest prices on all services and the best savings from any other booking site.')</p></div></div>
                        <div class="about-agent-list__item"><i class="las la-check-circle"></i><div><h5>@lang('Payment Methods')</h5><p>@lang('We provide secure payment methods that come in more than one currency.')</p></div></div>
                        <div class="about-agent-list__item"><i class="las la-check-circle"></i><div><h5>@lang('Trusted Travel Guide')</h5><p>@lang('Customer support is at your service to complete all your reservations with ease.')</p></div></div>
                        <div class="about-agent-list__item"><i class="las la-check-circle"></i><div><h5>@lang('Exclusive Member Perks')</h5><p>@lang('Unlock special discounts and priority access to premium services worldwide through our membership network.')</p></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-faq py-100 section--bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 text-center">
                    <span class="about-section__eyebrow">@lang('Our Faq')</span>
                    <h2 class="about-section__title mb-3">@lang('Frequently Asked Questions.')</h2>
                    <p class="about-section__lead mb-0">@lang('Find answers to some of the most frequently asked questions from our travelers.')</p>
                </div>
            </div>

            <div class="row mt-5 g-4 justify-content-center">
                @foreach ($aboutFaqItems as $index => $faqItem)
                    <div class="col-lg-6">
                        <div class="about-faq__item wow animate__fadeInUp animate__animated" data-wow-delay="{{ 0.1 * ($index + 1) }}s">
                            <button class="about-faq__toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aboutFaq{{ $index }}" aria-expanded="false" aria-controls="aboutFaq{{ $index }}">
                                <span>{{ $faqItem['question'] }}</span>
                                <i class="las la-plus"></i>
                            </button>
                            <div id="aboutFaq{{ $index }}" class="collapse about-faq__content">
                                <p>{{ $faqItem['answer'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('style')
<style>
    .about-hero,
    .about-operation,
    .about-stats,
    .about-features,
    .about-agents {
        position: relative;
    }

    .about-hero {
        overflow: hidden;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .about-hero__eyebrow,
    .about-section__eyebrow {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 14px;
        background: rgba(34, 87, 191, 0.08);
        color: #2257bf;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .about-hero__title,
    .about-section__title,
    .about-banner__title {
        color: #14213d;
        font-weight: 900;
        letter-spacing: -0.03em;
        line-height: 1.08;
    }

    .about-hero__title {
        font-size: clamp(40px, 5vw, 64px);
        margin-bottom: 16px;
    }

    .about-hero__lead,
    .about-section__lead,
    .about-banner__lead,
    .about-feature-card p,
    .about-mini-card p,
    .about-agent-list__item p {
        color: #5c677d;
        line-height: 1.85;
        margin-bottom: 0;
    }

    .about-hero__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .about-hero__club-btn {
        background: #ffffff !important;
        color: #14213d !important;
        border: 1px solid rgba(20, 33, 61, 0.08) !important;
        box-shadow: 0 12px 28px rgba(20, 33, 61, 0.06);
        transition: all 0.25s ease;
    }

    .about-hero__club-btn:hover {
        background: #39bff9 !important;
        color: #ffffff !important;
        border-color: #39bff9 !important;
        transform: translateY(-2px);
        box-shadow: 0 16px 30px rgba(57, 191, 249, 0.22);
    }

    .about-hero__contact-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(34, 87, 191, 0.12);
        color: #2257bf;
        font-size: 22px;
    }

    .about-hero__contact {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        padding: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(34, 87, 191, 0.08);
        box-shadow: 0 16px 36px rgba(20, 33, 61, 0.08);
    }

    .about-hero__contact::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(57, 191, 249, 0.10);
        pointer-events: none;
    }

    .about-hero__contact-inner {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 16px;
    }

    .about-hero__contact-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .about-hero__contact-body strong {
        color: #14213d;
        font-size: 20px;
        font-weight: 800;
    }

    .about-hero__contact-body p {
        color: #63708a;
        line-height: 1.8;
        font-size: 15px;
    }

    .about-hero__contact-btn {
        width: fit-content;
        margin-inline-start: 62px;
    }

    .about-hero__visual {
        position: relative;
        min-height: 520px;
    }

    .about-hero__visual-main,
    .about-hero__visual-small {
        overflow: hidden;
        border-radius: 28px;
        box-shadow: 0 20px 50px rgba(20, 33, 61, 0.12);
    }

    .about-hero__visual-main {
        width: 72%;
        min-height: 500px;
        margin-inline-start: auto;
    }

    .about-hero__visual-main img,
    .about-hero__visual-small img,
    .about-story__media img,
    .about-agents__gallery img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 34px;
    }

    .about-story__media {
        overflow: hidden;
        border-radius: 34px;
        box-shadow: 0 20px 50px rgba(20, 33, 61, 0.12);
    }

    .about-hero__visual-small {
        position: absolute;
        width: 42%;
        height: 180px;
        border: 8px solid #fff;
        background: #fff;
    }

    .about-hero__visual-small--one {
        left: 0;
        top: 40px;
    }

    .about-hero__visual-small--two {
        left: 0;
        bottom: 38px;
    }

    .about-operation,
    .about-features,
    .about-agents,
    .faq-section {
        background: #fff;
    }

    .about-feature-card,
    .about-mini-card,
    .about-rate-card,
    .about-agent-list__item,
    .about-banner {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(34, 87, 191, 0.08);
        box-shadow: 0 18px 40px rgba(20, 33, 61, 0.06);
    }

    .about-feature-card {
        height: 100%;
        border-radius: 24px;
        padding: 24px;
    }

    .about-feature-card__icon,
    .about-mini-card__icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #2257bf;
        background: linear-gradient(135deg, rgba(34, 87, 191, 0.12), rgba(57, 191, 249, 0.16));
        font-size: 22px;
    }

    .about-mini-card__icon {
        flex-shrink: 0;
        margin-bottom: 6px;
    }

    .about-mini-card__icon i {
        color: inherit;
        font-size: inherit;
    }

    .about-feature-card h5,
    .about-mini-card h5,
    .about-rate-card strong,
    .about-agent-list__item h5 {
        margin-bottom: 10px;
        color: #14213d;
        font-weight: 800;
    }

    .about-banner {
        border-radius: 30px;
        padding: 30px;
        background: linear-gradient(135deg, rgba(34, 87, 191, 0.06), rgba(0, 188, 212, 0.05));
    }

    .about-package-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .about-package-card {
        border-radius: 20px;
        padding: 18px;
        text-align: center;
        background: #fff;
        border: 1px solid rgba(34, 87, 191, 0.08);
        box-shadow: 0 12px 30px rgba(20, 33, 61, 0.05);
    }

    .about-package-card i {
        display: inline-flex;
        width: 44px;
        height: 44px;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        margin-bottom: 10px;
        color: #39bff9;
        background: rgba(57, 191, 249, 0.10);
        font-size: 20px;
    }

    .about-mini-card {
        min-height: 100%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        border-radius: 28px;
        padding: 28px;
        text-align: start;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .about-mini-card p {
        flex: 1 1 auto;
        margin-bottom: 0;
    }

    .about-mini-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 46px rgba(20, 33, 61, 0.10);
    }

    .about-agent-list {
        display: grid;
        gap: 14px;
    }

    .about-agent-list__item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        border-radius: 22px;
        padding: 18px 20px;
    }

    .about-agent-list__item i {
        color: #39bff9;
        font-size: 22px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .about-faq {
        background: #fff;
    }

    .about-faq__item {
        border-radius: 20px;
        border: 1px solid rgba(34, 87, 191, 0.08);
        background: #fff;
        box-shadow: 0 14px 34px rgba(20, 33, 61, 0.05);
        overflow: hidden;
    }

    .about-faq__toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border: 0;
        background: transparent;
        color: #14213d;
        font-size: 18px;
        font-weight: 800;
        text-align: start;
    }

    .about-faq__toggle i {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #2257bf;
        background: rgba(34, 87, 191, 0.08);
        transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease;
    }

    .about-faq__toggle[aria-expanded="true"] i {
        transform: rotate(45deg);
        background: rgba(57, 191, 249, 0.14);
        color: #39bff9;
    }

    .about-faq__content {
        padding: 0 24px 22px;
    }

    .about-faq__content p {
        color: #5c677d;
        line-height: 1.9;
        margin-bottom: 0;
    }

    .about-rate-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 24px;
    }

    .about-rate-card {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid rgba(34, 87, 191, 0.08);
        box-shadow: 0 8px 24px rgba(20, 33, 61, 0.04);
    }

    .about-rate-card strong {
        font-size: 20px;
        color: #2257bf;
        margin-bottom: 0;
    }

    .about-rate-card span {
        font-size: 14px;
        color: #5c677d;
        font-weight: 600;
        white-space: nowrap;
    }

    .about-storyline {
        background: #fff;
    }

    .about-storyline__list {
        display: grid;
        gap: 14px;
    }

    .about-storyline__content-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        gap: 12px;
        min-height: 0;
        padding: 0;
        padding-inline-end: 12px;
        border-radius: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .about-storyline__image {
        height: 100%;
        min-height: 540px;
        border-radius: 34px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(20, 33, 61, 0.14);
        background: linear-gradient(180deg, rgba(34, 87, 191, 0.10), rgba(255, 255, 255, 0.02));
        border: 1px solid rgba(34, 87, 191, 0.08);
        padding: 0;
    }

    .about-storyline__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 34px;
        display: block;
    }

    .about-storyline__image--framed {
        position: relative;
    }

    .about-storyline__image--framed::before {
        content: '';
        position: absolute;
        inset: 18px auto auto 18px;
        width: 120px;
        height: 120px;
        border-radius: 28px;
        background: rgba(57, 191, 249, 0.10);
        pointer-events: none;
    }

    .about-storyline__image--framed::after {
        content: '';
        position: absolute;
        right: 18px;
        bottom: 18px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(34, 87, 191, 0.08);
        pointer-events: none;
    }

    [dir="rtl"] .about-storyline__image--framed::before {
        inset: 18px 18px auto auto;
    }

    [dir="rtl"] .about-storyline__image--framed::after {
        left: 18px;
        right: auto;
    }

    .about-storyline__item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 18px 20px;
        border-radius: 20px;
        border: 1px solid rgba(34, 87, 191, 0.08);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 12px 30px rgba(20, 33, 61, 0.05);
    }

    .about-storyline__item i {
        color: #39bff9;
        font-size: 22px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .about-storyline__item strong {
        display: block;
        margin-bottom: 6px;
        color: #14213d;
        font-size: 18px;
        font-weight: 800;
    }

    .about-storyline__item p {
        color: #5c677d;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .about-storyline__panel {
        border-radius: 28px;
        padding: clamp(28px, 3vw, 44px);
        background: linear-gradient(180deg, #0f1f3c 0%, #173c72 100%);
        color: #fff;
        box-shadow: 0 20px 50px rgba(20, 33, 61, 0.12);
    }

    .about-storyline__panel-head span {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 14px;
        background: rgba(255, 255, 255, 0.12);
        color: #dcecff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .about-storyline__panel-head strong {
        display: block;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 900;
        margin-bottom: 22px;
        color: #ffffff;
    }

    .about-focus__layout {
        display: grid;
        grid-template-columns: minmax(320px, 0.95fr) minmax(0, 2fr);
        gap: 28px;
        align-items: center;
    }

    .about-focus__intro {
        max-width: 560px;
    }

    .about-focus__track-wrap {
        overflow: hidden;
    }

    .about-focus__track {
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
        align-items: stretch;
        min-height: 330px;
        padding: 34px 22px 0;
    }

    .about-focus__track-line {
        position: absolute;
        left: 22px;
        right: 22px;
        top: 98px;
        height: 18px;
        border-radius: 999px;
        background: linear-gradient(90deg, #2fb2ef 0%, #2a8fe0 35%, #1f5ea6 70%, #8bd6f7 100%);
        box-shadow: 0 16px 34px rgba(20, 33, 61, 0.12);
    }

    @media (min-width: 992px) {
        [dir="rtl"] .about-focus__track-line {
            background: linear-gradient(90deg, #8bd6f7 0%, #1f5ea6 30%, #2a8fe0 65%, #2fb2ef 100%);
        }
    }

    .about-focus__track-node {
        position: relative;
        z-index: 1;
        display: grid;
        align-content: start;
        gap: 14px;
    }

    .about-focus__track-node--center {
        margin-top: 56px;
    }

    .about-focus__track-node--bottom {
        margin-top: 96px;
    }

    .about-focus__year {
        width: fit-content;
        padding: 10px 16px;
        border-radius: 999px;
        background: #fff;
        color: #2a86de;
        font-size: 34px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.04em;
        box-shadow: 0 14px 30px rgba(20, 33, 61, 0.08);
    }

    .about-focus__node-card {
        min-height: 160px;
        border-radius: 24px;
        padding: 24px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(34, 87, 191, 0.08);
        box-shadow: 0 16px 36px rgba(20, 33, 61, 0.06);
    }

    .about-focus__node-card h5 {
        color: #14213d;
        font-weight: 900;
        margin-bottom: 10px;
    }

    .about-focus__node-card p {
        color: #5c677d;
        line-height: 1.85;
        margin-bottom: 0;
    }

    .about-focus__node-card--accent {
        background: linear-gradient(180deg, rgba(34, 87, 191, 0.06), rgba(57, 191, 249, 0.10));
    }

    .about-timeline__card h5 {
        color: #ffffff;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .about-focus {
        background: #fff;
    }

    .about-agents__gallery {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .about-agents__gallery img:first-child {
        grid-column: span 2;
        min-height: 220px;
        border-radius: 24px;
    }

    .about-agents__gallery img:nth-child(2),
    .about-agents__gallery img:nth-child(3) {
        min-height: 190px;
        border-radius: 24px;
    }

    @media (max-width: 991px) {
        .about-hero__visual {
            min-height: 420px;
        }

        .about-hero__visual-main {
            min-height: 360px;
        }

        .about-storyline__panel {
            padding: 24px;
        }

        .about-storyline__image {
            min-height: 380px;
        }

        .about-storyline__content-card {
            padding-inline-end: 8px;
            min-height: 0;
        }

        .about-storyline__image {
            height: auto;
            min-height: 380px;
        }

        .about-focus__layout {
            grid-template-columns: 1fr;
            gap: 22px;
        }

        .about-focus__track {
            grid-template-columns: 1fr;
            min-height: 0;
            padding-top: 24px;
            gap: 18px;
        }

        .about-focus__track-line {
            left: 16px;
            right: auto;
            top: 0;
            bottom: 0;
            width: 4px;
            height: auto;
            background: linear-gradient(180deg, #2fb2ef 0%, #2a8fe0 35%, #1f5ea6 70%, #8bd6f7 100%);
        }

        [dir="rtl"] .about-focus__track-line {
            left: auto !important;
            right: 16px;
        }

        .about-focus__track-node,
        .about-focus__track-node--center,
        .about-focus__track-node--bottom {
            margin-top: 0;
        }

        .about-focus__year {
            font-size: 24px;
        }

        .about-focus__node-card {
            min-height: 0;
            padding: 20px;
        }

        .about-package-grid,
        .about-rate-grid,
        .about-agents__gallery {
            grid-template-columns: 1fr;
        }

        .about-agents__gallery img:first-child {
            grid-column: span 1;
        }
    }

    @media (max-width: 575px) {
        .about-banner,
        .about-feature-card,
        .about-mini-card,
        .about-rate-card,
        .about-agent-list__item {
            padding: 20px;
        }

        .about-mini-card {
            gap: 12px;
        }

        .about-hero__visual-small {
            width: 48%;
            height: 150px;
        }

        .about-hero__visual-main {
            width: 78%;
        }

        .about-hero__contact-row {
            align-items: center;
        }

        .about-hero__contact-btn {
            margin-inline-start: 0;
            width: 100%;
        }

        .about-storyline__item,
        .about-storyline__card {
            padding: 18px;
        }

        .about-storyline__image {
            min-height: 300px;
        }

        .about-storyline__content-card {
            padding: 0;
            min-height: 0;
        }

        .about-focus__track {
            padding-left: 0;
            padding-right: 0;
        }

        .about-focus__track-line {
            left: 14px;
        }

        [dir="rtl"] .about-focus__track-line {
            left: auto !important;
            right: 14px;
        }

        .about-focus__node-card {
            padding: 18px;
        }

        .about-faq__toggle {
            padding: 18px 20px;
            font-size: 16px;
        }

        .about-faq__content {
            padding: 0 20px 18px;
        }
    }

    /* RTL: rely on <html dir="rtl"> + logical props; keep centered feature/FAQ headers centered. */
    [dir="rtl"] .about-storyline,
    [dir="rtl"] .about-focus__intro,
    [dir="rtl"] .about-operation,
    [dir="rtl"] .about-stats,
    [dir="rtl"] .about-story .col-lg-6:last-child,
    [dir="rtl"] .about-agents .col-lg-6:last-child {
        text-align: start;
    }

    [dir="rtl"] .about-storyline__content-card {
        text-align: start;
        align-items: flex-start;
    }

    [dir="rtl"] .about-storyline__item {
        text-align: start;
    }

    [dir="rtl"] .about-hero .col-lg-6:first-child {
        text-align: start;
    }

    [dir="rtl"] .about-banner .col-lg-7,
    [dir="rtl"] .about-operation .col-lg-8 {
        text-align: start;
    }

    [dir="rtl"] .about-hero__actions,
    [dir="rtl"] .about-hero__contact-inner,
    [dir="rtl"] .about-rate-grid {
        justify-content: flex-start;
    }

    [dir="rtl"] .about-hero__contact-row {
        direction: rtl;
    }

    [dir="rtl"] .about-hero__contact::after {
        right: auto;
        left: -40px;
    }

    [dir="rtl"] .about-hero__contact-btn {
        margin-inline-start: 0;
        margin-inline-end: 62px;
    }

    /*
      Hero collage LTR: small frames left (left:0), main 72% pushed right (margin-inline-start:auto).
      RTL mirror: small frames on physical right (right:0), main pushed left — use margin-inline-start:auto
      in RTL (margin-right:auto) so the large card sits on the opposite side from English.
    */
    [dir="rtl"] .about-hero__visual-main {
        margin-inline-start: auto;
        margin-inline-end: 0;
    }

    [dir="rtl"] .about-hero__visual-small {
        left: auto;
        right: 0;
    }

    [dir="rtl"] .about-agent-list__item {
        direction: rtl;
    }

    [dir="rtl"] .about-faq__toggle {
        direction: rtl;
        text-align: start;
    }

    [dir="rtl"] .about-focus__node-card h5,
    [dir="rtl"] .about-focus__node-card p,
    [dir="rtl"] .about-focus__intro {
        text-align: start;
    }
</style>
@endpush
