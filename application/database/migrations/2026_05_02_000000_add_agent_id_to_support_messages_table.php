<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('support_messages', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->default(0)->after('admin_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_messages', function (Blueprint $table) {
            if (Schema::hasColumn('support_messages', 'agent_id')) {
                $table->dropColumn('agent_id');
            }
        });
    }
};
