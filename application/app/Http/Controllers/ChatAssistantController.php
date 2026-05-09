<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\AdminNotification;
use App\Services\AiChatAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatAssistantController extends Controller
{
    public function __construct(protected AiChatAssistantService $assistant)
    {
        $this->middleware('throttle:30,1')->only(['message', 'handover', 'poll']);
    }

    public function page()
    {
        return view(activeTemplate() . 'chat_assistant', [
            'pageTitle' => __('Feliz AI Assistant'),
        ]);
    }

    public function bootstrap(Request $request)
    {
        if (! $this->assistant->enabled()) {
            return response()->json(['enabled' => false]);
        }

        $conversation = $this->assistant->resolveConversation([
            'session_key' => $request->string('session_key')->toString() ?: null,
            'name' => $request->string('name')->toString() ?: null,
            'email' => $request->string('email')->toString() ?: null,
            'locale' => $request->string('locale')->toString() ?: app()->getLocale(),
            'force_new' => $request->boolean('force_new'),
        ]);

        return response()->json([
            'enabled' => true,
            'assistant' => [
                'title' => $this->assistant->title(),
                'subtitle' => $this->assistant->subtitle(),
                'placeholder' => $this->assistant->placeholder(),
                'quick_actions' => $this->assistant->quickActions(),
                'poll_interval' => $this->assistant->pollInterval(),
            ],
            'conversation' => $this->conversationPayload($conversation),
            'messages' => $this->messagePayload($conversation),
        ]);
    }

    public function message(Request $request)
    {
        \Log::info('ChatAssistant message method ENTRY');
        try {
            \Log::info('ChatAssistant message received', $request->all());
            $request->validate([
                'session_key' => 'nullable|string|max:100',
                'message' => 'required|string|max:' . $this->assistant->maxMessageLength(),
                'source' => 'nullable|string|in:composer,suggestion',
                'name' => 'nullable|string|max:120',
                'email' => 'nullable|email|max:120',
                'locale' => 'nullable|string|max:12',
            ]);

            $conversation = $this->assistant->resolveConversation([
                'session_key' => $request->string('session_key')->toString() ?: null,
                'name' => $request->string('name')->toString() ?: null,
                'email' => $request->string('email')->toString() ?: null,
                'locale' => $request->string('locale')->toString() ?: app()->getLocale(),
            ]);

            if ($conversation->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'status' => 'closed',
                    'message' => __('This conversation is closed.'),
                ], 423);
            }

            $userMessage = ChatMessage::create([
                'chat_conversation_id' => $conversation->id,
                'sender_type' => 'user',
                'sender_id' => $conversation->user_id,
                'message' => trim($request->message),
                'metadata' => ['url' => $request->string('url')->toString(), 'referrer' => $request->string('referrer')->toString()],
            ]);

            $previousUnreadAdminCount = (int) $conversation->unread_admin_count;
            $conversation->update([
                'name' => $request->input('name', $conversation->name),
                'email' => $request->input('email', $conversation->email),
                'locale' => $request->input('locale', $conversation->locale),
                'last_message_at' => now(),
                'unread_admin_count' => $conversation->unread_admin_count + 1,
            ]);

            if ($previousUnreadAdminCount === 0) {
                $this->notifyAdminForNewMessage($conversation, $userMessage);
            }

            \Log::info('ChatAssistant checking AI condition', [
                'chat_type' => $conversation->chat_type,
                'human_requested_at' => $conversation->human_requested_at,
                'condition_met' => ($conversation->chat_type !== 'human' && ! $conversation->human_requested_at)
            ]);

            $responseData = ['reply' => null, 'suggested_replies' => []];
            $shouldGenerateAiReply = $request->input('source') === 'suggestion'
                || ($conversation->chat_type !== 'human' && ! $conversation->human_requested_at);

            if ($shouldGenerateAiReply) {
                $responseData = $this->assistant->generateReply($conversation->fresh(['messages']), $request->message);
                \Log::info('ChatAssistant AI response', $responseData);

                $assistantMessage = trim(implode("\n\n", array_filter(array_map('trim', (array) ($responseData['messages'] ?? [])))));
                if ($assistantMessage === '' && ! empty($responseData['reply'])) {
                    $assistantMessage = trim((string) $responseData['reply']);
                }

                if ($assistantMessage !== '') {
                    ChatMessage::create([
                        'chat_conversation_id' => $conversation->id,
                        'sender_type' => 'ai',
                        'sender_id' => 0,
                        'message' => $assistantMessage,
                        'is_suggested' => false,
                        'metadata' => [
                            'suggested_replies' => $responseData['suggested_replies'] ?? [],
                        ],
                    ]);

                    $conversation->update([
                        'last_message_at' => now(),
                    ]);
                }
            }

            if (($responseData['handover_recommended'] ?? false) === true) {
                $this->notifyAdminForHandover($conversation);
            }

            return response()->json([
                'success' => true,
                'conversation' => $this->conversationPayload($conversation->fresh()),
                'messages' => $this->messagePayload($conversation->fresh(['messages'])),
                'reply' => $responseData['reply'],
                'suggested_replies' => $responseData['suggested_replies'] ?? [],
            ]);
        } catch (\Exception $e) {
            \Log::error('ChatAssistant message error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function poll(Request $request)
    {
        $request->validate([
            'session_key' => 'required|string|max:100',
            'last_id' => 'nullable|integer|min:0',
        ]);

        $conversation = ChatConversation::query()
            ->where('session_key', $request->string('session_key')->toString())
            ->first();

        if (! $conversation) {
            return response()->json(['success' => true, 'messages' => [], 'conversation' => null]);
        }

        $messages = $conversation->messages()
            ->when($request->filled('last_id'), fn ($query) => $query->where('id', '>', (int) $request->last_id))
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(fn ($message) => $this->messageItem($message))->values(),
            'conversation' => $this->conversationPayload($conversation->fresh()),
        ]);
    }

    public function handover(Request $request)
    {
        \Log::info('ChatAssistant handover requested', $request->all());
        
        $request->validate([
            'session_key' => 'required|string|max:100',
            'name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:120',
            'locale' => 'nullable|string|max:12',
        ]);

        $conversation = $this->assistant->resolveConversation([
            'session_key' => $request->string('session_key')->toString(),
            'name' => $request->string('name')->toString() ?: null,
            'email' => $request->string('email')->toString() ?: null,
            'locale' => $request->string('locale')->toString() ?: app()->getLocale(),
        ]);

        $conversation->update([
            'status' => 'human_requested',
            'human_requested_at' => now(),
            'chat_type' => 'hybrid',
            'last_message_at' => now(),
            'unread_admin_count' => $conversation->unread_admin_count + 1,
        ]);

        // Create a message from the user side to make it visible in the admin chat list
        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => $conversation->user_id,
            'message' => __('I would like to speak with a human agent.'),
            'metadata' => ['handover_trigger' => true],
        ]);

        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'system',
            'sender_id' => 0,
            'message' => __('Your request has been sent. A human support agent will join shortly.'),
            'metadata' => ['handover' => true],
        ]);

        $this->notifyAdminForHandover($conversation);

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationPayload($conversation->fresh()),
            'messages' => $this->messagePayload($conversation->fresh(['messages'])),
        ]);
    }

    public function clear(Request $request)
    {
        $conversation = $this->assistant->resolveConversation([
            'session_key' => $request->string('session_key')->toString(),
        ]);

        if ($conversation->status === 'closed') {
            return response()->json([
                'success' => false,
                'status' => 'closed',
                'message' => __('This conversation is closed.'),
            ], 423);
        }

        $conversation->messages()->delete();
        $conversation->update([
            'last_message_at' => now(),
            'unread_admin_count' => 0,
            'unread_user_count' => 0,
            'status' => 'open',
            'chat_type' => 'hybrid',
            'human_requested_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    protected function notifyAdminForHandover(ChatConversation $conversation): void
    {
        $notification = new AdminNotification();
        $notification->user_id = $conversation->user_id ?: 0;
        $notification->agent_id = 0;
        $notification->title = 'New Handover Request: ' . ($conversation->name ?: 'Guest');
        $notification->click_url = route('admin.chat-assistant.index', ['conversation' => $conversation->id]);
        $notification->save();
    }

    protected function notifyAdminForNewMessage(ChatConversation $conversation, ChatMessage $message): void
    {
        $notification = new AdminNotification();
        $notification->user_id = $conversation->user_id ?: 0;
        $notification->agent_id = 0;
        $notification->title = 'New chat message from ' . ($conversation->name ?: 'Guest');
        $notification->click_url = route('admin.chat-assistant.view', $conversation->id);
        $notification->save();
    }

    protected function conversationPayload(ChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'session_key' => $conversation->session_key,
            'name' => $conversation->name,
            'email' => $conversation->email,
            'locale' => $conversation->locale,
            'chat_type' => $conversation->chat_type,
            'status' => $conversation->status,
            'human_requested_at' => optional($conversation->human_requested_at)?->toDateTimeString(),
            'last_message_at' => optional($conversation->last_message_at)?->toDateTimeString(),
            'unread_admin_count' => $conversation->unread_admin_count,
            'unread_user_count' => $conversation->unread_user_count,
        ];
    }

    protected function messagePayload(ChatConversation $conversation): array
    {
        return $conversation->messages()->get()->map(fn ($message) => $this->messageItem($message))->values()->all();
    }

    protected function messageItem(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender_type,
            'sender_type' => $message->sender_type,
            'message' => $message->message,
            'is_suggested' => (bool) $message->is_suggested,
            'created_at' => optional($message->created_at)?->toDateTimeString(),
            'created_at_human' => $message->created_at ? diffForHumans($message->created_at) : null,
            'metadata' => $message->metadata,
        ];
    }
}
