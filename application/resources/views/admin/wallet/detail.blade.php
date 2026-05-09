@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('User Information')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Username')
                        <span class="fw-bold">
                            <a href="{{ route('admin.users.detail', $request->user_id) }}">{{ $request->user->username }}</a>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Current Balance')
                        <span class="fw-bold">{{ showAmount($request->user->balance) }} {{ __(gs()->cur_text) }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card b-radius--10 overflow-hidden box--shadow1 mt-30">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Request Summary')</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Requested Amount')
                        <span class="fw-bold">{{ showAmount($request->amount) }} {{ __(gs()->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Type')
                        @if($request->type == 'use')
                            <span class="badge bg--primary">@lang('Usage/Allocation')</span>
                        @else
                            <span class="badge bg--info">@lang('Refund')</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Date')
                        <span class="fw-bold">{{ showDateTime($request->created_at) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status')
                        @if($request->status == 0)
                            <span class="badge bg--warning">@lang('Pending')</span>
                        @elseif($request->status == 1)
                            <span class="badge bg--success">@lang('Approved')</span>
                        @else
                            <span class="badge bg--danger">@lang('Rejected')</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('User Details')</h5>
                <p>{{ $request->details }}</p>

                @if($request->status == 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <button class="btn btn-outline--success ms-1 actionBtn" data-status="1">
                            <i class="las la-check-circle"></i> @lang('Approve')
                        </button>
                        <button class="btn btn-outline--danger ms-1 actionBtn" data-status="2">
                            <i class="las la-times-circle"></i> @lang('Reject')
                        </button>
                    </div>
                </div>
                @else
                <div class="mt-4 border-top pt-3">
                    <h6>@lang('Admin Feedback'):</h6>
                    <p>{{ $request->admin_feedback ?? __('No feedback provided.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Action Modal --}}
<div id="actionModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirm Action')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.wallet.action') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $request->id }}">
                <input type="hidden" name="status">
                <div class="modal-body">
                    <p class="question"></p>
                    <div class="form-group">
                        <label>@lang('Feedback / Note')</label>
                        <textarea name="admin_feedback" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        $('.actionBtn').on('click', function(){
            var modal = $('#actionModal');
            var status = $(this).data('status');
            modal.find('input[name=status]').val(status);
            if(status == 1){
                modal.find('.question').text("@lang('Are you sure to approve this wallet request?')");
            }else{
                modal.find('.question').text("@lang('Are you sure to reject this wallet request?')");
            }
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
