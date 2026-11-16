<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('invoices', 'currency')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('currency', 10)->nullable()->after('paid_amount');
            });
        }

        DB::table('invoices')
            ->whereNull('currency')
            ->orWhere('currency', '')
            ->update(['currency' => 'USD']);
    }

    public function down(): void
    {
        // The currency column may predate this migration, so rollback preserves it.
    }
};
