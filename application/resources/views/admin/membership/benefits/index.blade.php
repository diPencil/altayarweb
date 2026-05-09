@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h5 class="card-title mb-0">@lang('Membership Benefits (Grouped by User)')</h5>
                    <div class="d-flex align-items-center gap-2 flex-wrap flex-lg-nowrap ms-auto">
                        <form action="" method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-lg-nowrap mb-0">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control bg--white"
                                    placeholder="@lang('Search user...')"
                                    value="{{ request()->search }}">
                                <button class="btn btn--primary" type="submit">
                                    <i class="la la-search"></i>
                                </button>
                            </div>
                        </form>
                        <a href="{{ route('admin.membership.benefits.create') }}" class="btn btn--primary">
                            <i class="la la-plus"></i> @lang('Create Benefit')
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive admin-table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Current Plan')</th>
                                    <th>@lang('Benefits')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Used')</th>
                                    <th>@lang('Remaining')</th>
                                    <th>@lang('Active')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($benefits as $group)
                                    @php
                                        $remaining = (int) $group->total_qty - (int) $group->used_qty;
                                        $remaining = $remaining > 0 ? $remaining : 0;
                                        $user = $group->user;
                                        $plan = $user?->memberships?->first()?->plan;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration + ($benefits->currentPage() - 1) * $benefits->perPage() }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $user?->username ?? '-' }}</span>
                                        </td>
                                        <td>{{ $plan?->name ?? __('No Plan') }}</td>
                                        <td><span class="badge badge--info">{{ $group->benefits_count }}</span></td>
                                        <td>{{ $group->total_qty }}</td>
                                        <td>{{ $group->used_qty }}</td>
                                        <td>{{ $remaining }}</td>
                                        <td><span class="badge bg--success text-white">{{ $group->active_count }}</span></td>
                                        <td>
                                            @if($group->active_count > 0)
                                                <span class="badge bg--success text-white">@lang('Active')</span>
                                            @else
                                                <span class="badge bg--danger text-white">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button-group">
                                                <a href="{{ route('admin.membership.benefits.user', $group->user_id) }}" class="btn btn-sm btn--primary" title="@lang('View')">
                                                    <i class="la la-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.membership.benefits.create', ['user_id' => $group->user_id]) }}" class="btn btn-sm btn--success" title="@lang('Add')">
                                                    <i class="la la-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No benefits found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($benefits->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($benefits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
