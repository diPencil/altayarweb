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
        Schema::table('membership_plan_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('new_membership_id')->nullable()->change();
            $table->unsignedBigInteger('new_plan_id')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plan_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('new_membership_id')->nullable(false)->change();
            $table->unsignedBigInteger('new_plan_id')->nullable(false)->change();
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
