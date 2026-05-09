<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
    public function employee()
    {
    	return $this->belongsTo(Employee::class, 'agent_id');
    }
}

