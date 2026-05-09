@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
    $testimonialContent = getContent('testimonial.content', true);
    // Re-using the same 52+ testimonials from the home section
    $customTestimonials = [
        [
            'name' => 'فهد بن ناصر العتيبي',
            'designation' => 'الرياض - رجل أعمال',
            'description' => 'بيض الله وجيهكم على هالخدمة، بصراحة الطيار VIP غيروا مفهوم السفر عندنا. حجزت العضوية الماسية والخصومات اللي حصلتها خيالية، والدفع آمن جداً يخليك مرتاح وأنت حجز. الله يبارك لكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سلطان الشمري',
            'designation' => 'حائل - مهندس',
            'description' => 'يا جماعة شاشة الحجز عندهم سريعة وتفتح النفس، ما فيها أي تعقيد. الموظفين تعاملهم راقي جداً ودايماً موجودين لو احتجت شي. والصدق إن نظام النقاط عندهم يحمس الواحد يسافر كل أسبوع.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'نورة القحطاني',
            'designation' => 'جدة - مصممة',
            'description' => 'تجربتي كانت تجنن! حجزت رحلة لتركيا والعملية خلصت بلمح البصر. أهم شي عندي الأمان في الدفع وهذا اللي لقيته هنا. والموظفين قمة في الذوق والاحترافية.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'عبد العزيز الدوسري',
            'designation' => 'الدمام - إدارة مشاريع',
            'description' => 'خدمة ملكية بمعنى الكلمة. نظام العضويات مو مجرد كلام، فعلاً استفدت من مميزات المطار والفنادق. والموقع استجابتة سريعة والدفع فيه يطمن القلب. كفو والله!',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'تركي بن فيصل',
            'designation' => 'القصيم - أعمال حرة',
            'description' => 'ما قصرتوا يا شباب، أفضل موقع حجز تعاملت معه. الواجهة سهلة جداً حتى على الجوال، والدفع بالبطاقة سريع وآمن 100%. الله يوفقكم ومنها للأعلى.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سارة المطيري',
            'designation' => 'مكة المكرمة - دكتورة',
            'description' => 'بكل أمانة، الطيار VIP هم الأفضل حالياً. نظام النقاط فكرته جبارة وتخلي الواحد يحس بتقدير. والتعامل مع الموظفين يريح البال لأنهم فاهمين شغلهم صح.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ماجد العنزي',
            'designation' => 'المدينة المنورة - معلم',
            'description' => 'حجزت باقة السفر كاملة من الموقع، والدفع كان سهل جداً والموقع موثوق. الموظف اللي تواصل معي كان جداً ودود وخدوم. تجربة تكرر بإذن الله.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'فيصل الحربي',
            'designation' => 'تبوك - استشاري',
            'description' => 'مصداقية وأمان وسرعة. هذي الثلاث كلمات تختصر تجربتي معهم. شاشة الحجز متطورة جداً ونظام الدفع يخليك تحجز وأنت مغمض. استمروا يا وحوش.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سلمان القرني',
            'designation' => 'أبها - مصور',
            'description' => 'يا لبيه على هالخدمة! حجزت رحلة عائلية وكان تنسيقهم يبيض الوجه. الموظفين بشوشين وخدومين لأبعد درجة. الدفع كان سهل جداً والتأكيد وصلي في ثواني.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'مشاعل السبيعي',
            'designation' => 'الخرج - سيدة أعمال',
            'description' => 'دايم أدور الأمان في الدفع والسرعة في الحجز، ولقيتهم كلهم عند الطيار VIP. نظام العضوية البلاتينية اللي أنا فيه بطل وخدماته مالها مثيل. شكراً لكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'نايف الجابري',
            'designation' => 'الطائف - معلم',
            'description' => 'ما شاء الله تبارك الله، موقع مرتب وشغل متعوب عليه. الحجز عن طريقهم مريح وسهل، ونظام النقاط يخليك توفر مبالغ محترمة في كل رحلة. نوصي بالتعامل معهم وبقوة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'بندري الهذلي',
            'designation' => 'الجبيل - مهندس كيميائي',
            'description' => 'الصدق يقال، تعاملت مع مواقع كثير بس مثل احترافية الطيار VIP ما شفت. الدفع شغال زي الحلاوة وآمن، والموظفين يردون عليك بلمح البصر. أهنيكم على هالمستوى.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'هيا الغامدي',
            'designation' => 'الباحة - أخصائية',
            'description' => 'موقع ذكي ونظام متطور. العضويات عندهم فعلاً VIP وتحس بفرق كبير في المعاملة والخصومات. أهم شي المصداقية في التعامل والوضوح في الأسعار والدفع الآمن.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'راكان الرويلي',
            'designation' => 'عرعر - أعمال حرة',
            'description' => 'من أفضل المواقع اللي توفر تجربة مستخدم سهلة. حجزت وسددت وأنا جالس في بيتي بكل أمان. الموظفين الله يعطيهم العافية ما قصروا في التنسيق والمتابعة. كفو.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'منيرة البقمي',
            'designation' => 'نجران - صانعة محتوى',
            'description' => 'نظام النقاط عندهم يجنن! كل ما حجزت رحلة زادت نقاطي واستبدلتها بمميزات خرافية. الموقع آمن جداً في الدفع وسهل في التصفح والحجز. تجربة 10 على 10.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ثامر الصعيري',
            'designation' => 'شرورة - تقني',
            'description' => 'بصراحة، ما توقعت الحجز يكون بهالسهولة. الموقع طيارة والدفع آمن جداً. الموظفين يردون بسرعة البرق وتعاملهم يشرح الصدر. الله يقويكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ريم الرشيدي',
            'designation' => 'حائل - معلمة',
            'description' => 'الطيار VIP هم الخيار الأول لي ولعائلتي. نظام العضويات يوفر لنا مبالغ كبيرة والمصداقية عندهم فوق كل اعتبار. الدفع مريح ومضمون 100%.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'طلال السلمي',
            'designation' => 'الليث - مدير تسويق',
            'description' => 'شغل جبار وخدمة خمس نجوم. شاشة الحجز متطورة وتحسسك إنك فعلاً VIP. نقاطي زادت بفضل رحلات العمل والخصومات كانت مجزية جداً. كفو.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'عنود الشهري',
            'designation' => 'النماص - طبيبة',
            'description' => 'أفضل تجربة لعام 2024 بلا منازع. الدفع كان بضغطة زر وكل شي مرتب ومنظم. الموظفين محترمين جداً وساعدوني في كل تفاصيل رحلتي. كل الشكر.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ياسر الثبيتي',
            'designation' => 'الطائف - أعمال حرة',
            'description' => 'يا لبيه على الترتيب! الموقع سهل الاستخدام جداً والدفع فيه آمن بالحيل. نظام العضويات بطل وما يستغني عنه أي مسافر يحب التميز والراحة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'لولوة الخالدي',
            'designation' => 'الخبر - مهندسة ديكور',
            'description' => 'الطيار VIP فعلاً اسم على مسمى. حجزت وسددت في دقيقة وحدة والتأكيد جاني فوري. نظام النقاط عندهم يحفز جداً والخدمة تفوق التوقعات دائماً.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سعد الزهراني',
            'designation' => 'الباحة - متقاعد',
            'description' => 'بيض الله وجيهكم على هالتعامل الراقي. حجزت لعيالي والكل أثنى على ترتيبكم واهتمامكم بأدق التفاصيل. الدفع آمن والموقع واضح للجميع.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'خلود الفوزان',
            'designation' => 'بريدة - أستاذة جامعية',
            'description' => 'رقي في التعامل ومصداقية في المواعيد. نظام الدفع متطور جداً ويغنيك عن القلق. نقاط العضوية فادتني في حجز الفنادق بأسعار لا تصدق. مميزين!',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'بدر السيف',
            'designation' => 'سكاكا - كاتب',
            'description' => 'تجربة ممتعة وسهلة. الموقع سريع جداً في الحجز والرد من الموظفين دايماً وافي. الأمان في الدفع هو اللي خلاني أعتمد عليهم في كل سفراتي.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'أثير الشويعر',
            'designation' => 'الزلفي - مترجمة',
            'description' => 'من قلبي أشكركم على هالإنجاز. حجزت العضوية الماسية والفرق واضح في كل شي. الدفع سهل وآمن جداً والموظفين قمة في الاحترام والذوق.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'محمد الخالدي',
            'designation' => 'الخبر - مدير مشاريع',
            'description' => 'خدمة احترافية جداً، سرعة في التجاوب وحرص على أدق التفاصيل. عجبني جداً وضوح الأسعار ونظام العضويات اللي يوفر مميزات حقيقية.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'خالد بن فهد',
            'designation' => 'الرياض - رائد أعمال',
            'description' => 'أهنيكم على هذا الموقع المتطور. الحجز صار أسهل بكثير والدفع آمن جداً. نقاط المكافآت ميزة جبارة وتخلينا نفضلكم دائماً.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سارة السيف',
            'designation' => 'حائل - مصورة',
            'description' => 'تعامل راقي جداً من الموظفين، والموقع سهل وسلس في الاستخدام. حجزت رحلتي الأخيرة خلال دقائق وبدون أي تعقيد. شكراً لكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'عبدالله المطيري',
            'designation' => 'القصيم - مهندس',
            'description' => 'تجربة مميزة جداً مع الطيار VIP. سرعة التأكيد والأمان في عملية الدفع هي أهم ما يميزكم. العضويات فعلاً تستحق الاشتراك.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ريم القحطاني',
            'designation' => 'جدة - مصممة أزياء',
            'description' => 'أفضل موقع لحجز الرحلات في السعودية. سهولة في التعامل ومصداقية تامة. فريق الدعم متعاون جداً ويردون بسرعة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'يوسف العنزي',
            'designation' => 'تبوك - استشاري قانوني',
            'description' => 'نظام العضويات الماسية مذهل ويوفر خصومات حقيقية. الموقع آمن وسريع، والتعامل مع الموظفين يشعرك بالتقدير والتميز.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'منال الحربي',
            'designation' => 'المدينة المنورة - طبيبة',
            'description' => 'شكراً على الخدمة الرائعة. كل تفاصيل الرحلة كانت مرتبة بعناية والدفع كان سهلاً ومضموناً. أنصح الجميع بالتعامل معكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سعود بن عبدالعزيز',
            'designation' => 'الرياض - مستثمر',
            'description' => 'ما شاء الله تبارك الله، واجهة الموقع احترافية والدعم الفني عالمي. نظام النقاط فادني كثير في رحلات العمل المتكررة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'نوف السبيعي',
            'designation' => 'الخرج - سيدة أعمال',
            'description' => 'احترافية ومصداقية وسرعة. هذي أهم ميزات الطيار VIP. الحجوزات دقيقة والدفع آمن تماماً، وهذا يخليني دائماً أتعامل معكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'فواز الجابري',
            'designation' => 'الطائف - رائد نشاط',
            'description' => 'بكل أمانة، أفضل نظام حجز جربته. كل شيء واضح والدفع سريع، وخدمة العملاء دائماً في الخدمة. بيض الله وجيهكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'حصة الغامدي',
            'designation' => 'أبها - أستاذة',
            'description' => 'تجربة حجز مريحة جداً. نظام العضويات يوفر ميزات لا توجد في المواقع الأخرى. أهم شيء الأمان والموثوقية التي يتمتع بها الموقع.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'بدر الرويلي',
            'designation' => 'الجوف - كاتب',
            'description' => 'موقع متميز وخدمة استثنائية. حجزت رحلة عائلية والكل كان راضي عن التنسيق والترتيب. الدفع سهل وآمن جداً.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'لجين الشهري',
            'designation' => 'النماص - صانعة محتوى',
            'description' => 'يا لبيه على الخدمة المرتبة! الموقع سريع جداً ونظام النقاط يحمس الواحد يحجز كل مرة من عندكم. استمروا على هذا المستوى.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'مساعد الثبيتي',
            'designation' => 'مكة المكرمة - إدارة',
            'description' => 'فخور بوجود موقع سعودي بهذا المستوى من الاحترافية. الحجز والدفع آمن وسهل، والخدمة فعلاً تليق بلقب VIP.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'هناء الخالدي',
            'designation' => 'الدمام - محاسبة',
            'description' => 'سرعة في الإجراءات ومصداقية في التعامل. نظام الدفع متطور ويوفر حماية كاملة. شكراً لكل من ساهم في هذا العمل الرائع.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'فارس الزهراني',
            'designation' => 'الباحة - تقني',
            'description' => 'تجربة مستخدم ممتازة والموقع خفيف وسريع. نظام العضويات يقدم قيمة مضافة حقيقية للمسافر الدائم. كفو والله.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'بشرى الفوزان',
            'designation' => 'بريدة - باحثة',
            'description' => 'الطيار VIP جعل السفر أكثر سهولة ورفاهية. كل التفاصيل كانت منظمة والتعامل مع الفريق كان مريحاً جداً. أنصح به بشدة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سلطان السديري',
            'designation' => 'الرياض - مدير تنفيذي',
            'description' => 'خدمة عالمية على أيدٍ سعودية. فخور جداً بما تقدمونه من احترافية وأمان في الحجز والدفع. العضوية البلاتينية فاقت توقعاتي.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'دلال المطيري',
            'designation' => 'عنيزة - فنانة',
            'description' => 'موقع رائع وتصميم مريح للعين. سرعة الحجز والدفع الآمن تشعرك بالاطمئنان. تعامل الموظفين قمة في الرقي.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ريان الشمري',
            'designation' => 'حائل - مبرمج',
            'description' => 'كمتخصص أرى أن الموقع مبني باحترافية عالية. نظام الحجز سلس جداً والدفع مؤمن بالكامل. كفو يا شباب.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'عهود القحطاني',
            'designation' => 'نجران - صيدلانية',
            'description' => 'أفضل تجربة حجز خضتها على الإطلاق. كل شيء مرتب وسهل، والدعم الفني متواجد على مدار الساعة لمساعدة العملاء.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'نايف البلوي',
            'designation' => 'تبوك - عسكري',
            'description' => 'مصداقية كبيرة وسرعة في تنفيذ الطلبات. حجزت وسددت خلال ثواني والتأكيد وصلني فوراً. الله يوفقكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'جواهر السبيعي',
            'designation' => 'الرياض - رائدة أعمال',
            'description' => 'نظام العضويات في الطيار VIP هو ما يميزهم فعلاً عن البقية. الخصومات والميزات حقيقية وتوفر لنا الكثير. شكراً لكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'هادي القرني',
            'designation' => 'خميس مشيط - معلم',
            'description' => 'يا زين الحجز عن طريقكم، راحة بال وأمان تام. الموظفين تعاملهم يشرح الصدر والخدمة سريعة جداً. بيض الله وجيهكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'خلود العتيبي',
            'designation' => 'الدوادمي - أخصائية',
            'description' => 'موقع متميز بكل ما تعنيه الكلمة. السهولة في الحجز والأمان في الدفع جعلتني عميلة دائمة لكم. شكراً على مجهوداتكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'نواف الجوف',
            'designation' => 'سكاكا - موظف',
            'description' => 'خدمة ممتازة وسرعة في الإنجاز. حبيت جداً نظام النقاط وكيف الواحد يستفيد منها في الحجوزات القادمة. كفو.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'سارة الشواط',
            'designation' => 'الدمام - مديرة فرع',
            'description' => 'الطيار VIP هو الخيار الأفضل لكل مسافر يبحث عن التميز والراحة. الأمان في الدفع والمصداقية هي أساس تعاملي معكم.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'عمر السقاف',
            'designation' => 'جدة - مستشار سياحي',
            'description' => 'بكل صراحة، الطيار VIP هو الواجهة الأكثر احترافية التي تعاملت معها في السعودية. الدقة في المواعيد، وخدمة العملاء الراقية، وسهولة الدفع الإلكتروني تجعلهم في الصدارة دائماً. أنصح بهم بشدة لكل من يبحث عن التميز.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ],
        [
            'name' => 'ليلى الفايز',
            'designation' => 'الخبر - رائدة أعمال',
            'description' => 'تجربة استثنائية بكل المقاييس! حجزت رحلة العمل الأخيرة عبر الموقع وكان التنسيق في غاية الروعة. نظام النقاط ميزة ذكية جداً تشجع على الولاء، والأمان في المعاملات المالية يعطي راحة بال كبيرة.',
            'star_count' => 5,
            'image' => asset('assets/images/general/favicon.png')
        ]
    ];
@endphp


<section class="testimonial-section py-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-content mb-50">
                    <div class="title-wrap text-center">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0" dir="auto">
                            {{ getLangContent($testimonialContent?->data_values, 'title') }}
                        </h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s" dir="auto">
                            {{ getLangContent($testimonialContent?->data_values, 'heading') }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400 mx-auto"
                            data-wow-delay="0.3s" dir="auto" style="max-width: 800px;">
                            {{ getLangContent($testimonialContent?->data_values, 'sub_heading') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            @foreach($customTestimonials as $item)
            <div class="col-xl-4 col-lg-6 col-md-6 d-flex align-items-stretch">
                <div class="testimonial-item style-two h-100 w-100 p-4 shadow-sm border rounded bg-white d-flex flex-column">
                    <div class="testimonial-content">
                        <div class="quote-icon mb-1 text-center">
                             <h1 class="text--base" style="font-family: 'none'; font-size: 80px; line-height: 1; opacity: 0.2; margin-bottom: -20px;">"</h1>
                        </div>
                        <p class="description fs--16 text-center italic mb-4 flex-grow-1" dir="auto">{{ $item['description'] }}</p>

                        <div class="testimonial-user d-flex align-items-center justify-content-center flex-column mt-auto">
                            <div class="user-thumb mb-3">
                                <img src="{{ $item['image'] }}" alt="user" class="rounded-circle border border-2 border--base" style="width: 70px; height: 70px; object-fit: contain; padding: 5px;">
                            </div>
                            <div class="user-info text-center">
                                <h6 class="name mb-1 fw--700" dir="auto">{{ $item['name'] }}</h6>
                                <span class="designation fs--14 text--muted" dir="auto">{{ $item['designation'] }}</span>
                                <div class="rating mt-2">
                                    @for($i=0; $i<$item['star_count']; $i++)
                                        <i class="fas fa-star text--warning"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .testimonial-item {
        transition: all 0.3s ease;
    }
    .testimonial-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .text--warning {
        color: #ffc107;
    }
    .description.italic {
        font-style: italic;
        line-height: 1.6;
        color: #555;
    }
    .border--base {
        border-color: #f7941d !important; /* Adjust based on your theme color */
    }
</style>
@endsection
