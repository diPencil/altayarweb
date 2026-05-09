@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">@lang('Membership Plans')</h5>
                        <p class="mb-0 text-muted">@lang('Manage plan benefits, duration, bonus points and attached PDFs.')</p>
                    </div>
                    <a href="{{ route('admin.membership.plans.create') }}" class="btn btn--primary">
                        <i class="las la-plus me-1"></i>@lang('Create Plan')
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Bonus Points')</th>
                                    <th>@lang('Benefits')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($plan->image_file)
                                                        <img src="{{ asset(getFilePath('membershipPlanImage') . '/' . $plan->image_file) }}" alt="{{ $plan->name }}" style="width: 38px; height: 38px; object-fit: contain; background: #fff; padding: 3px; border: 1px solid #eef1f7; border-radius: 10px;">
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center text-muted bg--light" style="width: 38px; height: 38px; border-radius: 10px; font-size: 10px;">
                                                            @lang('No image')
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ is_rtl() && $plan->name_ar ? $plan->name_ar : $plan->name }}</div>
                                                    <small class="text-muted">{{ $plan->pdf_file ? $plan->pdf_file : __('No PDF uploaded') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($plan->price) }}</td>
                                        <td>{{ $plan->duration_days ? $plan->duration_days . ' ' . __('Days') : __('Lifetime') }}</td>
                                        <td>{{ $plan->bonus_points }}</td>
                                        <td>
                                            @php
                                                $benefitsList = $plan->benefits ?? [];
                                                $benefitsAr = $plan->benefits_ar ?? [];
                                            @endphp
                                            @if (count($benefitsList))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($benefitsList as $i => $benefit)
                                                        @php
                                                            $benefitLabel = $benefit;
                                                            if (is_rtl()) {
                                                                if (isset($benefitsAr[$i]) && $benefitsAr[$i] !== '') {
                                                                    $benefitLabel = $benefitsAr[$i];
                                                                } else {
                                                                    $benefitLabel = __(trim((string) $benefit));
                                                                }
                                                            }
                                                        @endphp
                                                        <span class="badge rounded-pill bg--info">{{ $benefitLabel }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">@lang('No benefits added yet')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $plan->status ? 'badge--success' : 'badge--danger' }}">
                                                {{ $plan->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.membership.plans.edit', $plan->id) }}" class="btn btn-sm btn--primary">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn--danger confirmationBtn" 
                                                    data-question="@lang('Delete this plan?')" 
                                                    data-action="{{ route('admin.membership.plans.delete', $plan->id) }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($plans->hasPages())
                    <div class="card-footer">
                        {{ $plans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <form action="" method="GET" class="form-inline">
        <div class="input-group">
            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by plan name')" value="{{ request()->search }}">
            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
@endpush
