@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="base--card">
                <form class="navbar-search">
                    <input type="text" name="" id="locationInput"
                        class="controls my-2 form-control form--control pills" placeholder="@lang('Enter a location')"
                        autocomplete="off">
                </form>
                <form action="{{ route('employee.tour.package.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <h5>@lang('Basic Information')</h5>
                        <div id="map" class="mb-3"></div>
                        <div class="row d-none">
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_id" class="form-control" hidden
                                        value="{{ auth('employee')->id() }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_type" class="form-control" hidden value="Employee">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lat" name="latitude" class="form-control" hidden
                                        value="{{ old('latitude') }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lon" name="longitude" class="form-control"
                                        value="{{ old('longitude') }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="city" name="city" class="form-control"
                                        value="{{ old('city') }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="zipCode" name="zipcode" class="form-control"
                                        value="{{ old('zipcode') }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="state" name="state" class="form-control"
                                        value="{{ old('state') }}" hidden>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="country" name="country" class="form-control"
                                        value="{{ old('country') }}" hidden>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Title')</label>
                                    <input type="text" name="tour_title" class="form-control form--control"
                                        placeholder="@lang('Title')" value="{{ old('tour_title') }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="location"class=" mb-2 form--label">@lang('Address')</label>
                                    <input type="text" id="location" name="address" class="form-control form--control"
                                        value="{{ old('address') }}" required readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Category')</label>
                                    <select name="category_id" id="status" class="form--control form--select" required>
                                        <option>@lang('Select category')</option>
                                        @foreach ($categories ?? [] as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == old('category_id') ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Flexible Date')</label>
                                    <select name="flexible_date" id="status" class="form--control form--select"
                                        required>
                                        <option>@lang('Select flexible date')</option>
                                        <option value="1">@lang('Fixed Date')</option>
                                        <option value="2">@lang('Custom Date')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Tour Start Date') </label>
                                    <input type="text" name="start_date"
                                        class="form-control form--control datepicker-active" autocomplete="off"
                                        data-language="en" placeholder="@lang('Start date')"
                                        value="{{ old('start_date') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Tour End Date')</label>
                                    <input type="text" name="end_date"
                                        class="form-control form--control datepicker-active" autocomplete="off"
                                        data-language="en" placeholder="@lang('End date')"
                                        value="{{ old('end_date') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Person Capability')</label>
                                    <input type="number" step="any" name="person_capability"
                                        class="form-control form--control" placeholder="@lang('Person Capability')" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Stay day & nights')</label>
                                    <input type="text" step="any" name="day_nights"
                                        class="form-control form--control" placeholder="@lang('3 day & 2 nights')" required>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Price')</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form--control" placeholder="Price"
                                            name="price" aria-label="price" aria-describedby="basic-addon2"
                                            value="{{ old('price') }}" required>
                                        <span class="input-group-text" id="basic-addon2">{{ gs()->cur_sym }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Discount')</label>
                                    <input type="number" step="any" name="discount"
                                        class="form-control form--control" placeholder="@lang('Discount')">
                                </div>
                            </div>

                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="images">@lang('Images')</label>
                                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg"
                                        multiple class="form-control form--control" required>
                                </div>
                                <div class="form-group">
                                    <div id="image_preview" class="image_preview-wrapper">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Description')</label>
                                    <textarea name="description" class="trumEdit1 form--control" placeholder="@lang('Description')">{{ old('description') }}</textarea>
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
                                        placeholder="@lang('Departure From')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Departure From Arabic')</label>
                                    <input type="text" name="departure_from_ar" id="departure_from_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" placeholder="@lang('Departure From Arabic')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Arrival')</label>
                                    <input type="text" name="destination_overview[arrival]" id="arrival"
                                        class="form-control form--control mb-0" required
                                        placeholder="@lang('Arrival')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Arrival Arabic')</label>
                                    <input type="text" name="arrival_ar" id="arrival_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" placeholder="@lang('Arrival Arabic')" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Transportation')</label>
                                    <input type="text" name="destination_overview[transportation]" id="transportation"
                                        class="form-control form--control mb-0" required
                                        placeholder="@lang('Transportation')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Transportation Arabic')</label>
                                    <input type="text" name="transportation_ar" id="transportation_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" placeholder="@lang('Transportation Arabic')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Accommodation')</label>
                                    <input type="text" name="destination_overview[accommodation]" id="accommodation"
                                        class="form-control form--control mb-0" required
                                        placeholder="@lang('Accommodation')" />
                                </div>
                            </div>
                            <div class="col-sm-6" dir="rtl">
                                <div class="file-upload">
                                    <label class="mb-2 form--label d-block text-end">@lang('Accommodation Arabic')</label>
                                    <input type="text" name="accommodation_ar" id="accommodation_ar" dir="rtl" style="text-align:right;"
                                        class="form-control form--control mb-0" placeholder="@lang('Accommodation Arabic')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="mb-0">@lang('Destination Information')</h5>

                                        <button type="button" class=" btn btn--base btn-md action--btn addHighlights">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 my-2">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination Highlights')</label>
                                                <input type="text" name="highlights[]" id="highlights"
                                                    class="form-control form--control mb-0" required
                                                    placeholder="@lang('Destination Highlights')" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fileUploadsContainer">

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-4">
                                <div class="form-group">
                                    <div class="text-end">
                                        <button type="button" class="btn btn--base btn-md action--btn addFeatures">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination icons')</label>
                                                <div class="file-upload input-group">
                                                    <input type="text" name="icons[]" id="inputIcon"
                                                        class="form-control form--control iconPicker icon" value=""
                                                        autocomplete="off" placeholder="@lang('Icons')" required>
                                                    <span class="input-group-text input-group-addon bg--base text--white"
                                                        data-icon="las la-home"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 my-2">
                                            <div class="file-upload">
                                                <label class="form-label">@lang('Destination Features')</label>
                                                <input type="text" name="features[]" id="features"
                                                    class="form-control form--control mb-0" required
                                                    placeholder="@lang('Destination Features')" />
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fileUploadFeatures">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="text-end">
                            <button type="submit" class="btn btn--base btn--lg pills">@lang('Submit')</button>
                        </div>
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
                                class="form-control form--control  iconPicker icon" placeholder="@lang('Icons')" required>
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

                $('#image_preview').html("");
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
                            i + "' class= 'delete-btn btn btn--danger' role='" + total_file[i].name +
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

            window.initMap = function() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: -33.8688,
                        lng: 151.2195
                    },
                    zoom: 13
                });

                var input = document.getElementById('locationInput');
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    anchorPoint: new google.maps.Point(0, -29),
                    title: "This marker is draggable."
                });

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
@endpush


