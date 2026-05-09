<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipPlanHistory extends Model
{
    protected $fillable = [
        'user_id',
        'previous_membership_id',
        'previous_plan_id',
        'new_membership_id',
        'new_plan_id',
        'change_type',
        'previous_price',
        'new_price',
        'price_difference',
        'start_date',
        'end_date',
        'created_by_admin_id',
        'created_by_user_id',
        'note',
    ];

    protected $casts = [
        'previous_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'price_difference' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousMembership(): BelongsTo
    {
        return $this->belongsTo(UserMembership::class, 'previous_membership_id');
    }

    public function newMembership(): BelongsTo
    {
        return $this->belongsTo(UserMembership::class, 'new_membership_id');
    }

    public function previousPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'previous_plan_id');
    }

    public function newPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'new_plan_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public static function recordChange(User $user, ?UserMembership $previousMembership, ?UserMembership $newMembership, ?MembershipPlan $newPlan, array $context = []): self
    {
        $previousPlan = $previousMembership?->plan;
        $previousPrice = (float) ($previousPlan?->price ?? 0);
        $newPrice = (float) ($newPlan?->price ?? 0);

        if (! $newMembership) {
            $changeType = 'removal';
        } elseif (! $previousMembership) {
            $changeType = 'subscribe';
        } elseif ($newPrice > $previousPrice) {
            $changeType = 'upgrade';
        } elseif ($newPrice < $previousPrice) {
            $changeType = 'downgrade';
        } else {
            $changeType = 'renewal';
        }

        return static::create([
            'user_id' => $user->id,
            'previous_membership_id' => $previousMembership?->id,
            'previous_plan_id' => $previousPlan?->id,
            'new_membership_id' => $newMembership?->id,
            'new_plan_id' => $newPlan?->id,
            'change_type' => $changeType,
            'previous_price' => $previousPrice,
            'new_price' => $newPrice,
            'price_difference' => $newPrice - $previousPrice,
            'start_date' => $newMembership?->start_date,
            'end_date' => $newMembership?->end_date,
            'created_by_admin_id' => $context['created_by_admin_id'] ?? null,
            'created_by_user_id' => $context['created_by_user_id'] ?? null,
            'note' => $context['note'] ?? null,
        ]);
    }
}
