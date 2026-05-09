<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $casts = [
        'features' => 'object',
        'destination_features_ar' => 'object',
        'icons' => 'object',
        'highlights' => 'object',
        'destination_highlights_ar' => 'object',
        'includes' => 'object',
        'includes_ar' => 'object',
        'excludes' => 'object',
        'excludes_ar' => 'object',
        'itinerary_days' => 'object',
        'destination_overview' => 'object',
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
    ];

    public function displayTitle(): string
    {
        return is_rtl() && filled($this->title_ar) ? $this->title_ar : (string) $this->title;
    }

    public function displayDescription(): string
    {
        return is_rtl() && filled($this->description_ar) ? $this->description_ar : (string) $this->description;
    }

    public function displayAddress(): string
    {
        return is_rtl() && filled($this->address_ar) ? $this->address_ar : (string) $this->address;
    }

    public function displayDepartureFrom(): string
    {
        return $this->displayLocalizedOverviewValue('departure_from_ar', ['departure_form', 'departure_from']);
    }

    public function displayArrival(): string
    {
        return $this->displayLocalizedOverviewValue('arrival_ar', ['arrival']);
    }

    public function displayTransportation(): string
    {
        return $this->displayLocalizedOverviewValue('transportation_ar', ['transportation']);
    }

    public function displayAccommodation(): string
    {
        return $this->displayLocalizedOverviewValue('accommodation_ar', ['accommodation']);
    }

    public function displayCitiesCovered(): string
    {
        return is_rtl() && filled($this->cities_covered_ar) ? (string) $this->cities_covered_ar : (string) $this->cities_covered;
    }

    public function displayPackageLabel(): string
    {
        return is_rtl() && filled($this->package_label_ar) ? (string) $this->package_label_ar : (string) $this->package_label;
    }

    public function displayTourType(): string
    {
        if (is_rtl() && filled($this->tour_type_ar)) {
            return (string) $this->tour_type_ar;
        }

        return filled($this->tour_type) ? (string) $this->tour_type : (string) ($this->category?->name ?? '');
    }

    public function displayCurrencyCode(): string
    {
        return strtoupper((string) ($this->currency ?: gs()->cur_text ?: 'USD'));
    }

    public function displayCurrencySymbol(): string
    {
        return match ($this->displayCurrencyCode()) {
            'EGP' => 'E£',
            'SAR' => 'SR',
            'EUR' => '€',
            default => '$',
        };
    }

    public function priceRangeText(): ?string
    {
        $from = $this->price_from;
        $to = $this->price_to;

        if (blank($from) && blank($to)) {
            return blank($this->price_note) ? null : (string) $this->price_note;
        }

        if (!blank($from) && !blank($to)) {
            return $this->displayCurrencySymbol() . showAmount($from) . ' - ' . $this->displayCurrencySymbol() . showAmount($to);
        }

        if (!blank($from)) {
            return __('From') . ' ' . $this->displayCurrencySymbol() . showAmount($from);
        }

        return __('Up to') . ' ' . $this->displayCurrencySymbol() . showAmount($to);
    }

    public function localizedHighlights(): array
    {
        return $this->localizedLines($this->highlights ?? [], $this->destination_highlights_ar ?? []);
    }

    public function localizedFeatures(): array
    {
        return $this->localizedRows($this->features ?? [], $this->destination_features_ar ?? []);
    }

    public function localizedIncludes(): array
    {
        return $this->localizedLines($this->includes ?? [], $this->includes_ar ?? []);
    }

    public function localizedExcludes(): array
    {
        return $this->localizedLines($this->excludes ?? [], $this->excludes_ar ?? []);
    }

    public function localizedItineraryDays(): array
    {
        $days = $this->itinerary_days ?? [];

        return collect($days)->map(function ($item) {
            $item = (object) $item;

            return [
                'day_number' => $item->day_number ?? null,
                'title' => is_rtl() && filled($item->title_ar ?? null) ? $item->title_ar : ($item->title ?? null),
                'description' => is_rtl() && filled($item->description_ar ?? null) ? $item->description_ar : ($item->description ?? null),
                'image' => $item->image ?? null,
            ];
        })->filter(function ($item) {
            return filled($item['day_number']) || filled($item['title']) || filled($item['description']) || filled($item['image']);
        })->values()->all();
    }

    public function itineraryImageUrl(?string $image): ?string
    {
        $image = trim((string) $image);

        if ($image === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $image)) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return asset(ltrim($image, '/'));
        }

        if (str_starts_with($image, 'assets/')) {
            return asset($image);
        }

        return getImage(getFilePath('tourPackageImage') . '/' . $image);
    }

    private function localizedLines($primary, $secondary): array
    {
        $items = is_rtl() && filled($secondary) ? $secondary : $primary;

        return collect($this->toPlainList($items))->values()->all();
    }

    private function localizedRows($primary, $secondary): array
    {
        $items = is_rtl() && filled($secondary) ? $secondary : $primary;

        return collect($this->toPlainList($items))->map(function ($item) {
            $item = is_array($item) ? (object) $item : $item;

            return (object) [
                'icon' => $item->icon ?? null,
                'feature' => is_rtl() && filled($item->feature_ar ?? null) ? $item->feature_ar : ($item->feature ?? null),
            ];
        })->values()->all();
    }

    private function displayLocalizedOverviewValue(string $arabicField, array $fallbackKeys): string
    {
        if (is_rtl() && filled($this->{$arabicField} ?? null)) {
            return (string) $this->{$arabicField};
        }

        foreach ($fallbackKeys as $fallbackKey) {
            $fallbackValue = data_get($this->destination_overview, $fallbackKey);

            if (filled($fallbackValue)) {
                return (string) $fallbackValue;
            }
        }

        return '';
    }

    private function toPlainList($items): array
    {
        if (blank($items)) {
            return [];
        }

        if ($items instanceof \Illuminate\Support\Collection) {
            $items = $items->all();
        }

        if ($items instanceof \stdClass) {
            $items = (array) $items;
        }

        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(function ($item) {
                if ($item instanceof \stdClass) {
                    return (array) $item;
                }

                return $item;
            })
            ->filter(function ($item) {
                if (is_array($item)) {
                    return collect($item)->filter(fn ($value) => filled($value))->isNotEmpty();
                }

                return filled($item);
            })
            ->values()
            ->all();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }

    public function agent()
    {
        return $this->employee();
    }

    public function admin()
    {
        return $this->belongsTo(admin::class, 'user_id', 'id');
    }

    public function TourPackagePrimaryImage()
    {
        return $this->hasOne(TourPackageImage::class, 'tour_package_id', 'id')->orderBy('id', 'asc');
    }

    public function tour_bookings()
    {
        return $this->hasMany(TourBooking::class, 'tour_package_id', 'id')->orderBy('id', 'asc');
    }

    public function tour_package_images()
    {
        return $this->hasMany(TourPackageImage::class, 'tour_package_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }



    public function scopeActive($query)
    {
        return $query->where('status',1);
    }

    public function scopeAllTour($query)
    {
        return $query;
    }

    public function scopePending()
    {
        return $this->where('status', 0);
    }

    public function scopeRunning()
    {
        return $this->where('status', 2);
    }
    
    public function scopeExpired()
    {
        return $this->where('status', 3);
    }

    public function scopeCancelled()
    {
        return $this->where('status', 4);
    }

    public function scopeAdminAll($query)
    {
        return $query->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminAgentAll($query)
    {
        return $query->where('user_type', 'agent');
    }

    public function scopeAdminApproved()
    {
        return $this->where('status', 1)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminPending()
    {
        return $this->where('status', 0)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminCanceled()
    {
        return $this->where('status', 2)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminEmployeeAll()
    {
        return $this->where('user_type', 'employee');
    }


    public function scopeEmployeeAll()
    {
        return $this->where('user_type', 'employee')->where('user_id', auth('employee')->id());
    }


    public function scopeEmployeeApproved()
    {
        return $this->where('status', 1)->where('user_type', 'employee')->where('user_id', auth('employee')->id());
    }

    public function scopeEmployeePending()
    {
        return $this->where('status', 0)->where('user_type', 'employee')->where('user_id', auth('employee')->id());
    }

    public function statusBadge($status)
    {
        $html = '';
        if ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } elseif ($this->status == 2) {
            $html = '<span class="badge badge--success">' . trans('Running') . '</span>';
        } elseif ($this->status == 3) {
            $html = '<span class="badge badge--danger">' . trans('Expired') . '</span>';
        } elseif ($this->status == 4) {
            $html = '<span class="badge badge--danger">' . trans('Cancelled') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        }
        return $html;
    }


    public function statusTourPositionBadge($status)
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('House Full') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }

        return $html;
    }

    public function tourPositionBadge()
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }
        return $html;
    }

    public function adminTourPositionBadge()
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }
        return $html;
    }
}
