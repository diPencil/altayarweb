@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ isset($card) ? route('admin.privilege.cards.update', $card->id) : route('admin.privilege.cards.store') }}" method="POST" enctype="multipart/form-data" class="card b-radius--10">
                @csrf
                <div class="card-body p-4 p-lg-5">
                    <div class="row gy-4">
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Card Name')</label>
                            <input type="text" name="name" value="{{ old('name', $card->name ?? '') }}" class="form-control" required>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Card Name (Arabic)')</label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $card->name_ar ?? '') }}" class="form-control" dir="rtl">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Subtitle')</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $card->subtitle ?? '') }}" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Subtitle (Arabic)')</label>
                            <input type="text" name="subtitle_ar" value="{{ old('subtitle_ar', $card->subtitle_ar ?? '') }}" class="form-control" dir="rtl">
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description')</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description', $card->description ?? '') }}</textarea>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description (Arabic)')</label>
                            <textarea name="description_ar" rows="4" class="form-control" dir="rtl">{{ old('description_ar', $card->description_ar ?? '') }}</textarea>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Price')</label>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $card->price ?? 0) }}" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Original Price')</label>
                            <input type="number" step="0.01" min="0" name="original_price" value="{{ old('original_price', $card->original_price ?? '') }}" class="form-control">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Sort Order')</label>
                            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $card->sort_order ?? 0) }}" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Benefits')</label>
                            <textarea name="benefits" rows="5" class="form-control" placeholder="@lang('One benefit per line')">{{ old('benefits', isset($card->benefits) ? implode("\n", $card->benefits) : '') }}</textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Features')</label>
                            <textarea name="features" rows="5" class="form-control" placeholder="@lang('One feature per line')">{{ old('features', isset($card->features) ? implode("\n", $card->features) : '') }}</textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Card Image')</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            @if(!empty($card?->image_file))
                                <small class="text-muted d-block mt-2">@lang('Current image:') {{ $card->image_file }}</small>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('PDF Document')</label>
                            <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                            @if(!empty($card?->pdf_file))
                                <small class="text-muted d-block mt-2">@lang('Current file:') {{ $card->pdf_file }}</small>
                            @endif
                        </div>
                        <div class="col-lg-12 d-flex flex-wrap gap-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="cardFeatured" @checked(old('is_featured', $card->is_featured ?? 0))>
                                <label class="form-check-label" for="cardFeatured">@lang('Featured card')</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="cardStatus" @checked(old('status', $card->status ?? 1))>
                                <label class="form-check-label" for="cardStatus">@lang('Active card')</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.privilege.cards.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                    <button type="submit" class="btn btn--primary">{{ isset($card) ? __('Update Card') : __('Save Card') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
