@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="mb-0">@lang('Cashback Transactions')</h6>
                    <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#cashbackAddModal">
                        <i class="las la-plus"></i> @lang('Add Cashback')
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashbacks as $row)
                                    <tr>
                                        <td>{{ $row->user?->username }}</td>
                                        <td>{{ $row->trx ?? __('-') }}</td>
                                        <td>{{ match (strtolower((string) $row->type)) {
                                            'earned' => __('Earned'),
                                            'used' => __('Used'),
                                            default => __(ucfirst((string) $row->type)),
                                        } }}</td>
                                        <td>{{ showAmount($row->amount) }}</td>
                                        <td>{{ showAmount($row->balance_after) }}</td>
                                        <td>{{ showDateTime($row->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($cashbacks->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cashbacks) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="cashbackAddModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Cashback')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.membership.cashback.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>@lang('User')</label>
                            <select name="user_id" class="form-control select2-basic" required>
                                <option value="">@lang('Select User')</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Amount')</label>
                            <input type="number" step="any" min="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea name="remark" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <form action="" method="GET" class="form-inline">
            <div class="input-group justify-content-end">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by Username or Trx')" value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush

@push('script')
    <script>
        (function ($) {
            'use strict';
            $('#cashbackAddModal .select2-basic').select2({
                dropdownParent: $('#cashbackAddModal')
            });
        })(jQuery);
    </script>
@endpush
