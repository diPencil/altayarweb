@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="base--card">
                <form class="navbar-search">
                    <input type="text" name="" id="locationInput" class="controls my-2 form-control form--control border-0"
                        placeholder="@lang('Enter a location')" autocomplete="off">
                </form>
                <form action="{{ route('employee.tour.package.update', $tourPackage->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="mb-5">
                        <h5 class="">@lang('Basic Information')</h5>
                        <div id="map" class="mb-3"></div>

                        <div class="row d-none">

                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_id" class="form-control form--control" hidden
                                        value="{{ auth('employee')->id() }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_type" class="form-control form--control" hidden
                                        value="Employee">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lat" name="latitude" class="form-control form--control"
                                        hidden value="{{ $tourPackage->latitude }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lon" name="longitude" class="form-control form--control"
                                        value="{{ $tourPackage->longitude }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="city" name="city" class="form-control form--control"
                                        value="{{ $tourPackage->city }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="zipCode" name="zipcode" class="form-control form--control"
                                        value="{{ $tourPackage->zip_code }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="state" name="state" class="form-control form--control"
                                        value="{{ $tourPackage->state }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="country" name="country" class="form-control form--control"
                                        value="{{ $tourPackage->country }}" hidden>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Title')</label>
                                    <input type="text" name="tour_title" class="form-control form--control"
                                        placeholder="@lang('Title')" value="{{ $tourPackage->title }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="location">@lang('Address')</label>
                                    <input type="text" id="location" name="address" class="form-control form--control"
                                        value="{{ $tourPackage->address }}" required readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Category')</label>
                                    <select name="category_id" id="status" class="form--select form--control" required>
                                        <option>@lang('Select category')</option>
                                        @foreach ($categories ?? [] as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $tourPackage->category_id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Flexible Date')</label>
                                    <select name="flexible_date" id="status" class="form-select form--control"
                                        required>
                                        <option>@lang('Select flexible date')</option>
                                        <option value="1" {{ $tourPackage->flexible_date == 1 ? 'selected' : '' }}>
                                            @lang('Fixed Date')</option>
                                        <option value="2" {{ $tourPackage->flexible_date == 2 ? 'selected' : '' }}>
                                            @lang('Custom Date')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Tour Start Date') </label>
                                    <input type="text" name="start_date" autocomplete="off"
                                        class="form-control form--control datepicker-active" data-language="en"
                                        placeholder="@lang('Start date')"
                                        value="{{ \Carbon\Carbon::parse($tourPackage->tour_start)->format('m/d/Y , h:i a') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Tour End Date')</label>
                                    <input type="text" name="end_date" autocomplete="off"
                                        class="form-control form--control datepicker-active" data-language="en"
                                        placeholder="@lang('End date')"
                                        value="{{ \Carbon\Carbon::parse($tourPackage->tour_end)->format('m/d/Y , h:i a') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Person Capability')</label>
                                    <input type="number" step="any" name="person_capability"
                                        class="form-control form--control" placeholder="@lang('Person Capability')"
                                        value="{{ $tourPackage->person_capability }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Stay day & nights')</label>
                                    <input type="text" step="any" name="day_nights"
                                        class="form-control form--control" placeholder="@lang('3 day & 2 nights')"
                                        value="{{ $tourPackage->day_nights }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Price')</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form--control" placeholder="price"
                                            name="price" aria-label="price" aria-describedby="basic-addon2"
                                            value="{{ $tourPackage->price }}" required>
                                        <span class="input-group-text bg--base text--white"
                                            id="basic-addon2">{{ gs()->cur_sym }}</span>
                                    </div>
                                </div>


                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Discount')</label>
                                    <input type="number" step="any" name="discount"
                                        class="form-control form--control" placeholder="@lang('Discount')"
                                        value ="{{ $tourPackage->discount }}">
                                </div>
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="images" class="form--label">@lang('Images')</label>
                                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg"
                                        multiple class="form-control form--control">
                                </div>
                                <div class="form-group">
                                    <div id="image_preview" class="image_preview-wrapper">
                                        @foreach ($tourPackage->tour_package_images as $i => $img)
                                            <div class='img-div' id='img-div{{ $i }}'
                                                @if ($i != 0) onclick=imageDelete(this,{{ $img->id }}); @endif>

                                                <input type="hidden" name="old_tour_package_images[]"
                                                    value="{{ $img->id }}">
                                                <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $img->image) }}"
                                                    class='img-responsive image img-thumbnail'
                                                    title='{{ $img->image }}' alt="tour-image">
                                                @if ($i != 0)
                                                    <div class='middle'>
                                                        <button id='action-icon' value='img-div{{ $i }}'
                                                            class='delete-btn btn btn--danger'
                                                            role='{{ $img->image }}'>
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Description')</label>
                                    <textarea name="description" class="trumEdit1 form--control" placeholder="@lang('Description')">{{ $tourPackage->description }}</textarea>

                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="mb-5">
                        <div class="mb-3">
                            <h5>@lang('Destination Overview')</h5>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Departure From')</label>
                                    <input type="text" name="destination_overview[departure_form]" id="departure_form"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->departure_form }}"
                                        placeholder="@lang('Departure From')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Departure From Arabic')</label>
                                    <input type="text" name="departure_from_ar" id="departure_from_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" value="{{ $tourPackage->departure_from_ar }}"
                                        placeholder="@lang('Departure From Arabic')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Arrival')</label>
                                    <input type="text" name="destination_overview[arrival]" id="arrival"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->arrival }}"
                                        placeholder="@lang('Arrival')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Arrival Arabic')</label>
                                    <input type="text" name="arrival_ar" id="arrival_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" value="{{ $tourPackage->arrival_ar }}"
                                        placeholder="@lang('Arrival Arabic')" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Transportation')</label>
                                    <input type="text" name="destination_overview[transportation]" id="transportation"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->transportation }}"
                                        placeholder="@lang('Transportation')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Transportation Arabic')</label>
                                    <input type="text" name="transportation_ar" id="transportation_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" value="{{ $tourPackage->transportation_ar }}"
                                        placeholder="@lang('Transportation Arabic')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Accommodation')</label>
                                    <input type="text" name="destination_overview[accommodation]" id="accommodation"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->accommodation }}"
                                        placeholder="@lang('Accommodation')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Accommodation Arabic')</label>
                                    <input type="text" name="accommodation_ar" id="accommodation_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" value="{{ $tourPackage->accommodation_ar }}"
                                        placeholder="@lang('Accommodation Arabic')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="mb-0">@lang('Destination Information')</h5>

                                        <button type="button" class="btn btn btn--base btn-md pill addHighlights">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 my-2">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination Highlights')</label>
                                                <input type="text" name="highlights[]" id="highlights"
                                                    class="form-control form--control mb-0" required
                                                    placeholder="@lang('Destination Highlights')"
                                                    value="{{ $tourPackage->highlights[0] }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fileUploadsContainer">

                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mt-3">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="">@lang('Destination Features')</h5>

                                        <button type="button" class="btn btn btn--base btn-md pill addFeatures">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-sm-4">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination icons')</label>
                                                <div class="file-upload input-group">
                                                    <input type="text" name="icons[]" id="inputIcon"
                                                        class="form-control form--control iconPicker icon"
                                                        value="{{ $tourPackage->features[0]->icon }}"
                                                        placeholder="@lang('Icons')" required>
                                                    <span class="input-group-text input-group-addon bg--base text--white"
                                                        data-icon="las la-home">@php echo $tourPackage->features[0]->icon @endphp</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination Features')</label>
                                                <input type="text" name="features[]" id="features"
                                                    value="{{ $tourPackage->features[0]->feature }}"
                                                    class="form-control form--control mb-0" required
                                                    placeholder="@lang('Destination Features')" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fileUploadFeatures">
                                        @php
                                            $features = $tourPackage->features;
                                            unset($features[0]);
                                        @endphp
                                        @foreach ($features as $item)
                                            <div class="row mb-4">
                                                <div class="col-sm-4">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination icons')</label>
                                                        <div class="file-upload input-group">
                                                            <input type="text" name="icons[]" id="inputIcon"
                                                                class="form-control form--control iconPicker icon"
                                                                value="{{ $item->icon }}"
                                                                placeholder="@lang('Icons')" required>
                                                            <span
                                                                class="input-group-text input-group-addon bg--base text--white"
                                                                data-icon="las la-home">
                                                                @php echo $item->icon @endphp
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination Features')</label>
                                                        <input type="text" name="features[]" id="features"
                                                            class="form-control form--control mb-0" required
                                                            value="{{ $item->feature }}"
                                                            placeholder="@lang('Destination Features')" />
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 text-end mt-3">
                        <button type="submit" class="btn btn--base btn--lg pills">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>
