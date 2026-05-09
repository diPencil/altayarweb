@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-end mb-3">
                <form action="" method="GET" class="search-form dashboard-filter-search" style="max-width: 400px; width: 100%;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Search by Invoice Number...')">
                        <button class="btn btn--base input-group-text" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Invoice Number')</th>
                            <th class="text-center">@lang('Booking No')</th>
                            <th class="text-center">@lang('Amount')</th>
                            <th class="text-center">@lang('Date')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th class="text-start">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td><span class="fw-bold">{{ $invoice->invoice_number }}</span></td>
                                <td class="text-center">
                                    @if($invoice->booking_id)
                                        @lang('Booking') #{{ $invoice->booking_id }}
                                    @else
                                        @lang('Manual/Multi-item')
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ showAmount($invoice->total_amount) }} {{ __($general->cur_text) }}
                                </td>
                                <td class="text-center">
                                    {{ showDateTime($invoice->created_at, 'd M, Y') }}
                                </td>
                                <td class="text-center">
                                    @php echo $invoice->statusBadge @endphp
                                </td>
                                <td class="text-start">
                                    <a href="{{ route('user.invoice.show', $invoice->invoice_number) }}" class="btn btn--base btn-sm">
                                        <i class="fa fa-file-invoice"></i> @lang('View')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($invoices->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $invoices->links() }}
        </div>
    @endif
@endsection
