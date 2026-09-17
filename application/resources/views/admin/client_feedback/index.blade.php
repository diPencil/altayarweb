@extends('admin.layouts.app')
@section('panel')
<div class="table-responsive">
    <table class="table table--light style--two">
        <thead><tr><th>@lang('Name')</th><th>@lang('Profession')</th><th>@lang('City')</th><th>@lang('Comment')</th><th>@lang('Rating')</th><th>@lang('Status')</th><th>@lang('Action')</th></tr></thead>
        <tbody>
            @forelse($feedback as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->profession }}</td>
                    <td>{{ $item->city }}</td>
                    <td style="min-width: 220px; max-width: 420px; white-space: normal; overflow-wrap: anywhere;">{{ $item->comment }}</td>
                    <td>{{ $item->rating }} / 5</td>
                    <td><span class="badge {{ $item->is_approved ? 'badge--success' : 'badge--warning' }}">{{ $item->is_approved ? __('Approved') : __('Pending') }}</span></td>
                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            @unless($item->is_approved)
                                <form method="POST" action="{{ route('admin.client-feedback.approve', $item->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline--success" title="@lang('Approve')" aria-label="@lang('Approve')"><i class="las la-check"></i></button>
                                </form>
                            @endunless
                            <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.client-feedback.edit', $item->id) }}" title="@lang('Edit')" aria-label="@lang('Edit')"><i class="las la-edit"></i></a>
                            <form method="POST" action="{{ route('admin.client-feedback.delete', $item->id) }}" onsubmit="return confirm(@js(__('Delete this feedback permanently?')))">
                                @csrf
                                <button class="btn btn-sm btn-outline--danger" title="@lang('Delete')" aria-label="@lang('Delete')"><i class="las la-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">@lang('No feedback yet')</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $feedback->links() }}
@endsection
