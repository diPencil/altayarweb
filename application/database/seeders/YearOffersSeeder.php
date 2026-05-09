<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class YearOffersSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::create(2026, 12, 29)->toDateString();
        $endDate = Carbon::create(2027, 1, 2)->toDateString();

        $yearOffersType = ListingType::updateOrCreate(
            ['name' => 'Year’s Offers'],
            [
                'name_ar' => 'عروض السنة',
                'status' => 1,
            ]
        );

        $availableTimes = ['09:00 AM', '01:00 PM', '05:00 PM'];
        $facilities = ['Hotel Stay', 'Limited Availability', 'Booking Support', 'Selected Room Type', 'Year’s Offer'];
        $facilitiesAr = ['إقامة فندقية', 'توفر محدود', 'دعم الحجز', 'نوع الغرفة المحدد', 'عرض السنة'];
        $includes = ['Hotel accommodation', 'Selected room type', 'Booking support', 'Limited-time offer'];
        $includesAr = ['إقامة فندقية', 'نوع الغرفة المحدد', 'دعم الحجز', 'عرض محدود المدة'];
        $excludes = ['Flight tickets', 'Visa', 'Personal expenses', 'Extra meals', 'Tourism tax if applicable'];
        $excludesAr = ['تذاكر الطيران', 'التأشيرة', 'المصاريف الشخصية', 'الوجبات الإضافية', 'الضريبة السياحية إن وجدت'];

        $offers = [
            ['title' => 'Grand Mercure Dubai City', 'title_ar' => 'جراند ميركيور دبي سيتي', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 3200, 'location_area' => 'Dubai'],
            ['title' => 'Media Rotana', 'title_ar' => 'ميديا روتانا', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 3200, 'location_area' => 'Dubai'],
            ['title' => 'Elite Byblos Hotel - Mall Of The Emirates', 'title_ar' => 'فندق إيليت بيبلوس - مول الإمارات', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 2500, 'location_area' => 'Al Barsha / Mall of the Emirates'],
            ['title' => 'Tolip Family Park Hotel', 'title_ar' => 'فندق توليب فاميلي بارك', 'city' => 'Cairo', 'country' => 'Egypt', 'price' => 1400, 'location_area' => 'New Cairo / Al Rehab'],
            ['title' => 'Hilton Pyramids Golf Resort', 'title_ar' => 'هيلتون بيراميدز جولف ريزورت', 'city' => 'Giza', 'country' => 'Egypt', 'price' => 1450, 'location_area' => '6th of October / Pyramids Area'],
            ['title' => 'Hilton Cairo Zamalek Residence', 'title_ar' => 'هيلتون القاهرة الزمالك ريزيدنس', 'city' => 'Cairo', 'country' => 'Egypt', 'price' => 2800, 'location_area' => 'Zamalek'],
            ['title' => 'Ibis Seef Manama', 'title_ar' => 'إيبيس السيف المنامة', 'city' => 'Manama', 'country' => 'Bahrain', 'price' => 800, 'location_area' => 'Seef'],
            ['title' => 'The Juffair Grand', 'title_ar' => 'ذا الجفير جراند', 'city' => 'Manama', 'country' => 'Bahrain', 'price' => 920, 'location_area' => 'Juffair'],
            ['title' => 'Ramada Hotel & Suites By Wyndham Amwaj Islands Manama', 'title_ar' => 'رمادا هوتل آند سويتس باي ويندام جزر أمواج المنامة', 'city' => 'Manama', 'country' => 'Bahrain', 'price' => 1250, 'location_area' => 'Amwaj Islands'],
            ['title' => 'Concorde El Salam Sharm El Sheikh Sport Hotel', 'title_ar' => 'كونكورد السلام شرم الشيخ سبورت هوتيل', 'city' => 'Sharm El Sheikh', 'country' => 'Egypt', 'price' => 1980, 'location_area' => 'Sharm El Sheikh'],
            ['title' => 'Naama Bay Promenade Beach Resort', 'title_ar' => 'منتجع نعمة باي بروميناد بيتش', 'city' => 'Sharm El Sheikh', 'country' => 'Egypt', 'price' => 1800, 'location_area' => 'Naama Bay'],
            ['title' => 'Xperience Kiroseiz Parkland', 'title_ar' => 'إكسبيرينس كيروسيز باركلاند', 'city' => 'Sharm El Sheikh', 'country' => 'Egypt', 'price' => 2200, 'location_area' => 'Naama Bay / Sharm El Sheikh'],
            ['title' => 'Captain’s Inn', 'title_ar' => 'كابتنز إن', 'city' => 'El Gouna', 'country' => 'Egypt', 'price' => 3000, 'location_area' => 'El Gouna'],
            ['title' => 'The Three Corners Rihana Resort', 'title_ar' => 'منتجع ذا ثري كورنرز ريحانة', 'city' => 'El Gouna', 'country' => 'Egypt', 'price' => 3700, 'location_area' => 'El Gouna'],
            ['title' => 'Vintage Grand Hotel', 'title_ar' => 'فندق فينتج جراند', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 2400, 'location_area' => 'Dubai'],
            ['title' => 'Hues Boutique Hotel', 'title_ar' => 'فندق هيوز بوتيك', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 2500, 'location_area' => 'Dubai'],
            ['title' => 'Golden Tulip Al Barsha Hotel', 'title_ar' => 'فندق جولدن توليب البرشاء', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 2800, 'location_area' => 'Al Barsha'],
            ['title' => 'The S Hotel', 'title_ar' => 'ذا إس هوتيل', 'city' => 'Dubai', 'country' => 'United Arab Emirates', 'price' => 2880, 'location_area' => 'Dubai'],
            ['title' => 'Golden Tulip Dammam Corniche Hotel', 'title_ar' => 'فندق جولدن توليب كورنيش الدمام', 'city' => 'Dammam', 'country' => 'Saudi Arabia', 'price' => 1600, 'location_area' => 'Dammam Corniche'],
            ['title' => 'Gloria Inn Riyadh Hotel', 'title_ar' => 'فندق جلوريا إن الرياض', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'price' => 1900, 'location_area' => 'Riyadh'],
            ['title' => 'Novotel Suites Olaya Riyadh Hotel', 'title_ar' => 'فندق نوفوتيل سويتس العليا الرياض', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'price' => 1950, 'location_area' => 'Olaya'],
            ['title' => 'Holiday Inn Riyadh Izdihar Hotel', 'title_ar' => 'فندق هوليداي إن الرياض الإزدهار', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'price' => 2300, 'location_area' => 'Riyadh Izdihar'],
            ['title' => 'Centro Waha Riyadh Hotel', 'title_ar' => 'فندق سنترو واحه الرياض', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'price' => 2500, 'location_area' => 'Riyadh'],
            ['title' => 'Radisson Blu Dhahran Al Khobar Hotel', 'title_ar' => 'فندق راديسون بلو الظهران الخبر', 'city' => 'Al Khobar', 'country' => 'Saudi Arabia', 'price' => 1600, 'location_area' => 'Dhahran / Al Khobar'],
            ['title' => 'Naviti Warwick Al Khobar Hotel', 'title_ar' => 'فندق نافيتي وارويك الخبر', 'city' => 'Al Khobar', 'country' => 'Saudi Arabia', 'price' => 1100, 'location_area' => 'Al Khobar'],
            ['title' => 'Mercure Al Khobar Hotel', 'title_ar' => 'فندق ميركيور الخبر', 'city' => 'Al Khobar', 'country' => 'Saudi Arabia', 'price' => 1200, 'location_area' => 'Al Khobar'],
            ['title' => 'Warwick Jeddah Hotel', 'title_ar' => 'فندق وارويك جدة', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'price' => 975, 'location_area' => 'Jeddah'],
            ['title' => 'Movenpick Jeddah Hotel', 'title_ar' => 'فندق موفنبيك جدة', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'price' => 1300, 'location_area' => 'Jeddah'],
            ['title' => 'Address Al Hamra Jeddah Hotel', 'title_ar' => 'فندق أدرس الحمراء جدة', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'price' => 1600, 'location_area' => 'Al Hamra'],
            ['title' => 'Elaf Ajyad Makkah Hotel', 'title_ar' => 'فندق إيلاف أجياد مكة', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'price' => 1700, 'location_area' => 'Ajyad'],
            ['title' => 'Sheraton Makkah Jabal Al Kaaba Hotel', 'title_ar' => 'فندق شيراتون مكة جبل الكعبة', 'city' => 'Makkah', 'country' => 'Saudi Arabia', 'price' => 4500, 'location_area' => 'Jabal Al Kaaba'],
            ['title' => 'Saja Al Madinah Hotel', 'title_ar' => 'فندق سجا المدينة', 'city' => 'Madinah', 'country' => 'Saudi Arabia', 'price' => 3400, 'location_area' => 'Madinah'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($offers as $offer) {
            $description = $this->buildDescription($offer);
            $descriptionAr = $this->buildDescriptionAr($offer);

            $listing = Listing::updateOrCreate(
                [
                    'title' => $offer['title'],
                    'listing_type_id' => $yearOffersType->id,
                ],
                [
                    'slug' => Str::slug($offer['title']),
                    'type' => $yearOffersType->getRawOriginal('name'),
                    'title_ar' => $offer['title_ar'],
                    'summary' => 'Year’s hotel offer from 29 Dec to 2 Jan with selected room type, limited availability, and booking support.',
                    'summary_ar' => 'عرض فندقي ضمن عروض العام من 29 ديسمبر إلى 2 يناير يشمل نوع الغرفة المحدد مع توفر محدود ودعم للحجز.',
                    'description' => $description,
                    'description_ar' => $descriptionAr,
                    'city' => $offer['city'],
                    'country' => $offer['country'],
                    'address' => $offer['location_area'] ?: $offer['city'] . ', ' . $offer['country'],
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'available_times' => $availableTimes,
                    'facilities' => $facilities,
                    'facilities_ar' => $facilitiesAr,
                    'includes' => $includes,
                    'includes_ar' => $includesAr,
                    'excludes' => $excludes,
                    'excludes_ar' => $excludesAr,
                    'price' => $offer['price'],
                    'currency' => 'SAR',
                    'discount' => 0,
                    'offer_type' => 'custom',
                    'offer_first_value' => null,
                    'offer_second_value' => null,
                    'offer_text' => 'From 29 Dec to 2 Jan',
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

        Listing::where('listing_type_id', $yearOffersType->id)->update(['currency' => 'SAR']);

        if ($this->command) {
            $this->command->info(sprintf('Year’s Offers seeder completed. Created: %d, Updated: %d', $created, $updated));
        }
    }

    private function buildDescription(array $offer): string
    {
        $location = $offer['location_area'] ?: trim($offer['city'] . ', ' . $offer['country']);

        return sprintf(
            "%s in %s is part of Year’s Offers from 29 Dec to 2 Jan. This limited hotel offer includes a selected room type, limited availability, and booking support. Price: %s SAR.",
            $offer['title'],
            $location,
            number_format((float) $offer['price'], 0, '.', ',')
        );
    }

    private function buildDescriptionAr(array $offer): string
    {
        $location = $offer['location_area'] ?: trim($offer['city'] . '، ' . $offer['country']);

        return sprintf(
            "%s في %s ضمن عروض السنة من 29 ديسمبر إلى 2 يناير. يشمل هذا العرض الفندقي المحدود نوع الغرفة المحدد مع توفر محدود ودعم للحجز. السعر: %s ريال سعودي.",
            $offer['title_ar'],
            $location,
            number_format((float) $offer['price'], 0, '.', ',')
        );
    }
}