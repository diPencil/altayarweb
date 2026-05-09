@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12">
            <div class="base--card">
                <div class="text-end mb-3">
                    <a href="{{route('ticket') }}" class="btn btn--lg btn--base mb-2 pills">@lang('My Support Ticket')</a>
                </div>
                <form action="{{route('ticket.store')}}" method="post" enctype="multipart/form-data"
                    onsubmit="return submitUserForm();">
                    @csrf
                    <div class="row gy-3">

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Name')</label>
                                <input type="text" name="name" value="{{$user->firstname . ' ' . $user->lastname}}"
                                    class="form-control form--control" required readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Email Address')</label>
                                <input type="email" name="email" value="{{$user->email}}" class="form-control form--control"
                                    required readonly>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Subject')</label>
                                <input type="text" name="subject" value="{{old('subject')}}" class="form-control form--control"
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Priority')</label>
                                <select name="priority" class="form-control form--control form--select" required>
                                    <option value="3">@lang('High')</option>
                                    <option value="2">@lang('Medium')</option>
                                    <option value="1">@lang('Low')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form--label">@lang('Message')</label>
                                <textarea name="message" id="inputMessage" rows="6" class="form-control form--control"
                                    required>{{old('message')}}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="text-end">
                                    <button type="button" class="btn btn--base btn--md pills  addFile">
                                        <i class="fa fa-plus"></i> @lang('Add New')
                                    </button>
                                </div>
                                <div class="file-upload">
                                    <label class="form--label">@lang('Attachments')</label>
                                    <input type="file" name="attachments[]" id="inputAttachments"
                                        class="form-control form--control mb-2" />
                                    <div id="fileUploadsContainer"></div>
                                    <small class="ticket-attachments-message text-muted">
                                        @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'),
                                        .@lang('pdf'), .@lang('doc'), .@lang('docx')
                                    </small>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="text-end">
                                <button class="btn btn--base btn--lg pills" type="submit" id="recaptcha">@lang('Submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            var fileAdded = 0;
            $('.addFile').on('click', function () {
                if (fileAdded >= 4) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                        <div class="input-group my-3">
                            <input type="file" name="attachments[]" class="form-control form--control" required />
                            <button class="input-group-text btn-danger remove-btn"><i class="las la-times"></i></button>
                        </div>
                    `)
            });
            $(document).on('click', '.remove-btn', function () {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
    </script>
@endpush
