<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reel_id',
        'type',
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
