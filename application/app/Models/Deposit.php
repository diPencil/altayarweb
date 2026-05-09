<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $casts = [
        'detail' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'agent_id');
    }
    public function agent()
    {
        return $this->employee();
    }
    public function tour_booking()
    {
        return $this->belongsTo(TourBooking::class, 'tour_booking_id','id');
    }
    public function service_booking()
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id','id');
    }
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->badgeData(),
        );
    }

    public function badgeData(){
        $html = '';
        if($this->status == 2){
            $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
        }
        elseif($this->status == 1 && $this->method_code >= 1000){
            $html = '<span class="badge badge--success">'.trans('Approved').'</span>';
        }
        elseif($this->status == 1 && $this->method_code < 1000){
            $html = '<span class="badge badge--success">'.trans('Succeed').'</span>';
        }
        elseif($this->status == 3){
            $html = '<span class="badge badge--danger">'.trans('Rejected').'</span>';
        }else{
            $html = '<span class="badge badge--dark">'.trans('Initiated').'</span>';
        }
        return $html;
    }

    // scope
    public function scopeGatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function scopeBaseCurrency()
    {
        return $this->gateway->crypto == 1 ? 'USD' : $this->method_currency;
    }

    public function scopePending()
    {
        return $this->where('status', 2);
    }

    public function scopeRejected()
    {
        return $this->where('status', 3);
    }

    public function scopeApproved()
    {
        return $this->where('method_code','>=',1000)->where('status', 1);
    }

    public function scopeSuccessful()
    {
        return $this->where('status', 1);
    }

    public function scopeInitiated()
    {
        return $this->where('status', 0);
    }
}
