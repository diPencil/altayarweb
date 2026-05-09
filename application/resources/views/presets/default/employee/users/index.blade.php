@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4 align-items-center">
        <div class="col-lg-12">
            <div class="base--card radius--20 p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1">@lang('Users')</h4>
                    <p class="mb-0 text-muted">@lang('Customers assigned to this employee.')</p>
                </div>
                <div class="text-end">
                    <h2 class="mb-0">{{ $usersCount }}</h2>
                    <small class="text-muted">@lang('Total Users')</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <form action="" method="GET">
                <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}" placeholder="@lang('Search by name, username, email or mobile')">
                        <button type="submit" class="input-group-text bg--base text-white border-0"><i class="las la-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Username')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Membership')</th>
                            <th>@lang('Joined')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td data-label="@lang('SI')">{{ $loop->iteration }}</td>
                                <td data-label="@lang('Name')">{{ $user->fullname }}</td>
                                <td data-label="@lang('Username')">{{ $user->username }}</td>
                                <td data-label="@lang('Email')">{{ $user->email }}</td>
                                <td data-label="@lang('Membership')">{{ $user->currentMembership?->plan?->name ?? __('Not Assigned') }}</td>
                                <td data-label="@lang('Joined')">{{ showDateTime($user->created_at) }}</td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('employee.users.detail', $user->id) }}" class="btn btn-sm btn--base">
                                        <i class="la la-eye"></i>
                                    </a>
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
    </div>

    @if ($users->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $users->links() }}
            </div>
        </div>
    @endif
@endsection