<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'user_id',
        'session_key',
        'name',
        'email',
        'locale',
        'chat_type',
        'status',
        'ai_enabled',
        'human_requested_at',
        'assigned_admin_id',
        'last_message_at',
        'unread_admin_count',
        'unread_user_count',
        'metadata',
    ];

    protected $casts = [
        'ai_enabled' => 'boolean',
        'human_requested_at' => 'datetime',
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latest('id');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'human_requested']);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