@endpush

@push('style')
    <style>
        .ck.ck-editor__main>.ck-editor__editable {
            height: 250px;
        }

        .image_preview-wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        .img-div {
            position: relative;
            width: 150px;
            margin-right: 5px;
            margin-left: 5px;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .image {
            opacity: 1;
            display: block;
            width: 100%;
            max-width: auto;
            transition: .5s ease;
            backface-visibility: hidden;
        }

        .middle {
            transition: .5s ease;
            opacity: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            text-align: center;
        }

        .img-div:hover .image {
            opacity: 0.3;
        }

        .img-div:hover .middle {
            opacity: 1;
        }

        #map {
            height: 400px;
            width: 100%;
        }

        .pac-target-input {
            width: 300px !important;
            margin-top: 40px !important;
            background-color: white !important;
            border: 1px solid hsl(var(--base)) !important;
        }
        
    </style>
@endpush


@push('script')
    <script>
        $(document).ready(function() {
            'use strict'
            $(".datepicker-active").datepicker({
                minDate: new Date(),
                timepicker: true,
                timeFormat: ', hh:ii aa'

            });

        });
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addHighlights').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                <div class="row elements">
                    <div class="col-sm-12 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="highlights[]" class="form-control form--control"
                                placeholder="@lang('Destination Highlights')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addOverview').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsOverview").append(`
                <div class="row elements">
                    <div class="col-sm-6 my-2">
                             <div class="file-upload input-group">
                            <input type="text" name="title[]" class="form-control form--control"
                                placeholder="@lang('title')" required />                                        
                        </div>
                    </div>

                    <div class="col-sm-6 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="value[]" class="form-control form--control"
                                placeholder="@lang('value')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
                $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                    $(this).closest('.file-upload').find('.iconpicker-input').val(
                        `<i class="${e.iconpickerValue}"></i>`);
                });
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFeatures').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadFeatures").append(`
                <div class="row elements">
                    <div class="col-sm-4 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="icons[]" id="inputIcon"
                                class="form-control form--control iconPicker icon" placeholder="@lang('Icons')" required>
                            <span class="input-group-text bg--base text--white input-group-addon" data-icon="las la-home"></span>
                        </div>
                    </div>

                    <div class="col-sm-8 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="features[]" class="form-control form--control"
                                placeholder="@lang('Destination Features')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
                $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                    $(this).closest('.file-upload').find('.iconpicker-input').val(
                        `<i class="${e.iconpickerValue}"></i>`);
                });
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict"
            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.file-upload').find('.iconpicker-input').val(
                    `<i class="${e.iconpickerValue}"></i>`);
            });
        })(jQuery);
    </script>

    <script>
        $(document).ready(function() {
            "use strict";
            var fileArr = [];
            $("#images").on('change', function() {
                // check if fileArr length is greater than 0
                if (fileArr.length > 0) fileArr = [];

                var total_file = document.getElementById("images").files;
                if (!total_file.length) return;
                for (var i = 0; i < total_file.length; i++) {
                    if (total_file[i].size > 1048576) {
                        return false;
                    } else {
                        fileArr.push(total_file[i]);
                        $('#image_preview').append("<div class='img-div' id='img-div" + i + "'><img src='" +
                            URL.createObjectURL(event.target.files[i]) +
                            "' class='img-responsive image img-thumbnail' title='" + total_file[i]
                            .name + "'><div class='middle'><button id='action-icon' value='img-div" +
                            i + "' class='delete-btn btn btn--danger' role='" + total_file[i].name +
                            "'><i class='fa fa-trash'></i></button></div></div>");
                    }
                }
            });

            $('body').on('click', '#action-icon', function(evt) {
                var divName = this.value;
                var fileName = $(this).attr('role');
                $(`#${divName}`).remove();

                for (var i = 0; i < fileArr.length; i++) {
                    if (fileArr[i].name === fileName) {
                        fileArr.splice(i, 1);
                    }
                }
                document.getElementById('images').files = FileListItem(fileArr);
                evt.preventDefault();
            });

            function FileListItem(file) {
                file = [].slice.call(Array.isArray(file) ? file : arguments)
                for (var c, b = c = file.length, d = !0; b-- && d;) d = file[b] instanceof File
                if (!d) throw new TypeError("expected argument to FileList is File or array of File objects")
                for (b = (new ClipboardEvent("")).clipboardData || new DataTransfer; c--;) b.items.add(file[c])
                return b.files
            }
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $general->map_api_key }}&callback=initMap"
        async defer></script>

    <script>
        (function($) {
            "use strict";
            var map;
            var marker;

            var initialLat = {{ $tourPackage->latitude }};
            var initialLng = {{ $tourPackage->longitude }};

            window.initMap = function() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: initialLat, //-33.8688,
                        lng: initialLng // 151.2195
                    },
                    zoom: 13
                });

                marker = new google.maps.Marker({
                    position: {
                        lat: initialLat,
                        lng: initialLng
                    },
                    map: map,
                    draggable: true,
                    anchorPoint: new google.maps.Point(0, -29),
                    title: "Current Location"
                });

                var input = document.getElementById('locationInput');
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                // marker = new google.maps.Marker({
                //     map: map,
                //     draggable: true,
                //     anchorPoint: new google.maps.Point(0, -29),
                //     title: "This marker is draggable."
                // });

                var addressElement = document.getElementById('location');
                var latElement = document.getElementById('lat');
                var lonElement = document.getElementById('lon');
                var cityElement = document.getElementById('city');
                var zipCodeElement = document.getElementById('zipCode');
                var stateElement = document.getElementById('state');
                var countryElement = document.getElementById('country');

                infowindow.setContent('');

                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }

                    marker.setPosition(place.geometry.location);
                    map.setCenter(place.geometry.location);
                    marker.setVisible(true);

                    marker.setTitle(place.name);

                    infowindow.setContent('Name: ' + place.name);

                    addressElement.value = place.formatted_address;
                    latElement.value = place.geometry.location.lat();
                    lonElement.value = place.geometry.location.lng();
                    cityElement.value = getComponentValue(place, 'locality');
                    zipCodeElement.value = getComponentValue(place, 'postal_code');
                    stateElement.value = getComponentValue(place, 'administrative_area_level_1');
                    countryElement.value = getComponentValue(place, 'country');

                });

                google.maps.event.addListener(map, 'click', function(event) {
                    var latLng = event.latLng;
                    var lat = latLng.lat();
                    var lng = latLng.lng();

                    marker.setPosition(event.latLng);
                    marker.setVisible(true);
                    marker.setTitle('Custom Name');

                    latElement.value = lat;
                    lonElement.value = lng;

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        location: event.latLng
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            var placeData = results[0];
                            addressElement.value = placeData.formatted_address;
                            cityElement.value = getComponentValue(placeData, 'locality');
                            zipCodeElement.value = getComponentValue(placeData, 'postal_code');
                            stateElement.value = getComponentValue(placeData,
                                'administrative_area_level_1');
                            countryElement.value = getComponentValue(placeData, 'country');
                        } else {
                            // Handle error if geocoding fails
                        }
                    });

                    infowindow.setContent('Place Name: ' + addressElement.value + '<br>Latitude: ' + lat +
                        '<br>Longitude: ' + lng);
                    infowindow.open(map, marker);
                });

                marker.addListener('dragend', function(event) {
                    var lat = event.latLng.lat();
                    var lng = event.latLng.lng();

                    latElement.value = lat;
                    lonElement.value = lng;

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        location: event.latLng
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            var placeData = results[0];
                            addressElement.value = placeData.formatted_address;
                            cityElement.value = getComponentValue(placeData, 'locality');
                            zipCodeElement.value = getComponentValue(placeData, 'postal_code');
                            stateElement.value = getComponentValue(placeData,
                                'administrative_area_level_1');
                            countryElement.value = getComponentValue(placeData, 'country');
                        } else {
                            // Handle error if geocoding fails
                        }
                    });

                    infowindow.setContent('Place Name: ' + addressElement.value + '<br>Latitude: ' + lat +
                        '<br>Longitude: ' + lng);
                    infowindow.open(map, marker);
                });
            }

            function getComponentValue(placeData, componentType) {
                for (var i = 0; i < placeData.address_components.length; i++) {
                    var component = placeData.address_components[i];
                    for (var j = 0; j < component.types.length; j++) {
                        if (component.types[j] === componentType) {
                            return component.long_name;
                        }
                    }
                }
                return '';
            }

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                "use strict";
                if ($(".trumEdit1")[0]) {
                    ClassicEditor
                        .create(document.querySelector('.trumEdit1'))
                        .then(editor => {
                            window.editor = editor;
                        });
                }
            });
        })(jQuery);
    </script>

    <script>
        "use strict";
        function imageDelete(object, $id) {
            var url = "{{ route('admin.tour.package.image.delete') }}";
            var token = '{{ csrf_token() }}';
            var id = $id;
            var data = {
                id: id,
                _token: token
            }
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(data) {

                },
                error: function(data, status, error) {
                    $.each(data.responseJSON.errors, function(key, item) {
                        Toast.fire({
                            icon: 'error',
                            title: item
                        })
                    });
                }
            });
        }
    </script>
@endpush


