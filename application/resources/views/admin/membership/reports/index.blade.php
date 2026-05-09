@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card b-radius--10">
                <div class="card-header bg--primary text-white">
                    <h6 class="mb-0">@lang('Points Transactions')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Points')</th>
                                    <th>@lang('Balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($points as $row)
                                    <tr>
                                        <td>{{ $row->user?->username }}</td>
                                        <td>{{ ucfirst($row->type) }}</td>
                                        <td>{{ $row->points }}</td>
                                        <td>{{ $row->balance_after }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card b-radius--10">
                <div class="card-header bg--success text-white">
                    <h6 class="mb-0">@lang('Cashback Transactions')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Balance')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashbacks as $row)
                                    <tr>
                                        <td>{{ $row->user?->username }}</td>
                                        <td>{{ ucfirst($row->type) }}</td>
                                        <td>{{ showAmount($row->amount) }}</td>
                                        <td>{{ showAmount($row->balance_after) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
