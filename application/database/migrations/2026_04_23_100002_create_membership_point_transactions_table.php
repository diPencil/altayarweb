<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('membership_plan_id')->nullable();
            $table->bigInteger('tour_booking_id')->nullable();
            $table->string('trx', 40)->unique();
            $table->enum('type', ['earned', 'used']);
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('remark')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_plan_id')->references('id')->on('membership_plans')->nullOnDelete();
            $table->foreign('tour_booking_id')->references('id')->on('tour_bookings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_point_transactions');
    }
};
