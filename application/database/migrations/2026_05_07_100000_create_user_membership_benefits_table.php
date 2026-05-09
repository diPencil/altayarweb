<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_membership_benefits')) {
            return;
        }

        Schema::create('user_membership_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('membership_plan_id')->nullable();
            $table->string('benefit_type')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('used_quantity')->default(0);
            $table->string('unit')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('membership_plan_id');
            $table->index('benefit_type');
            $table->index('status');
            $table->index('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_plan_id')->references('id')->on('membership_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_membership_benefits');
    }
};
