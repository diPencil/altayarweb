<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = NotificationLog::where('user_id', $user->id);
        $total = (clone $query)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'unread' => 0,
                'by_type' => [
                    'system' => $total,
                ],
                'by_priority' => [
                    'normal' => $total,
                ],
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = max(1, min((int) $request->integer('per_page', 20), 100));

        $notifications = NotificationLog::where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate($perPage);

        $mapped = $notifications->getCollection()->map(function (NotificationLog $log) {
            $message = $log->message ?? $log->subject ?? 'Notification';

            return [
                'id' => $log->id,
                'type' => 'system',
                'title' => $log->subject ?: 'Notification',
                'message' => strip_tags((string) $message),
                'action_url' => null,
                'related_entity_type' => null,
                'related_entity_id' => null,
                'target_user_id' => $log->user_id,
                'is_read' => true,
                'read_at' => optional($log->created_at)->toISOString(),
                'priority' => 'normal',
                'created_at' => optional($log->created_at)->toISOString(),
                'updated_at' => optional($log->updated_at)->toISOString(),
                'triggered_by_id' => null,
                'triggered_by_role' => null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $mapped,
                'total' => $notifications->total(),
                'unread_count' => 0,
            ],
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => 0,
            ],
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = NotificationLog::where('user_id', $user->id)->find($id);

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}