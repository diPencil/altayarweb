<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'NO_ZERO_DATE', '')");

        Schema::table('tour_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_packages', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }

            if (!Schema::hasColumn('tour_packages', 'description_ar')) {
                $table->longText('description_ar')->nullable()->after('description');
            }

            if (!Schema::hasColumn('tour_packages', 'address_ar')) {
                $table->string('address_ar')->nullable()->after('address');
            }

            if (!Schema::hasColumn('tour_packages', 'destination_highlights_ar')) {
                $table->json('destination_highlights_ar')->nullable()->after('highlights');
            }

            if (!Schema::hasColumn('tour_packages', 'destination_features_ar')) {
                $table->json('destination_features_ar')->nullable()->after('features');
            }

            if (!Schema::hasColumn('tour_packages', 'includes')) {
                $table->json('includes')->nullable()->after('destination_features_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'includes_ar')) {
                $table->json('includes_ar')->nullable()->after('includes');
            }

            if (!Schema::hasColumn('tour_packages', 'excludes')) {
                $table->json('excludes')->nullable()->after('includes_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'excludes_ar')) {
                $table->json('excludes_ar')->nullable()->after('excludes');
            }

            if (!Schema::hasColumn('tour_packages', 'itinerary_days')) {
                $table->json('itinerary_days')->nullable()->after('excludes_ar');
            }

            if (!Schema::hasColumn('tour_packages', 'cities_covered')) {
                $table->string('cities_covered')->nullable()->after('itinerary_days');
            }

            if (!Schema::hasColumn('tour_packages', 'accommodation_level')) {
                $table->string('accommodation_level')->nullable()->after('cities_covered');
            }

            if (!Schema::hasColumn('tour_packages', 'package_label')) {
                $table->string('package_label')->nullable()->after('accommodation_level');
            }

            if (!Schema::hasColumn('tour_packages', 'tour_type')) {
                $table->string('tour_type')->nullable()->after('package_label');
            }

            if (!Schema::hasColumn('tour_packages', 'currency')) {
                $table->string('currency', 10)->nullable()->after('tour_type');
            }

            if (!Schema::hasColumn('tour_packages', 'price_from')) {
                $table->decimal('price_from', 12, 2)->nullable()->after('currency');
            }

            if (!Schema::hasColumn('tour_packages', 'price_to')) {
                $table->decimal('price_to', 12, 2)->nullable()->after('price_from');
            }

            if (!Schema::hasColumn('tour_packages', 'price_note')) {
                $table->string('price_note')->nullable()->after('price_to');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'NO_ZERO_DATE', '')");

        Schema::table('tour_packages', function (Blueprint $table) {
            foreach ([
                'title_ar',
                'description_ar',
                'address_ar',
                'destination_highlights_ar',
                'destination_features_ar',
                'includes',
                'includes_ar',
                'excludes',
                'excludes_ar',
                'itinerary_days',
                'cities_covered',
                'accommodation_level',
                'package_label',
                'tour_type',
                'currency',
                'price_from',
                'price_to',
                'price_note',
            ] as $column) {
                if (Schema::hasColumn('tour_packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};