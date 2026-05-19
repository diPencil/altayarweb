<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'agents';

    
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
        'dashboard_permissions' => 'array',
        'menu_permissions' => 'array',
        'page_permissions' => 'array',
        'user_permissions' => 'array',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class, 'agent_id');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'agent_id');
    }

    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'agent_id');
    }



    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'agent_id')->orderBy('id','desc');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'agent_id')->where('status','!=',0);
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
        if ((int) $this->status === 1) {
            return __('This employee is active and cannot be deleted. Please ban/deactivate first.');
        }

        return null;
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

}
