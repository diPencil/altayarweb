@extends('admin.layouts.app')

@section('panel')
    {{-- Metric Statistics Cards --}}
    <div class="row gy-4 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card prod-p-card background-pattern-white bg--primary">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5 text-white">@lang('Total Available Miles')</h6>
                            <h3 class="m-b-0 text-white">{{ number_format($totalAvailable) }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="dashboard-widget__icon las la-plane text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card prod-p-card background-pattern">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5">@lang('Total Miles Granted')</h6>
                            <h3 class="m-b-0 text--success">+{{ number_format($totalGranted) }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="dashboard-widget__icon las la-arrow-up text--success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card prod-p-card background-pattern">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5">@lang('Total Miles Deducted')</h6>
                            <h3 class="m-b-0 text--danger">-{{ number_format($totalDeducted) }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="dashboard-widget__icon las la-arrow-down text--danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="show-filter mb-3 text-end">
        <button type="button" class="btn btn--primary showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
    </div>
    <div class="card responsive-filter-card mb-4 b-radius--10">
        <div class="card-body">
            <form action="" method="GET">
                <div class="d-flex flex-wrap gap-4">
                    <div class="flex-grow-1">
                        <label>@lang('Search')</label>
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="@lang('Username, Email, TRX, Reference...')">
                    </div>
                    <div class="flex-grow-1">
                        <label>@lang('Operation Type')</label>
                        <select name="type" class="form-control">
                            <option value="">@lang('All Types')</option>
                            <option value="credit" @selected(request()->type === 'credit')>@lang('Credit (Grant)')</option>
                            <option value="debit" @selected(request()->type === 'debit')>@lang('Debit (Deduct)')</option>
                        </select>
                    </div>
                    <div class="flex-grow-1">
                        <label>@lang('Source')</label>
                        <select name="source" class="form-control">
                            <option value="">@lang('All Sources')</option>
                            @foreach($sources as $src)
                                <option value="{{ $src }}" @selected(request()->source === $src)>{{ ucfirst($src) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-grow-1">
                        <label>@lang('Date Range')</label>
                        <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - "
                            data-language="en" class="datepicker-here form-control" data-position='bottom right'
                            placeholder="@lang('Start date - End date')" autocomplete="off" value="{{ request()->date }}">
                    </div>
                    <div class="flex-grow-1 align-self-end d-flex gap-2">
                        <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Apply')</button>
                        <a href="{{ route('admin.membership.miles.index') }}" class="btn btn--dark h-45"><i class="las la-undo"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('TRX / Reference')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Miles')</th>
                                    <th>@lang('Balance After')</th>
                                    <th>@lang('Source & Remark')</th>
                                    <th>@lang('Actor')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $row)
                                    <tr>
                                        <td>
                                            @if($row->user)
                                                <span class="fw-bold d-block">{{ $row->user->fullname }}</span>
                                                <a href="{{ route('admin.users.detail', $row->user_id) }}" class="text--primary small">
                                                    {{ '@' . $row->user->username }}
                                                </a>
                                            @else
                                                <span class="text-muted">@lang('Deleted User') (#{{ $row->user_id }})</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold d-block">{{ $row->trx }}</span>
                                            @if($row->reference_id)
                                                <small class="text-muted">@lang('Ref'): {{ $row->reference_id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->is_reversal)
                                                <span class="badge badge--warning">@lang('Reversal')</span>
                                            @elseif($row->type === 'credit')
                                                <span class="badge badge--success">@lang('Credit (+)')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Debit (-)')</span>
                                            @endif

                                            @if($row->is_reversed)
                                                <span class="badge badge--dark d-block mt-1">@lang('Reversed')</span>
                                            @endif
                                        </td>
                                        <td class="{{ $row->direction === 1 ? 'text--success' : 'text--danger' }} fw-bold">
                                            {{ $row->direction === 1 ? '+' : '-' }}{{ number_format($row->miles) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($row->balance_after) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--light text-dark mb-1">{{ ucfirst($row->source) }}</span>
                                            <div class="small text-muted" title="{{ $row->remark }}">{{ Str::limit($row->remark, 30) }}</div>
                                        </td>
                                        <td>
                                            @if($row->admin)
                                                <span class="badge badge--primary" title="@lang('Admin')"><i class="las la-user-shield"></i> {{ $row->admin->username }}</span>
                                            @elseif($row->employee)
                                                <span class="badge badge--info" title="@lang('Employee')"><i class="las la-user-tie"></i> {{ $row->employee->username }}</span>
                                            @else
                                                <span class="badge badge--secondary">@lang('System')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($row->created_at, 'd M Y, h:i A') }}
                                            <br>
                                            <small class="text-muted">{{ diffForHumans($row->created_at) }}</small>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                {{-- View Details Modal Trigger --}}
                                                <button type="button" class="btn btn-sm btn-outline--primary detailBtn"
                                                    data-trx="{{ $row->trx }}"
                                                    data-user="{{ $row->user?->username ?? 'N/A' }}"
                                                    data-user-name="{{ $row->user?->fullname ?? 'N/A' }}"
                                                    data-type="{{ ucfirst($row->type) }}"
                                                    data-direction="{{ $row->direction === 1 ? '+ (Credit)' : '- (Debit)' }}"
                                                    data-miles="{{ number_format($row->miles) }}"
                                                    data-balance-after="{{ number_format($row->balance_after) }}"
                                                    data-source="{{ ucfirst($row->source) }}"
                                                    data-ref="{{ $row->reference_id ?? '-' }}"
                                                    data-remark="{{ $row->remark }}"
                                                    data-actor="{{ $row->actor_name }}"
                                                    data-date="{{ showDateTime($row->created_at, 'd M Y, h:i A') }}"
                                                    data-is-reversed="{{ $row->is_reversed ? 'Yes' : 'No' }}"
                                                    data-reversed-at="{{ $row->reversed_at ? showDateTime($row->reversed_at, 'd M Y, h:i A') : '-' }}"
                                                    data-is-reversal="{{ $row->is_reversal ? 'Yes' : 'No' }}"
                                                    data-reversal-of="{{ $row->reversalOf?->trx ?? '-' }}"
                                                    title="@lang('Details')">
                                                    <i class="las la-desktop"></i>
                                                </button>

                                                {{-- Reverse Action Button --}}
                                                @if(adminCanMiles('reverse') && !$row->is_reversed && !$row->is_reversal)
                                                    <button type="button" class="btn btn-sm btn-outline--danger reverseBtn"
                                                        data-id="{{ $row->id }}"
                                                        data-trx="{{ $row->trx }}"
                                                        data-miles="{{ number_format($row->miles) }}"
                                                        data-type="{{ $row->type }}"
                                                        data-user="{{ $row->user?->username ?? 'User' }}"
                                                        title="@lang('Reverse Transaction')">
                                                        <i class="las la-undo"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center py-4" colspan="100%">
                                            <i class="las la-inbox fs--40 d-block mb-2 text-muted"></i>
                                            {{ __($emptyMessage ?? 'No miles transactions found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal 1: Grant Miles Modal --}}
    @if(adminCanMiles('grant'))
    <div id="grantMilesModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus-circle text--success me-1"></i> @lang('Grant Miles')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.membership.miles.operate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="act" value="grant">
                    <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="required">@lang('User')</label>
                            <select name="user_id" class="form-control select2-basic" required>
                                <option value="">@lang('Select User')</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->username }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="required">@lang('Miles Amount')</label>
                            <input type="number" step="1" min="1" max="10000000" name="amount" class="form-control" placeholder="@lang('e.g. 500')" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Source')</label>
                            <input type="text" name="source" class="form-control" value="admin" placeholder="@lang('e.g. admin, promotional, campaign')">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Reference ID / Booking ID (Optional)')</label>
                            <input type="text" name="reference_id" class="form-control" placeholder="@lang('e.g. BOOK-10294')">
                        </div>
                        <div class="form-group">
                            <label class="required">@lang('Reason / Remark')</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="@lang('State reason for granting miles...')" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary btn-global"><i class="las la-check"></i> @lang('Confirm Grant')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal 2: Deduct Miles Modal --}}
    @if(adminCanMiles('deduct'))
    <div id="deductMilesModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-minus-circle text--danger me-1"></i> @lang('Deduct Miles')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.membership.miles.operate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="act" value="deduct">
                    <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="required">@lang('User')</label>
                            <select name="user_id" class="form-control select2-basic" required>
                                <option value="">@lang('Select User')</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->username }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="required">@lang('Miles Amount')</label>
                            <input type="number" step="1" min="1" max="10000000" name="amount" class="form-control" placeholder="@lang('e.g. 200')" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Source')</label>
                            <input type="text" name="source" class="form-control" value="admin" placeholder="@lang('e.g. admin, penalty, adjustment')">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Reference ID / Booking ID (Optional)')</label>
                            <input type="text" name="reference_id" class="form-control" placeholder="@lang('e.g. BOOK-10294')">
                        </div>
                        <div class="form-group">
                            <label class="required">@lang('Reason / Remark')</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="@lang('State reason for deducting miles...')" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--danger btn-global"><i class="las la-check"></i> @lang('Confirm Deduction')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal 3: Reverse Transaction Confirmation Modal --}}
    @if(adminCanMiles('reverse'))
    <div id="reverseMilesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-undo text--warning me-1"></i> @lang('Reverse Mileage Transaction')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form id="reverseMilesForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="las la-exclamation-triangle fs--24 me-2"></i>
                            <div>
                                @lang('You are about to reverse transaction') <strong id="reverseTrxCode"></strong> @lang('for user') <strong id="reverseUsername"></strong>.
                                <br>
                                <small>@lang('An opposing compensating transaction will be created. The original transaction and complete audit trail will be permanently preserved.')</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required">@lang('Reversal Reason')</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="@lang('Mandatory reason for this reversal...')" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--warning btn-global"><i class="las la-undo"></i> @lang('Confirm Reversal')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal 4: Transaction Details Modal --}}
    <div id="detailMilesModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-info-circle text--primary me-1"></i> @lang('Transaction Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('TRX Number')</strong>
                            <span id="dtTrx" class="fw-bold"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('User')</strong>
                            <span id="dtUser"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Type & Direction')</strong>
                            <span id="dtType"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Miles Amount')</strong>
                            <span id="dtMiles" class="fw-bold fs--16"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Balance After Snapshot')</strong>
                            <span id="dtBalanceAfter" class="fw-bold"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Source')</strong>
                            <span id="dtSource"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Reference ID')</strong>
                            <span id="dtRef"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Initiated By (Actor)')</strong>
                            <span id="dtActor"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Date & Time')</strong>
                            <span id="dtDate"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Is Reversed?')</strong>
                            <span id="dtIsReversed"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Reversed At')</strong>
                            <span id="dtReversedAt"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Is Compensating Reversal?')</strong>
                            <span id="dtIsReversal"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>@lang('Reversal Of Original TRX')</strong>
                            <span id="dtReversalOf"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>@lang('Remark')</strong>
                            <p id="dtRemark" class="mt-1 mb-0 text-muted"></p>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap gap-2">
        @if(adminCanMiles('grant'))
            <button type="button" class="btn btn--success" data-bs-toggle="modal" data-bs-target="#grantMilesModal">
                <i class="las la-plus-circle"></i> @lang('Grant Miles')
            </button>
        @endif
        @if(adminCanMiles('deduct'))
            <button type="button" class="btn btn--danger" data-bs-toggle="modal" data-bs-target="#deductMilesModal">
                <i class="las la-minus-circle"></i> @lang('Deduct Miles')
            </button>
        @endif
    </div>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            // Initialize datepicker
            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }

            // Initialize Select2 with modal dropdownParent to fix search input typing/focus
            $('#grantMilesModal .select2-basic').select2({
                dropdownParent: $('#grantMilesModal')
            });
            $('#deductMilesModal .select2-basic').select2({
                dropdownParent: $('#deductMilesModal')
            });

            // Handle Reversal Modal Trigger
            $('.reverseBtn').on('click', function () {
                let id = $(this).data('id');
                let trx = $(this).data('trx');
                let username = $(this).data('user');
                let actionUrl = "{{ route('admin.membership.miles.reverse', ':id') }}".replace(':id', id);

                $('#reverseTrxCode').text(trx);
                $('#reverseUsername').text(username);
                $('#reverseMilesForm').attr('action', actionUrl);
                $('#reverseMilesModal').modal('show');
            });

            // Handle Detail Modal Trigger
            $('.detailBtn').on('click', function () {
                let modal = $('#detailMilesModal');
                modal.find('#dtTrx').text($(this).data('trx'));
                modal.find('#dtUser').text($(this).data('user-name') + ' (@' + $(this).data('user') + ')');
                modal.find('#dtType').text($(this).data('type') + ' | ' + $(this).data('direction'));
                modal.find('#dtMiles').text($(this).data('miles'));
                modal.find('#dtBalanceAfter').text($(this).data('balance-after'));
                modal.find('#dtSource').text($(this).data('source'));
                modal.find('#dtRef').text($(this).data('ref'));
                modal.find('#dtActor').text($(this).data('actor'));
                modal.find('#dtDate').text($(this).data('date'));
                modal.find('#dtIsReversed').text($(this).data('is-reversed'));
                modal.find('#dtReversedAt').text($(this).data('reversed-at'));
                modal.find('#dtIsReversal').text($(this).data('is-reversal'));
                modal.find('#dtReversalOf').text($(this).data('reversal-of'));
                modal.find('#dtRemark').text($(this).data('remark'));
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
