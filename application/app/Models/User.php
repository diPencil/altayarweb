<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'agent_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function memberships()
    {
        return $this->hasMany(UserMembership::class)->orderBy('id', 'desc');
    }

    public function userMembershipBenefits()
    {
        return $this->hasMany(\App\Models\UserMembershipBenefit::class)->orderBy('id', 'desc');
    }

    public function currentMembership()
    {
        return $this->hasOne(UserMembership::class)->whereIn('status', [0, 1])->latestOfMany();
    }

    public function membershipPointTransactions()
    {
        return $this->hasMany(MembershipPointTransaction::class)->orderBy('id', 'desc');
    }

    public function membershipCashbackTransactions()
    {
        return $this->hasMany(MembershipCashbackTransaction::class)->orderBy('id', 'desc');
    }

    public function membershipPlanHistories()
    {
        return $this->hasMany(MembershipPlanHistory::class)->orderBy('id', 'desc');
    }

    public function serviceBookings()
    {
        return $this->hasMany(ServiceBooking::class)->orderBy('id', 'desc');
    }

    public function membershipPointsBalance(): Attribute
    {
        return new Attribute(
            get: fn () => (int) $this->membershipPointTransactions()->where('type', 'earned')->sum('points') - (int) $this->membershipPointTransactions()->where('type', 'used')->sum('points'),
        );
    }

    public function cashbackBalance(): Attribute
    {
        return new Attribute(
            get: fn () => (float) $this->membershipCashbackTransactions()->where('type', 'earned')->sum('amount') - (float) $this->membershipCashbackTransactions()->where('type', 'used')->sum('amount'),
        );
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname || $this->lastname ? $this->firstname . ' ' . $this->lastname : '@'.$this->username,
        );
    }


    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', 0);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', 0);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', 2);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }

    public function hasActiveMembership()
    {
        return $this->currentMembership && $this->currentMembership->status == 1;
    }

}
