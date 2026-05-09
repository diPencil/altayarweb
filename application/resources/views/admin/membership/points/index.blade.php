@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h6 class="mb-0">@lang('Points Transactions')</h6>
                    <button type="button" class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#pointAddModal">
                        <i class="las la-plus"></i> @lang('Add Points')
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Points')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($points as $row)
                                    <tr>
                                        <td>{{ $row->user?->username }}</td>
                                        <td>
                                            @php
                                                $plan = $row->plan;
                                                if (!$plan) {
                                                    $plan = $row->user?->currentMembership?->plan;
                                                }
                                                if (!$plan) {
                                                    $plan = $row->user?->memberships->first()?->plan;
                                                }
                                            @endphp
                                            {{ $plan ? (is_rtl() && $plan->name_ar ? $plan->name_ar : __($plan->name)) : __('-') }}
                                        </td>
                                        <td>{{ $row->trx ?? __('-') }}</td>
                                        <td>{{ match (strtolower((string) $row->type)) {
                                            'earned' => __('Earned'),
                                            'used' => __('Used'),
                                            default => __(ucfirst((string) $row->type)),
                                        } }}</td>
                                        <td>{{ $row->points }}</td>
                                        <td>{{ $row->balance_after }}</td>
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
                @if ($points->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($points) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="pointAddModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Points')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.membership.points.store') }}" method="POST">
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
                            <label>@lang('Points')</label>
                            <input type="number" step="1" min="1" name="amount" class="form-control" required>
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
            $('#pointAddModal .select2-basic').select2({
                dropdownParent: $('#pointAddModal')
            });
        })(jQuery);
    </script>
@endpush
