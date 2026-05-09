<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->json('facilities_ar')->nullable()->after('facilities');
            $table->json('includes_ar')->nullable()->after('includes');
            $table->json('excludes_ar')->nullable()->after('excludes');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['facilities_ar', 'includes_ar', 'excludes_ar']);
        });
    }
};