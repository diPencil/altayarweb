@extends('admin.layouts.app')

@section('panel')
    <style>
        .chat-list-status--open {
            background: #16a34a;
            color: #fff !important;
        }

        .chat-list-status--closed {
            background: #ef4444;
            color: #fff !important;
        }
    </style>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('#')</th>
                                <th>@lang('Customer')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Unread')</th>
                                <th>@lang('Last Message')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        <div class="fw-bold text--muted">{{ $item->name ?? __('Guest') }}</div>
                                        <div class="text-muted small">{{ $item->email }}</div>
                                    </td>
                                    <td>
                                        @if($item->status === 'closed')
                                            <span class="badge chat-list-status--closed">@lang('Closed')</span>
                                        @else
                                            <span class="badge chat-list-status--open">@lang('Open')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->unread_admin_count > 0)
                                            <span class="badge badge--primary">{{ $item->unread_admin_count }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ strLimit(optional($item->latestMessage)->message ?? '-', 35) }}
                                    </td>
                                    <td>
                                        {{ $item->last_message_at ? showDateTime($item->last_message_at, 'd M Y, h:i A') : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('admin.chat-assistant.view', $item->id) }}" class="btn btn-sm btn--primary" title="@lang('View')" aria-label="@lang('View')">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn--danger confirmationBtn"
                                                title="@lang('Delete')"
                                                aria-label="@lang('Delete')"
                                                data-question="@lang('Delete this conversation?')"
                                                data-action="{{ route('admin.chat-assistant.delete', $item->id) }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
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
            @if ($items->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($items) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
