<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'legacy_booking_id')) {
                $table->string('legacy_booking_id')->nullable()->index();
            }
            if (!Schema::hasColumn('service_bookings', 'legacy_order_id')) {
                $table->string('legacy_order_id')->nullable()->index();
            }
            if (!Schema::hasColumn('service_bookings', 'legacy_order_item_id')) {
                $table->string('legacy_order_item_id')->nullable()->index();
            }
            if (!Schema::hasColumn('service_bookings', 'legacy_booking_obj_id')) {
                $table->string('legacy_booking_obj_id')->nullable()->index();
            }
            if (!Schema::hasColumn('service_bookings', 'paid_amount')) {
                $table->decimal('paid_amount', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('service_bookings', 'qty')) {
                $table->integer('qty')->default(1);
            }
            if (!Schema::hasColumn('service_bookings', 'guests')) {
                $table->integer('guests')->default(1);
            }
            if (!Schema::hasColumn('service_bookings', 'old_payment_status')) {
                $table->string('old_payment_status')->nullable();
            }
            if (!Schema::hasColumn('service_bookings', 'old_order_status')) {
                $table->string('old_order_status')->nullable();
            }
            if (!Schema::hasColumn('service_bookings', 'legacy_import')) {
                $table->boolean('legacy_import')->default(false);
            }
            if (!Schema::hasColumn('service_bookings', 'legacy_source')) {
                $table->string('legacy_source')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $columns = [
                'legacy_booking_id',
                'legacy_order_id',
                'legacy_order_item_id',
                'legacy_booking_obj_id',
                'paid_amount',
                'qty',
                'guests',
                'old_payment_status',
                'old_order_status',
                'legacy_import',
                'legacy_source',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('service_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
