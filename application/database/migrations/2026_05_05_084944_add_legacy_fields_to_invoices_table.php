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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'legacy_invoice_id')) {
                $table->string('legacy_invoice_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('invoices', 'legacy_order_id')) {
                $table->string('legacy_order_id')->nullable()->after('legacy_invoice_id')->index();
            }
            if (!Schema::hasColumn('invoices', 'legacy_order_item_id')) {
                $table->string('legacy_order_item_id')->nullable()->after('legacy_order_id')->index();
            }
            if (!Schema::hasColumn('invoices', 'legacy_booking_obj_id')) {
                $table->string('legacy_booking_obj_id')->nullable()->after('legacy_order_item_id')->index();
            }
            if (!Schema::hasColumn('invoices', 'legacy_import')) {
                $table->boolean('legacy_import')->default(false)->after('legacy_booking_obj_id');
            }
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 10)->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = [
                'legacy_invoice_id',
                'legacy_order_id',
                'legacy_order_item_id',
                'legacy_booking_obj_id',
                'legacy_import',
                'payment_method',
                'currency'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
