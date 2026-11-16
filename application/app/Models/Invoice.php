<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public const CURRENCIES = [
        'USD' => '$',
        'SAR' => 'SAR',
        'EGP' => 'EGP',
        'EUR' => '€',
    ];

    protected $guarded = ['id'];

    public function getCurrencySymbolAttribute(): string
    {
        $currency = strtoupper((string) ($this->currency ?: 'USD'));

        return self::CURRENCIES[$currency] ?? $currency;
    }

    public function getCurrencyPrefixAttribute(): string
    {
        return in_array(strtoupper((string) ($this->currency ?: 'USD')), ['USD', 'EUR'], true)
            ? $this->currency_symbol
            : $this->currency_symbol . ' ';
    }

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
