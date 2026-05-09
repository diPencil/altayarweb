@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('admin.reels.store') }}" method="POST" enctype="multipart/form-data" class="card b-radius--10">
                @csrf
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Title')</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Title (Arabic)')</label>
                            <input type="text" name="title_ar" value="{{ old('title_ar') }}" class="form-control" dir="rtl">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Source Name')</label>
                            <input type="text" name="source_name" value="{{ old('source_name') }}" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Source Name (Arabic)')</label>
                            <input type="text" name="source_name_ar" value="{{ old('source_name_ar') }}" class="form-control" dir="rtl">
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description')</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">@lang('Description (Arabic)')</label>
                            <textarea name="description_ar" rows="4" class="form-control" dir="rtl">{{ old('description_ar') }}</textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Related Tour Package')</label>
                            <select name="tour_package_id" class="form-control">
                                <option value="">@lang('No related page')</option>
                                @foreach($tourPackages as $tourPackage)
                                    <option value="{{ $tourPackage->id }}" @selected(old('tour_package_id') == $tourPackage->id)>{{ $tourPackage->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">@lang('Redirect URL')</label>
                            <input type="url" name="link_url" value="{{ old('link_url') }}" class="form-control" placeholder="https://">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Priority Order')</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Status')</label>
                            <select name="status" class="form-control" required>
                                <option value="1" @selected(old('status', '1') == '1')>@lang('Active')</option>
                                <option value="0" @selected(old('status') === '0')>@lang('Inactive')</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Video File')</label>
                            <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/quicktime" required>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">@lang('Thumbnail')</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.reels.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                    <button type="submit" class="btn btn--primary">@lang('Save Reel')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
