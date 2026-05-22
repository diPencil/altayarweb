<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];
}
