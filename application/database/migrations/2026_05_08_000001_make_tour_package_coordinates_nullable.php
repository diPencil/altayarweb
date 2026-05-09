<?php

use Illuminate\Database\Migrations\Migration;
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
        DB::statement('ALTER TABLE tour_packages MODIFY latitude varchar(255) NULL, MODIFY longitude varchar(255) NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', ''), 'NO_ZERO_DATE', '')");
        DB::statement('ALTER TABLE tour_packages MODIFY latitude varchar(255) NOT NULL, MODIFY longitude varchar(255) NOT NULL');
    }
};
