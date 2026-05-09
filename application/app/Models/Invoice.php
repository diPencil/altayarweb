<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 0) {
            return '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        } elseif ($this->status == 1) {
            return '<span class="badge badge--success">' . trans('Paid') . '</span>';
        } elseif ($this->status == 2) {
            return '<span class="badge badge--info">' . trans('Partially Paid') . '</span>';
        } elseif ($this->status == 3) {
            return '<span class="badge badge--danger">' . trans('Cancelled') . '</span>';
        }
    }
}
