<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Carbon\Carbon|null $start_date
 * @property \Carbon\Carbon|null $end_date
 */
class Listing extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'listing_type_id',
        'type',
        'summary',
        'description',
        'city',
        'country',
        'address',
        'start_date',
        'end_date',
        'available_times',
        'facilities',
        'facilities_ar',
        'includes',
        'includes_ar',
        'excludes',
        'excludes_ar',
        'price',
        'currency',
        'discount',
        'offer_type',
        'offer_first_value',
        'offer_second_value',
        'offer_text',
        'image',
        'meta',
        'status',
        'user_id',
        'user_type',
    ];

    protected $casts = [
        'meta' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'available_times' => 'array',
        'facilities' => 'array',
        'facilities_ar' => 'array',
        'includes' => 'array',
        'includes_ar' => 'array',
        'excludes' => 'array',
        'excludes_ar' => 'array',
        'offer_first_value' => 'integer',
        'offer_second_value' => 'integer',
    ];

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->title_ar) ? $this->title_ar : $value,
        );
    }

    public function summary(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->summary_ar) ? $this->summary_ar : $value,
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (is_rtl() && $this->description_ar) ? $this->description_ar : $value,
        );
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->image) return null;
                if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                    return $this->image;
                }
                return asset(getFilePath('listingImage') . '/' . $this->image);
            },
        );
    }

    public function listingType(): BelongsTo
    {
        return $this->belongsTo(ListingType::class, 'listing_type_id');
    }

    public function originalPrice(): float
    {
        return (float) ($this->price ?? 0);
    }

    public function discountAmount(): float
    {
        return max((float) ($this->discount ?? 0), 0);
    }

    public function finalPrice(): float
    {
        $originalPrice = $this->originalPrice();
        $discountAmount = $this->discountAmount();

        return $discountAmount > 0 ? max($originalPrice - $discountAmount, 0) : $originalPrice;
    }

    public function discountPercent(): ?int
    {
        $originalPrice = $this->originalPrice();
        $discountAmount = $this->discountAmount();

        if ($originalPrice <= 0 || $discountAmount <= 0) {
            return null;
        }

        return (int) round(100 * ($discountAmount / $originalPrice));
    }

    public function durationDays(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function availableTimes(): array
    {
        return array_values(array_filter($this->available_times ?? []));
    }

    public function facilitiesList(): array
    {
        return $this->localizedList($this->facilities ?? [], $this->facilities_ar ?? []);
    }

    public function includesList(): array
    {
        return $this->localizedList($this->includes ?? [], $this->includes_ar ?? []);
    }

    public function excludesList(): array
    {
        return $this->localizedList($this->excludes ?? [], $this->excludes_ar ?? []);
    }

    public function offerSummary(): ?string
    {
        if (!$this->offer_type) {
            return null;
        }

        return match ($this->offer_type) {
            'stay_pay' => $this->offer_first_value && $this->offer_second_value
                ? __('Stay :first nights, pay :second', ['first' => $this->offer_first_value, 'second' => $this->offer_second_value])
                : null,
            'day_bundle' => $this->offer_first_value && $this->offer_second_value
                ? __(':first Days / :second Days', ['first' => $this->offer_first_value, 'second' => $this->offer_second_value])
                : null,
            'custom' => $this->offer_text ? __($this->offer_text) : null,
            default => null,
        };
    }

    public function hasOffer(): bool
    {
        return (bool) $this->offerSummary();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    private function localizedList(array $englishList, array $arabicList): array
    {
        $preferredList = is_rtl()
            ? (!empty($arabicList) ? $arabicList : $englishList)
            : (!empty($englishList) ? $englishList : $arabicList);

        return array_values(array_filter(is_array($preferredList) ? $preferredList : []));
    }
}
