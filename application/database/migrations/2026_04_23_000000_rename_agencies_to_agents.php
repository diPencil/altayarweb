<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('agencies')) {
            Schema::rename('agencies', 'agents');
        }

        $tables = [
            ['admin_notifications', 'agency_id', 'agent_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['notification_logs', 'agency_id', 'agent_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['support_tickets', 'agency_id', 'agent_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['transactions', 'agency_id', 'agent_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['user_logins', 'agency_id', 'agent_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['general_settings', 'agency_socialite_credentials', 'agent_socialite_credentials', 'TEXT NULL'],
        ];

        foreach ($tables as $item) {
            if (Schema::hasTable($item[0]) && Schema::hasColumn($item[0], $item[1])) {
                DB::statement("ALTER TABLE `{$item[0]}` CHANGE `{$item[1]}` `{$item[2]}` {$item[3]}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            ['general_settings', 'agent_socialite_credentials', 'agency_socialite_credentials', 'TEXT NULL'],
            ['user_logins', 'agent_id', 'agency_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['transactions', 'agent_id', 'agency_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['support_tickets', 'agent_id', 'agency_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['notification_logs', 'agent_id', 'agency_id', 'INT(11) NOT NULL DEFAULT 0'],
            ['admin_notifications', 'agent_id', 'agency_id', 'INT(11) NOT NULL DEFAULT 0'],
        ];

        foreach ($tables as $item) {
            if (Schema::hasTable($item[0]) && Schema::hasColumn($item[0], $item[1])) {
                DB::statement("ALTER TABLE `{$item[0]}` CHANGE `{$item[1]}` `{$item[2]}` {$item[3]}");
            }
        }

        if (Schema::hasTable('agents')) {
            Schema::rename('agents', 'agencies');
        }
    }
};
