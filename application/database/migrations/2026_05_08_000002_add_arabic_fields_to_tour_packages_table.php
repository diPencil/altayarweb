<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'NO_ZERO_DATE', '')");

        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'departure_from_ar')) {
                $table->string('departure_from_ar', 255)->nullable()->after('destination_overview');
            }

            if (!Schema::hasColumn('tour_packages', 'arrival_ar')) {
                $table->string('arrival_ar', 255)->nullable()->after('departure_from_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'transportation_ar')) {
                $table->string('transportation_ar', 255)->nullable()->after('arrival_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'accommodation_ar')) {
                $table->string('accommodation_ar', 255)->nullable()->after('transportation_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'cities_covered_ar')) {
                $table->string('cities_covered_ar', 255)->nullable()->after('cities_covered');
            }

            if (!Schema::hasColumn('tour_packages', 'package_label_ar')) {
                $table->string('package_label_ar', 255)->nullable()->after('package_label');
            }

            if (!Schema::hasColumn('tour_packages', 'tour_type_ar')) {
                $table->string('tour_type_ar', 255)->nullable()->after('tour_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'NO_ZERO_DATE', '')");

        Schema::table('tour_packages', function (Blueprint $table) {
            foreach ([
                'departure_from_ar',
                'arrival_ar',
                'transportation_ar',
                'accommodation_ar',
                'cities_covered_ar',
                'package_label_ar',
                'tour_type_ar',
            ] as $column) {
                if (Schema::hasColumn('tour_packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};