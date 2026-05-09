@php
    $faq = getContent('faq.content', true);
    $faqElements = getContent('faq.element', false, 8);
    $time = 0.1;

    if (request()->path() === 'blog') {
        $faq = (object) [
            'data_values' => (object) [
                'title' => 'About Us',
                'title_ar' => 'من نحن',
                'heading' => 'Common Questions',
                'heading_ar' => 'الأسئلة الشائعة',
                'sub_heading' => 'Quick answers about our booking flow, support, and membership experience.',
                'sub_heading_ar' => 'إجابات سريعة عن مسار الحجز والدعم وتجربة العضوية.',
            ],
        ];

        $faqElements = collect([
            [
                'question' => 'How do I start booking with your platform?',
                'question_ar' => 'كيف أبدأ الحجز عبر منصتكم؟',
                'answer' => '<p>Start by exploring the available travel services, then choose the option that matches your trip. From there, continue through the same page with a simple flow that keeps the process clear from search to confirmation.</p>',
                'answer_ar' => '<p>ابدأ بتصفح خدمات السفر المتاحة، ثم اختر الخيار المناسب لرحلتك. بعد ذلك يمكنك المتابعة من نفس الصفحة عبر مسار بسيط يحافظ على وضوح الخطوات من البحث حتى التأكيد.</p>',
            ],
            [
                'question' => 'Can I compare services before I choose?',
                'question_ar' => 'هل يمكنني مقارنة الخدمات قبل الاختيار؟',
                'answer' => '<p>Yes. The site is set up to help you compare travel options, support levels, and membership benefits before you make a decision. We keep the layout focused so the details stay easy to review.</p>',
                'answer_ar' => '<p>نعم. تم تصميم الموقع لمساعدتك على مقارنة خيارات السفر ومستوى الدعم ومزايا العضوية قبل اتخاذ القرار. نحافظ على تصميم واضح حتى تبقى التفاصيل سهلة المراجعة.</p>',
            ],
            [
                'question' => 'Do you support Arabic and English?',
                'question_ar' => 'هل تدعمون العربية والإنجليزية؟',
                'answer' => '<p>Yes. We support both languages so the experience feels natural no matter which language you prefer. The content is written to stay clear and easy to follow in both Arabic and English.</p>',
                'answer_ar' => '<p>نعم. نحن ندعم اللغتين حتى تكون التجربة طبيعية مهما كانت اللغة التي تفضلها. المحتوى مكتوب ليبقى واضحاً وسهل المتابعة بالعربية والإنجليزية.</p>',
            ],
            [
                'question' => 'What happens if I need help after booking?',
                'question_ar' => 'ماذا يحدث إذا احتجت إلى مساعدة بعد الحجز؟',
                'answer' => '<p>Our support team is available to help after booking, whether you need clarification, follow-up, or a quick update. You can reach out through the available contact channels and continue with confidence.</p>',
                'answer_ar' => '<p>فريق الدعم لدينا متاح للمساعدة بعد الحجز، سواء كنت تحتاج إلى توضيح أو متابعة أو تحديث سريع. يمكنك التواصل عبر قنوات الاتصال المتاحة والمتابعة بثقة.</p>',
            ],
            [
                'question' => 'Are membership benefits connected to travel activity?',
                'question_ar' => 'هل ترتبط مزايا العضوية بنشاط السفر؟',
                'answer' => '<p>Yes. Membership is designed to add value to your travel journey by giving you a clearer path to support, access, and premium service features. It keeps everything connected in one place instead of splitting the experience across separate systems.</p>',
                'answer_ar' => '<p>نعم. تم تصميم العضوية لتضيف قيمة إلى رحلة سفرك عبر منحك وصولاً أوضح إلى الدعم والمزايا والخدمات المميزة. وهي تجمع التجربة في مكان واحد بدل تقسيمها بين أنظمة مختلفة.</p>',
            ],
            [
                'question' => 'Can I use the platform for business travel too?',
                'question_ar' => 'هل يمكن استخدام المنصة لرحلات العمل أيضاً؟',
                'answer' => '<p>Yes. The platform works for personal and business travel alike, so you can use it for weekend trips, corporate movement, or any journey that needs a clean and reliable booking flow.</p>',
                'answer_ar' => '<p>نعم. تعمل المنصة للرحلات الشخصية ورحلات العمل معاً، لذلك يمكنك استخدامها للرحلات القصيرة أو التنقلات الخاصة بالشركات أو أي رحلة تحتاج إلى مسار حجز واضح وموثوق.</p>',
            ],
            [
                'question' => 'How can I contact your team quickly?',
                'question_ar' => 'كيف يمكنني التواصل مع فريقكم بسرعة؟',
                'answer' => '<p>You can use the contact buttons on the site, visit the membership page, or reach the support team directly through the available contact methods. Every path is meant to shorten the time between your question and a real answer.</p>',
                'answer_ar' => '<p>يمكنك استخدام أزرار التواصل في الموقع، أو زيارة صفحة العضوية، أو التواصل مباشرة مع فريق الدعم عبر وسائل الاتصال المتاحة. كل طريق مصمم لتقليل الوقت بين سؤالك وبين الحصول على جواب فعلي.</p>',
            ],
        ])->map(function ($item) {
            return (object) ['data_values' => (object) $item];
        })->all();
    } elseif (request()->path() === 'browse') {
        $faq = (object) [
            'data_values' => (object) [
                'title' => 'Common Questions',
                'title_ar' => 'الأسئلة الشائعة',
                'heading' => 'Everything You Need Before Booking',
                'heading_ar' => 'كل ما تحتاج معرفته قبل الحجز',
                'sub_heading' => 'Quick answers about packages, support, and how the travel search works.',
                'sub_heading_ar' => 'إجابات سريعة عن الباقات والدعم وطريقة عمل البحث عن الرحلات.',
            ],
        ];

        $faqElements = collect([
            [
                'question' => 'How do I book a tour package?',
                'question_ar' => 'كيف أحجز باقة رحلة؟',
                'answer' => '<p>Select the tour package you like, open the details page, and continue to booking. The flow is designed to keep the steps short and easy to follow.</p>',
                'answer_ar' => '<p>اختر باقة الرحلة المناسبة لك، ثم افتح صفحة التفاصيل وانتقل إلى الحجز. صممنا المسار ليكون قصيرًا وواضحًا وسهل المتابعة.</p>',
            ],
            [
                'question' => 'Can I search by location, date, or number of travelers?',
                'question_ar' => 'هل يمكنني البحث حسب الموقع أو التاريخ أو عدد المسافرين؟',
                'answer' => '<p>Yes. You can use the browse filters to narrow results by location, travel date, and the number of travelers so you reach the right trip faster.</p>',
                'answer_ar' => '<p>نعم. يمكنك استخدام فلاتر التصفح لتضييق النتائج حسب الموقع وتاريخ السفر وعدد المسافرين حتى تصل إلى الرحلة المناسبة بسرعة أكبر.</p>',
            ],
            [
                'question' => 'What if I need help choosing the right trip?',
                'question_ar' => 'ماذا لو احتجت مساعدة في اختيار الرحلة المناسبة؟',
                'answer' => '<p>Our support team can guide you through the options and help you compare the available packages before you book.</p>',
                'answer_ar' => '<p>يمكن لفريق الدعم مساعدتك في استعراض الخيارات ومقارنة الباقات المتاحة قبل إتمام الحجز.</p>',
            ],
            [
                'question' => 'Do you offer member-only travel benefits?',
                'question_ar' => 'هل تقدمون مزايا سفر خاصة بالأعضاء؟',
                'answer' => '<p>Yes. Some offers and services are designed for club members, giving them exclusive access and better booking value.</p>',
                'answer_ar' => '<p>نعم. بعض العروض والخدمات مخصصة لأعضاء النادي، بما يمنحهم وصولًا حصريًا وقيمة أفضل عند الحجز.</p>',
            ],
            [
                'question' => 'Can I continue in Arabic or English?',
                'question_ar' => 'هل يمكنني المتابعة بالعربية أو الإنجليزية؟',
                'answer' => '<p>Absolutely. The platform supports both Arabic and English so the experience stays clear in the language you prefer.</p>',
                'answer_ar' => '<p>بالتأكيد. المنصة تدعم العربية والإنجليزية حتى تبقى التجربة واضحة باللغة التي تفضلها.</p>',
            ],
            [
                'question' => 'Is booking available right away from the result card?',
                'question_ar' => 'هل يمكنني الحجز مباشرة من بطاقة النتيجة؟',
                'answer' => '<p>Yes. Each card links to the trip details and booking flow, making it easy to move from browsing to reserving your trip.</p>',
                'answer_ar' => '<p>نعم. كل بطاقة تنقلك إلى تفاصيل الرحلة ومسار الحجز، لذلك يمكنك الانتقال بسهولة من التصفح إلى الحجز.</p>',
            ],
        ])->map(function ($item) {
            return (object) ['data_values' => (object) $item];
        })->all();
    }
