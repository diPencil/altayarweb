@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card b-radius--10">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <h5 class="text-muted">@lang('User Info')</h5>
                        <p class="mb-0"><strong>@lang('Username'):</strong> {{ $subscription->user->username }}</p>
                        <p class="mb-0"><strong>@lang('Email'):</strong> {{ $subscription->user->email }}</p>
                    </div>

                    <form action="{{ route('admin.membership.subscriptions.update', $subscription->id) }}" method="POST">
                        @csrf
                        <div class="row gy-4">
                            <div class="col-lg-12">
                                <label class="form-label">@lang('Membership Plan')</label>
                                <select name="membership_plan_id" class="form-control" required>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected($subscription->membership_plan_id == $plan->id)>
                                            {{ $plan->name }} ({{ gs()->cur_sym }}{{ showAmount($plan->price) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">@lang('Start Date')</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $subscription->start_date?->format('Y-m-d')) }}" class="form-control" required>
                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">@lang('End Date')</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $subscription->end_date?->format('Y-m-d')) }}" class="form-control">
                                <small class="text-muted">@lang('Leave empty for lifetime membership.')</small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">@lang('Status')</label>
                                <select name="status" class="form-control" required>
                                    <option value="1" @selected($subscription->status == 1)>@lang('Active')</option>
                                    <option value="0" @selected($subscription->status == 0)>@lang('Pending / Inactive')</option>
                                </select>
                            </div>

                            @if($subscription->legacy_import)
                            <div class="col-lg-12">
                                <div class="alert alert-info">
                                    <i class="la la-info-circle"></i>
                                    @lang('This is a legacy imported subscription. Editing will preserve legacy data fields.')
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.membership.subscriptions') }}" class="btn btn--dark">@lang('Cancel')</a>
                            <button type="submit" class="btn btn--primary">@lang('Update Subscription')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
