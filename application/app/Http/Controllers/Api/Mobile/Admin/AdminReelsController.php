<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\ReelComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminReelsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = max(1, min((int) $request->integer('per_page', 20), 100));

            $reels = Reel::query()
                ->orderByDesc('id')
                ->paginate($perPage);

            $mapped = $reels->getCollection()->map(function (Reel $reel): array {
                $videoUrl = $reel->video_url ?? ($reel->video_path
                    ? asset(getFilePath('reelVideo') . '/' . $reel->video_path)
                    : null);

                $thumbnailUrl = $reel->thumbnail_url ?? ($reel->thumbnail_path
                    ? asset(getFilePath('reelThumbnail') . '/' . $reel->thumbnail_path)
                    : null);

                return [
                    'id'             => (string) $reel->id,
                    'title'          => $reel->title,
                    'title_ar'       => $reel->title_ar,
                    'description'    => $reel->description,
                    'video_url'      => $videoUrl,
                    'thumbnail_url'  => $thumbnailUrl,
                    'video_type'     => $reel->video_type ?? 'URL',
                    'status'         => $reel->status ? 'ACTIVE' : 'INACTIVE',
                    'views_count'    => (int) ($reel->views_count ?? 0),
                    'likes_count'    => (int) ($reel->likes_count ?? 0),
                    'saves_count'    => (int) ($reel->saves_count ?? 0),
                    'comments_count' => $reel->comments()->count(),
                    'sort_order'     => (int) ($reel->sort_order ?? 0),
                    'created_at'     => optional($reel->created_at)->toISOString(),
                ];
            })->values();

            // Analytics summary
            $totalReels    = Reel::count();
            $totalViews    = Reel::sum('views_count');
            $totalLikes    = Reel::sum('likes_count');
            $totalComments = ReelComment::count();

            return response()->json([
                'success'   => true,
                'data'      => $mapped,
                'analytics' => [
                    'total_reels'    => $totalReels,
                    'total_views'    => (int) $totalViews,
                    'total_likes'    => (int) $totalLikes,
                    'total_comments' => $totalComments,
                ],
                'meta'      => [
                    'current_page' => $reels->currentPage(),
                    'per_page'     => $reels->perPage(),
                    'total'        => $reels->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch reels: ' . $e->getMessage()], 500);
        }
    }

    public function comments(Request $request): JsonResponse
    {
        try {
            $perPage = max(1, min((int) $request->integer('per_page', 30), 100));
            $reelId  = $request->query('reel_id');

            $query = ReelComment::query()
                ->with([
                    'user:id,firstname,lastname,username',
                    'reel:id,title,title_ar',
                ]);

            if ($reelId) {
                $query->where('reel_id', $reelId);
            }

            $comments = $query->orderByDesc('id')->paginate($perPage);

            $mapped = $comments->getCollection()->map(function (ReelComment $c): array {
                return [
                    'id'          => $c->id,
                    'reel_id'     => (string) $c->reel_id,
                    'reel_title'  => $c->reel?->title,
                    'user_id'     => (string) ($c->user_id ?? ''),
                    'user_name'   => $c->user ? trim($c->user->firstname . ' ' . $c->user->lastname) : null,
                    'username'    => $c->user?->username,
                    'content'     => $c->comment,
                    'admin_reply' => $c->admin_reply,
                    'status'      => $c->status ? 'APPROVED' : 'PENDING',
                    'created_at'  => optional($c->created_at)->toISOString(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data'    => $mapped,
                'meta'    => [
                    'current_page' => $comments->currentPage(),
                    'per_page'     => $comments->perPage(),
                    'total'        => $comments->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['detail' => 'Failed to fetch reel comments: ' . $e->getMessage()], 500);
        }
    }
}
