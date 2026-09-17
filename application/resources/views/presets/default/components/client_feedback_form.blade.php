<div class="feedback-form mb-5" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="feedback-form-heading d-flex align-items-center gap-3 mb-4">
        <span class="feedback-form-icon text--base" aria-hidden="true"><i class="fas fa-comment-alt"></i></span>
        <h3 class="mb-0">@lang('client_feedback.heading')</h3>
    </div>
    @if(session('feedback_success'))
        <div class="alert alert-success" role="status">{{ session('feedback_success') }}</div>
    @endif
    <form method="POST" action="{{ route('public.client.feedback.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label for="feedback-name" class="form-label">@lang('client_feedback.name')</label>
                <input id="feedback-name" class="form-control" name="name" value="{{ old('name') }}" maxlength="100" autocomplete="name" required>
                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="feedback-profession" class="form-label">@lang('client_feedback.profession')</label>
                <input id="feedback-profession" class="form-control" name="profession" list="feedback-professions" value="{{ old('profession') }}" maxlength="100" placeholder="{{ __('client_feedback.profession_placeholder') }}" required>
                <datalist id="feedback-professions">
                    @foreach($professions as $profession) <option value="{{ $profession }}"></option> @endforeach
                </datalist>
                @error('profession') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="feedback-city" class="form-label">@lang('client_feedback.city')</label>
                <input id="feedback-city" class="form-control" name="city" list="feedback-cities" value="{{ old('city') }}" maxlength="100" autocomplete="address-level2" placeholder="{{ __('client_feedback.city_placeholder') }}" required>
                <datalist id="feedback-cities">
                    @foreach($cities as $city) <option value="{{ $city }}"></option> @endforeach
                </datalist>
                @error('city') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label for="feedback-comment" class="form-label">@lang('client_feedback.comment')</label>
                <textarea id="feedback-comment" class="form-control" name="comment" rows="4" minlength="10" maxlength="3000" required>{{ old('comment') }}</textarea>
                @error('comment') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <fieldset>
                    <legend class="fs-6">@lang('client_feedback.rating')</legend>
                    <div class="d-flex flex-wrap gap-2">
                        @for($rating = 1; $rating <= 5; $rating++)
                            <label class="feedback-rating d-flex align-items-center gap-2 border rounded px-3 py-2">
                                <input type="radio" name="rating" value="{{ $rating }}" @checked((int) old('rating', 5) === $rating) required>
                                <span>{{ $rating }} <i class="fas fa-star text--warning" aria-hidden="true"></i></span>
                            </label>
                        @endfor
                    </div>
                </fieldset>
                @error('rating') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn--base"><i class="fas fa-paper-plane me-2" aria-hidden="true"></i> @lang('client_feedback.submit')</button>
            </div>
        </div>
    </form>
</div>
<style>
    .feedback-form {
        padding: 32px;
        background: #fff;
        border: 1px solid #e0e8ed;
        border-top: 3px solid #32bff2;
        border-radius: 8px;
        box-shadow: 0 8px 28px rgba(25, 50, 65, 0.08);
    }
    .feedback-form-heading h3 { font-size: 24px; line-height: 1.4; overflow-wrap: anywhere; }
    .feedback-form-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 8px;
        background: #effaff;
        font-size: 20px;
    }
    .feedback-form .form-label { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
    .feedback-form .form-control {
        min-height: 48px;
        background: #f8fafb;
        border: 1px solid #dbe3e8;
        border-radius: 6px;
        color: #222;
        font-size: 16px;
    }
    .feedback-form .form-control:focus {
        background: #fff;
        border-color: #32bff2;
        box-shadow: 0 0 0 3px rgba(50, 191, 242, 0.15);
    }
    .feedback-form textarea.form-control { min-height: 140px; resize: vertical; }
    .feedback-form .feedback-rating { min-height: 44px; cursor: pointer; background: #fff; }
    .feedback-form .feedback-rating:has(input:checked) { background: #effaff; border-color: #32bff2 !important; }
    .feedback-form .feedback-rating:focus-within { outline: 2px solid #32bff2; outline-offset: 2px; }
    .feedback-form input[type="radio"] { appearance: auto; width: 18px; height: 18px; flex: 0 0 18px; }
    .feedback-form .btn { min-height: 44px; }
    @media (max-width: 575px) {
        .feedback-form { padding: 20px 16px; }
        .feedback-form-heading h3 { font-size: 20px; }
        .feedback-form .btn { width: 100%; }
    }
</style>
