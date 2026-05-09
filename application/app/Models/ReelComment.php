<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reel_id',
        'user_id',
        'comment',
        'admin_reply',
        'replied_by',
        'replied_at',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adminReplier()
    {
        return $this->belongsTo(Admin::class, 'replied_by');
    }
}
