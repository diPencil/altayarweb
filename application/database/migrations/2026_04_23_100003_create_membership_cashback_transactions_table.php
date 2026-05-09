<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_cashback_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('tour_booking_id')->nullable();
            $table->string('trx', 40)->unique();
            $table->enum('type', ['earned', 'used']);
            $table->decimal('amount', 18, 2);
            $table->decimal('balance_after', 18, 2);
            $table->string('remark')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tour_booking_id')->references('id')->on('tour_bookings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_cashback_transactions');
    }
};
