<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('membership_plan_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();       // null = lifetime
            $table->tinyInteger('status')->default(1); // 0=pending, 1=active, 2=expired
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_plan_id')->references('id')->on('membership_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};
