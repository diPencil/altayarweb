@php
    /** @var \App\Models\Listing|null $listing */
    /** @var \App\Models\ListingType[]|\Illuminate\Support\Collection $listingTypes */
    $listing = $listing ?? null;
@endphp
<div class="row justify-content-center">
    <div class="col-12 col-xxl-10">
        <form action="{{ $listing ? route('admin.listing.update', $listing->id) : route('admin.listing.store') }}" method="POST" enctype="multipart/form-data" class="card b-radius--10">
            @csrf
            @if(isset($listing))
                @method('PUT')
            @endif
            <style>
                .listing-form__label {
                    display: block;
                    margin-bottom: 0.7rem;
                    color: #1f2937;
                    font-weight: 700;
                    line-height: 1.25;
                }

                .listing-form__help {
                    display: block;
                    margin-top: 0.5rem;
                    color: #6b7280;
                    font-size: 0.875rem;
                    line-height: 1.45;
                }

                .listing-form__section + .listing-form__section {
                    margin-top: 0.25rem;
                }

                .listing-form__card-body {
                    padding-top: 1.5rem;
                    padding-bottom: 1.5rem;
                }

                @media (max-width: 767px) {
                    .listing-form__label {
                        margin-bottom: 0.6rem;
                    }
                }
            </style>
            <div class="card-body p-4 p-lg-5 listing-form__card-body">
                @php
                    $availableTimesValue = old('available_times', !empty(optional($listing)->available_times) ? implode("\n", optional($listing)->available_times) : '');
                    $facilitiesValue = old('facilities', !empty(optional($listing)->facilities) ? implode("\n", optional($listing)->facilities) : '');
                    $facilitiesArValue = old('facilities_ar', !empty(optional($listing)->facilities_ar) ? implode("\n", optional($listing)->facilities_ar) : '');
                    $includesValue = old('includes', !empty(optional($listing)->includes) ? implode("\n", optional($listing)->includes) : '');
                    $includesArValue = old('includes_ar', !empty(optional($listing)->includes_ar) ? implode("\n", optional($listing)->includes_ar) : '');
                    $excludesValue = old('excludes', !empty(optional($listing)->excludes) ? implode("\n", optional($listing)->excludes) : '');
                    $excludesArValue = old('excludes_ar', !empty(optional($listing)->excludes_ar) ? implode("\n", optional($listing)->excludes_ar) : '');
                    $offerTypeValue = old('offer_type', optional($listing)->offer_type ?? '');
                    $currentImage = optional($listing)->image ?? '';
                @endphp

                <div class="d-grid gap-4 listing-form__section">
                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Basic Information')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Title')</label>
                                    <input type="text" name="title" value="{{ old('title', optional($listing)->title ?? '') }}" class="form-control" required>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Title (Arabic)')</label>
                                    <input type="text" name="title_ar" value="{{ old('title_ar', optional($listing)->title_ar ?? '') }}" class="form-control">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Type')</label>
                                    <select name="listing_type_id" class="form-control" required>
                                        <option value="">@lang('Select Type')</option>
                                        @foreach($listingTypes ?? [] as $type)
                                            <option value="{{ $type->id }}" @selected((string) old('listing_type_id', optional($listing)->listing_type_id ?? '') === (string) $type->id)>
                                                {{ __($type->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" id="listingStatus" @checked(old('status', optional($listing)->status ?? 1))>
                                        <label class="form-check-label" for="listingStatus">@lang('Active listing')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Short Content')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Summary')</label>
                                    <input type="text" name="summary" value="{{ old('summary', optional($listing)->summary ?? '') }}" class="form-control" placeholder="@lang('Short intro')">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Summary (Arabic)')</label>
                                    <input type="text" name="summary_ar" value="{{ old('summary_ar', optional($listing)->summary_ar ?? '') }}" class="form-control" placeholder="@lang('Short intro (Arabic)')">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Full Content')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Description')</label>
                                    <textarea name="description" rows="7" class="form-control">{{ old('description', optional($listing)->description ?? '') }}</textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Description (Arabic)')</label>
                                    <textarea name="description_ar" rows="7" class="form-control" dir="rtl" style="text-align: right;">{{ old('description_ar', optional($listing)->description_ar ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Travel Availability')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Start Date')</label>
                                    <input type="date" name="start_date" value="{{ old('start_date', optional($listing ?? null)->start_date?->format('Y-m-d')) }}" class="form-control" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('End Date')</label>
                                    <input type="date" name="end_date" value="{{ old('end_date', optional($listing ?? null)->end_date?->format('Y-m-d')) }}" class="form-control" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Duration')</label>
                                    <input type="text" class="form-control" value="{{ optional($listing)->durationDays() ? optional($listing)->durationDays() . ' ' . __('Days') : __('Auto calculated from dates') }}" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label listing-form__label">@lang('Available Travel Times')</label>
                                    <textarea name="available_times" rows="4" class="form-control" placeholder="09:00 AM&#10;01:00 PM&#10;05:00 PM">{{ $availableTimesValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write one time per line. These will appear in the booking page.')</small>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Offer Details')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Facilities')</label>
                                    <textarea name="facilities" rows="4" class="form-control" placeholder="Wi-Fi&#10;Breakfast&#10;Airport Pickup">{{ $facilitiesValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write one facility per line. These will show in the listing page.')</small>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Facilities (Arabic)')</label>
                                    <textarea name="facilities_ar" rows="4" class="form-control" dir="rtl" style="text-align: right;" placeholder="غرفة قياسية&#10;انتقال مجاني&#10;دعم الحجز">{{ $facilitiesArValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write one Arabic facility per line. These will show on the Arabic listing page.')</small>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Includes')</label>
                                    <textarea name="includes" rows="4" class="form-control" placeholder="Breakfast&#10;Airport Transfer&#10;Guided Tour">{{ $includesValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write what is included, one item per line.')</small>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Includes (Arabic)')</label>
                                    <textarea name="includes_ar" rows="4" class="form-control" dir="rtl" style="text-align: right;" placeholder="إقامة فندقية&#10;نوع الغرفة المحدد&#10;دعم الحجز">{{ $includesArValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write what is included in Arabic, one item per line.')</small>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Excludes')</label>
                                    <textarea name="excludes" rows="4" class="form-control" placeholder="Personal expenses&#10;Tips&#10;Lunch">{{ $excludesValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write what is excluded, one item per line.')</small>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Excludes (Arabic)')</label>
                                    <textarea name="excludes_ar" rows="4" class="form-control" dir="rtl" style="text-align: right;" placeholder="تذاكر الطيران&#10;التأشيرة&#10;المصاريف الشخصية">{{ $excludesArValue }}</textarea>
                                    <small class="listing-form__help">@lang('Write what is excluded in Arabic, one item per line.')</small>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Location & Pricing')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('City')</label>
                                    <input type="text" name="city" value="{{ old('city', optional($listing)->city ?? '') }}" class="form-control">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Country')</label>
                                    <input type="text" name="country" value="{{ old('country', optional($listing)->country ?? '') }}" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label listing-form__label">@lang('Address')</label>
                                    <input type="text" name="address" value="{{ old('address', optional($listing)->address ?? '') }}" class="form-control">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Price')</label>
                                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', optional($listing)->price ?? 0) }}" class="form-control">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Currency')</label>
                                    <select name="currency" class="form-control" required>
                                        <option value="USD" @selected(old('currency', optional($listing)->currency ?? 'USD') === 'USD')>USD</option>
                                        <option value="SAR" @selected(old('currency', optional($listing)->currency) === 'SAR')>SAR</option>
                                        <option value="EGP" @selected(old('currency', optional($listing)->currency) === 'EGP')>EGP</option>
                                        <option value="EUR" @selected(old('currency', optional($listing)->currency) === 'EUR')>EUR</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Discount')</label>
                                    <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', optional($listing)->discount ?? '') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Offer Configuration')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Offer Type')</label>
                                    <select name="offer_type" class="form-control">
                                        <option value="">@lang('Select Offer Type')</option>
                                        <option value="stay_pay" @selected($offerTypeValue === 'stay_pay')>@lang('Stay Nights / Pay Nights')</option>
                                        <option value="day_bundle" @selected($offerTypeValue === 'day_bundle')>@lang('Days Bundle')</option>
                                        <option value="custom" @selected($offerTypeValue === 'custom')>@lang('Custom Text')</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Offer First Value')</label>
                                    <input type="number" min="1" step="1" name="offer_first_value" value="{{ old('offer_first_value', optional($listing)->offer_first_value ?? '') }}" class="form-control" placeholder="3">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label listing-form__label">@lang('Offer Second Value')</label>
                                    <input type="number" min="1" step="1" name="offer_second_value" value="{{ old('offer_second_value', optional($listing)->offer_second_value ?? '') }}" class="form-control" placeholder="2">
                                </div>
                                <div class="col-12">
                                    <label class="form-label listing-form__label">@lang('Offer Text')</label>
                                    <input type="text" name="offer_text" value="{{ old('offer_text', optional($listing)->offer_text ?? '') }}" class="form-control" placeholder="@lang('Stay 3 nights, pay 2')">
                                    <small class="listing-form__help">@lang('Use this only when Offer Type is Custom Text.')</small>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border rounded-3 overflow-hidden bg-white shadow-sm listing-form__section">
                        <div class="border-bottom bg-light px-4 py-3">
                            <h6 class="mb-0 fw-semibold">@lang('Media')</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4 align-items-start">
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Image (Upload File)')</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label listing-form__label">@lang('Image Link (URL)')</label>
                                    <input type="text" name="image_url" value="{{ old('image_url', filter_var($currentImage, FILTER_VALIDATE_URL) ? $currentImage : '') }}" class="form-control" placeholder="https://example.com/image.jpg">
                                </div>
                                @if(!empty($listing?->image))
                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 bg-light text-center">
                                            <img src="{{ optional($listing)->imageUrl }}" alt="{{ optional($listing)->title }}" class="img-thumbnail mb-2" style="max-width: 150px;">
                                            <small class="listing-form__help">@lang('Current image:') {{ !filter_var(optional($listing)->image, FILTER_VALIDATE_URL) ? optional($listing)->image : __('URL Link') }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="card-footer border-top bg-white d-flex flex-wrap justify-content-end gap-2 py-3">
                <a href="{{ route('admin.listing.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                <button type="submit" class="btn btn--primary">{{ isset($listing) ? __('Update Listing') : __('Save Listing') }}</button>
            </div>
        </form>
    </div>
</div>
