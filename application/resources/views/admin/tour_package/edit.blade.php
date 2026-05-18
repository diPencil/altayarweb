@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.tour.package.update', $tourPackage->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        @include('admin.tour_package.partials.form-fields')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

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
    </style>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            'use strict'

            $('.datepicker-active').datepicker({
                minDate: new Date(),
                dateFormat: 'dd-mm-yyyy'
            });

            $(".iconPicker").iconpicker();

            let highlightIndex = $('#fileUploadsContainer .highlight-row').length;
            let featureIndex = $('#fileUploadFeatures .feature-row').length;
            let itineraryIndex = $('#itineraryDayContainer .itinerary-row').length;

            $('.addHighlights').on('click', function() {
                highlightIndex += 1;
                $('#fileUploadsContainer').append(`
                    <div class="row highlight-row mt-2">
                        <div class="col-sm-6">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination Highlights')</label>
                                <input type="text" name="highlights[]" class="form-control form--control mb-0" placeholder="@lang('Destination Highlights')" />
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination Highlights Arabic')</label>
                                <input type="text" name="highlights_ar[]" class="form-control form--control mb-0" placeholder="@lang('Destination Highlights Arabic')" />
                            </div>
                        </div>
                        <div class="col-sm-1 my-2 d-flex align-items-center">
                            <button type="button" class="btn btn--danger btn--sm remove-btn border-0"><i class="las la-times"></i></button>
                        </div>
                    </div>
                `);
            });

            $('.addFeatures').on('click', function() {
                featureIndex += 1;
                $('#fileUploadFeatures').append(`
                    <div class="row feature-row mt-2">
                        <div class="col-sm-3">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination icons')</label>
                                <div class="file-upload input-group">
                                    <input type="text" name="icons[]" class="form-control form--control iconPicker icon" placeholder="@lang('Icons')" />
                                    <span class="input-group-text input-group-addon" data-icon="las la-home"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination Features')</label>
                                <input type="text" name="features[]" class="form-control form--control mb-0" placeholder="@lang('Destination Features')" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="file-upload">
                                <label class="form-label">@lang('Destination Features Arabic')</label>
                                <input type="text" name="features_ar[]" class="form-control form--control mb-0" placeholder="@lang('Destination Features Arabic')" />
                            </div>
                        </div>
                        <div class="col-sm-1 my-2 d-flex align-items-center">
                            <button type="button" class="btn btn--danger btn--sm remove-btn border-0"><i class="las la-times"></i></button>
                        </div>
                    </div>
                `);
                $(".iconPicker").iconpicker();
            });

            $('.addItineraryDay').on('click', function() {
                itineraryIndex += 1;
                $('#itineraryDayContainer').append(`
                    <div class="itinerary-row border rounded-3 p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-12 col-lg-3">
                                <label class="form-label">@lang('Day Number / Label')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][day_number]" class="form-control" placeholder="@lang('Day 1')" />
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Day Title')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][title]" class="form-control" placeholder="@lang('Day Title')" />
                            </div>
                            <div class="col-12 col-lg-5" dir="rtl">
                                <label class="form-label d-block text-end">@lang('Day Title Arabic')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][title_ar]" dir="rtl" class="form-control text-end" placeholder="@lang('Day Title Arabic')" />
                            </div>

                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Itinerary Day Image')</label>
                                <div class="ratio ratio-16x9 rounded-3 overflow-hidden border bg-light mb-2">
                                    <div class="d-flex align-items-center justify-content-center text-muted small">@lang('No image selected')</div>
                                </div>
                                <input type="file" name="itinerary_days[${itineraryIndex}][image_file]" class="form-control" accept=".png,.jpg,.jpeg,.webp">
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Day Description')</label>
                                <textarea name="itinerary_days[${itineraryIndex}][description]" class="form-control" rows="4" placeholder="@lang('Day Description')"></textarea>
                            </div>
                            <div class="col-12 col-lg-4" dir="rtl">
                                <label class="form-label d-block text-end">@lang('Day Description Arabic')</label>
                                <textarea name="itinerary_days[${itineraryIndex}][description_ar]" dir="rtl" class="form-control text-end" rows="4" placeholder="@lang('Day Description Arabic')"></textarea>
                            </div>

                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('City')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][city]" class="form-control" placeholder="@lang('City')" />
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Country')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][country]" class="form-control" placeholder="@lang('Country')" />
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Latitude') (@lang('Optional'))</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][latitude]" class="form-control" placeholder="@lang('Latitude')" />
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Longitude') (@lang('Optional'))</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][longitude]" class="form-control" placeholder="@lang('Longitude')" />
                            </div>
                            <div class="col-12 col-lg-4">
                                <label class="form-label">@lang('Image URL Fallback')</label>
                                <input type="text" name="itinerary_days[${itineraryIndex}][image]" class="form-control" placeholder="@lang('Image URL fallback')" />
                            </div>

                            <div class="col-12 text-end mt-2">
                                <button type="button" class="btn btn--danger btn--sm remove-itinerary-row">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });

            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.row').remove();
            });

            $(document).on('click', '.remove-itinerary-row', function() {
                $(this).closest('.itinerary-row').remove();
            });

            $('#images').on('change', function(event) {
                // Keep the previews of existing images (which contain the old_tour_package_images hidden input)
                // and only remove the previously generated new previews.
                $('#image_preview .img-div').not(':has(input[name="old_tour_package_images[]"])').remove();

                const files = event.target.files || [];

                Array.from(files).forEach(function(file, index) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image_preview').append(`
                            <div class="img-div new-preview" id="new-img-div${index}">
                                <img src="${e.target.result}" class="image img-fluid rounded border img-thumbnail" alt="preview">
                                <div class="middle">
                                    <button type="button" class="btn btn--danger btn--sm remove-new-img" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                });
            });

            $(document).on('click', '.remove-new-img', function() {
                const index = $(this).data('index');
                $(`#new-img-div${index}`).remove();
            });

            if (typeof ClassicEditor !== 'undefined') {
                document.querySelectorAll('.trumEdit1').forEach(function(editor) {
                    ClassicEditor.create(editor).catch(function(error) {
                        console.error(error);
                    });
                });
            }
        });

        function imageDelete(object, id) {
            if (!confirm("Are you sure you want to delete this image?")) {
                return false;
            }
            var url = "{{ route('admin.tour.package.image.delete') }}";
            var token = '{{ csrf_token() }}';
            var data = {
                id: id,
                _token: token
            };
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $(object).remove();
                        notify('success', response.success);
                    } else if (response.error) {
                        notify('error', response.error);
                    }
                },
                error: function(data, status, error) {
                    notify('error', "Something went wrong while deleting the image.");
                }
            });
        }
    </script>
@endpush
