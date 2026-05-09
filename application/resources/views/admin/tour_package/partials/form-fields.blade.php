@php
    $package = $tourPackage ?? null;
    $scalar = fn ($value, $default = '') => is_scalar($value) || is_null($value) ? ($value ?? $default) : $default;
    $field = fn ($key, $default = '') => $scalar(old($key, data_get($package, $key, $default)), $default);
    $destinationField = fn ($key, $default = '') => $scalar(old('destination_overview.' . $key, data_get($package, 'destination_overview.' . $key, $default)), $default);
    $plainList = function ($value) {
        return collect(is_object($value) ? (array) $value : (array) $value)
            ->map(function ($item) {
                if (is_object($item)) {
                    return data_get($item, 'value', data_get($item, 'title', data_get($item, 'feature', data_get($item, 'feature_ar', ''))));
                }

                return $item;
            })
            ->filter(fn ($item) => filled($item))
            ->values()
            ->all();
    };
    $itineraryDays = collect(old('itinerary_days', data_get($package, 'itinerary_days', [])));
    if ($itineraryDays->isEmpty()) {
        $itineraryDays = collect([(object) []]);
    }
    $resolveItineraryImage = function ($dayImage) use ($package) {
        if ($package && method_exists($package, 'itineraryImageUrl')) {
            return $package->itineraryImageUrl($dayImage);
        }

        $dayImage = trim((string) $dayImage);

        return $dayImage !== '' ? $dayImage : null;
    };
    $highlights = $plainList(old('highlights', data_get($package, 'highlights', [])));
    $highlightsAr = $plainList(old('highlights_ar', data_get($package, 'destination_highlights_ar', [])));
    $features = collect(old('features', data_get($package, 'features', [])))->map(function ($item) {
        $item = is_object($item) ? (array) $item : (array) $item;

        return (object) [
            'icon' => data_get($item, 'icon', ''),
            'feature' => data_get($item, 'feature', ''),
        ];
    })->values();
    $icons = $plainList(old('icons', data_get($package, 'icons', [])));
    $featuresAr = $plainList(old('features_ar', data_get($package, 'destination_features_ar', [])));
    $includes = old('includes', implode(PHP_EOL, $plainList(data_get($package, 'includes', []))));
    $includesAr = old('includes_ar', implode(PHP_EOL, $plainList(data_get($package, 'includes_ar', []))));
    $excludes = old('excludes', implode(PHP_EOL, $plainList(data_get($package, 'excludes', []))));
    $excludesAr = old('excludes_ar', implode(PHP_EOL, $plainList(data_get($package, 'excludes_ar', []))));
    $imagesLabel = $package ? __('Gallery Images') : __('Gallery Images');
    $statusText = $package ? ($package->status == 1 ? __('Active') : __('Inactive')) : __('Active on save');
@endphp

