@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-12">
            <div class="base--card radius--20 p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="mb-1">@lang('Membership Benefits')</h4>
                        <p class="text-muted mb-0">@lang('View your active membership additions, usage, and remaining benefits.')</p>
                    </div>
                    @if($currentMembership)
                        <span class="badge bg--success">@lang('Current Plan'): {{ is_rtl() && $currentMembership->plan?->name_ar ? $currentMembership->plan?->name_ar : $currentMembership->plan?->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Benefit')</th>
                                    <th>@lang('Addition Details')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Used')</th>
                                    <th>@lang('Remaining')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Expiry Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($benefits as $benefit)
                                    <tr>
                                        <td>{{ $loop->iteration + ($benefits->currentPage()-1) * $benefits->perPage() }}</td>
                                        <td>{{ $benefit->title }}</td>
                                        <td>{{ $benefit->description ?? '-' }}</td>
                                        <td>{{ $benefit->total_quantity }}</td>
                                        <td>{{ $benefit->used_quantity }}</td>
                                        <td>{{ $benefit->remaining_quantity }}</td>
                                        <td>{!! $benefit->status ? '<span class="badge bg--success text-white">' . __('Active') . '</span>' : '<span class="badge bg--danger text-white">' . __('Inactive') . '</span>' !!}</td>
                                        <td>{{ $benefit->expires_at ? showDateTime($benefit->expires_at, 'd M Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="9">@lang('No membership additions assigned to your account.')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($benefits) }}
                </div>
            </div>
        </div>
    </div>
@endsection

