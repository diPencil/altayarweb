<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('privilege_cards')) {
            Schema::create('privilege_cards', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('subtitle_ar')->nullable();
                $table->text('description')->nullable();
                $table->text('description_ar')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->json('benefits')->nullable();
                $table->json('features')->nullable();
                $table->string('image_file')->nullable();
                $table->string('pdf_file')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('privilege_cards');
    }
};