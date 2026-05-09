@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="base--card">
                <form action="{{route('employee.collection.update',$collection->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="mb-2 form--label">@lang('Name')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form--control" name="name" value="{{ old('name', $collection->name) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="mb-2 form--label">@lang('Image')<span class="text-danger">*</span></label>
                                <input type="file" class="form-control form--control" name="image">
                                <small class="text-danger">@lang('recomanded size'):({{getFileSize('collection')}}@lang('px'))</small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="mb-2 form--label">@lang('Artwork/Gelleries')<span class="text-danger">*</span></label>
                                <select class="select2-auto-tokenize form-control form--control" multiple="multiple" name="artwork_ids[]" required>
                                    @foreach ($artworks as $artwork )
                                    <option value="{{ $artwork->id }}" {{ in_array($artwork->id, $collection->artworks->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ __($artwork->title) }}
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h5 class="mb-2 form--label">@lang('Current Image')</h5>
                            <img src="{{ getImage(getFilePath('collection').'/'.$collection->image) }}" alt="@lang('image')" class="rounded" style="width: 200px; height:150px;">
                        </div>
                        <div class="col-lg-12">
                            <div class="text-end">
                                <button type="submit" class="btn btn--base btn--lg">@lang('Update')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@push('script')
<script>
     (function($) {
            "use strict";
            $(".select2-auto-tokenize").select2({
                tags: true,
                tokenSeparators: [","],
                dropdownParent: $(".base--card"),
            });
    })(jQuery);
</script>
@endpush



