<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->unsignedBigInteger('service_booking_id')->nullable()->after('tour_booking_id');
            $table->foreign('service_booking_id')->references('id')->on('service_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['service_booking_id']);
            $table->dropColumn('service_booking_id');
        });
    }
};
