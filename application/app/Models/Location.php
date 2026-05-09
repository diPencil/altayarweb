<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Location extends Model
{
    use HasFactory;

    public function displayName(): string
    {
        $rawName = (string) $this->getRawOriginal('name');

        if (is_rtl() && filled($this->name_ar)) {
            return (string) $this->name_ar;
        }

        return $rawName !== '' ? $rawName : (string) $this->name;
    }

    public function displayLocation(): string
    {
        $rawLocation = (string) $this->getRawOriginal('location');

        if (is_rtl() && filled($this->location_ar)) {
            return (string) $this->location_ar;
        }

        return $rawLocation !== '' ? $rawLocation : (string) $this->location;
    }

    public function displayCountLabel(): string
    {
        $count = trim((string) $this->count);

        if ($count === '') {
            return '';
        }

        $normalizedCount = trim(preg_replace('/\s*(?:Destinations|وجهات|وجهة)\s*$/u', '', $count));

        if ($normalizedCount === '') {
            return '';
        }

        return $normalizedCount . ' ' . (is_rtl() ? 'وجهة' : 'Destinations');
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->name_ar) ? $this->name_ar : $value,
        );
    }

    public function location(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->location_ar) ? $this->location_ar : $value,
        );
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function statusBadge($status){
        $html = '';
        if($this->status == 1){
            $html = '<span class="badge badge--success">'.trans('Active').'</span>';
        }else{
            $html = '<span class="badge badge--warning">'.trans('Inactive').'</span>';
        }

        return $html;
    }
}
