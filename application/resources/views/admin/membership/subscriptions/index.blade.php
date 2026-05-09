@extends('admin.layouts.app')
@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive admin-table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Plan')</th>
                            <th>@lang('Start')</th>
                            <th>@lang('End')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->user?->username }}<br><small class="text-muted">{{ $subscription->user?->email }}</small></td>
                                <td>{{ is_rtl() && $subscription->plan?->name_ar ? $subscription->plan?->name_ar : $subscription->plan?->name }}</td>
                                <td>{{ showDateTime($subscription->start_date, 'd M Y') }}</td>
                                <td>{{ $subscription->end_date ? showDateTime($subscription->end_date, 'd M Y') : __('Lifetime') }}</td>
                                <td>
                                    <span class="badge {{ $subscription->status == 1 ? 'badge--success' : ($subscription->status == 0 ? 'badge--warning' : 'badge--danger') }}">
                                        {{ $subscription->status == 1 ? __('Active') : ($subscription->status == 0 ? __('Pending') : __('Expired')) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.membership.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn--primary">
                                        <i class="la la-edit"></i> @lang('Edit')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($subscriptions->hasPages())
            <div class="card-footer">{{ $subscriptions->links() }}</div>
        @endif
    </div>
@endsection
