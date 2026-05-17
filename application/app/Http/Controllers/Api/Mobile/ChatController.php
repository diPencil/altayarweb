<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function my(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = ChatConversation::query()
            ->where('user_id', $user->id)
            ->with(['messages'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ChatConversation $conversation) => $this->conversationPayload($conversation))
            ->values();

        return response()->json($conversations);
    }

    public function active(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = ChatConversation::query()
            ->where('user_id', $user->id)
            ->open()
            ->with(['messages'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ChatConversation $conversation) => $this->conversationPayload($conversation))
            ->values();

        return response()->json($conversations);
    }

    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'chat_type' => ['nullable', 'string', 'max:30'],
        ]);

        $user = $request->user();

        $activeConversation = ChatConversation::query()
            ->where('user_id', $user->id)
            ->open()
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        if ($activeConversation) {
            return response()->json([
                'detail' => 'You already have an open conversation.',
                'conversation' => $this->conversationPayload($activeConversation),
            ], 400);
        }

        $subject = trim((string) ($validated['subject'] ?? ''));
        $initialMessage = trim((string) ($validated['message'] ?? ''));
        $chatType = trim((string) ($validated['chat_type'] ?? 'hybrid')) ?: 'hybrid';

        $conversation = new ChatConversation();
        $conversation->user_id = $user->id;
        $conversation->session_key = (string) Str::uuid();
        $conversation->name = $this->customerName($user);
        $conversation->email = $user->email;
        $conversation->locale = app()->getLocale();
        $conversation->chat_type = $chatType;
        $conversation->status = 'open';
        $conversation->ai_enabled = false;
        $conversation->human_requested_at = null;
        $conversation->assigned_admin_id = null;
        $conversation->last_message_at = now();
        $conversation->unread_admin_count = 0;
        $conversation->unread_user_count = 0;
        $conversation->metadata = array_filter([
            'subject' => $subject !== '' ? $subject : 'Support chat',
            'source' => 'mobile',
        ], static fn ($value) => $value !== null && $value !== '');
        $conversation->save();

        if ($initialMessage !== '') {
            $message = $this->createCustomerMessage($conversation, $initialMessage, 'text');
            $conversation->update([
                'last_message_at' => $message->created_at,
                'unread_admin_count' => 1,
            ]);
            $conversation->load('messages');
        }

        return response()->json($this->conversationPayload($conversation->fresh(['messages'])));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $conversation = $this->userConversationOrFail($request->user(), $id);

        if ($conversation->unread_user_count > 0) {
            $conversation->update(['unread_user_count' => 0]);
        }

        $conversation->load(['messages']);

        return response()->json($this->conversationPayload($conversation));
    }

    public function message(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'message_type' => ['nullable', 'string', 'max:30'],
        ]);

        $conversation = $this->userConversationOrFail($request->user(), $id);

        if (in_array($conversation->status, ['closed', 'resolved'], true)) {
            return response()->json([
                'detail' => 'This conversation is closed.',
            ], 403);
        }

        $messageType = trim((string) ($validated['message_type'] ?? 'text')) ?: 'text';
        $message = $this->createCustomerMessage($conversation, $validated['content'], $messageType);

        $conversation->update([
            'last_message_at' => $message->created_at,
            'unread_admin_count' => (int) $conversation->unread_admin_count + 1,
        ]);

        return response()->json($this->messagePayload($message->fresh()));
    }

    protected function userConversationOrFail(User $user, int $id): ChatConversation
    {
        $conversation = ChatConversation::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->with(['messages'])
            ->first();

        abort_if(! $conversation, 404);

        return $conversation;
    }

    protected function createCustomerMessage(ChatConversation $conversation, string $content, string $messageType): ChatMessage
    {
        return ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => $conversation->user_id,
            'message' => trim($content),
            'is_suggested' => false,
            'metadata' => [
                'message_type' => $messageType ?: 'text',
            ],
        ]);
    }

    protected function conversationPayload(ChatConversation $conversation): array
    {
        $conversation->loadMissing(['messages']);

        $messages = $conversation->messages
            ->map(fn (ChatMessage $message) => $this->messagePayload($message))
            ->values()
            ->all();

        $latestMessage = $conversation->messages->last();
        $subject = (string) data_get($conversation->metadata, 'subject', 'Support chat');

        return [
            'id' => $conversation->id,
            'customer_id' => $conversation->user_id,
            'customer_name' => $this->customerName($conversation->user ?? null, $conversation),
            'assigned_to' => $conversation->assigned_admin_id,
            'assigned_to_name' => $this->assignedAdminName($conversation->assigned_admin_id),
            'status' => $this->normalizeStatus($conversation->status),
            'subject' => $subject,
            'last_message_at' => optional($conversation->last_message_at)->toISOString(),
            'last_message_preview' => $latestMessage ? Str::limit((string) $latestMessage->message, 120) : $subject,
            'customer_unread_count' => (int) $conversation->unread_user_count,
            'employee_unread_count' => (int) $conversation->unread_admin_count,
            'created_at' => optional($conversation->created_at)->toISOString(),
            'customer_avatar' => null,
            'messages' => $messages,
        ];
    }

    protected function messagePayload(ChatMessage $message): array
    {
        $conversation = $message->relationLoaded('conversation') ? $message->conversation : $message->conversation()->with('user')->first();
        $user = $conversation?->user;

        return [
            'id' => $message->id,
            'conversation_id' => $message->chat_conversation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $this->messageSenderName($message, $user),
            'sender_role' => $this->normalizeSenderRole($message->sender_type),
            'message_type' => $this->normalizeMessageType($message),
            'content' => (string) $message->message,
            'file_url' => null,
            'offer_id' => null,
            'is_read' => true,
            'created_at' => optional($message->created_at)->toISOString(),
        ];
    }

    protected function normalizeStatus(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'open' => 'OPEN',
            'human_requested', 'waiting' => 'WAITING',
            'assigned' => 'ASSIGNED',
            'active' => 'ACTIVE',
            'closed' => 'CLOSED',
            'resolved' => 'RESOLVED',
            default => strtoupper((string) $status ?: 'OPEN'),
        };
    }

    protected function normalizeSenderRole(?string $senderType): string
    {
        return strtolower((string) $senderType) === 'user' ? 'CUSTOMER' : 'SUPPORT';
    }

    protected function normalizeMessageType(ChatMessage $message): string
    {
        return (string) data_get($message->metadata, 'message_type', 'text') ?: 'text';
    }

    protected function customerName(?User $user = null, ?ChatConversation $conversation = null): string
    {
        if ($user) {
            return (string) ($user->fullname ?? $user->name ?? $user->username ?? 'Guest');
        }

        if ($conversation && $conversation->name) {
            return (string) $conversation->name;
        }

        return 'Guest';
    }

    protected function assignedAdminName(?int $adminId): ?string
    {
        if (! $adminId) {
            return null;
        }

        $admin = Admin::find($adminId);

        if (! $admin) {
            return null;
        }

        return (string) ($admin->name ?? $admin->username ?? $admin->email ?? null);
    }

    protected function messageSenderName(ChatMessage $message, ?User $user = null): string
    {
        if (strtolower((string) $message->sender_type) === 'user') {
            return $this->customerName($user);
        }

        return 'Support Team';
    }
}