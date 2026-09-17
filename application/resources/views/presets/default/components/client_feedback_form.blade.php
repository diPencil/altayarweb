<div class="feedback-form mb-5 pb-4 border-bottom" dir="rtl">
    <h3 class="mb-4">اكتب تعليقك</h3>
    @if(session('feedback_success'))
        <div class="alert alert-success" role="status">{{ session('feedback_success') }}</div>
    @endif
    <form method="POST" action="{{ route('public.client.feedback.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label for="feedback-name" class="form-label">الاسم</label>
                <input id="feedback-name" class="form-control" name="name" value="{{ old('name') }}" maxlength="100" autocomplete="name" required>
                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="feedback-profession" class="form-label">المهنة</label>
                <input id="feedback-profession" class="form-control" name="profession" list="feedback-professions" value="{{ old('profession') }}" maxlength="100" placeholder="اختر مهنتك أو اكتب مهنة جديدة" required>
                <datalist id="feedback-professions">
                    @foreach($professions as $profession) <option value="{{ $profession }}"></option> @endforeach
                </datalist>
                @error('profession') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="feedback-city" class="form-label">المدينة / المنطقة</label>
                <input id="feedback-city" class="form-control" name="city" list="feedback-cities" value="{{ old('city') }}" maxlength="100" autocomplete="address-level2" placeholder="اختر مدينتك أو اكتب مكانًا آخر" required>
                <datalist id="feedback-cities">
                    @foreach($cities as $city) <option value="{{ $city }}"></option> @endforeach
                </datalist>
                @error('city') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label for="feedback-comment" class="form-label">التعليق</label>
                <textarea id="feedback-comment" class="form-control" name="comment" rows="4" minlength="10" maxlength="3000" required>{{ old('comment') }}</textarea>
                @error('comment') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <fieldset>
                    <legend class="fs-6">التقييم</legend>
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
                <button type="submit" class="btn btn--base"><i class="fas fa-paper-plane me-2" aria-hidden="true"></i> إرسال التعليق</button>
            </div>
        </div>
    </form>
</div>
<style>
    .feedback-form .form-control { min-height: 44px; background: #fff; color: #222; }
    .feedback-form .feedback-rating { min-height: 44px; cursor: pointer; }
    .feedback-form input[type="radio"] { appearance: auto; width: 18px; height: 18px; flex: 0 0 18px; }
    .feedback-form .btn { min-height: 44px; }
</style>
