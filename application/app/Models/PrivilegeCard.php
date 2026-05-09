<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PrivilegeCard extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'subtitle',
        'subtitle_ar',
        'description',
        'description_ar',
        'price',
        'original_price',
        'benefits',
        'features',
        'image_file',
        'pdf_file',
        'sort_order',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'benefits' => 'array',
        'features' => 'array',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_file ? asset(getFilePath('privilegeCardImage') . '/' . $this->image_file) : null,
        );
    }

    public function pdfUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->pdf_file ? asset(getFilePath('privilegeCardPdf') . '/' . $this->pdf_file) : null,
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
