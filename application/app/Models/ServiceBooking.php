<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBooking extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            if (empty($booking->reference_no)) {
                do {
                    $booking->reference_no = 'SB-' . getTrx(10);
                } while (self::where('reference_no', $booking->reference_no)->exists());
            }
        });
    }

    protected $fillable = [
        'user_id',
        'created_by_admin_id',
        'booking_type',
        'title',
        'reference_no',
        'booking_date',
        'service_date',
        'service_time',
        'service_end_date',
        'amount',
        'currency',
        'status',
        'notes',
        'legacy_booking_id',
        'legacy_order_id',
        'legacy_order_item_id',
        'legacy_booking_obj_id',
        'paid_amount',
        'qty',
        'guests',
        'old_payment_status',
        'old_order_status',
        'legacy_import',
        'legacy_source',
        'raw_total_amount',
        'legacy_benefit_value',
        'review_flags',
        'source_excel_row',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'service_date' => 'date',
        'service_end_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'legacy_benefit_value' => 'decimal:2',
        'legacy_import' => 'boolean',
        'source_excel_row' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function scopeLatestBookings($query)
    {
        return $query->latest();
    }

    public function statusBadge(): string
    {
        return match ((int) $this->status) {
            1 => '<span class="badge badge--success">' . trans('Confirmed') . '</span>',
            2 => '<span class="badge badge--primary">' . trans('Completed') . '</span>',
            3 => '<span class="badge badge--danger">' . trans('Canceled') . '</span>',
            default => '<span class="badge badge--warning">' . trans('Pending') . '</span>',
        };
    }
}