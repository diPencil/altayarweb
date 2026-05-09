<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Marina Bay',
                'name_ar' => 'مارينا باي',
                'location' => 'Marina Bay, Singapore',
                'location_ar' => 'مارينا باي، سنغافورة',
                'latitude' => '1.2834',
                'longitude' => '103.8607',
                'count' => '120+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Orchard Road',
                'name_ar' => 'طريق أورشارد',
                'location' => 'Orchard Road, Singapore',
                'location_ar' => 'طريق أورشارد، سنغافورة',
                'latitude' => '1.3048',
                'longitude' => '103.8318',
                'count' => '100+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Chinatown Singapore',
                'name_ar' => 'الحي الصيني سنغافورة',
                'location' => 'Chinatown, Singapore',
                'location_ar' => 'الحي الصيني، سنغافورة',
                'latitude' => '1.2840',
                'longitude' => '103.8439',
                'count' => '90+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Chiang Mai',
                'name_ar' => 'شيانغ ماي',
                'location' => 'Chiang Mai, Thailand',
                'location_ar' => 'شيانغ ماي، تايلاند',
                'latitude' => '18.7883',
                'longitude' => '98.9853',
                'count' => '130+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Orlando',
                'name_ar' => 'أورلاندو',
                'location' => 'Florida, USA',
                'location_ar' => 'فلوريدا، الولايات المتحدة الأمريكية',
                'latitude' => '28.5383',
                'longitude' => '-81.3792',
                'count' => '160+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Sharm El Sheikh',
                'name_ar' => 'شرم الشيخ',
                'location' => 'South Sinai, Egypt',
                'location_ar' => 'جنوب سيناء، مصر',
                'latitude' => '27.9158',
                'longitude' => '34.3299',
                'count' => '150+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Riyadh',
                'name_ar' => 'الرياض',
                'location' => 'Riyadh, Saudi Arabia',
                'location_ar' => 'الرياض، المملكة العربية السعودية',
                'latitude' => '24.7136',
                'longitude' => '46.6753',
                'count' => '180+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Makkah',
                'name_ar' => 'مكة المكرمة',
                'location' => 'Makkah, Saudi Arabia',
                'location_ar' => 'مكة المكرمة، المملكة العربية السعودية',
                'latitude' => '21.3891',
                'longitude' => '39.8579',
                'count' => '160+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Madinah',
                'name_ar' => 'المدينة المنورة',
                'location' => 'Madinah, Saudi Arabia',
                'location_ar' => 'المدينة المنورة، المملكة العربية السعودية',
                'latitude' => '24.5247',
                'longitude' => '39.5692',
                'count' => '140+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Sharjah',
                'name_ar' => 'الشارقة',
                'location' => 'Sharjah, UAE',
                'location_ar' => 'الشارقة، الإمارات العربية المتحدة',
                'latitude' => '25.3463',
                'longitude' => '55.4209',
                'count' => '110+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Ras Al Khaimah',
                'name_ar' => 'رأس الخيمة',
                'location' => 'Ras Al Khaimah, UAE',
                'location_ar' => 'رأس الخيمة، الإمارات العربية المتحدة',
                'latitude' => '25.8007',
                'longitude' => '55.9762',
                'count' => '100+ Destinations',
                'status' => 1,
                'image' => '',
            ],
            [
                'name' => 'Fujairah',
                'name_ar' => 'الفجيرة',
                'location' => 'Fujairah, UAE',
                'location_ar' => 'الفجيرة، الإمارات العربية المتحدة',
                'latitude' => '25.1288',
                'longitude' => '56.3265',
                'count' => '90+ Destinations',
                'status' => 1,
                'image' => '',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($locations as $attributes) {
            $location = Location::firstOrNew(['name' => $attributes['name']]);
            $exists = $location->exists;

            $location->name = $attributes['name'];
            $location->name_ar = $attributes['name_ar'];
            $location->location = $attributes['location'];
            $location->location_ar = $attributes['location_ar'];
            $location->latitude = $attributes['latitude'];
            $location->longitude = $attributes['longitude'];
            $location->count = $attributes['count'];
            $location->status = $attributes['status'];
            $location->image = $attributes['image'];
            $location->save();

            if ($exists) {
                $updated++;
            } else {
                $created++;
            }
        }

        $this->command?->info("Locations seeded. Created: {$created}, Updated: {$updated}");
    }
}
