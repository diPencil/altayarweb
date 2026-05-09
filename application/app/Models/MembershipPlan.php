<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'price',
        'duration_days',
        'benefits',
        'benefits_ar',
        'bonus_points',
        'image_file',
        'cover_image',
        'pdf_file',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'benefits' => 'array',
        'benefits_ar' => 'array',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    public function userMembershipBenefits(): HasMany
    {
        return $this->hasMany(\App\Models\UserMembershipBenefit::class, 'membership_plan_id');
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(MembershipPointTransaction::class);
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_file ? asset(getFilePath('membershipPlanPdf') . '/' . $this->pdf_file) : null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_file ? asset(getFilePath('membershipPlanImage') . '/' . $this->image_file) : null;
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image ? asset(getFilePath('membershipPlanCover') . '/' . $this->cover_image) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
