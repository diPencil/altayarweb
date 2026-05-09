@php
    $listing = $listing ?? null;
    $formAction = $listing ? route('employee.listing.update', $listing->id) : route('employee.listing.store');
    $availableTimesValue = old('available_times', !empty(optional($listing)->available_times) ? implode("\n", optional($listing)->available_times) : '');
    $facilitiesValue = old('facilities', !empty(optional($listing)->facilities) ? implode("\n", optional($listing)->facilities) : '');
    $facilitiesArValue = old('facilities_ar', !empty(optional($listing)->facilities_ar) ? implode("\n", optional($listing)->facilities_ar) : '');
    $includesValue = old('includes', !empty(optional($listing)->includes) ? implode("\n", optional($listing)->includes) : '');
    $includesArValue = old('includes_ar', !empty(optional($listing)->includes_ar) ? implode("\n", optional($listing)->includes_ar) : '');
    $excludesValue = old('excludes', !empty(optional($listing)->excludes) ? implode("\n", optional($listing)->excludes) : '');
    $excludesArValue = old('excludes_ar', !empty(optional($listing)->excludes_ar) ? implode("\n", optional($listing)->excludes_ar) : '');
    $offerTypeValue = old('offer_type', optional($listing)->offer_type ?? '');
@endphp

<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <div class="base--card radius--20">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                <style>
                    .employee-listing-form__label {
                        display: block;
                        margin-bottom: 0.7rem;
                        color: #1f2937;
                        font-weight: 700;
                        line-height: 1.25;
                    }

                    .employee-listing-form__help {
                        display: block;
                        margin-top: 0.5rem;
                        color: #6b7280;
                        font-size: 0.875rem;
                        line-height: 1.45;
                    }
                </style>
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Title')</label>
                        <input type="text" name="title" value="{{ old('title', optional($listing)->title ?? '') }}" class="form--control" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Title (Arabic)')</label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', optional($listing)->title_ar ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Listing Type')</label>
                        <select name="listing_type_id" class="form--control form-select" required>
                            <option value="">@lang('Select One')</option>
                            @foreach($listingTypes as $type)
                                <option value="{{ $type->id }}" @selected((string) old('listing_type_id', optional($listing)->listing_type_id ?? '') === (string) $type->id)>{{ __($type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Summary')</label>
                        <input type="text" name="summary" value="{{ old('summary', optional($listing)->summary ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-12">
                        <label class="form--label employee-listing-form__label">@lang('Summary (Arabic)')</label>
                        <input type="text" name="summary_ar" value="{{ old('summary_ar', optional($listing)->summary_ar ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Description')</label>
                        <textarea name="description" rows="5" class="form--control">{{ old('description', optional($listing)->description ?? '') }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Description (Arabic)')</label>
                        <textarea name="description_ar" rows="5" class="form--control">{{ old('description_ar', optional($listing)->description_ar ?? '') }}</textarea>
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('Start Date')</label>
                        <input type="date" name="start_date" value="{{ old('start_date', optional($listing)->start_date?->format('Y-m-d')) }}" class="form--control" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('End Date')</label>
                        <input type="date" name="end_date" value="{{ old('end_date', optional($listing)->end_date?->format('Y-m-d')) }}" class="form--control" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('Price')</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', optional($listing)->price ?? 0) }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('City')</label>
                        <input type="text" name="city" value="{{ old('city', optional($listing)->city ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Country')</label>
                        <input type="text" name="country" value="{{ old('country', optional($listing)->country ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-12">
                        <label class="form--label employee-listing-form__label">@lang('Address')</label>
                        <input type="text" name="address" value="{{ old('address', optional($listing)->address ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-12">
                        <label class="form--label employee-listing-form__label">@lang('Available Travel Times')</label>
                        <textarea name="available_times" rows="4" class="form--control" placeholder="09:00 AM&#10;01:00 PM">{{ $availableTimesValue }}</textarea>
                    </div>
                    <div class="col-lg-12">
                        <label class="form--label employee-listing-form__label">@lang('Facilities')</label>
                        <textarea name="facilities" rows="4" class="form--control" placeholder="Wi-Fi&#10;Breakfast">{{ $facilitiesValue }}</textarea>
                    </div>
                    <div class="col-lg-12">
                        <label class="form--label employee-listing-form__label">Facilities (Arabic)</label>
                        <textarea name="facilities_ar" rows="4" class="form--control" dir="rtl" style="text-align: right;" placeholder="غرفة قياسية&#10;انتقال مجاني&#10;دعم الحجز">{{ $facilitiesArValue }}</textarea>
                        <small class="employee-listing-form__help">Write one Arabic facility per line. These will show on the Arabic listing page.</small>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Includes')</label>
                        <textarea name="includes" rows="4" class="form--control" placeholder="Breakfast&#10;Airport Transfer">{{ $includesValue }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">Includes (Arabic)</label>
                        <textarea name="includes_ar" rows="4" class="form--control" dir="rtl" style="text-align: right;" placeholder="إقامة فندقية&#10;نوع الغرفة المحدد&#10;دعم الحجز">{{ $includesArValue }}</textarea>
                        <small class="employee-listing-form__help">Write what is included in Arabic, one item per line.</small>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Excludes')</label>
                        <textarea name="excludes" rows="4" class="form--control" placeholder="Personal expenses&#10;Tips">{{ $excludesValue }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">Excludes (Arabic)</label>
                        <textarea name="excludes_ar" rows="4" class="form--control" dir="rtl" style="text-align: right;" placeholder="تذاكر الطيران&#10;التأشيرة&#10;المصاريف الشخصية">{{ $excludesArValue }}</textarea>
                        <small class="employee-listing-form__help">Write what is excluded in Arabic, one item per line.</small>
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('Discount')</label>
                        <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', optional($listing)->discount ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('Offer Type')</label>
                        <select name="offer_type" class="form--control form-select">
                            <option value="">@lang('Select Offer Type')</option>
                            <option value="stay_pay" @selected($offerTypeValue === 'stay_pay')>@lang('Stay Nights / Pay Nights')</option>
                            <option value="day_bundle" @selected($offerTypeValue === 'day_bundle')>@lang('Days Bundle')</option>
                            <option value="custom" @selected($offerTypeValue === 'custom')>@lang('Custom Text')</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form--label employee-listing-form__label">@lang('Offer First Value')</label>
                        <input type="number" min="1" step="1" name="offer_first_value" value="{{ old('offer_first_value', optional($listing)->offer_first_value ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Offer Second Value')</label>
                        <input type="number" min="1" step="1" name="offer_second_value" value="{{ old('offer_second_value', optional($listing)->offer_second_value ?? '') }}" class="form--control">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Offer Text')</label>
                        <input type="text" name="offer_text" value="{{ old('offer_text', optional($listing)->offer_text ?? '') }}" class="form--control" placeholder="Stay 3 nights, pay 2">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Image')</label>
                        <input type="file" name="image" class="form--control" accept="image/*">
                    </div>
                    <div class="col-lg-6">
                        <label class="form--label employee-listing-form__label">@lang('Image Link (URL)')</label>
                        <input type="text" name="image_url" value="{{ old('image_url', filter_var(optional($listing)->image ?? '', FILTER_VALIDATE_URL) ? optional($listing)->image : '') }}" class="form--control" placeholder="https://example.com/image.jpg">
                    </div>
                    @if(!empty($listing?->image))
                        <div class="col-lg-12 text-center">
                            <img src="{{ $listing->imageUrl }}" alt="{{ $listing->title }}" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    @endif
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn--base btn--md w-100">
                            {{ $listing ? __('Update Listing') : __('Create Listing') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
