@extends($activeTemplate.'layouts.employee.master')
@section('content')

<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <div class="text-end">
            <a href="{{route('employee.ticket.open') }}" class="btn btn--lg btn--base mb-2 pills"> <i class="fa fa-plus"></i> @lang('New Ticket')</a>
        </div>
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
            <tr>
                <th>@lang('Subject')</th>
                <th>@lang('Status')</th>
                <th>@lang('Priority')</th>
                <th>@lang('Last Reply')</th>
                <th>@lang('Action')</th>
            </tr>
            </thead>
            <tbody>
                @forelse($supports as $support)
                    <tr>
                        <td data-label="@lang('Subject')"> <a href="{{ route('employee.ticket.view', $support->ticket) }}" class="fw-bold"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                        <td data-label="@lang('Status')">
                            @php echo $support->statusBadge; @endphp
                        </td>
                        <td data-label="@lang('Priority')">
                            @if($support->priority == 1)
                                <span class="badge badge--primary">@lang('Low')</span>
                            @elseif($support->priority == 2)
                                <span class="badge badge--success">@lang('Medium')</span>
                            @elseif($support->priority == 3)
                                <span class="badge badge--info">@lang('High')</span>
                            @endif
                        </td>
                        <td data-label="@lang('Last Reply')">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }} </td>

                        <td data-label="@lang('Action')">
                            <a href="{{ route('employee.ticket.view', $support->ticket) }}" class="btn btn--base btn-md action--btn">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" data-label="@lang('Subject')" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>

<div class="row mx-xxl-5 mx-lg-0 my-4">
    <div class="col-lg-12 justify-content-end d-flex">
        {{$supports->links()}}
    </div>
</div>

@endsection


