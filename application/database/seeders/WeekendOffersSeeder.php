<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WeekendOffersSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(90)->toDateString();

        $weekendType = ListingType::updateOrCreate(
            ['name' => 'Weekend Offers'],
            [
                'name_ar' => 'عروض نهاية الأسبوع',
                'status' => 1,
            ]
        );

        $availableTimes = ['09:00 AM', '01:00 PM', '05:00 PM'];
        $excludes = [
            'Flight tickets',
            'Visa',
            'Personal expenses',
            'Extra meals',
            'Tourism tax if applicable',
        ];

        $offers = [
            [
                'title' => 'Millennium Plaza Downtown Dubai',
                'title_ar' => 'ميلينيوم بلازا داون تاون دبي',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'price' => 280,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 280 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 280 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Bristol Hotel Dubai',
                'title_ar' => 'فندق بريستول دبي',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'price' => 310,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 310 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 310 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Crowne Plaza Hotel Bahrain',
                'title_ar' => 'فندق كراون بلازا البحرين',
                'city' => 'Bahrain',
                'country' => 'Bahrain',
                'price' => 300,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 300 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 300 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Downtown Rotana Hotel Bahrain',
                'title_ar' => 'فندق داون تاون روتانا البحرين',
                'city' => 'Bahrain',
                'country' => 'Bahrain',
                'price' => 380,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 380 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 380 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Saraya Corniche Hotel Doha',
                'title_ar' => 'فندق سرايا كورنيش الدوحة',
                'city' => 'Doha',
                'country' => 'Qatar',
                'price' => 200,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 200 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 200 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Top Tio Sea Resort Hotel Doha',
                'title_ar' => 'فندق توب تيو سي ريزورت الدوحة',
                'city' => 'Doha',
                'country' => 'Qatar',
                'price' => 220,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 220 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 220 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Coral Beach Resort Hotel Beirut',
                'title_ar' => 'فندق كورال بيتش ريزورت بيروت',
                'city' => 'Beirut',
                'country' => 'Lebanon',
                'price' => 240,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 240 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 240 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Louis V Hotel Beirut',
                'title_ar' => 'فندق لويس في بيروت',
                'city' => 'Beirut',
                'country' => 'Lebanon',
                'price' => 270,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 270 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 270 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Al Masa Hotel Nasr City',
                'title_ar' => 'فندق الماسة مدينة نصر',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'price' => 290,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 290 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 290 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Pyramisa Suites Hotel Cairo',
                'title_ar' => 'فندق بيراميزا سويتس القاهرة',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'price' => 320,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 320 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 320 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Pyramisa Luxor + Pyramisa Isis Aswan',
                'title_ar' => 'بيراميزا الأقصر + بيراميزا إيزيس أسوان',
                'city' => 'Luxor & Aswan',
                'country' => 'Egypt',
                'price' => 300,
                'currency' => 'USD',
                'package' => 'Weekend Offer + 2 nights Luxor + 1 night Aswan + Pay 2',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 2 nights in Luxor, 1 night in Aswan, and pay 2 nights. Price: 300 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل ليلتين في الأقصر، وليلة واحدة في أسوان، مع دفع ليلتين. السعر: 300 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['ليلتان في الأقصر', 'ليلة واحدة في أسوان', 'ادفع ليلتين فقط', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 2,
                'offer_second_value' => 2,
                'offer_text' => '2 nights Luxor + 1 night Aswan, pay 2',
            ],
            [
                'title' => 'Naama Bay Promenade Resort Sharm El Sheikh',
                'title_ar' => 'منتجع نعمة باي بروميناد شرم الشيخ',
                'city' => 'Sharm El Sheikh',
                'country' => 'Egypt',
                'price' => 200,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 200 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 200 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Mövenpick Resort El Gouna',
                'title_ar' => 'منتجع موفنبيك الجونة',
                'city' => 'El Gouna',
                'country' => 'Egypt',
                'price' => 390,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Bed & Breakfast',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 390 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 390 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إفطار', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Shaden Resort',
                'title_ar' => 'منتجع شادن',
                'city' => 'AlUla',
                'country' => 'Saudi Arabia',
                'price' => 1900,
                'currency' => 'SAR',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Desert Room',
                'meal_plan' => 'Bed & Breakfast',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 1900 SAR.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 1900 ريال سعودي.',
                'facilities_ar' => ['غرفة الصحراء', 'إفطار', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'InterContinental Jeddah',
                'title_ar' => 'إنتركونتيننتال جدة',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'price' => 340,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Corniche Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 340 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 340 دولار أمريكي.',
                'facilities_ar' => ['غرفة كورنيش', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
            [
                'title' => 'Crowne Plaza Palace Riyadh',
                'title_ar' => 'فندق كراون بلازا بالاس الرياض',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'price' => 360,
                'currency' => 'USD',
                'package' => 'Weekend Offer + Stay 3 nights, pay 2 + Free transfer',
                'room' => 'Standard Room',
                'meal_plan' => 'Room Only',
                'summary' => 'Weekend hotel offer including 3 nights stay, pay 2 nights, and free transfer. Price: 360 USD.',
                'summary_ar' => 'عرض فندقي لعطلة نهاية الأسبوع يشمل إقامة 3 ليالٍ، ادفع ليلتين، مع نقل مجاني. السعر: 360 دولار أمريكي.',
                'facilities_ar' => ['غرفة قياسية', 'إقامة فندقية', 'انتقال مجاني', 'دعم الحجز'],
                'includes_ar' => ['إقامة 3 ليالي', 'ادفع ليلتين فقط', 'انتقال مجاني', 'نوع الغرفة المحدد', 'دعم الحجز'],
                'excludes_ar' => ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'ضريبة السياحة إن وجدت'],
                'offer_first_value' => 3,
                'offer_second_value' => 2,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($offers as $offer) {
            $listing = Listing::updateOrCreate(
                [
                    'title' => $offer['title'],
                    'listing_type_id' => $weekendType->id,
                ],
                [
                    'slug' => Str::slug($offer['title']),
                    'type' => $weekendType->name,
                    'title_ar' => $offer['title_ar'],
                    'summary' => $offer['summary'],
                    'summary_ar' => $offer['summary_ar'],
                    'description' => $this->buildDescription($offer),
                    'description_ar' => $this->buildDescriptionAr($offer),
                    'city' => $offer['city'],
                    'country' => $offer['country'],
                    'address' => $offer['city'] . ', ' . $offer['country'],
                    'start_date' => $today,
                    'end_date' => $endDate,
                    'available_times' => $availableTimes,
                    'facilities' => [
                        $offer['room'],
                        $offer['meal_plan'],
                        'Free Transfer',
                        'Hotel Stay',
                        'Booking Support',
                    ],
                    'facilities_ar' => $offer['facilities_ar'] ?? [],
                    'includes' => $offer['title'] === 'Pyramisa Luxor + Pyramisa Isis Aswan'
                        ? [
                            '2 nights in Luxor',
                            '1 night in Aswan',
                            'Pay 2 nights only',
                            $offer['room'],
                            'Booking support',
                        ]
                        : [
                            '3 nights stay',
                            'Pay 2 nights only',
                            'Free transfer',
                            $offer['room'],
                            'Booking support',
                        ],
                    'includes_ar' => $offer['includes_ar'] ?? [],
                    'excludes' => $excludes,
                    'excludes_ar' => $offer['excludes_ar'] ?? [],
                    'price' => $offer['price'],
                    'currency' => $offer['currency'] ?? 'USD',
                    'discount' => 0,
                    'offer_type' => 'custom',
                    'offer_first_value' => $offer['offer_first_value'],
                    'offer_second_value' => $offer['offer_second_value'],
                    'offer_text' => $offer['offer_text'] ?? ($offer['title'] === 'Pyramisa Luxor + Pyramisa Isis Aswan'
                        ? '2 nights Luxor + 1 night Aswan, pay 2'
                        : 'Stay 3 nights, pay 2 + Free transfer'),
                    'image' => null,
                    'status' => 1,
                    'user_id' => null,
                    'user_type' => 'admin',
                ]
            );

            if ($listing->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        if ($this->command) {
            $this->command->info(sprintf(
                'Weekend offers import completed. Created: %d. Updated: %d. Missing images: %d.',
                $created,
                $updated,
                16
            ));
        }
    }

    private function buildDescription(array $offer): string
    {
        $destination = trim($offer['city'] . ', ' . $offer['country']);

        if ($offer['title'] === 'Pyramisa Luxor + Pyramisa Isis Aswan') {
            return sprintf(
                '%s is a limited-time member-style weekend offer across Luxor and Aswan in Egypt. The package includes 2 nights in Luxor, 1 night in Aswan, and pay 2 nights only. Guests stay in %s with %s, supported by booking assistance for a smooth short break. Price starts from %s %s.',
                $offer['title'],
                $offer['room'],
                $offer['meal_plan'],
                $offer['price'],
                $offer['currency']
            );
        }

        return sprintf(
            '%s is a limited-time member-style weekend offer in %s. The package includes %s. Guests stay in %s with %s, and enjoy booking support as part of a short break designed for value-focused leisure travel. Price starts from %s %s.',
            $offer['title'],
            $destination,
            $offer['package'],
            $offer['room'],
            $offer['meal_plan'],
            $offer['price'],
            $offer['currency']
        );
    }

    private function buildDescriptionAr(array $offer): string
    {
        $destination = trim($offer['city'] . '، ' . $offer['country']);

        if ($offer['title'] === 'Pyramisa Luxor + Pyramisa Isis Aswan') {
            return sprintf(
                '%s هو عرض عطلة نهاية الأسبوع محدود المدة بأسلوب عروض الأعضاء في الأقصر وأسوان، مصر. يشمل العرض ليلتين في الأقصر وليلة واحدة في أسوان مع دفع ليلتين فقط. الإقامة في %s مع خطة وجبات %s، إضافة إلى دعم الحجز لتجربة سفر قصيرة وسلسة. يبدأ السعر من %s %s.',
                $offer['title_ar'],
                $offer['room'],
                $offer['meal_plan'],
                $offer['price'],
                $offer['currency']
            );
        }

        return sprintf(
            '%s هو عرض عطلة نهاية الأسبوع محدود المدة بأسلوب عروض الأعضاء في %s. يشمل العرض %s. الإقامة في %s مع خطة وجبات %s، مع دعم الحجز لتجربة إقامة قصيرة ومناسبة للباحثين عن قيمة ممتازة. يبدأ السعر من %s %s.',
            $offer['title_ar'],
            $destination,
            $offer['package'],
            $offer['room'],
            $offer['meal_plan'],
            $offer['price'],
            $offer['currency']
        );
    }
}