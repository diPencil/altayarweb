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
        Schema::create('user_membership_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('benefit_type', 100)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('used_quantity')->default(0);
            $table->string('unit', 50)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_membership_benefits');
    }
};
