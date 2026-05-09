@extends($activeTemplate.'layouts.employee.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
       <div class="d-flex justify-content-end">
        <form action="">
            <div class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form--control bg--white" value="{{ request()->search }}"
                        placeholder="@lang('Search by transactions')">
                    <button class="input-group-text bg--base text-white">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>
        </form>
       </div>

       <div class="base--card radius--20">
         <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('TRX')</th>
                    <th class="text-center">@lang('Gateway')</th>
                    <th class="text-center">@lang('Initiated')</th>
                    <th class="text-center">@lang('Amount')</th>
                    <th class="text-center">@lang('Conversion')</th>
                    <th class="text-center">@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>

                @forelse($withdraws as $withdraw)
                <tr>
                    <td data-label="@lang('Gateway')">
                        <span class="">
                            <span class="">#{{ $withdraw->trx}}</span></span>
                    </td>
                    <td data-label="@lang('Gateway')">
                        <span class=""><span class="text--base"> {{ __($withdraw->method->name) }}</span></span>
                    </td>
                    <td data-label="@lang('Initiated')" class="text-center">
                        <i class="fa-regular fa-clock"></i>
                        {{ showDateTime($withdraw->created_at) }} </td>
                    <td data-label="@lang('Amount')" class="text-center"> {{ __($general->cur_sym) }}{{ showAmount($withdraw->amount ) }}</td>
                    <td data-label="@lang('Conversion')" class="text-center">
                        {{ showAmount($withdraw->final_amount) }} {{ __($withdraw->currency)}}
                    </td>
                    <td data-label="@lang('Status')" class="text-center">
                        @php echo $withdraw->statusBadge @endphp
                    </td>
                    <td data-label="@lang('Action')">
                        <button class="btn btn-md btn--base detailBtn action--btn"
                            data-user_data="{{ json_encode($withdraw->withdraw_information) }}"
                            @if ($withdraw->status == 3) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                            <i class="la la-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="text-muted text-center" data-label="@lang('Withdrawal Table')" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>

<div class="row mx-xxl-5 mx-lg-0 my-4">
    <div class="col-lg-12 justify-content-end d-flex">
        @if($withdraws->hasPages())
            {{$withdraws->links()}}
        @endif
    </div>
</div>

{{-- approval modal --}}
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Details')</h5>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <ul class="list-group userData"></ul>
                <div class="feedback"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--danger btn--lg pills" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        $('.detailBtn').on('click', function () {
            var modal = $('#detailModal');
            var userData = $(this).data('user_data');
            var html = ``;
            userData.forEach(element => {
                if (element.type != 'file') {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                }
            });
            modal.find('.userData').html(html);

            if ($(this).data('admin_feedback') != undefined) {
                var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
            } else {
                var adminFeedback = '';
            }
            modal.find('.feedback').html(adminFeedback);
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush


