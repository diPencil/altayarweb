<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('service_bookings', 'raw_total_amount')) {
                $table->text('raw_total_amount')->nullable()->after('legacy_source');
            }

            if (! Schema::hasColumn('service_bookings', 'legacy_benefit_value')) {
                $table->decimal('legacy_benefit_value', 18, 2)->nullable()->after('raw_total_amount');
            }

            if (! Schema::hasColumn('service_bookings', 'review_flags')) {
                $table->text('review_flags')->nullable()->after('legacy_benefit_value');
            }

            if (! Schema::hasColumn('service_bookings', 'source_excel_row')) {
                $table->integer('source_excel_row')->nullable()->after('review_flags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            foreach (['source_excel_row', 'review_flags', 'legacy_benefit_value', 'raw_total_amount'] as $column) {
                if (Schema::hasColumn('service_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};