@endphp

<section class="faq-section {{ request()->path() == 'about' ? 'py-100 ' : 'py-100 section--bg' }} position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ getLangContent($faq->data_values, 'title') }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ getLangContent($faq->data_values, 'heading') }}</h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ getLangContent($faq->data_values, 'sub_heading') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @php
            $leftColumn = [];
            $rightColumn = [];
            $time = 0;
        @endphp

        @foreach ($faqElements ?? [] as $index => $item)
            @if ($index % 2 == 0)
                @php $leftColumn[] = ['item' => $item, 'delay' => $time += 0.1]; @endphp
            @else
                @php $rightColumn[] = ['item' => $item, 'delay' => $time += 0.1]; @endphp
            @endif
        @endforeach

        <div class="row gy-5 justify-content-between position-relative">
            @foreach (['leftColumn' => $leftColumn, 'rightColumn' => $rightColumn] as $col => $items)
                @php $accordionId = "accordionFlush_$col"; @endphp
                <div class="col-lg-6 mt-4">
                    <div class="accordion custom--accordion1 accordion-flush" id="{{ $accordionId }}">
                        @foreach ($items as $i => $data)
                            @php
                                $item = $data['item'];
                                $delay = $data['delay'];
                                $collapseId = "flush-collapse-{$col}-{$i}";
                            @endphp
                            <div class="accordion-item wow animate__fadeInUp animate__animated" data-wow-delay="{{ $delay }}s">
                                <div class="accordion-header">
                                    <div class="bar"></div>
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}" aria-expanded="false"
                                        aria-controls="{{ $collapseId }}">
                                            {{ getLangContent($item->data_values, 'question') }}
                                    </button>
                                </div>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                                    data-bs-parent="#{{ $accordionId }}">
                                    <div class="accordion-body">
                                            {!! getLangContent($item->data_values, 'answer') !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>


    </div>


</section>
