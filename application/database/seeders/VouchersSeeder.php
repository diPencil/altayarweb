<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VouchersSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(90)->toDateString();

        $voucherType = ListingType::updateOrCreate(
            ['name' => 'Vouchers'],
            [
                'name_ar' => 'قسائم',
                'status' => 1,
            ]
        );

        $availableTimes = ['09:00 AM', '01:00 PM', '05:00 PM'];
        $facilities = ['Travel Voucher', 'Member Benefit', 'Booking Support', 'Limited Availability'];
        $facilitiesAr = ['قسيمة سفر', 'ميزة للأعضاء', 'دعم الحجز', 'توفر محدود'];
        $includes = ['Selected voucher benefit', 'Booking support', 'Member-only offer'];
        $includesAr = ['ميزة القسيمة المحددة', 'دعم الحجز', 'عرض مخصص للأعضاء'];
        $excludes = ['Flight tickets unless mentioned', 'Visa', 'Personal expenses', 'Extra services', 'Taxes if applicable'];
        $excludesAr = ['تذاكر الطيران ما لم يتم ذكرها', 'التأشيرة', 'المصاريف الشخصية', 'الخدمات الإضافية', 'الضرائب إن وجدت'];

        $offers = [
            [
                'title' => 'Rixos Alamein Summer Voucher',
                'title_ar' => 'قسيمة ريكسوس العلمين الصيفية',
                'city' => 'North Coast',
                'country' => 'Egypt',
                'address' => 'Rixos Alamein, North Coast, Egypt',
                'currency' => 'EGP',
                'offer_text' => 'North Coast Summer Voucher',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Book your summer escape at Rixos Alamein through AltayarVIP and enjoy a luxury beachfront experience with ultra all-inclusive service, private beach access, luxury rooms, and world-class hospitality. This voucher is designed for members looking for premium North Coast stays during the summer season.',
                'description_ar' => 'احجز رحلتك الصيفية إلى ريكسوس العلمين من خلال الطيار VIP واستمتع بتجربة فاخرة على شاطئ البحر تشمل إقامة مميزة وخدمة ألترا أول إنكلوسيف وشاطئ خاص وغرف فاخرة وخدمة عالمية المستوى. هذه القسيمة مناسبة للأعضاء الباحثين عن إقامة راقية في الساحل الشمالي خلال موسم الصيف.',
            ],
            [
                'title' => 'Rotana North Coast Summer Voucher',
                'title_ar' => 'قسيمة روتانا الساحل الشمالي الصيفية',
                'city' => 'North Coast',
                'country' => 'Egypt',
                'address' => 'Rotana North Coast, Egypt',
                'currency' => 'EGP',
                'offer_text' => 'North Coast Summer Voucher',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Enjoy a relaxing summer vacation at Rotana North Coast with a resort-style experience, pools and beach access, family-friendly atmosphere, and great activities. This AltayarVIP voucher helps members request and book their summer stay easily.',
                'description_ar' => 'استمتع بإجازة صيفية مريحة في روتانا الساحل الشمالي مع تجربة منتجع متكاملة وحمامات سباحة وشاطئ وأجواء مناسبة للعائلات وأنشطة مميزة. تساعدك هذه القسيمة من الطيار VIP على طلب وحجز إقامتك الصيفية بسهولة.',
            ],
            [
                'title' => 'Al Alamein Hotel Summer Voucher',
                'title_ar' => 'قسيمة فندق العلمين الصيفية',
                'city' => 'North Coast',
                'country' => 'Egypt',
                'address' => 'Al Alamein Hotel, North Coast, Egypt',
                'currency' => 'EGP',
                'offer_text' => 'North Coast Summer Voucher',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Experience comfort and elegance at Al Alamein Hotel on the Mediterranean coast. This voucher is suitable for members looking for sea-view rooms, a relaxing stay, elegant atmosphere, and a prime North Coast location.',
                'description_ar' => 'استمتع بالراحة والأناقة في فندق العلمين على ساحل البحر المتوسط. هذه القسيمة مناسبة للأعضاء الباحثين عن غرف بإطلالة بحرية وإقامة مريحة وأجواء راقية وموقع مميز في الساحل الشمالي.',
            ],
            [
                'title' => 'Vida Marassi Summer Voucher',
                'title_ar' => 'قسيمة فيدا مراسي الصيفية',
                'city' => 'North Coast',
                'country' => 'Egypt',
                'address' => 'Vida Marassi, North Coast, Egypt',
                'currency' => 'EGP',
                'offer_text' => 'North Coast Summer Voucher',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Enjoy a modern summer stay at Vida Marassi with beach access, stylish design, chill vibes, and a smart resort experience. This voucher is ideal for AltayarVIP members who want a fresh and elegant North Coast getaway.',
                'description_ar' => 'استمتع بإقامة صيفية عصرية في فيدا مراسي مع وصول للشاطئ وتصميم حديث وأجواء هادئة وتجربة منتجع ذكية. هذه القسيمة مثالية لأعضاء الطيار VIP الباحثين عن عطلة أنيقة ومميزة في الساحل الشمالي.',
            ],
            [
                'title' => 'Travel The World Voucher',
                'title_ar' => 'قسيمة السفر إلى كل العالم',
                'city' => 'Worldwide',
                'country' => 'Worldwide',
                'address' => 'Worldwide Travel Voucher',
                'currency' => 'EGP',
                'offer_text' => 'Year-Round Travel Voucher',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'From Saudi Arabia to the whole world, AltayarVIP members can enjoy year-round travel offers, complete stays, premium-level trips, and personalized booking support for international destinations.',
                'description_ar' => 'من السعودية إلى كل العالم، يمكن لأعضاء الطيار VIP الاستفادة من عروض سفر طوال السنة تشمل إقامات كاملة ورحلات على أعلى مستوى ودعمًا مخصصًا للحجز إلى الوجهات العالمية.',
            ],
            [
                'title' => 'Dubai Hotel Discount Voucher',
                'title_ar' => 'قسيمة خصم فنادق دبي',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'address' => 'Dubai, United Arab Emirates',
                'currency' => 'EGP',
                'offer_text' => 'Up to 50% Hotel Discount',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Explore Dubai with AltayarVIP and enjoy a special limited-time hotel booking voucher with up to 50% discount on selected hotel stays. This voucher is suitable for members planning a premium Dubai escape.',
                'description_ar' => 'اكتشف دبي مع الطيار VIP واستفد من قسيمة حجز فندقي لفترة محدودة بخصم يصل إلى 50% على إقامات مختارة في الفنادق. هذه القسيمة مناسبة للأعضاء الراغبين في تجربة سفر مميزة إلى دبي.',
            ],
            [
                'title' => 'Paris Stay 3 Pay 2 Voucher',
                'title_ar' => 'قسيمة باريس احجز 3 ليالي وادفع ليلتين',
                'city' => 'Paris',
                'country' => 'France',
                'address' => 'Paris, France',
                'currency' => 'EGP',
                'offer_text' => 'Stay 3 Nights, Pay 2',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Explore Paris with a special AltayarVIP hotel voucher. Book 3 nights in selected Paris hotels and get 1 night free. This voucher is ideal for members planning a romantic, cultural, or family trip to Paris.',
                'description_ar' => 'اكتشف باريس من خلال قسيمة فندقية خاصة من الطيار VIP. احجز 3 ليالي في فنادق مختارة في باريس واحصل على ليلة مجانية. هذه القسيمة مثالية للأعضاء الراغبين في رحلة رومانسية أو ثقافية أو عائلية.',
            ],
            [
                'title' => 'Membership Upgrade Cashback 10000 USD Voucher',
                'title_ar' => 'قسيمة ترقية العضوية وكاش باك 10,000 دولار',
                'city' => 'Worldwide',
                'country' => 'Worldwide',
                'address' => 'AltayarVIP Membership Voucher',
                'currency' => 'USD',
                'offer_text' => '10,000 USD Cashback',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Upgrade your AltayarVIP membership and get exclusive member offers with a cashback benefit up to 10,000 USD. This voucher is designed for premium members who want higher-value travel benefits and exclusive deals.',
                'description_ar' => 'قم بترقية عضويتك في الطيار VIP واحصل على عروض حصرية للأعضاء مع ميزة كاش باك تصل إلى 10,000 دولار. هذه القسيمة مخصصة للأعضاء المميزين الباحثين عن مزايا سفر أعلى قيمة وعروض حصرية.',
            ],
            [
                'title' => 'Membership Upgrade Cashback 5000 USD Voucher',
                'title_ar' => 'قسيمة ترقية العضوية وكاش باك 5,000 دولار',
                'city' => 'Worldwide',
                'country' => 'Worldwide',
                'address' => 'AltayarVIP Membership Voucher',
                'currency' => 'USD',
                'offer_text' => '5,000 USD Cashback',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Upgrade your AltayarVIP membership and enjoy exclusive travel offers with cashback up to 5,000 USD. This voucher is suitable for members looking for more travel value and special VIP benefits.',
                'description_ar' => 'قم بترقية عضويتك في الطيار VIP واستمتع بعروض سفر حصرية مع كاش باك يصل إلى 5,000 دولار. هذه القسيمة مناسبة للأعضاء الباحثين عن قيمة سفر أعلى ومزايا VIP خاصة.',
            ],
            [
                'title' => 'Membership Upgrade Cashback 2000 USD Voucher',
                'title_ar' => 'قسيمة ترقية العضوية وكاش باك 2,000 دولار',
                'city' => 'Worldwide',
                'country' => 'Worldwide',
                'address' => 'AltayarVIP Membership Voucher',
                'currency' => 'USD',
                'offer_text' => '2,000 USD Cashback',
                'summary' => 'Exclusive travel voucher for AltayarVIP members with limited availability, booking support, and special member benefits.',
                'summary_ar' => 'قسيمة سفر حصرية لأعضاء الطيار VIP مع توفر محدود ودعم للحجز ومزايا خاصة للأعضاء.',
                'description' => 'Upgrade your AltayarVIP membership and receive exclusive member-only offers with cashback up to 2,000 USD. This voucher gives members access to extra travel value and personalized booking benefits.',
                'description_ar' => 'قم بترقية عضويتك في الطيار VIP واحصل على عروض حصرية للأعضاء مع كاش باك يصل إلى 2,000 دولار. تمنح هذه القسيمة الأعضاء قيمة سفر إضافية ومزايا حجز مخصصة.',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($offers as $offer) {
            $listing = Listing::updateOrCreate(
                [
                    'title' => $offer['title'],
                    'listing_type_id' => $voucherType->id,
                ],
                [
                    'slug' => Str::slug($offer['title']),
                    'type' => $voucherType->getRawOriginal('name'),
                    'title_ar' => $offer['title_ar'],
                    'summary' => $offer['summary'],
                    'summary_ar' => $offer['summary_ar'],
                    'description' => $offer['description'],
                    'description_ar' => $offer['description_ar'],
                    'city' => $offer['city'],
                    'country' => $offer['country'],
                    'address' => $offer['address'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'available_times' => $availableTimes,
                    'facilities' => $facilities,
                    'facilities_ar' => $facilitiesAr,
                    'includes' => $includes,
                    'includes_ar' => $includesAr,
                    'excludes' => $excludes,
                    'excludes_ar' => $excludesAr,
                    'price' => 0,
                    'currency' => $offer['currency'],
                    'discount' => 0,
                    'offer_type' => 'custom',
                    'offer_first_value' => null,
                    'offer_second_value' => null,
                    'offer_text' => $offer['offer_text'],
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
            $this->command->info(sprintf('Vouchers seeder completed. Created: %d, Updated: %d', $created, $updated));
        }
    }
}