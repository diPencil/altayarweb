<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update historical transaction descriptions to remove third-party gateway branding
        DB::table('transactions')
            ->where('details', 'E-Payment Via Fawaterk')
            ->update(['details' => 'E-Payment Online']);

        DB::table('transactions')
            ->where('details', 'Payment Via Fawaterk')
            ->update(['details' => 'Payment Online']);

        // Update gateway display name while preserving the technical alias ('Fawaterk')
        DB::table('gateways')
            ->where('alias', 'Fawaterk')
            ->update(['name' => 'Online Payment']);

        DB::table('gateway_currencies')
            ->where('gateway_alias', 'Fawaterk')
            ->update(['name' => 'Online Payment']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('gateways')
            ->where('alias', 'Fawaterk')
            ->update(['name' => 'Fawaterk']);

        DB::table('gateway_currencies')
            ->where('gateway_alias', 'Fawaterk')
            ->update(['name' => 'Fawaterk']);

        DB::table('transactions')
            ->where('details', 'E-Payment Online')
            ->update(['details' => 'E-Payment Via Fawaterk']);

        DB::table('transactions')
            ->where('details', 'Payment Online')
            ->update(['details' => 'Payment Via Fawaterk']);
    }
};
