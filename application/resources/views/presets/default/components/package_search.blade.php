@php
    $bannerDatepickerLang = is_rtl() ? 'ar' : 'en';
@endphp

<form action="{{ route('browse') }}" method="GET" autocomplete="off" class="custom-hero-form">
    <div class="banner--filter__wrap d-flex flex-column flex-lg-row gap--16 align-items-stretch align-items-lg-end">
        <div class="banner--filter__inputs d-flex flex-column flex-md-row flex-wrap flex-lg-nowrap gap-3 flex-grow-1">
            <div class="form-group position-relative pills flex-grow-1">
                <span class="icon--wrap position-absolute fs--18">
                    <i class="fa-solid fa-location-dot"></i>
                </span>
                @php
                    $locations = App\Models\Location::where('status', 1)->get();
                @endphp
                <select class="form--control form-control from-select3" name="location">
                    <option value="">@lang('Location')</option>
                    @foreach ($locations as $item)
                        <option value="{{ $item->name }}">{{ __($item->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group position-relative pills d-none d-md-block flex-grow-1">
                <span class="icon--wrap position-absolute fs--18">
                    <i class="fa-regular fa-compass"></i>
                </span>

                <select class="form--control form-control from-select3" name="category_id">
                    <option value="0">@lang('Select Category')</option>
                    @foreach ($tourCategories as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group position-relative flex-grow-1">
                <span class="icon--wrap position-absolute fs--18">
                    <i class="fa-regular fa-calendar-days"></i>
                </span>
                <input class="form--control form-control pills datepicker-here" name="start_date"
                    dir="auto" data-language="{{ $bannerDatepickerLang }}"
                    placeholder="@lang('Date')">
            </div>

            <div class="form-group position-relative d-none d-md-block flex-grow-1">
                <span class="icon--wrap position-absolute fs--18">
                    <i class="fa-regular fa-user"></i>
                </span>
                <input class="form--control pills" type="number" name="person" dir="auto"
                    placeholder="@lang('Person')">
            </div>
        </div>
        <div class="banner--filter__btn flex-shrink-0">
            <button class="btn btn--base btn--lg pills w-100">
                <i class="fa-solid fa-magnifying-glass"></i> @lang('Search')
            </button>
        </div>
    </div>
</form>