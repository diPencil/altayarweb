<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('offer_type', 20)->nullable()->after('discount');
            $table->unsignedInteger('offer_first_value')->nullable()->after('offer_type');
            $table->unsignedInteger('offer_second_value')->nullable()->after('offer_first_value');
            $table->string('offer_text', 255)->nullable()->after('offer_second_value');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['offer_type', 'offer_first_value', 'offer_second_value', 'offer_text']);
        });
    }
};