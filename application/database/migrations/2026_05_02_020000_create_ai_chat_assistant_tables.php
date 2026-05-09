<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_key', 100)->unique();
            $table->string('name', 120)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('locale', 12)->default('ar');
            $table->string('chat_type', 20)->default('hybrid');
            $table->string('status', 30)->default('open');
            $table->boolean('ai_enabled')->default(true);
            $table->timestamp('human_requested_at')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable()->index();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->unsignedInteger('unread_admin_count')->default(0);
            $table->unsignedInteger('unread_user_count')->default(0);
            $table->longText('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->string('sender_type', 20)->default('user');
            $table->unsignedBigInteger('sender_id')->nullable()->index();
            $table->longText('message');
            $table->boolean('is_suggested')->default(false);
            $table->longText('metadata')->nullable();
            $table->timestamps();

            $table->index(['chat_conversation_id', 'id']);
        });

        DB::table('extensions')->updateOrInsert(
            ['act' => 'ai-chat-assistant'],
            [
                'name' => 'Feliz AI',
                'description' => 'Smart travel sales, support, and human handover assistant',
                'image' => 'chat-png.png',
                'script' => null,
                'shortcode' => json_encode([
                    'enabled' => [
                        'title' => 'Enable Assistant',
                        'value' => '1',
                    ],
                    'provider' => [
                        'title' => 'AI Brand',
                        'value' => 'feliz_ai',
                    ],
                    'api_key' => [
                        'title' => 'API Key',
                        'value' => '',
                        'type' => 'textarea',
                    ],
                    'model' => [
                        'title' => 'Model',
                        'value' => 'feliz-ai',
                    ],
                    'system_prompt' => [
                        'title' => 'System Prompt',
                        'value' => implode(PHP_EOL, [
                            "You are Altayar VIP's AI travel sales and support assistant. Be friendly, short, sales-oriented, and helpful.",
                            "Always answer in the user's language, defaulting to Arabic for Arabic users.",
                            'Use the provided knowledge base and business data. Suggest memberships, offers, and trips.',
                            'If the user asks for a human, stop AI selling and hand over politely.',
                        ]),
                        'type' => 'textarea',
                    ],
                    'static_knowledge' => [
                        'title' => 'Static Knowledge',
                        'value' => 'Altayar VIP is a travel platform that sells membership plans, cashback benefits, loyalty points, offers, and travel bookings. The assistant should keep answers concise and conversion-focused.',
                        'type' => 'textarea',
                    ],
                    'knowledge_urls' => [
                        'title' => 'Knowledge URLs',
                        'value' => "/offers/limited\n/offers/yearly\n/offers/weekend\n/membership-details\n/browse\n/privilege-cards",
                        'type' => 'textarea',
                    ],
                    'chat_settings' => [
                        'title' => 'Chat Settings',
                        'value' => json_encode([
                            'title' => 'Feliz AI',
                            'subtitle' => 'Fast answers for trips, offers, and memberships',
                            'placeholder' => 'Ask about offers, memberships, cashback, or booking help...',
                            'quick_actions' => [
                                ['label' => 'View offers', 'url' => '/offers/limited'],
                                ['label' => 'View membership', 'url' => '/membership-details'],
                                ['label' => 'Book now', 'url' => '/browse'],
                            ],
                            'poll_interval' => 4000,
                            'max_length' => 2000,
                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'type' => 'textarea',
                    ],
                    'asset_url' => [
                        'title' => 'Widget Asset URL',
                        'value' => '/assets/common/js/ai-chat-assistant.js',
                    ],
                    'bootstrap_url' => [
                        'title' => 'Bootstrap URL',
                        'value' => '/chat-assistant/bootstrap',
                    ],
                    'message_url' => [
                        'title' => 'Message URL',
                        'value' => '/chat-assistant/message',
                    ],
                    'poll_url' => [
                        'title' => 'Poll URL',
                        'value' => '/chat-assistant/poll',
                    ],
                    'handover_url' => [
                        'title' => 'Human Handover URL',
                        'value' => '/chat-assistant/handover',
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'support' => 'chat-png.png',
                'status' => 1,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('extensions')->where('act', 'ai-chat-assistant')->delete();
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};