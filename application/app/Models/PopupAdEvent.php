<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupAdEvent extends Model
{
    protected $fillable = [
        'popup_ad_id',
        'event_type',
        'viewer_type',
        'viewer_id',
        'visitor_key',
        'url',
        'ip',
        'user_agent',
    ];

    public function popupAd(): BelongsTo
    {
        return $this->belongsTo(PopupAd::class);
    }
}
