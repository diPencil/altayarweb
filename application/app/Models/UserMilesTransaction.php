<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserMilesTransaction extends Model
{
    protected $table = 'user_miles_transactions';

    protected $fillable = [
        'user_id',
        'trx',
        'type',
        'direction',
        'miles',
        'balance_after',
        'source',
        'reference_type',
        'reference_id',
        'remark',
        'admin_id',
        'agent_id',
        'is_reversal',
        'reversal_of_id',
        'is_reversed',
        'reversed_at',
        'idempotency_key',
        'meta',
    ];

    protected $casts = [
        'miles' => 'integer',
        'balance_after' => 'integer',
        'direction' => 'integer',
        'is_reversal' => 'boolean',
        'is_reversed' => 'boolean',
        'reversed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'agent_id');
    }

    public function agent(): BelongsTo
    {
        return $this->employee();
    }

    public function getActorNameAttribute(): string
    {
        if ($this->admin) {
            return $this->admin->username . ' (Admin)';
        }
        if ($this->employee) {
            return $this->employee->username . ' (Employee)';
        }
        return 'System';
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(self::class, 'reversal_of_id');
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeNonReversals($query)
    {
        return $query->where('is_reversal', false);
    }

    public function scopeReversals($query)
    {
        return $query->where('is_reversal', true);
    }
}
