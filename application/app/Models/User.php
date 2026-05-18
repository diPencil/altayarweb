<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

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
        'ver_code_send_at' => 'datetime',
        'dashboard_permissions' => 'array',
        'menu_permissions' => 'array'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function tourBookings(): HasMany
    {
        return $this->hasMany(TourBooking::class)->orderBy('id', 'desc');
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

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class)->orderBy('id', 'desc');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderBy('id', 'desc');
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

    public function depositRecords(): HasMany
    {
        return $this->hasMany(Deposit::class)->orderBy('id', 'desc');
    }

    public function withdrawalRecords(): HasMany
    {
        return $this->hasMany(Withdrawal::class)->orderBy('id', 'desc');
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

    protected function countOrExists(string $countKey, string $relation): bool
    {
        if (array_key_exists($countKey, $this->getAttributes())) {
            return (int) $this->getAttribute($countKey) > 0;
        }

        return $this->{$relation}()->exists();
    }

    public function deleteBlockReason(): ?string
    {
        if ($this->hasActiveMembership() || $this->countOrExists('memberships_count', 'memberships')) {
            return __('Cannot delete user with membership. Please deactivate/ban instead.');
        }

        $hasRelatedRecords = $this->countOrExists('tour_bookings_count', 'tourBookings')
            || $this->countOrExists('service_bookings_count', 'serviceBookings')
            || $this->countOrExists('invoices_count', 'invoices')
            || $this->countOrExists('deposit_records_count', 'depositRecords')
            || $this->countOrExists('withdrawal_records_count', 'withdrawalRecords')
            || $this->countOrExists('transactions_count', 'transactions')
            || $this->countOrExists('support_tickets_count', 'supportTickets')
            || $this->countOrExists('membership_plan_histories_count', 'membershipPlanHistories')
            || $this->countOrExists('membership_point_transactions_count', 'membershipPointTransactions')
            || $this->countOrExists('membership_cashback_transactions_count', 'membershipCashbackTransactions')
            || $this->countOrExists('user_membership_benefits_count', 'userMembershipBenefits')
            || $this->countOrExists('login_logs_count', 'loginLogs');

        if ($hasRelatedRecords) {
            return __('This user has related records and cannot be deleted. Please ban/deactivate instead.');
        }

        return null;
    }

}
