<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\TourBooking;
use App\Services\AiChatAssistantService;
use Illuminate\Http\Request;

class ChatAssistantController extends Controller
{
    public function __construct(protected AiChatAssistantService $assistant)
    {
        $this->middleware(function ($request, $next) {
            $this->assistant->enabled();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $pageTitle = __('Live Chat');
        $items = ChatConversation::query()
            ->with(['user', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.chat_assistant.index', compact('pageTitle', 'items'));
    }

    public function conversations(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $conversations = ChatConversation::query()
            ->with(['user', 'latestMessage'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('session_key', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations->map(fn ($conversation) => $this->conversationSummary($conversation))->values(),
        ]);
    }

    public function show(ChatConversation $conversation)
    {
        $conversation->update(['unread_admin_count' => 0]);

        return response()->json([
            'success' => true,
            'conversation' => $this->conversationDetail($conversation->load(['user', 'messages'])) ,
        ]);
    }

    public function view(ChatConversation $conversation)
    {
        $pageTitle = __('Dashboard') . ' » ' . __('Live Chat') . ' » #' . $conversation->id;
        $conversation->update(['unread_admin_count' => 0]);

        return view('admin.chat_assistant.view', [
            'pageTitle' => $pageTitle,
            'conversation' => $conversation->load(['user', 'messages']),
            'detail' => $this->conversationDetail($conversation->load(['user', 'messages'])),
        ]);
    }

    public function reopen(Request $request, ChatConversation $conversation)
    {
        $conversation->update(['status' => 'open']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->withNotify([['success', __('Conversation reopened successfully')]]);
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => auth('admin')->id(),
            'message' => trim($request->message),
        ]);

        $conversation->update([
            'status' => 'open',
            'chat_type' => 'human',
            'last_message_at' => now(),
            'unread_user_count' => $conversation->unread_user_count + 1,
            'assigned_admin_id' => auth('admin')->id(),
            'human_requested_at' => $conversation->human_requested_at ?? now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'conversation' => $this->conversationDetail($conversation->load(['user', 'messages'])),
            ]);
        }

        return back()->withNotify([['success', __('Reply sent successfully')]]);
    }

    public function close(ChatConversation $conversation)
    {
        $conversation->update(['status' => 'closed']);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->withNotify([['success', __('Conversation closed successfully')]]);
    }

    public function destroy(ChatConversation $conversation)
    {
        \DB::transaction(function () use ($conversation) {
            $conversation->messages()->delete();
            $conversation->delete();
        });

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->withNotify([['success', __('Conversation deleted successfully')]]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:chat_conversations,id',
        ]);

        $ids = $request->ids;

        \DB::transaction(function () use ($ids) {
            ChatMessage::whereIn('chat_conversation_id', $ids)->delete();
            ChatConversation::whereIn('id', $ids)->delete();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Selected chat conversations deleted successfully')
            ]);
        }

        return back()->withNotify([['success', __('Selected chat conversations deleted successfully')]]);
    }

    protected function conversationSummary(ChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'name' => $conversation->name ?: __('Guest'),
            'email' => $conversation->email,
            'status' => $conversation->status,
            'chat_type' => $conversation->chat_type,
            'last_message' => optional($conversation->latestMessage)->message,
            'last_message_at' => optional($conversation->last_message_at)?->toDateTimeString(),
            'unread_admin_count' => $conversation->unread_admin_count,
            'session_key' => $conversation->session_key,
        ];
    }

    protected function conversationDetail(ChatConversation $conversation): array
    {
        return [
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name ?: __('Guest'),
                'email' => $conversation->email,
                'status' => $conversation->status,
                'chat_type' => $conversation->chat_type,
                'session_key' => $conversation->session_key,
                'locale' => $conversation->locale,
                'created_at' => optional($conversation->created_at)?->toDateTimeString(),
                'last_message_at' => optional($conversation->last_message_at)?->toDateTimeString(),
                'user' => $conversation->user ? [
                    'id' => $conversation->user->id,
                    'username' => $conversation->user->username,
                    'name' => $conversation->user->fullname,
                    'email' => $conversation->user->email,
                    'phone' => $conversation->user->mobile ?? $conversation->user->phone ?? $conversation->user->mobile_number ?? null,
                    'status' => (int) $conversation->user->status,
                    'joined_at' => optional($conversation->user->created_at)?->toDateTimeString(),
                    'membership' => optional($conversation->user->currentMembership?->plan)->name,
                    'wallet_balance' => (float) $conversation->user->balance,
                    'points' => (int) $conversation->user->membership_points_balance,
                    'cashback' => (float) $conversation->user->cashback_balance,
                    'bookings' => [
                        'tour' => TourBooking::where('user_id', $conversation->user->id)->count(),
                        'service' => $conversation->user->serviceBookings()->count(),
                        'total' => TourBooking::where('user_id', $conversation->user->id)->count() + $conversation->user->serviceBookings()->count(),
                    ],
                ] : [
                    'id' => null,
                    'username' => null,
                    'name' => $conversation->name,
                    'email' => $conversation->email,
                    'phone' => null,
                    'status' => null,
                    'joined_at' => null,
                    'membership' => null,
                    'wallet_balance' => 0,
                    'points' => null,
                    'cashback' => null,
                    'bookings' => [
                        'tour' => 0,
                        'service' => 0,
                        'total' => 0,
                    ],
                ],
            ],
            'messages' => $conversation->messages->map(fn ($message) => [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'message' => $message->message,
                'created_at' => optional($message->created_at)?->toDateTimeString(),
                'is_suggested' => (bool) $message->is_suggested,
                'metadata' => $message->metadata,
            ])->values(),
        ];
    }
}