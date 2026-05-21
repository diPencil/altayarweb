<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tour_package_id');
            $table->timestamps();

            $table->unique(['user_id', 'tour_package_id']);
            $table->index(['tour_package_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_favorites');
    }
};
