@extends(auth()->check() ? $activeTemplate . 'layouts.user.' . $layout : $activeTemplate . 'layouts.frontend')
@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="base--card">
                <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mt-0 text--black7">
                        @php echo $myTicket->statusBadge; @endphp
                        [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                    </h5>
                    @if ($myTicket->status != 3 && $myTicket->user)
                        <button class="btn btn--danger close-button btn-sm confirmationBtn" type="button"
                            data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}"><i
                                class="fa fa-lg fa-times-circle"></i>
                        </button>
                    @endif
                </div>

                @if ($myTicket->status != 4)
                    <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row gy-3 justify-content-between">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <textarea name="message" class="form-control form--control" rows="4">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <a href="javascript:void(0)" class="btn btn--base btn-md addFile pills"><i
                                            class="fa fa-plus"></i>
                                        @lang('Add New')</a>
                                </div>
                                <div class="form-group">
                                    <label class="form--label">@lang('Attachments')</label>
                                    <input type="file" name="attachments[]" class="form-control form--control" />
                                    <div id="fileUploadsContainer"></div>
                                    <small class="my-2 ticket-attachments-message text-muted">
                                        @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'),
                                        .@lang('pdf'), .@lang('doc'), .@lang('docx')
                                    </small>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn--base btn--lg pills">@lang('Reply')</button>
                            </div>
                        </div>
                    </form>
                @endif
                <div class="mt-5">
                    @foreach ($messages as $message)
                        @if ($message->admin_id == 0 && $message->agent_id == 0)
                            <div class="ticket--replay radius--20 mb-3">
                                <div class="row">
                                    <div class="col-md-12 text-start">
                                        <p class="fs--14 mb-1">
                                            {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                        </p>
                                        <h5 class="mb-3">{{ $message->ticket->name }}</h5>
                                    </div>
                                    <div class="col-md-9">

                                        <p>{{ $message->message }}</p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                        class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                        {{ ++$k }} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif ($message->agent_id != 0)
                            <div class="ticket--replay radius--20 mb-3">
                                <div class="row">
                                    <div class="col-12 text-start">
                                        <p class="fs--14 mb-1">
                                            {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                        </p>
                                        <h5 class="mb-3">{{ $message->employee?->fullname ?? __('Employee') }}</h5>
                                    </div>
                                    <div class="col-md-9">

                                        <p>{{ $message->message }}</p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                        class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                        {{ ++$k }} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="ticket--replay radius--20 mb-3">
                                <div class="row">
                                    <div class="col-12 text-start">
                                        <p class="fs--14 mb-1">
                                            {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                        </p>
                                        <h5 class="mb-3">{{ $message->admin->name }}</h5>
                                    </div>
                                    <div class="col-md-9">

                                        <p>{{ $message->message }}</p>
                                        @if ($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach ($message->attachments as $k => $image)
                                                    <a href="{{ route('ticket.download', encrypt($image->id)) }}"
                                                        class="me-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                        {{ ++$k }} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal></x-confirmation-modal>
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
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFile').on('click', function() {
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
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
    </script>
@endpush
