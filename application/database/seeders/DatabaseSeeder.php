<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(WeekendOffersSeeder::class);
        $this->call(YearOffersSeeder::class);
        $this->call(SpaBeautyOffersSeeder::class);
        $this->call(CouponsSeeder::class);
        $this->call(VouchersSeeder::class);
        $this->call(LocationsSeeder::class);
        $this->call(TourPackagesSeeder::class);
    }
}
