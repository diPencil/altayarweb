<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('membership_plan_histories')) {
            return;
        }

        Schema::create('membership_plan_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('previous_membership_id')->nullable();
            $table->unsignedBigInteger('previous_plan_id')->nullable();
            $table->unsignedBigInteger('new_membership_id')->nullable();
            $table->unsignedBigInteger('new_plan_id');
            $table->string('change_type', 30);
            $table->decimal('previous_price', 12, 2)->default(0);
            $table->decimal('new_price', 12, 2)->default(0);
            $table->decimal('price_difference', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['new_plan_id']);
            $table->index(['previous_plan_id']);
            $table->index(['new_membership_id']);
            $table->index(['previous_membership_id']);
            $table->index(['created_by_admin_id']);
            $table->index(['created_by_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plan_histories');
    }
};