<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->json('available_times')->nullable()->after('end_date');
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('service_time', 30)->nullable()->after('service_date');
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn('service_time');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('available_times');
        });
    }
};