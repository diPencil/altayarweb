<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            if (empty($booking->reference_no)) {
                do {
                    $booking->reference_no = 'TB-' . getTrx(10);
                } while (self::where('reference_no', $booking->reference_no)->exists());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'owner_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'owner_id', 'id');
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function tour_package()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function scopeAdminAll($query)
    {
        return $query->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }


    public function scopeUserAll($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeAdminApproved($query)
    {
        return $query->where('status', 1)->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }

    public function scopeAdminPending()
    {
        return $this->where('status', 2)
        ->where('owner_type','admin')
        ->where('owner_id', auth('admin')->id());
    }

    public function scopeAdminCanceled()
    {
        return $this->where('status', 3)->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }

    public function scopeEmployee()
    {
        return $this->where('owner_type','employee')->where('owner_id', auth('employee')->id());
    }

    public function scopeEmployeeApproved($query)
    {
        return $query->where('status', 1)->where('owner_type','employee')->where('owner_id', auth('employee')->id());
    }

    public function scopeEmployeePending()
    {
        return $this->where('status', 2)->where('owner_type','employee')->where('owner_id', auth('employee')->id());
    }

    public function scopeEmployeeCanceled()
    {
        return $this->where('status', 3)->where('owner_type','employee')->where('owner_id', auth('employee')->id());
    }

    public function scopeUserApproved($query)
    {
        return $query->where('status', 1)->where('user_id', auth()->id());
    }

    public function scopeUserPending()
    {
        return $this->where('status', 0)

        ->where('user_id', auth()->id());
    }

    public function scopeUserCanceled()
    {
        return $this->where('status', 2)->where('user_id', auth()->id());
    }


    public function statusBadge($status)
    {
        $html = '';
        if ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } elseif ($this->status == 0) {
            $html = '<span class="badge badge--warning">' . trans('Processing') . '</span>';
        } elseif ($this->status == 2) {
            $html = '<span class="badge badge--danger">' . trans('Canceled') . '</span>';
        } 
        return $html;
    }

    public function scopeStatusPaymentBadge()
    {
        $html = '';
        if ($this->status == 2) {
            $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        } elseif ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
        } elseif ($this->status == 3) {
            $html = '<span class="badge badge--danger">' . trans('Canceled') . '</span>';
        } 
        return $html;
    }

    
}
