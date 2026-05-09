<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popup_ads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('title')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('body')->nullable();
            $table->text('body_ar')->nullable();
            $table->string('cta_text', 120)->nullable();
            $table->string('cta_text_ar', 120)->nullable();
            $table->string('cta_url')->nullable();
            $table->string('image')->nullable();
            $table->string('placement', 40)->default('modal');
            $table->string('size', 40)->default('medium');
            $table->string('audience_type', 40)->default('all');
            $table->json('display_contexts')->nullable();
            $table->json('page_rules')->nullable();
            $table->json('membership_plan_ids')->nullable();
            $table->json('target_user_ids')->nullable();
            $table->json('target_employee_ids')->nullable();
            $table->string('created_by_type', 30)->default('admin');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('trigger_type', 30)->default('on_load');
            $table->unsignedInteger('trigger_value')->default(0);
            $table->string('frequency', 30)->default('once');
            $table->unsignedInteger('frequency_value')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('closeable')->default(true);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('priority')->default(10);
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('unique_impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->unsignedBigInteger('closes_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['audience_type']);
            $table->index(['created_by_type', 'created_by_id']);
        });

        Schema::create('popup_ad_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('popup_ad_id')->constrained('popup_ads')->cascadeOnDelete();
            $table->string('event_type', 30);
            $table->string('viewer_type', 30)->default('guest');
            $table->unsignedBigInteger('viewer_id')->nullable();
            $table->string('visitor_key', 80)->nullable();
            $table->string('url')->nullable();
            $table->string('ip', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['popup_ad_id', 'event_type']);
            $table->index(['viewer_type', 'viewer_id']);
            $table->index(['visitor_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_ad_events');
        Schema::dropIfExists('popup_ads');
    }
};
