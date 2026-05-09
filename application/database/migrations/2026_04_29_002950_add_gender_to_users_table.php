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
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('username');
            }
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname', 40)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname', 40)->nullable()->after('firstname');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'firstname', 'lastname']);
        });
    }
};
