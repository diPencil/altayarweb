<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'saves_count' => 'integer',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    public function interactions()
    {
        return $this->hasMany(ReelInteraction::class);
    }

    public function comments()
    {
        return $this->hasMany(ReelComment::class);
    }

    public function approvedComments()
    {
        return $this->comments()->where('status', 1);
    }

    public function likes()
    {
        return $this->interactions()->where('type', 'like');
    }

    public function saves()
    {
        return $this->interactions()->where('type', 'save');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function titleDisplay(): Attribute
    {
        return new Attribute(
            get: fn () => is_rtl() && $this->title_ar ? $this->title_ar : $this->title,
        );
    }

    public function descriptionDisplay(): Attribute
    {
        return new Attribute(
            get: fn () => is_rtl() && $this->description_ar ? $this->description_ar : ($this->description ?? ''),
        );
    }

    public function sourceNameDisplay(): Attribute
    {
        return new Attribute(
            get: fn () => is_rtl() && $this->source_name_ar ? $this->source_name_ar : ($this->source_name ?? ''),
        );
    }

    public function videoUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->video_path ? asset(getFilePath('reelVideo') . '/' . $this->video_path) : null,
        );
    }

    public function thumbnailUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->thumbnail_path ? asset(getFilePath('reelThumbnail') . '/' . $this->thumbnail_path) : null,
        );
    }

    public function relatedUrl(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->tourPackage) {
                    return route('tour.package.details', [slug($this->tourPackage->title), $this->tourPackage->id]);
                }

                return $this->link_url;
            },
        );
    }

    public function statusBadge($status = null)
    {
        return $this->status
            ? '<span class="badge badge--success">' . trans('Active') . '</span>'
            : '<span class="badge badge--danger">' . trans('Inactive') . '</span>';
    }
}
