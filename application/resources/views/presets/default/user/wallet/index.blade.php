@extends($activeTemplate . 'layouts.user.master')
@section('content')
<div class="row gy-4">
    <div class="col-lg-4">
        <div class="base--card p-4 wallet-card">
            <h5 class="mb-3">@lang('Wallet Balance')</h5>
            <div class="wallet-card__member-info mb-3">
                <div class="wallet-card__member-row">
                    <span class="wallet-card__member-label">@lang('Name')</span>
                    <strong class="wallet-card__member-value">{{ $user->fullname }}</strong>
                </div>
                <div class="wallet-card__member-row">
                    <span class="wallet-card__member-label">@lang('Membership')</span>
                    <strong class="wallet-card__member-value">
                        {{ $currentMembership?->plan ? (is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name) : __('Not Activated') }}
                    </strong>
                </div>
                <div class="wallet-card__member-row">
                    <span class="wallet-card__member-label">@lang('Member ID')</span>
                    <strong class="wallet-card__member-value">{{ $currentMembership?->member_code ?? '-' }}</strong>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap bg--primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="las la-wallet fs--24"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ gs()->cur_sym }}{{ showAmount($user->balance) }}</h3>
                </div>
            </div>
            <hr>
            <div class="d-flex flex-column gap-2">
                <button type="button" class="btn btn--base w-100 openModal" data-type="use">
                    <i class="las la-hand-holding-usd"></i> @lang('Use Funds')
                </button>
                <button type="button" class="btn btn--danger w-100 openModal" data-type="refund" style="background-color: #ea5455; border-color: #ea5455; color: #fff;">
                    <i class="las la-reply"></i> @lang('Request Refund')
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="base--card p-0">
            <div class="card-header bg-transparent border-bottom p-4">
                <h5 class="mb-0">@lang('Wallet Requests')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table custom--table">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td>{{ showDateTime($request->created_at) }}</td>
                                <td>{{ gs()->cur_sym }}{{ showAmount($request->amount) }}</td>
                                <td>
                                    @if($request->type == 'use')
                                        <span class="badge badge--primary">@lang('Allocation/Use')</span>
                                    @else
                                        <span class="badge badge--info">@lang('Refund')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->status == 0)
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @elseif($request->status == 1)
                                        <span class="badge badge--success">@lang('Approved')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Rejected')</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn--sm btn--base viewDetail" data-details="{{ $request->details }}" data-feedback="{{ $request->admin_feedback }}">
                                        <i class="las la-desktop"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-center text-muted">@lang('No wallet requests found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($requests->hasPages())
            <div class="card-footer bg-transparent p-4">
                {{ paginateLinks($requests) }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('style')
<style>
    .wallet-card__member-info {
        padding: 14px 16px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(91, 156, 249, 0.09), rgba(91, 156, 249, 0.03));
        border: 1px solid rgba(91, 156, 249, 0.12);
    }

    .wallet-card__member-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .wallet-card__member-row + .wallet-card__member-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
    }

    .wallet-card__member-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        flex: 0 0 auto;
    }

    .wallet-card__member-value {
        color: #173c36;
        font-size: 14px;
        font-weight: 700;
        text-align: end;
        overflow-wrap: anywhere;
    }
</style>
@endpush

{{-- Submit Modal --}}
<div id="submitModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.wallet.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="type">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>@lang('Amount')</label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" class="form-control" required>
                            <span class="input-group-text">{{ gs()->cur_sym }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('Brief Details')</label>
                        <textarea name="details" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base w-100">@lang('Confirm')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Request Details')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>@lang('My Request Details'):</h6>
                <p class="user-details mb-4 text-muted"></p>
                <h6>@lang('Admin Feedback'):</h6>
                <p class="admin-feedback text-muted"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
        "use strict";
        $('.openModal').on('click', function(){
            var modal = $('#submitModal');
            var type = $(this).data('type');
            modal.find('input[name=type]').val(type);
            if(type == 'use'){
                modal.find('.modal-title').text("@lang('Request Funds Allocation')");
            }else{
                modal.find('.modal-title').text("@lang('Request Refund to Bank')");
            }
            modal.modal('show');
        });

        $('.viewDetail').on('click', function(){
            var modal = $('#detailModal');
            modal.find('.user-details').text($(this).data('details') || "@lang('No details provided')");
            modal.find('.admin-feedback').text($(this).data('feedback') || "@lang('No feedback yet')");
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