<div class="card mt-2">
    <h5 class="card-header">@lang('Basic Information')</h5>
    <div class="card-body purpose">
        <div class="row d-none">
            <div class="col-12">
                <input type="hidden" name="user_id" value="{{ auth('admin')->id() }}">
                <input type="hidden" name="user_type" value="admin">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Title')</label>
                    <input type="text" name="tour_title" class="form-control" placeholder="@lang('Title')" value="{{ $field('tour_title', data_get($package, 'title')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Title Arabic')</label>
                    <input type="text" name="title_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Title Arabic')" value="{{ $field('title_ar', data_get($package, 'title_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Category')</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">@lang('Select category')</option>
                        @foreach ($categories ?? [] as $item)
                            <option value="{{ $item->id }}" @selected($item->id == $field('category_id', data_get($package, 'category_id')))>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Tour Type')</label>
                    <input type="text" name="tour_type" class="form-control" placeholder="@lang('Tour Type')" value="{{ $field('tour_type', data_get($package, 'tour_type', data_get($package, 'category.name'))) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Tour Type Arabic')</label>
                    <input type="text" name="tour_type_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Tour Type Arabic')" value="{{ $field('tour_type_ar', data_get($package, 'tour_type_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Package Label')</label>
                    <input type="text" name="package_label" class="form-control" placeholder="@lang('Package Label')" value="{{ $field('package_label', data_get($package, 'package_label')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Package Label Arabic')</label>
                    <input type="text" name="package_label_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Package Label Arabic')" value="{{ $field('package_label_ar', data_get($package, 'package_label_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Status')</label>
                    <input type="text" class="form-control" value="{{ $statusText }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Location Information')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Address')</label>
                    <input type="text" name="address" class="form-control" placeholder="@lang('Address')" value="{{ $field('address', data_get($package, 'address')) }}">
                </div>
            </div>
            <div class="col-lg-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Address Arabic')</label>
                    <input type="text" name="address_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Address Arabic')" value="{{ $field('address_ar', data_get($package, 'address_ar')) }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('City')</label>
                    <input type="text" name="city" class="form-control" placeholder="@lang('City')" value="{{ $field('city', data_get($package, 'city')) }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Country')</label>
                    <input type="text" name="country" class="form-control" placeholder="@lang('Country')" value="{{ $field('country', data_get($package, 'country')) }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Cities Covered')</label>
                    <input type="text" name="cities_covered" class="form-control" placeholder="@lang('Cities Covered')" value="{{ $field('cities_covered', data_get($package, 'cities_covered')) }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Cities Covered Arabic')</label>
                    <input type="text" name="cities_covered_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Cities Covered Arabic')" value="{{ $field('cities_covered_ar', data_get($package, 'cities_covered_ar')) }}">
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Latitude') (@lang('Optional'))</label>
                    <input type="text" name="latitude" class="form-control" placeholder="@lang('Latitude')" value="{{ $field('latitude', data_get($package, 'latitude')) }}">
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Longitude') (@lang('Optional'))</label>
                    <input type="text" name="longitude" class="form-control" placeholder="@lang('Longitude')" value="{{ $field('longitude', data_get($package, 'longitude')) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Dates & Availability')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Flexible Date')</label>
                    <select name="flexible_date" class="form-control" required>
                        <option value="">@lang('Select flexible date')</option>
                        <option value="2" @selected($field('flexible_date', data_get($package, 'flexible_date')) == 2)>@lang('Fixed Date')</option>
                        <option value="1" @selected($field('flexible_date', data_get($package, 'flexible_date')) == 1)>@lang('Custom Date')</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Tour Start Date')</label>
                    <input type="text" name="start_date" class="form-control datepicker-active" data-language="en" placeholder="@lang('Start date')" value="{{ $field('start_date', data_get($package, 'tour_start')) }}" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Tour End Date')</label>
                    <input type="text" name="end_date" class="form-control datepicker-active" data-language="en" placeholder="@lang('End date')" value="{{ $field('end_date', data_get($package, 'tour_end')) }}" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Duration') / @lang('Stay day & nights')</label>
                    <input type="text" name="day_nights" class="form-control" placeholder="@lang('3 day & 2 nights')" value="{{ $field('day_nights', data_get($package, 'day_nights')) }}" required>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Person Capability')</label>
                    <input type="number" step="any" name="person_capability" class="form-control" placeholder="@lang('Person Capability')" value="{{ $field('person_capability', data_get($package, 'person_capability')) }}" required>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Pricing')</h5>
    <div class="card-body purpose">
        <div class="row g-3 align-items-start">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Price')</label>
                    <div class="input-group">
                        <input type="number" step="0.01" inputmode="decimal" class="form-control" placeholder="@lang('Price')" name="price" value="{{ $field('price', data_get($package, 'price')) }}" required>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Currency')</label>
                    <select name="currency" class="form-control">
                        @php $currencyValue = $field('currency', data_get($package, 'currency', gs()->cur_text)); @endphp
                        @foreach (['EGP', 'SAR', 'USD', 'EUR'] as $currency)
                            <option value="{{ $currency }}" @selected($currencyValue === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Discount')</label>
                    <input type="number" step="any" name="discount" class="form-control" placeholder="@lang('Discount')" value="{{ $field('discount', data_get($package, 'discount')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Price Note')</label>
                    <input type="text" name="price_note" class="form-control" placeholder="@lang('Price Note')" value="{{ $field('price_note', data_get($package, 'price_note')) }}">
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Price From')</label>
                    <input type="number" step="0.01" name="price_from" class="form-control" placeholder="@lang('Price From')" value="{{ $field('price_from', data_get($package, 'price_from')) }}">
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Price To')</label>
                    <input type="number" step="0.01" name="price_to" class="form-control" placeholder="@lang('Price To')" value="{{ $field('price_to', data_get($package, 'price_to')) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Media')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-12">
                <div class="form-group">
                    <label for="images">{{ $imagesLabel }}</label>
                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg" multiple class="form-control" @if(!$package) required @endif>
                </div>
                <div class="form-group mt-3">
                    <div id="image_preview" class="image_preview-wrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Main Content')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Description')</label>
                    <textarea name="description" class="trumEdit1 form-control" rows="8" placeholder="@lang('Description')">{{ $field('description', data_get($package, 'description')) }}</textarea>
                </div>
            </div>
            <div class="col-lg-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Description Arabic')</label>
                    <textarea name="description_ar" dir="rtl" style="text-align:right;" class="form-control" rows="8" placeholder="@lang('Description Arabic')">{{ $field('description_ar', data_get($package, 'description_ar')) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Package Details')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Accommodation Level')</label>
                    <input type="text" name="accommodation_level" class="form-control" placeholder="@lang('Accommodation Level')" value="{{ $field('accommodation_level', data_get($package, 'accommodation_level')) }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Departure From')</label>
                    <input type="text" name="destination_overview[departure_form]" class="form-control" placeholder="@lang('Departure From')" value="{{ $destinationField('departure_form', '') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Departure From Arabic')</label>
                    <input type="text" name="departure_from_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Departure From Arabic')" value="{{ $field('departure_from_ar', data_get($package, 'departure_from_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Arrival')</label>
                    <input type="text" name="destination_overview[arrival]" class="form-control" placeholder="@lang('Arrival')" value="{{ $destinationField('arrival', '') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Arrival Arabic')</label>
                    <input type="text" name="arrival_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Arrival Arabic')" value="{{ $field('arrival_ar', data_get($package, 'arrival_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Transportation')</label>
                    <input type="text" name="destination_overview[transportation]" class="form-control" placeholder="@lang('Transportation')" value="{{ $destinationField('transportation', '') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Transportation Arabic')</label>
                    <input type="text" name="transportation_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Transportation Arabic')" value="{{ $field('transportation_ar', data_get($package, 'transportation_ar')) }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Accommodation')</label>
                    <input type="text" name="destination_overview[accommodation]" class="form-control" placeholder="@lang('Accommodation')" value="{{ $destinationField('accommodation', '') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Accommodation Arabic')</label>
                    <input type="text" name="accommodation_ar" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Accommodation Arabic')" value="{{ $field('accommodation_ar', data_get($package, 'accommodation_ar')) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Includes & Excludes')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Includes')</label>
                    <textarea name="includes" rows="4" class="form-control" placeholder="@lang('One item per line')">{{ $includes }}</textarea>
                </div>
            </div>
            <div class="col-lg-6" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Includes Arabic')</label>
                    <textarea name="includes_ar" rows="4" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('One item per line')">{{ $includesAr }}</textarea>
                </div>
            </div>
            <div class="col-lg-6 mt-lg-3">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Excludes')</label>
                    <textarea name="excludes" rows="4" class="form-control" placeholder="@lang('One item per line')">{{ $excludes }}</textarea>
                </div>
            </div>
            <div class="col-lg-6 mt-lg-3" dir="rtl">
                <div class="form-group">
                    <label class="mb-2 form--label d-block text-end">@lang('Excludes Arabic')</label>
                    <textarea name="excludes_ar" rows="4" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('One item per line')">{{ $excludesAr }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mt-4 mb-3">
    <h5 class="mb-0">@lang('Itinerary Days')</h5>
    <button type="button" class="btn btn--primary btn--sm addItineraryDay">
        <i class="fa fa-plus"></i> @lang('Add New')
    </button>
</div>
<div id="itineraryDayContainer">
    @foreach ($itineraryDays as $index => $day)
        @php($dayImage = $resolveItineraryImage(data_get($day, 'image', '')))
        <div class="itinerary-row border rounded-3 p-3 mb-3 bg-white">
            <div class="row g-3">
                <div class="col-12 col-lg-3">
                    <label class="form-label">@lang('Day Number / Label')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][day_number]" class="form-control" placeholder="@lang('Day 1')" value="{{ data_get($day, 'day_number', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Day Title')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][title]" class="form-control" placeholder="@lang('Day Title')" value="{{ data_get($day, 'title', '') }}">
                </div>
                <div class="col-12 col-lg-5" dir="rtl">
                    <label class="form-label d-block text-end">@lang('Day Title Arabic')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][title_ar]" dir="rtl" style="text-align:right;" class="form-control" placeholder="@lang('Day Title Arabic')" value="{{ data_get($day, 'title_ar', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Itinerary Day Image')</label>
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden border bg-light mb-2">
                        @if ($dayImage)
                            <img src="{{ $dayImage }}" alt="@lang('Itinerary Day Image')" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center text-muted small">@lang('No image selected')</div>
                        @endif
                    </div>
                    <input type="file" name="itinerary_days[{{ $index }}][image_file]" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Day Description')</label>
                    <textarea name="itinerary_days[{{ $index }}][description]" class="form-control" rows="4" placeholder="@lang('Day Description')">{{ data_get($day, 'description', '') }}</textarea>
                </div>
                <div class="col-12 col-lg-4" dir="rtl">
                    <label class="form-label d-block text-end">@lang('Day Description Arabic')</label>
                    <textarea name="itinerary_days[{{ $index }}][description_ar]" dir="rtl" style="text-align:right;" class="form-control" rows="4" placeholder="@lang('Day Description Arabic')">{{ data_get($day, 'description_ar', '') }}</textarea>
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('City')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][city]" class="form-control" placeholder="@lang('City')" value="{{ data_get($day, 'city', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Country')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][country]" class="form-control" placeholder="@lang('Country')" value="{{ data_get($day, 'country', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Latitude') (@lang('Optional'))</label>
                    <input type="text" name="itinerary_days[{{ $index }}][latitude]" class="form-control" placeholder="@lang('Latitude')" value="{{ data_get($day, 'latitude', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Longitude') (@lang('Optional'))</label>
                    <input type="text" name="itinerary_days[{{ $index }}][longitude]" class="form-control" placeholder="@lang('Longitude')" value="{{ data_get($day, 'longitude', '') }}">
                </div>
                <div class="col-12 col-lg-4">
                    <label class="form-label">@lang('Image URL Fallback')</label>
                    <input type="text" name="itinerary_days[{{ $index }}][image]" class="form-control" placeholder="@lang('Image URL fallback')" value="{{ data_get($day, 'image', '') }}">
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="button" class="btn btn--danger btn--sm remove-itinerary-row">
                        <i class="las la-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mt-2">
    <h5 class="card-header">@lang('Destination Information')</h5>
    <div class="card-body purpose">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">@lang('Destination Highlights')</label>
                        <button type="button" class="btn btn--primary btn--sm addHighlights">
                            <i class="fa fa-plus"></i> @lang('Add New')
                        </button>
                    </div>
                    <input type="text" name="highlights[]" id="highlights" class="form-control form--control mb-3" required placeholder="@lang('Destination Highlights')" value="{{ $highlights[0] ?? '' }}" />
                    <div class="mt-2">
                        <label class="form-label">@lang('Destination Highlights Arabic')</label>
                        <input type="text" name="highlights_ar[]" id="highlightsAr" class="form-control form--control mb-0" placeholder="@lang('Destination Highlights Arabic')" value="{{ $highlightsAr[0] ?? '' }}" />
                    </div>
                    <div id="fileUploadsContainer">
                        @foreach (array_slice($highlights, 1) as $index => $item)
                            <div class="row elements g-3 mt-1">
                                <div class="col-sm-6 my-2">
                                    <div class="file-upload input-group">
                                        <input type="text" name="highlights[]" class="form-control form--control" placeholder="@lang('Destination Highlights')" value="{{ $item }}" />
                                    </div>
                                </div>
                                <div class="col-sm-5 my-2">
                                    <div class="file-upload input-group">
                                        <input type="text" name="highlights_ar[]" class="form-control form--control" placeholder="@lang('Destination Highlights Arabic')" value="{{ $highlightsAr[$index + 1] ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-sm-1 my-2 d-flex align-items-center justify-content-end">
                                    <button class="input-group-text btn--danger remove-btn border-0" type="button"><i class="las la-times"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">@lang('Destination Features')</label>
                        <button type="button" class="btn btn--primary btn--sm addFeatures">
                            <i class="fa fa-plus"></i> @lang('Add New')
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-4">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination icons')</label>
                                <div class="file-upload input-group">
                                    <input type="text" name="icons[]" id="inputIcon" class="form-control form--control iconPicker icon" value="{{ $icons[0] ?? '' }}" placeholder="@lang('Icons')" required>
                                    <span class="input-group-text input-group-addon" data-icon="las la-home"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination Features')</label>
                                <input type="text" name="features[]" id="features" class="form-control form--control mb-0" required placeholder="@lang('Destination Features')" value="{{ data_get($features->first(), 'feature', '') }}" />
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-4" dir="rtl">
                            <div class="file-upload">
                                <label class="form-label d-block text-end">@lang('Destination Features Arabic')</label>
                                <input type="text" name="features_ar[]" id="featuresAr" dir="rtl" style="text-align:right;" class="form-control form--control mb-0" placeholder="@lang('Destination Features Arabic')" value="{{ $featuresAr[0] ?? '' }}" />
                            </div>
                        </div>
                    </div>
                    <div id="fileUploadFeatures">
                        @foreach ($features->slice(1) as $index => $item)
                            <div class="row elements g-3 mt-2">
                                <div class="col-lg-3 col-md-4 my-2">
                                    <div class="file-upload input-group">
                                        <input type="text" name="icons[]" class="form-control form--control iconPicker icon" placeholder="@lang('Icons')" value="{{ data_get($item, 'icon', '') }}" required>
                                        <span class="input-group-text input-group-addon" data-icon="las la-home"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 my-2">
                                    <div class="file-upload input-group">
                                        <input type="text" name="features[]" class="form-control form--control" placeholder="@lang('Destination Features')" value="{{ data_get($item, 'feature', '') }}" required />
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-3 my-2" dir="rtl">
                                    <div class="file-upload input-group">
                                        <input type="text" name="features_ar[]" class="form-control form--control" placeholder="@lang('Destination Features Arabic')" value="{{ $featuresAr[$index + 1] ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 my-2 d-flex align-items-center justify-content-end">
                                    <button class="input-group-text btn--danger remove-btn border-0" type="button"><i class="las la-times"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 text-end mt-3">
    <button type="submit" class="btn btn--primary">{{ $package ? __('Update') : __('Save') }}</button>
</div>
