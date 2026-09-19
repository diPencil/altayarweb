<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('name_ar', 120)->nullable();
            $table->string('kind', 30)->default('link');
            $table->string('url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('target_blank')->default(false);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });

        $now = now();
        $add = static function (array $item) use ($now): int {
            return DB::table('navigation_items')->insertGetId(array_merge([
                'parent_id' => null,
                'name_ar' => null,
                'kind' => 'link',
                'url' => null,
                'sort_order' => 0,
                'target_blank' => false,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], $item));
        };

        $add(['name' => 'Home', 'name_ar' => 'الرئيسية', 'url' => '/', 'sort_order' => 10]);
        $add([
            'name' => 'E-Payment',
            'name_ar' => 'الدفع الإلكتروني',
            'url' => 'https://app.fawaterk.com/ec/altayarvip-e-payment',
            'target_blank' => true,
            'sort_order' => 20,
        ]);

        $travel = $add([
            'name' => "Traveler's Guide",
            'name_ar' => 'دليل المسافر',
            'kind' => 'group',
            'sort_order' => 30,
        ]);
        $add([
            'parent_id' => $travel,
            'name' => 'Limited Offers',
            'name_ar' => 'عروض محدودة',
            'kind' => 'listing_types',
            'sort_order' => 10,
        ]);
        $moreTravel = $add([
            'parent_id' => $travel,
            'name' => 'More Travel',
            'name_ar' => 'المزيد من السفر',
            'kind' => 'group',
            'sort_order' => 20,
        ]);
        $add(['parent_id' => $moreTravel, 'name' => 'Tour Packages', 'name_ar' => 'برامج سياحية', 'url' => '/more-travel/packages', 'sort_order' => 10]);
        $add(['parent_id' => $moreTravel, 'name' => 'Destinations', 'name_ar' => 'الوجهات', 'url' => '/more-travel/destinations', 'sort_order' => 20]);
        $add(['parent_id' => $moreTravel, 'name' => 'Hotels', 'name_ar' => 'الفنادق', 'url' => '/more-travel/hotels', 'sort_order' => 30]);
        $add(['parent_id' => $moreTravel, 'name' => 'Flights', 'name_ar' => 'الرحلات الجوية', 'url' => '/more-travel/flights', 'sort_order' => 40]);
        $add(['parent_id' => $moreTravel, 'name' => 'Transportation', 'name_ar' => 'المواصلات', 'url' => '/more-travel/transportation', 'sort_order' => 50]);

        $categories = $add([
            'name' => 'Categories',
            'name_ar' => 'الفئات',
            'kind' => 'group',
            'sort_order' => 40,
        ]);
        $add(['parent_id' => $categories, 'name' => 'Membership Card', 'name_ar' => 'بطاقة العضوية', 'url' => '/membership-card', 'sort_order' => 10]);
        $add(['parent_id' => $categories, 'name' => 'Membership Details', 'name_ar' => 'تفاصيل العضوية', 'url' => '/membership-details', 'sort_order' => 20]);
        $add(['parent_id' => $categories, 'name' => 'Privilege Card', 'name_ar' => 'بطاقة الامتياز', 'url' => '/privilege-cards', 'sort_order' => 30]);
        $add(['parent_id' => $categories, 'name' => 'Engine Screen', 'name_ar' => 'شاشة المحرك', 'url' => '/engine-screen', 'sort_order' => 40]);

        $company = $add([
            'name' => 'About Company',
            'name_ar' => 'عن الشركة',
            'kind' => 'group',
            'sort_order' => 50,
        ]);
        $add(['parent_id' => $company, 'name' => 'About Us', 'name_ar' => 'من نحن', 'url' => '/about', 'sort_order' => 10]);
        $add(['parent_id' => $company, 'name' => 'Contact Us', 'name_ar' => 'اتصل بنا', 'url' => '/contact', 'sort_order' => 20]);
        $add(['parent_id' => $company, 'name' => 'News & Updates', 'name_ar' => 'الأخبار والتحديثات', 'url' => '/blog', 'sort_order' => 30]);
        $add(['parent_id' => $company, 'name' => 'Client Feedback', 'name_ar' => 'آراء العملاء', 'url' => '/client-feedback', 'sort_order' => 40]);
        $add(['parent_id' => $company, 'name' => 'Reels', 'name_ar' => 'ريلز', 'url' => '/reels', 'sort_order' => 50]);
        $add(['parent_id' => $company, 'name' => 'Policy & Terms', 'name_ar' => 'السياسات والشروط', 'url' => '/our-privacy', 'sort_order' => 60]);
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
