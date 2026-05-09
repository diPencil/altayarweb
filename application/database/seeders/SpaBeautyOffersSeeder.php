<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpaBeautyOffersSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(90)->toDateString();

        $spaType = ListingType::updateOrCreate(
            ['name' => 'Spa & Beauty Offers'],
            [
                'name_ar' => 'عروض السبا والجمال',
                'status' => 1,
            ]
        );

        $availableTimes = ['09:00 AM', '01:00 PM', '05:00 PM'];
        $facilities = ['Spa Service', 'Beauty Service', 'Limited Availability', 'Booking Support', 'Member Offer'];
        $facilitiesAr = ['خدمة سبا', 'خدمة تجميل', 'توفر محدود', 'دعم الحجز', 'عرض للأعضاء'];
        $includes = ['Selected spa or beauty service', 'Booking support', 'Limited-time member offer'];
        $includesAr = ['خدمة السبا أو التجميل المحددة', 'دعم الحجز', 'عرض محدود للأعضاء'];
        $excludes = ['Transportation', 'Personal expenses', 'Extra services', 'Taxes if applicable'];
        $excludesAr = ['الانتقالات', 'المصاريف الشخصية', 'الخدمات الإضافية', 'الضرائب إن وجدت'];

        $offers = [
            [
                'title' => 'Him Spa Riyadh',
                'title_ar' => 'هم سبا الرياض',
                'service' => 'Beard & Haircut at Him Spa',
                'tag' => 'Spa',
                'badge' => 'SPA & BEAUTY Offer',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'price' => 40,
                'old_price' => 40,
                'discount' => 10,
            ],
            [
                'title' => 'Holiday Inn Al Qasr Riyadh',
                'title_ar' => 'هوليداي إن القصر الرياض',
                'service' => 'Swedish Massage or Moroccan Bath at Holiday Inn',
                'tag' => 'Spa',
                'badge' => 'SPA & BEAUTY Offer',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'price' => 265,
                'old_price' => 265,
                'discount' => 116,
            ],
            [
                'title' => 'Anfas Hospital Riyadh',
                'title_ar' => 'مستشفى أنفاس الرياض',
                'service' => 'MRI Scan at Anfas Hospital',
                'tag' => 'Wellness',
                'badge' => 'WELLNESS Offer',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'price' => 2070,
                'old_price' => 2070,
                'discount' => 1035,
            ],
            [
                'title' => 'Dr. Bassam Alhemsi Center Riyadh',
                'title_ar' => 'مركز د. بسام الحمصي الرياض',
                'service' => 'Teeth Whitening & More',
                'tag' => 'Wellness',
                'badge' => 'WELLNESS Offer',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'price' => 1000,
                'old_price' => 1000,
                'discount' => 651,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($offers as $offer) {
            $description = $this->buildDescription($offer);
            $descriptionAr = $this->buildDescriptionAr($offer);

            $listing = Listing::updateOrCreate(
                [
                    'title' => $offer['title'],
                    'listing_type_id' => $spaType->id,
                ],
                [
                    'slug' => Str::slug($offer['title']),
                    'type' => $spaType->getRawOriginal('name'),
                    'title_ar' => $offer['title_ar'],
                    'summary' => 'Exclusive spa and beauty offer available now for AltayarVIP members with limited availability and booking support.',
                    'summary_ar' => 'عرض حصري للسبا والجمال متاح الآن لأعضاء الطيار VIP مع توفر محدود ودعم للحجز.',
                    'description' => $description,
                    'description_ar' => $descriptionAr,
                    'city' => $offer['city'],
                    'country' => $offer['country'],
                    'address' => 'Riyadh, Saudi Arabia',
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
                    'discount' => $offer['discount'],
                    'offer_type' => 'custom',
                    'offer_first_value' => null,
                    'offer_second_value' => null,
                    'offer_text' => 'Available Now',
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
            $this->command->info(sprintf('Spa & Beauty Offers seeder completed. Created: %d, Updated: %d', $created, $updated));
        }
    }

    private function buildDescription(array $offer): string
    {
        $finalPrice = max((float) $offer['price'] - (float) $offer['discount'], 0);

        return sprintf(
            '%s in Riyadh is part of Spa & Beauty Offers for AltayarVIP members. This listing highlights %s, available now with limited availability, member-style booking support, and a %d%% saving from %s SAR to %s SAR.',
            $offer['title'],
            $offer['service'],
            (int) $offer['discount'],
            number_format((float) $offer['price'], 0, '.', ','),
            number_format($finalPrice, 0, '.', ',')
        );
    }

    private function buildDescriptionAr(array $offer): string
    {
        $finalPrice = max((float) $offer['price'] - (float) $offer['discount'], 0);

        return sprintf(
            '%s في الرياض ضمن عروض السبا والجمال لأعضاء الطيار VIP. يبرز هذا العرض خدمة %s، وهو متاح الآن مع توفر محدود ودعم للحجز، مع توفير بنسبة %d%% من %s ريال سعودي إلى %s ريال سعودي.',
            $offer['title_ar'],
            $offer['service'],
            (int) $offer['discount'],
            number_format((float) $offer['price'], 0, '.', ','),
            number_format($finalPrice, 0, '.', ',')
        );
    }
}