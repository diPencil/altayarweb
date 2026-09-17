@extends('admin.layouts.app')
@section('panel')
<form method="POST" action="{{ route('admin.client-feedback.update', $feedback->id) }}">
    @csrf
    <div class="row g-3">
        @foreach(['name' => 'Name', 'profession' => 'Profession', 'city' => 'City'] as $field => $label)
            <div class="col-md-4">
                <label for="feedback-{{ $field }}">{{ __($label) }}</label>
                <input id="feedback-{{ $field }}" class="form-control" name="{{ $field }}" value="{{ old($field, $feedback->$field) }}" maxlength="100" required>
                @error($field) <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        @endforeach
        <div class="col-12">
            <label for="feedback-comment">@lang('Comment')</label>
            <textarea id="feedback-comment" class="form-control" name="comment" rows="6" minlength="10" maxlength="3000" required>{{ old('comment', $feedback->comment) }}</textarea>
            @error('comment') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="feedback-rating">@lang('Rating')</label>
            <select id="feedback-rating" class="form-control" name="rating" required>
                @for($rating = 1; $rating <= 5; $rating++)
                    <option value="{{ $rating }}" @selected((int) old('rating', $feedback->rating) === $rating)>{{ $rating }} / 5</option>
                @endfor
            </select>
            @error('rating') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label for="feedback-status">@lang('Status')</label>
            <select id="feedback-status" class="form-control" name="is_approved" required>
                <option value="0" @selected(!old('is_approved', $feedback->is_approved))>@lang('Pending')</option>
                <option value="1" @selected(old('is_approved', $feedback->is_approved))>@lang('Approved')</option>
            </select>
            @error('is_approved') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <button class="btn btn--primary" type="submit"><i class="las la-save"></i> @lang('Save')</button>
            <a href="{{ route('admin.client-feedback.index') }}" class="btn btn-outline--primary">@lang('Back')</a>
        </div>
    </div>
</form>
@endsection
