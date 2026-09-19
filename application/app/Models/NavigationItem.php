<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    public const KIND_LINK = 'link';
    public const KIND_GROUP = 'group';
    public const KIND_LISTING_TYPES = 'listing_types';

    protected $guarded = ['id'];

    protected $casts = [
        'target_blank' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getLocalizedNameAttribute(): string
    {
        return is_rtl() && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            self::KIND_GROUP => __('Dropdown'),
            self::KIND_LISTING_TYPES => __('Dynamic Listing Types'),
            default => __('Link'),
        };
    }

    public function statusBadge(): string
    {
        return $this->status
            ? '<span class="badge badge--success">' . __('Active') . '</span>'
            : '<span class="badge badge--danger">' . __('Disabled') . '</span>';
    }
}
