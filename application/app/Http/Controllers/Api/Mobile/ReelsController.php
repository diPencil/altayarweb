<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\ReelInteraction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReelsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$skip, $limit, $currentPage, $perPage] = $this->paginationFromRequest($request);
        $userId = $request->user()?->id;

        $query = $this->baseQuery()
            ->skip($skip)
            ->take($limit);

        $reels = $query->get();
        $reelIds = $reels->pluck('id')->all();
        $interactionMap = $this->interactionMap($userId, $reelIds);

        $normalized = $reels->map(function (Reel $reel) use ($interactionMap, $userId) {
            return $this->normalizeReel($reel, $userId, $interactionMap[$reel->id] ?? []);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'reels' => $normalized,
                'total' => $this->baseQuery()->count(),
            ],
            'meta' => [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $this->baseQuery()->count(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $userId = $request->user()?->id;
        $interactionData = $this->interactionMap($userId, [$reel->id]);

        return response()->json([
            'success' => true,
            'data' => $this->normalizeReel($reel, $userId, $interactionData[$reel->id] ?? []),
        ]);
    }

    public function view(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $reel->increment('views_count');

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $this->interactionPayload($reel, $request->user()?->id),
        ]);
    }

    public function like(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $user = $request->user();

        if (! $user) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        $this->toggleInteraction($reel, $user->id, 'like');

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $this->interactionPayload($reel, $user->id),
        ]);
    }

    public function save(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $user = $request->user();

        if (! $user) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        $existing = ReelInteraction::where('user_id', $user->id)
            ->where('reel_id', $reel->id)
            ->where('type', 'save')
            ->first();

        if (! $existing) {
            ReelInteraction::create([
                'user_id' => $user->id,
                'reel_id' => $reel->id,
                'type' => 'save',
            ]);

            $reel->increment('saves_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $this->interactionPayload($reel, $user->id),
        ]);
    }

    public function unsave(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $user = $request->user();

        if (! $user) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        $interaction = ReelInteraction::where('user_id', $user->id)
            ->where('reel_id', $reel->id)
            ->where('type', 'save')
            ->first();

        if ($interaction) {
            $interaction->delete();
            if ((int) ($reel->saves_count ?? 0) > 0) {
                $reel->decrement('saves_count');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $this->interactionPayload($reel, $user->id),
        ]);
    }

    public function comments(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $perPage = max(1, min((int) $request->integer('limit', 50), 100));
        $skip = max(0, (int) $request->integer('skip', 0));

        $comments = ReelComment::with('user')
            ->where('reel_id', $reel->id)
            ->where('status', 1)
            ->orderBy('id')
            ->skip($skip)
            ->take($perPage)
            ->get()
            ->map(fn (ReelComment $comment) => $this->normalizeComment($comment))
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'comments' => $comments,
            ],
        ]);
    }

    public function storeComment(Request $request, int $id): JsonResponse
    {
        $reel = $this->baseQuery()->whereKey($id)->firstOrFail();
        $user = $request->user();

        if (! $user) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        ReelComment::create([
            'reel_id' => $reel->id,
            'user_id' => $user->id,
            'comment' => $validated['comment'],
            'status' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $this->interactionPayload($reel, $user->id),
        ]);
    }

    public function favorites(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min((int) $request->integer('limit', 20), 100));
        $skip = max(0, (int) $request->integer('skip', 0));

        $savedIds = ReelInteraction::where('user_id', $user->id)
            ->where('type', 'save')
            ->orderByDesc('id')
            ->pluck('reel_id')
            ->unique()
            ->values();

        $query = $this->baseQuery()->whereIn('id', $savedIds->all());
        $total = $query->count();
        $reels = $query->orderByDesc('id')->skip($skip)->take($perPage)->get();
        $interactionMap = $this->interactionMap($user->id, $reels->pluck('id')->all());

        return response()->json([
            'success' => true,
            'data' => [
                'reels' => $reels->map(fn (Reel $reel) => $this->normalizeReel($reel, $user->id, $interactionMap[$reel->id] ?? []))->values(),
                'total' => $total,
            ],
            'meta' => [
                'current_page' => (int) floor($skip / $perPage) + 1,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    private function baseQuery(): Builder
    {
        return Reel::query()
            ->with('uploader')
            ->withCount([
                'comments as comments_count' => fn (Builder $query) => $query->where('status', 1),
            ])
            ->active()
            ->ordered();
    }

    private function normalizeReel(Reel $reel, ?int $userId = null, array $interactionTypes = []): array
    {
        $title = (string) ($reel->title_display ?? $reel->title ?? '');
        $description = (string) ($reel->description_display ?? $reel->description ?? '');
        $sourceName = (string) ($reel->source_name_display ?? $reel->source_name ?? 'AltayarVIP');

        return [
            'id' => $reel->id,
            'title' => $title,
            'description' => $description,
            'video_url' => $reel->video_url,
            'thumbnail_url' => $reel->thumbnail_url,
            'video_type' => $this->videoTypeFor($reel),
            'status' => 'ACTIVE',
            'views_count' => (int) ($reel->views_count ?? 0),
            'likes_count' => (int) ($reel->likes_count ?? 0),
            'comments_count' => (int) ($reel->comments_count ?? 0),
            'shares_count' => 0,
            'is_liked' => in_array('like', $interactionTypes, true),
            'is_saved' => in_array('save', $interactionTypes, true),
            'user' => [
                'id' => null,
                'name' => $sourceName !== '' ? $sourceName : 'AltayarVIP',
                'avatar_url' => null,
            ],
            'created_at' => optional($reel->created_at)->toISOString(),
            'updated_at' => optional($reel->updated_at)->toISOString(),
        ];
    }

    private function normalizeComment(ReelComment $comment): array
    {
        $user = $comment->user;

        return [
            'id' => $comment->id,
            'reel_id' => $comment->reel_id,
            'parent_id' => null,
            'user_id' => $comment->user_id,
            'user_name' => $user ? trim((string) (data_get($user, 'fullname') ?: data_get($user, 'username') ?: data_get($user, 'name') ?: 'demo member')) : 'demo member',
            'user_avatar' => $user && data_get($user, 'image') ? asset(getFilePath('userProfile') . '/' . data_get($user, 'image')) : null,
            'content' => (string) $comment->comment,
            'likes_count' => 0,
            'created_at' => optional($comment->created_at)->toISOString(),
            'replies' => [],
        ];
    }

    private function interactionPayload(Reel $reel, ?int $userId = null): array
    {
        $interactionTypes = $this->interactionMap($userId, [$reel->id])[$reel->id] ?? [];

        return [
            'views_count' => (int) ($reel->fresh()->views_count ?? 0),
            'likes_count' => (int) ($reel->fresh()->likes_count ?? 0),
            'comments_count' => (int) ($reel->fresh()->comments()->where('status', 1)->count()),
            'shares_count' => 0,
            'is_liked' => in_array('like', $interactionTypes, true),
            'is_saved' => in_array('save', $interactionTypes, true),
        ];
    }

    private function interactionMap(?int $userId, array $reelIds): array
    {
        if (! $userId || empty($reelIds)) {
            return [];
        }

        return ReelInteraction::where('user_id', $userId)
            ->whereIn('reel_id', $reelIds)
            ->get()
            ->groupBy('reel_id')
            ->map(function ($items) {
                return $items->pluck('type')->values()->all();
            })
            ->all();
    }

    private function toggleInteraction(Reel $reel, int $userId, string $type): void
    {
        $interaction = ReelInteraction::where('user_id', $userId)
            ->where('reel_id', $reel->id)
            ->where('type', $type)
            ->first();

        if ($interaction) {
            $interaction->delete();
            $counterColumn = $type === 'like' ? 'likes_count' : 'saves_count';
            if ((int) ($reel->{$counterColumn} ?? 0) > 0) {
                $reel->decrement($counterColumn);
            }
            return;
        }

        ReelInteraction::create([
            'user_id' => $userId,
            'reel_id' => $reel->id,
            'type' => $type,
        ]);

        $counterColumn = $type === 'like' ? 'likes_count' : 'saves_count';
        $reel->increment($counterColumn);
    }

    private function videoTypeFor(Reel $reel): string
    {
        $videoUrl = (string) ($reel->video_url ?? '');
        $linkUrl = trim((string) ($reel->link_url ?? ''));

        if ($videoUrl !== '') {
            return 'UPLOAD';
        }

        if ($linkUrl !== '' && preg_match('/(?:youtube\.com|youtu\.be)/i', $linkUrl)) {
            return 'YOUTUBE';
        }

        return $linkUrl !== '' ? 'URL' : 'UPLOAD';
    }

    private function paginationFromRequest(Request $request): array
    {
        $limit = max(1, min((int) $request->integer('limit', $request->integer('per_page', 20)), 100));
        $skip = max(0, (int) $request->integer('skip', 0));
        $page = max(1, (int) $request->integer('page', $skip > 0 ? (int) floor($skip / $limit) + 1 : 1));

        if ($request->filled('page') && ! $request->filled('skip')) {
            $skip = ($page - 1) * $limit;
        }

        $perPage = $request->filled('per_page') ? max(1, min((int) $request->integer('per_page'), 100)) : $limit;

        return [$skip, $limit, $page, $perPage];
    }
}