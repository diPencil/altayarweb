<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CouponsSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(90)->toDateString();

        $couponType = ListingType::updateOrCreate(
            ['name' => 'Coupons'],
            [
                'name_ar' => 'كوبونات',
                'status' => 1,
            ]
        );

        $availableTimes = ['09:00 AM', '01:00 PM', '05:00 PM'];
        $facilities = ['Membership Coupon', 'Free Member Benefit', 'Limited Availability', 'Booking Support'];
        $facilitiesAr = ['قسيمة عضوية', 'ميزة مجانية للأعضاء', 'توفر محدود', 'دعم الحجز'];
        $includes = ['Selected coupon service', 'Membership benefit', 'Booking support'];
        $includesAr = ['خدمة القسيمة المحددة', 'ميزة ضمن العضوية', 'دعم الحجز'];
        $excludes = ['Transportation', 'Personal expenses', 'Extra services', 'Taxes if applicable'];
        $excludesAr = ['الانتقالات', 'المصاريف الشخصية', 'الخدمات الإضافية', 'الضرائب إن وجدت'];

        $offers = [
            [
                'title' => 'Hair Care Service',
                'title_ar' => 'خدمة العناية بالشعر',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'service' => 'Hair-care service at home. Customer can choose from haircut, blow-dry, or roots dye.',
                'service_ar' => 'خدمة عناية خاصة بالشعر في المنزل مع إمكانية الاختيار بين قص الشعر أو الاستشوار أو صبغ الجذور.',
            ],
            [
                'title' => 'Laser Hair Removal',
                'title_ar' => 'إزالة الشعر بالليزر',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'service' => 'Free laser hair removal session for upper lip or underarm only.',
                'service_ar' => 'جلسة مجانية لإزالة الشعر بالليزر لمنطقة الشفة العلوية أو الإبط فقط.',
            ],
            [
                'title' => 'Teeth Cleaning & Polishing',
                'title_ar' => 'تنظيف وتلميع الأسنان',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'service' => 'Free dental cleaning and polishing session with tartar removal and dental examination.',
                'service_ar' => 'جلسة مجانية لتنظيف وتلميع الأسنان مع إزالة الجير وفحص للأسنان.',
            ],
            [
                'title' => 'Car Cleaning & Polishing',
                'title_ar' => 'تنظيف وتلميع السيارة',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'service' => 'Free car cleaning package including steam washing and edge polishing.',
                'service_ar' => 'باقة تنظيف مجانية للسيارة تشمل الغسيل بالبخار وتلميع الأطراف.',
            ],
            [
                'title' => 'Massage For Men',
                'title_ar' => 'مساج للرجال',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'service' => 'Swedish massage or Moroccan bath session for men.',
                'service_ar' => 'جلسة مساج سويدي أو حمام مغربي للرجال.',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($offers as $offer) {
            $listing = Listing::updateOrCreate(
                [
                    'title' => $offer['title'],
                    'listing_type_id' => $couponType->id,
                ],
                [
                    'slug' => Str::slug($offer['title']),
                    'type' => $couponType->getRawOriginal('name'),
                    'title_ar' => $offer['title_ar'],
                    'summary' => 'Exclusive coupon available only for AltayarVIP members with booking support and limited availability.',
                    'summary_ar' => 'قسيمة حصرية متاحة فقط لأعضاء الطيار VIP مع دعم للحجز وتوفر محدود.',
                    'description' => $this->buildDescription($offer),
                    'description_ar' => $this->buildDescriptionAr($offer),
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
                    'price' => 0,
                    'currency' => 'SAR',
                    'discount' => 0,
                    'offer_type' => 'custom',
                    'offer_first_value' => null,
                    'offer_second_value' => null,
                    'offer_text' => 'Free only for Membership',
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
            $this->command->info(sprintf('Coupons seeder completed. Created: %d, Updated: %d', $created, $updated));
        }
    }

    private function buildDescription(array $offer): string
    {
        return sprintf(
            '%s in Riyadh is part of Coupons for AltayarVIP members. This membership benefit is available with limited availability and booking support. %s',
            $offer['title'],
            $offer['service']
        );
    }

    private function buildDescriptionAr(array $offer): string
    {
        return sprintf(
            '%s في الرياض ضمن قسم الكوبونات لأعضاء الطيار VIP. هذه الميزة ضمن العضوية متاحة مع توفر محدود ودعم للحجز. %s',
            $offer['title_ar'],
            $offer['service_ar']
        );
    }
}