<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'is_suggested',
        'metadata',
    ];

    protected $casts = [
        'is_suggested' => 'boolean',
        'metadata' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sender_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}