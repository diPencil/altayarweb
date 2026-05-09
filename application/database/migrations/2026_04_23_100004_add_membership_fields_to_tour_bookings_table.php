<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->decimal('cashback_used', 18, 2)->default(0)->after('discount');
            $table->integer('membership_points_earned')->default(0)->after('cashback_used');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropColumn(['cashback_used', 'membership_points_earned']);
        });
    }
};
