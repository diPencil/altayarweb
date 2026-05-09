<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();
            $table->unsignedBigInteger('tour_package_id')->nullable()->index();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_name_ar')->nullable();
            $table->string('video_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('saves_count')->default(0);
            $table->timestamps();
        });

        Schema::create('reel_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('reel_id')->index();
            $table->enum('type', ['like', 'save']);
            $table->timestamps();

            $table->unique(['user_id', 'reel_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reel_interactions');
        Schema::dropIfExists('reels');
    }
};