<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('membership_plans', 'benefits_ar')) {
            return;
        }

        Schema::table('membership_plans', function (Blueprint $table) {
            $table->json('benefits_ar')->nullable()->after('benefits');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('membership_plans', 'benefits_ar')) {
            return;
        }

        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('benefits_ar');
        });
    }
};