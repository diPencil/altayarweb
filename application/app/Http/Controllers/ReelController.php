<?php

namespace App\Http\Controllers;

use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\ReelInteraction;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReelController extends Controller
{
    public function index()
    {
        $pageTitle = 'Reels';
        $reels = Reel::active()->with(['tourPackage', 'approvedComments.user'])->ordered()->get();
        $interactions = auth()->check()
            ? ReelInteraction::where('user_id', auth()->id())->get()->groupBy('reel_id')
            : collect();

        return view($this->activeTemplate . 'reels.index', compact('pageTitle', 'reels', 'interactions'));
    }

    public function trackView(Request $request, Reel $reel)
    {
        $reel->increment('views_count');

        return response()->json([
            'status' => 'success',
            'views_count' => $reel->fresh()->views_count,
        ]);
    }

    public function toggleLike(Reel $reel)
    {
        return $this->toggleInteraction($reel, 'like');
    }

    public function toggleSave(Reel $reel)
    {
        return $this->toggleInteraction($reel, 'save');
    }

    public function storeComment(Request $request, Reel $reel)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'login_required',
                'message' => 'Please log in to comment',
                'redirect' => route('user.login'),
            ], 401);
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        ReelComment::create([
            'reel_id' => $reel->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'status' => 1,
        ]);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = auth()->user()->username . ' commented on a reel';
        $adminNotification->click_url = urlPath('admin.reels.comments'); // Direct to admin comments manage page
        $adminNotification->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
        ]);
    }

    public function library()
    {
        $pageTitle = 'Saved Reels';
        $items = ReelInteraction::with(['reel.tourPackage'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->groupBy('type');

        $comments = ReelComment::with(['reel.tourPackage', 'adminReplier'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $likedReels = $items->get('like', collect())->pluck('reel')->filter();
        $savedReels = $items->get('save', collect())->pluck('reel')->filter();

        return view($this->activeTemplate . 'user.reels', compact('pageTitle', 'likedReels', 'savedReels', 'comments'));
    }

    protected function toggleInteraction(Reel $reel, string $type)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'login_required',
                'message' => 'Please log in to continue',
                'redirect' => route('user.login'),
            ], 401);
        }

        $userId = auth()->id();
        $interaction = ReelInteraction::where('user_id', $userId)
            ->where('reel_id', $reel->id)
            ->where('type', $type)
            ->first();

        DB::beginTransaction();
        try {
            if ($interaction) {
                $interaction->delete();
                $counterColumn = $type === 'like' ? 'likes_count' : 'saves_count';
                if ($reel->{$counterColumn} > 0) {
                    $reel->decrement($counterColumn);
                }
                $active = false;
                $message = $type === 'like' ? 'Like removed' : 'Removed from saved reels';
            } else {
                ReelInteraction::create([
                    'user_id' => $userId,
                    'reel_id' => $reel->id,
                    'type' => $type,
                ]);

                $counterColumn = $type === 'like' ? 'likes_count' : 'saves_count';
                $reel->increment($counterColumn);
                $active = true;
                $message = $type === 'like' ? 'Liked successfully' : 'Saved successfully';

                // Add Admin Notification for Likes
                if ($type === 'like') {
                    $adminNotification = new AdminNotification();
                    $adminNotification->user_id = $userId;
                    $adminNotification->title = auth()->user()->username . ' liked a reel';
                    $adminNotification->click_url = urlPath('admin.reels.index');
                    $adminNotification->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'active' => $active,
                'message' => $message,
                'likes_count' => $reel->fresh()->likes_count,
                'saves_count' => $reel->fresh()->saves_count,
            ]);
        } catch (\Throwable $exp) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
