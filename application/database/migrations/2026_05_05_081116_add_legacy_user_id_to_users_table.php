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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'legacy_user_id')) {
                $table->unsignedBigInteger('legacy_user_id')->nullable()->after('id')->index();
            }
        });

        Schema::table('user_memberships', function (Blueprint $table) {
            if (!Schema::hasColumn('user_memberships', 'payment_summary')) {
                $table->json('payment_summary')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'legacy_user_id')) {
                $table->dropColumn('legacy_user_id');
            }
        });

        Schema::table('user_memberships', function (Blueprint $table) {
            if (Schema::hasColumn('user_memberships', 'payment_summary')) {
                $table->dropColumn('payment_summary');
            }
        });
    }
};
