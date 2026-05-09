<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\TourPackage;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdditionalTourPackagesSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = $this->resolveCategoryId();
        $baseDate = Carbon::now()->addDay()->startOfDay();

        DB::transaction(function () use ($categoryId, $baseDate) {
            TourPackage::unguard();

            try {
                foreach ($this->packages() as $package) {
                    $durationDays = (int) ($package['duration_days'] ?? 1);
                    $payload = Arr::except($package, ['duration_days']);
                    $payload['category_id'] = $categoryId;
                    $payload['user_id'] = 1;
                    $payload['user_type'] = 'admin';
                    $payload['status'] = 1;
                    $payload['flexible_date'] = 1;

                    $dates = $this->tourDates($baseDate, $durationDays);
                    $payload['tour_start'] = $dates['start'];
                    $payload['tour_end'] = $dates['end'];

                    TourPackage::updateOrCreate(
                        ['title' => $package['title']],
                        $payload
                    );
                }
            } finally {
                TourPackage::reguard();
            }
        });
    }

    private function resolveCategoryId(): int
    {
        $categoryId = Category::where('name', 'Tour Packages')->value('id');

        if ($categoryId) {
            return (int) $categoryId;
        }

        $fallbackId = Category::where('status', 1)->orderByDesc('id')->value('id');

        if ($fallbackId) {
            return (int) $fallbackId;
        }

        throw new RuntimeException('No active category found for tour packages.');
    }

    private function tourDates(Carbon $baseDate, int $durationDays): array
    {
        $durationDays = max(1, $durationDays);

        return [
            'start' => $baseDate->copy()->toDateTimeString(),
            'end' => $baseDate->copy()->addDays($durationDays)->toDateTimeString(),
        ];
    }

    private function packages(): array
    {
        return [
            [
                'title' => 'Vietnam 8 Days 7 Nights Travel Package',
                'title_ar' => 'باقة فيتنام 8 أيام / 7 ليالي',
                'tour_type' => 'International Travel Package',
                'tour_type_ar' => 'باقة سفر دولية',
                'package_label' => '8 Days / 7 Nights',
                'package_label_ar' => '8 أيام / 7 ليالي',
                'address' => 'Vietnam',
                'address_ar' => 'فيتنام',
                'city' => 'Hanoi, Ha Long Bay, Da Nang, Hoi An',
                'country' => 'Vietnam',
                'cities_covered' => 'Hanoi, Ha Long Bay, Da Nang, Hoi An',
                'cities_covered_ar' => 'هانوي، خليج ها لونغ، دا نانغ، هوي آن',
                'latitude' => null,
                'longitude' => null,
                'day_nights' => '8 Days / 7 Nights',
                'person_capability' => 20,
                'price' => 750,
                'currency' => 'USD',
                'price_from' => 750,
                'price_to' => 1200,
                'price_note' => 'Estimated price per person. Final price may vary by season, hotel availability, flight timing, and private tour options.',
                'description' => 'Discover Vietnam in an 8-day journey covering Hanoi, Ha Long Bay, Da Nang, and Hoi An. This package combines city culture, natural beauty, a Ha Long Bay cruise, the famous Golden Bridge at Ba Na Hills, and the magical lantern streets of Hoi An.',
                'description_ar' => 'اكتشف فيتنام في رحلة مميزة لمدة 8 أيام تشمل هانوي، خليج ها لونغ، دا نانغ، وهوي آن. تجمع هذه الباقة بين الثقافة، والطبيعة، ورحلة بحرية في خليج ها لونغ، وزيارة الجسر الذهبي في با نا هيلز، وأجواء شوارع الفوانيس الساحرة في هوي آن.',
                'destination_overview' => [
                    'departure_from' => 'Airport / Client location',
                    'arrival' => 'Hanoi',
                    'transportation' => 'Private transfers, domestic flights, cruise transfer, and local transportation during tours.',
                    'accommodation' => '4-star hotels or similar with breakfast, plus 1-night cruise accommodation in Ha Long Bay.',
                    'cities_covered' => 'Hanoi, Ha Long Bay, Da Nang, Hoi An',
                ],
                'departure_from_ar' => 'المطار / موقع العميل',
                'arrival_ar' => 'هانوي',
                'transportation_ar' => 'انتقالات خاصة، رحلات داخلية، انتقالات الكروز، ومواصلات محلية أثناء الجولات.',
                'accommodation_level' => '4-star hotels or similar',
                'accommodation_ar' => 'فنادق 4 نجوم أو ما يعادلها مع الإفطار، بالإضافة إلى إقامة ليلة واحدة على الكروز في خليج ها لونغ.',
                'highlights' => [
                    'Hanoi city tour',
                    'Ha Long Bay overnight cruise',
                    'Kayaking and cave visit',
                    'Ba Na Hills and Golden Bridge',
                    'Hoi An ancient town walking tour',
                    'Lantern streets experience',
                ],
                'destination_highlights_ar' => [
                    'جولة في مدينة هانوي',
                    'رحلة بحرية ومبيت في خليج ها لونغ',
                    'كاياك وزيارة الكهوف',
                    'با نا هيلز والجسر الذهبي',
                    'جولة مشي في مدينة هوي آن القديمة',
                    'تجربة شوارع الفوانيس',
                ],
                'features' => $this->featureRows([
                    ['fa-solid fa-city', 'City tour', 'جولة في المدينة'],
                    ['fa-solid fa-ship', 'Overnight cruise', 'رحلة بحرية ومبيت'],
                    ['fa-solid fa-water', 'Kayaking', 'التجديف بالكاياك'],
                    ['fa-solid fa-mountain-sun', 'Golden Bridge', 'الجسر الذهبي'],
                    ['fa-solid fa-umbrella-beach', 'Hoi An lanterns', 'فوانيس هوي آن'],
                    ['fa-solid fa-hotel', '4-star stays', 'إقامة 4 نجوم'],
                ]),
                'destination_features_ar' => $this->destinationFeatureRows([
                    ['fa-solid fa-city', 'جولة في المدينة'],
                    ['fa-solid fa-ship', 'رحلة بحرية ومبيت'],
                    ['fa-solid fa-water', 'التجديف بالكاياك'],
                    ['fa-solid fa-mountain-sun', 'الجسر الذهبي'],
                    ['fa-solid fa-umbrella-beach', 'فوانيس هوي آن'],
                    ['fa-solid fa-hotel', 'إقامة 4 نجوم'],
                ]),
                'includes' => [
                    'Accommodation with breakfast',
                    '1-night cruise in Ha Long Bay',
                    'Domestic flights',
                    'All transfers',
                    'English-speaking guide',
                    'Entrance fees and tours',
                ],
                'includes_ar' => [
                    'الإقامة مع الإفطار',
                    'ليلة واحدة على كروز في خليج ها لونغ',
                    'الطيران الداخلي',
                    'جميع الانتقالات',
                    'مرشد سياحي باللغة الإنجليزية',
                    'رسوم الدخول والجولات السياحية',
                ],
                'excludes' => [
                    'International flights',
                    'Visa',
                    'Personal expenses',
                ],
                'excludes_ar' => [
                    'الطيران الدولي',
                    'التأشيرة',
                    'المصروفات الشخصية',
                ],
                'itinerary_days' => [
                    [
                        'day_number' => 'Day 1',
                        'title' => 'Arrival in Hanoi',
                        'title_ar' => 'الوصول إلى هانوي',
                        'description' => 'Meet and assist at the airport, transfer to the hotel, and enjoy free time to relax or explore nearby areas.',
                        'description_ar' => 'الاستقبال والمساعدة في المطار، ثم الانتقال إلى الفندق والاستمتاع بوقت حر للراحة أو استكشاف المناطق القريبة.',
                    ],
                    [
                        'day_number' => 'Day 2',
                        'title' => 'Hanoi City Tour',
                        'title_ar' => 'جولة في مدينة هانوي',
                        'description' => 'Visit Ho Chi Minh Complex, Temple of Literature, Old Quarter, and enjoy a traditional Cyclo ride.',
                        'description_ar' => 'زيارة مجمع هو تشي منه، ومعبد الأدب، والحي القديم، مع تجربة ركوب السيكلو التقليدية.',
                    ],
                    [
                        'day_number' => 'Day 3',
                        'title' => 'Ha Long Bay Cruise',
                        'title_ar' => 'رحلة كروز في خليج ها لونغ',
                        'description' => 'Transfer to Ha Long Bay, board the cruise, enjoy lunch, kayaking, cave visits, and overnight on the cruise.',
                        'description_ar' => 'الانتقال إلى خليج ها لونغ، والصعود إلى الكروز، والاستمتاع بالغداء، والكاياك، وزيارة الكهوف، والمبيت على الكروز.',
                    ],
                    [
                        'day_number' => 'Day 4',
                        'title' => 'Ha Long Bay to Hanoi',
                        'title_ar' => 'من خليج ها لونغ إلى هانوي',
                        'description' => 'Enjoy morning cruise activities, breakfast on board, then return to Hanoi.',
                        'description_ar' => 'الاستمتاع بأنشطة صباحية على الكروز، وتناول الإفطار على متن القارب، ثم العودة إلى هانوي.',
                    ],
                    [
                        'day_number' => 'Day 5',
                        'title' => 'Flight to Da Nang',
                        'title_ar' => 'الطيران إلى دا نانغ',
                        'description' => 'Transfer to the airport for the domestic flight to Da Nang, then hotel check-in and free time.',
                        'description_ar' => 'الانتقال إلى المطار للسفر الداخلي إلى دا نانغ، ثم تسجيل الوصول إلى الفندق والاستمتاع بوقت حر.',
                    ],
                    [
                        'day_number' => 'Day 6',
                        'title' => 'Ba Na Hills Tour',
                        'title_ar' => 'جولة با نا هيلز',
                        'description' => 'Visit Ba Na Hills, enjoy the cable car, Golden Bridge, French Village, and mountain views.',
                        'description_ar' => 'زيارة با نا هيلز، والاستمتاع بالتلفريك، والجسر الذهبي، والقرية الفرنسية، والإطلالات الجبلية.',
                    ],
                    [
                        'day_number' => 'Day 7',
                        'title' => 'Hoi An Ancient Town Tour',
                        'title_ar' => 'جولة مدينة هوي آن القديمة',
                        'description' => 'Explore Hoi An ancient town, Japanese Bridge, local streets, and the famous lantern atmosphere.',
                        'description_ar' => 'استكشاف مدينة هوي آن القديمة، والجسر الياباني، والشوارع المحلية، وأجواء الفوانيس الشهيرة.',
                    ],
                    [
                        'day_number' => 'Day 8',
                        'title' => 'Departure',
                        'title_ar' => 'المغادرة',
                        'description' => 'Transfer to the airport for final departure.',
                        'description_ar' => 'الانتقال إلى المطار للمغادرة النهائية.',
                    ],
                ],
                'duration_days' => 8,
            ],
            [
                'title' => 'Bosnia and Herzegovina 8 Days 7 Nights Travel Package',
                'title_ar' => 'باقة البوسنة والهرسك 8 أيام / 7 ليالي',
                'tour_type' => 'International Travel Package',
                'tour_type_ar' => 'باقة سفر دولية',
                'package_label' => '8 Days / 7 Nights',
                'package_label_ar' => '8 أيام / 7 ليالي',
                'address' => 'Bosnia and Herzegovina',
                'address_ar' => 'البوسنة والهرسك',
                'city' => 'Sarajevo, Mostar, Jajce, Travnik',
                'country' => 'Bosnia and Herzegovina',
                'cities_covered' => 'Sarajevo, Mostar, Jajce, Travnik',
                'cities_covered_ar' => 'سراييفو، موستار، يايتسه، ترافنيك',
                'latitude' => null,
                'longitude' => null,
                'day_nights' => '8 Days / 7 Nights',
                'person_capability' => 20,
                'price' => 700,
                'currency' => 'EUR',
                'price_from' => 700,
                'price_to' => 1200,
                'price_note' => 'Estimated price per person for 4-star accommodation. Final price may change depending on season, hotel availability, and private tour arrangements.',
                'description' => 'Experience Bosnia and Herzegovina in an 8-day journey through Sarajevo, Mostar, Jajce, and Travnik. This package combines Ottoman heritage, old towns, bridges, waterfalls, mountain nature, and cultural landmarks in one memorable trip.',
                'description_ar' => 'استمتع برحلة مميزة في البوسنة والهرسك لمدة 8 أيام تشمل سراييفو، موستار، يايتسه، وترافنيك. تجمع هذه الباقة بين التراث العثماني، والمدن القديمة، والجسور التاريخية، والشلالات، والطبيعة الجبلية، والمعالم الثقافية.',
                'destination_overview' => [
                    'departure_from' => 'Airport / Client location',
                    'arrival' => 'Sarajevo',
                    'transportation' => 'Private transfers and local transportation between Sarajevo, Mostar, Jajce, and Travnik.',
                    'accommodation' => '4-star hotels or similar with daily breakfast.',
                    'cities_covered' => 'Sarajevo, Mostar, Jajce, Travnik',
                ],
                'departure_from_ar' => 'المطار / موقع العميل',
                'arrival_ar' => 'سراييفو',
                'transportation_ar' => 'انتقالات خاصة ومواصلات محلية بين سراييفو وموستار ويايتسه وترافنيك.',
                'accommodation_level' => '4-star hotels or similar',
                'accommodation_ar' => 'فنادق 4 نجوم أو ما يعادلها مع إفطار يومي.',
                'highlights' => [
                    'Sarajevo old town tour',
                    'Latin Bridge and Tunnel Museum',
                    'Mostar Old Bridge',
                    'Kravice Waterfalls',
                    'Blagaj Tekke and Pocitelj village',
                    'Jajce waterfalls and lakes',
                    'Travnik fortress',
                    'Nature tour to Bijambare caves or mountains',
                ],
                'destination_highlights_ar' => [
                    'جولة في مدينة سراييفو القديمة',
                    'الجسر اللاتيني ومتحف النفق',
                    'جسر موستار القديم',
                    'شلالات كرافيتسه',
                    'تكية بلاجاي وقرية بوتشيتلي',
                    'شلالات وبحيرات يايتسه',
                    'قلعة ترافنيك',
                    'جولة طبيعية إلى كهوف بيامباري أو الجبال',
                ],
                'features' => $this->featureRows([
                    ['fa-solid fa-mosque', 'Ottoman heritage', 'التراث العثماني'],
                    ['fa-solid fa-bridge', 'Historic bridges', 'الجسور التاريخية'],
                    ['fa-solid fa-waterfall', 'Waterfalls', 'الشلالات'],
                    ['fa-solid fa-mountain-sun', 'Mountain nature', 'الطبيعة الجبلية'],
                    ['fa-solid fa-landmark', 'Old towns', 'المدن القديمة'],
                    ['fa-solid fa-compass', 'Cultural landmarks', 'معالم ثقافية'],
                ]),
                'destination_features_ar' => $this->destinationFeatureRows([
                    ['fa-solid fa-mosque', 'التراث العثماني'],
                    ['fa-solid fa-bridge', 'الجسور التاريخية'],
                    ['fa-solid fa-waterfall', 'الشلالات'],
                    ['fa-solid fa-mountain-sun', 'الطبيعة الجبلية'],
                    ['fa-solid fa-landmark', 'المدن القديمة'],
                    ['fa-solid fa-compass', 'معالم ثقافية'],
                ]),
                'includes' => [
                    'Hotel accommodation with breakfast',
                    'Transfers',
                    'Guided tours',
                    'Entrance tickets',
                ],
                'includes_ar' => [
                    'الإقامة الفندقية مع الإفطار',
                    'الانتقالات',
                    'الجولات السياحية مع مرشد',
                    'تذاكر الدخول',
                ],
                'excludes' => [
                    'Flights',
                    'Lunch and dinner',
                    'Personal expenses',
                ],
                'excludes_ar' => [
                    'الطيران',
                    'الغداء والعشاء',
                    'المصروفات الشخصية',
                ],
                'itinerary_days' => [
                    [
                        'day_number' => 'Day 1',
                        'title' => 'Arrival in Sarajevo',
                        'title_ar' => 'الوصول إلى سراييفو',
                        'description' => 'Meet and transfer to the hotel, then enjoy free time to relax or explore the city.',
                        'description_ar' => 'الاستقبال والانتقال إلى الفندق، ثم الاستمتاع بوقت حر للراحة أو استكشاف المدينة.',
                    ],
                    [
                        'day_number' => 'Day 2',
                        'title' => 'Sarajevo City Tour',
                        'title_ar' => 'جولة في مدينة سراييفو',
                        'description' => 'Visit the Old Town Bascarsija, Latin Bridge, Tunnel Museum, and key cultural landmarks.',
                        'description_ar' => 'زيارة المدينة القديمة باشارشيا، والجسر اللاتيني، ومتحف النفق، وأهم المعالم الثقافية.',
                    ],
                    [
                        'day_number' => 'Day 3',
                        'title' => 'Sarajevo to Mostar',
                        'title_ar' => 'من سراييفو إلى موستار',
                        'description' => 'Travel from Sarajevo to Mostar with a stop at Konjic, visit the Old Bridge, and overnight in Mostar.',
                        'description_ar' => 'الانتقال من سراييفو إلى موستار مع التوقف في كونيتس، وزيارة الجسر القديم، والمبيت في موستار.',
                    ],
                    [
                        'day_number' => 'Day 4',
                        'title' => 'Herzegovina Tour',
                        'title_ar' => 'جولة الهرسك',
                        'description' => 'Visit Kravice Waterfalls, Blagaj Tekke, and Pocitelj village in a full-day cultural and nature tour.',
                        'description_ar' => 'زيارة شلالات كرافيتسه، وتكية بلاجاي، وقرية بوتشيتلي في جولة تجمع بين الطبيعة والثقافة.',
                    ],
                    [
                        'day_number' => 'Day 5',
                        'title' => 'Mostar to Jajce',
                        'title_ar' => 'من موستار إلى يايتسه',
                        'description' => 'Travel to Jajce, visit waterfalls, lakes, and traditional mills.',
                        'description_ar' => 'الانتقال إلى يايتسه، وزيارة الشلالات، والبحيرات، والطواحين التقليدية.',
                    ],
                    [
                        'day_number' => 'Day 6',
                        'title' => 'Jajce to Travnik to Sarajevo',
                        'title_ar' => 'من يايتسه إلى ترافنيك ثم سراييفو',
                        'description' => 'Visit Travnik Fortress, enjoy the old town atmosphere, then return to Sarajevo.',
                        'description_ar' => 'زيارة قلعة ترافنيك، والاستمتاع بأجواء المدينة القديمة، ثم العودة إلى سراييفو.',
                    ],
                    [
                        'day_number' => 'Day 7',
                        'title' => 'Nature Tour',
                        'title_ar' => 'جولة طبيعية',
                        'description' => 'Enjoy a nature tour to Bijambare caves or nearby mountain areas.',
                        'description_ar' => 'الاستمتاع بجولة طبيعية إلى كهوف بيامباري أو المناطق الجبلية القريبة.',
                    ],
                    [
                        'day_number' => 'Day 8',
                        'title' => 'Departure',
                        'title_ar' => 'المغادرة',
                        'description' => 'Transfer to the airport for final departure.',
                        'description_ar' => 'الانتقال إلى المطار للمغادرة النهائية.',
                    ],
                ],
                'duration_days' => 8,
            ],
        ];
    }

    private function featureRows(array $rows): array
    {
        return array_map(static function (array $row): array {
            return [
                'icon' => $row[0],
                'feature' => $row[1],
                'feature_ar' => $row[2],
            ];
        }, $rows);
    }

    private function destinationFeatureRows(array $rows): array
    {
        return array_map(static function (array $row): array {
            return [
                'icon' => $row[0],
                'feature_ar' => $row[1],
            ];
        }, $rows);
    }
}
