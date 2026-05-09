<?php

namespace App\Http\Controllers;

use App\Models\PopupAd;
use App\Models\PopupAdEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PopupAdTrackController extends Controller
{
    public function track(Request $request, PopupAd $popupAd)
    {
        $data = $request->validate([
            'event' => 'required|string|in:impression,click,close',
            'visitor_key' => 'nullable|string|max:80',
            'url' => 'nullable|string|max:255',
        ]);

        if (!$popupAd->status) {
            return response()->json(['ok' => false]);
        }

        $viewer = PopupAd::viewer();
        $visitorKey = $data['visitor_key'] ?? null;
        $eventType = $data['event'];

        DB::transaction(function () use ($popupAd, $viewer, $visitorKey, $eventType, $request, $data) {
            $isUnique = false;
            if ($eventType === 'impression') {
                $isUnique = !PopupAdEvent::where('popup_ad_id', $popupAd->id)
                    ->where('event_type', 'impression')
                    ->where(function ($query) use ($viewer, $visitorKey) {
                        if ($viewer['id']) {
                            $query->where('viewer_type', $viewer['type'])->where('viewer_id', $viewer['id']);
                        } else {
                            $query->where('visitor_key', $visitorKey);
                        }
                    })->exists();
            }

            PopupAdEvent::create([
                'popup_ad_id' => $popupAd->id,
                'event_type' => $eventType,
                'viewer_type' => $viewer['type'],
                'viewer_id' => $viewer['id'],
                'visitor_key' => $visitorKey,
                'url' => $data['url'] ?? $request->headers->get('referer'),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            ]);

            if ($eventType === 'impression') {
                $popupAd->increment('impressions_count');
                if ($isUnique) {
                    $popupAd->increment('unique_impressions_count');
                }
            } elseif ($eventType === 'click') {
                $popupAd->increment('clicks_count');
            } elseif ($eventType === 'close') {
                $popupAd->increment('closes_count');
            }
        });

        return response()->json(['ok' => true]);
    }
}
