<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeviceToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tokenable_id',
        'tokenable_type',
        'token',
        'device_type',
        'provider',
        'device_name',
        'app_version',
    ];

    /**
     * Get the owning tokenable model (User, Admin, or Employee).
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }
}
