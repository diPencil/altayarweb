@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="row gy">

                        <form action="{{ route('admin.location.update', optional($location)->id) }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="image-upload">
                                                    <div class="thumb">
                                                        <div class="avatar-preview">
                                                            <div class="profilePicPreview"
                                                                style="background-image: url({{ getImage(getFilePath('location') . '/' . optional($location)->image) }})">
                                                                <button type="button" class="remove-image"><i
                                                                        class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="avatar-edit">
                                                            <input type="file" class="profilePicUpload"
                                                                name="image" id="profilePicUpload1"
                                                                accept=".png, .jpg, .jpeg">
                                                            <label for="profilePicUpload1"
                                                                class="bg--primary">@lang('Upload Image')</label>
                                                            <small class="mt-2 fw-10">@lang('Recomended size:')
                                                                {{ getFileSize('location') }}@lang('px'). </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Name')</label>
                                                        <input type="text" name="name" class="form-control"
                                                            placeholder="@lang('Name')" value="{{ optional($location)->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Name (Arabic)')</label>
                                                        <input type="text" name="name_ar" class="form-control"
                                                            placeholder="@lang('Name (Arabic)')" value="{{ optional($location)->name_ar }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Location')</label>
                                                        <div id="map" class="mb-3 d-none"></div>
                                                        <input type="text" name="location" id="locationInput"
                                                            class="controls form-control" value="{{$location->location}}"  placeholder="@lang('Enter a location')"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Location (Arabic)')</label>
                                                        <input type="text" name="location_ar" class="form-control"
                                                            value="{{$location->location_ar}}"  placeholder="@lang('Location (Arabic)')">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Latitude')</label>
                                                        <input type="text" id="lat" name="latitude"
                                                            class="form-control" value="{{ optional($location)->latitude }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Longitude')</label>
                                                        <input type="text" id="lon" name="longitude"
                                                            class="form-control" value="{{ optional($location)->longitude }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Count (e.g. 100+ Destinations)')</label>
                                                        <input type="text" name="count" class="form-control"
                                                            placeholder="@lang('e.g. 100+ Destinations')" value="{{ optional($location)->count }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="mb-2 form--label">@lang('Status')</label>
                                                        <select name="status" id="status" class="form-control"
                                                            required>
                                                            <option>@lang('Select Status')</option>
                                                            <option value="1" {{ optional($location)->status == 1 ? 'selected' : '' }}>@lang('Active')</option>
                                                            <option value="0" {{ optional($location)->status == 0 ? 'selected' : '' }}>@lang('Deactivate')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div></div>
                            </div>
                            <div class="col-lg-12 text-end mt-3">
                                <button type="submit" class="btn btn--primary">@lang('Update')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
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
                    latElement.value = place.geometry.location.lat();
                    lonElement.value = place.geometry.location.lng();
                });

              
            }

    

        })(jQuery);
    </script>
@endpush
