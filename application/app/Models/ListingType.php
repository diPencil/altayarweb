<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ListingType extends Model
{
    protected $guarded = ['id'];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->name_ar) ? $this->name_ar : $value,
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function statusBadge($status)
    {
        $html = '';
        if ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } else {
            $html = '<span class="badge badge--danger">' . trans('Deactivate') . '</span>';
        }

        return $html;
    }
}
