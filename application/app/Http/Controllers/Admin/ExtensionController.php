<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExtensionController extends Controller
{
    public function index()
    {
        $this->ensureAiChatAssistantExtension();
        $pageTitle = __('Plugins');
        $extensions = Extension::orderBy('status','desc')->get();
        return view('admin.extension.index', compact('pageTitle', 'extensions'));
    }

    protected function ensureAiChatAssistantExtension(): void
    {
        if (! Schema::hasTable('extensions')) {
            return;
        }

        $defaults = [
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
                        'value' => <<<'PROMPT'
You are Altayar VIP's AI travel sales and support assistant, powered by Pencil Studio. Be friendly, natural, sharp, and helpful. Always answer in the user's language, defaulting to Arabic for Arabic users. Do not sound scripted or repeat canned openings. Infer the user's intent from context, give a useful answer first, and ask one smart follow-up question only when needed. If the user greets you or asks casual questions like "عامل ايه" or "how are you", answer naturally first and do not force a sales pitch. Use the provided knowledge base and business data when the user asks about offers, memberships, trips, bookings, or payments. If the user asks for a human, stop AI selling and hand over politely.
PROMPT,
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
                ];

        $extension = Extension::firstOrNew(['act' => 'ai-chat-assistant']);
        $shortcode = $extension->exists ? $extension->shortcode : [];

        if (is_object($shortcode)) {
            $shortcode = json_decode(json_encode($shortcode), true) ?: [];
        } elseif (is_string($shortcode)) {
            $shortcode = json_decode($shortcode, true) ?: [];
        } elseif (! is_array($shortcode)) {
            $shortcode = [];
        }

        foreach ($defaults as $key => $default) {
            $shortcode[$key] = array_merge($default, $shortcode[$key] ?? []);
            $shortcode[$key]['title'] = $shortcode[$key]['title'] ?? $default['title'];
            $shortcode[$key]['type'] = $shortcode[$key]['type'] ?? ($default['type'] ?? null);
        }

        $extension->fill([
                'name' => 'Feliz AI',
                'description' => 'Smart travel sales, support, and human handover assistant',
                'image' => 'chat-png.png',
                'script' => null,
                'shortcode' => $shortcode,
                'support' => 'chat-png.png',
        ]);

        if (! $extension->exists) {
            $extension->status = 1;
        }

        $extension->save();
    }

    public function update(Request $request, $id)
    {
        $extension = Extension::findOrFail($id);
        $shortcode = $extension->shortcode;

        if (is_string($shortcode)) {
            $shortcode = json_decode($shortcode, true) ?: [];
        } elseif (is_object($shortcode)) {
            $shortcode = json_decode(json_encode($shortcode), true) ?: [];
        } elseif (! is_array($shortcode)) {
            $shortcode = [];
        }

        $validation_rule = [];
        foreach ($shortcode as $key => $val) {
            $validation_rule[$key] = 'nullable';
        }

        $request->validate($validation_rule);

        foreach ($shortcode as $key => $value) {
            if ($request->has($key)) {
                $shortcode[$key]['value'] = $request->$key;
            }
        }

        $extension->shortcode = $shortcode;
        $extension->save();
        $notify[] = ['success', __(':name has been updated successfully', ['name' => $extension->name])];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $extension = Extension::findOrFail($id);
        if ($extension->status == 1) {
            $extension->status = 0;
            $notify[] = ['success', __(':name activated successfully', ['name' => $extension->name])];
        }else{
            $extension->status = 1;
            $notify[] = ['success', __(':name activated successfully', ['name' => $extension->name])];
        }
        $extension->save();
        return back()->withNotify($notify);
    }
}
