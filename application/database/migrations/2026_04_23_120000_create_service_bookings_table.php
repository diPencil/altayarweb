<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->string('booking_type', 50);
            $table->string('title');
            $table->string('reference_no', 120)->nullable();
            $table->date('booking_date')->nullable();
            $table->date('service_date')->nullable();
            $table->date('service_end_date')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->tinyInteger('status')->default(0); // 0=pending,1=confirmed,2=completed,3=canceled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by_admin_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};