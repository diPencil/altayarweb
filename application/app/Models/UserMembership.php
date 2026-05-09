<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    protected $fillable = [
        'user_id',
        'membership_plan_id',
        'start_date',
        'end_date',
        'status',
        'member_code',
        'payment_summary',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_summary' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function memberCode(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getRawOriginal('member_code') ?: 'ALT' . str_pad($this->user_id, 6, '0', STR_PAD_LEFT),
        );
    }
}
