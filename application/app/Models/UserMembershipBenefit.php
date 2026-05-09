<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembershipBenefit extends Model
{
    protected $fillable = [
        'user_id',
        'membership_plan_id',
        'benefit_type',
        'title',
        'description',
        'total_quantity',
        'used_quantity',
        'unit',
        'starts_at',
        'expires_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function getRemainingQuantityAttribute(): int
    {
        $remaining = (int) $this->total_quantity - (int) $this->used_quantity;
        return $remaining > 0 ? $remaining : 0;
    }
}
