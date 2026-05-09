@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20 filter--wrap mb-4 px-3">
                <form action="" method="GET">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="flex-grow-1">
                            <label class="form--label">@lang('Search')</label>
                            <input type="text" name="search" class="form--control" value="{{ request()->search }}" placeholder="@lang('Search by title, type or location')">
                        </div>
                        <div class="flex-grow-1 align-self-end">
                            <button type="submit" class="btn btn--lg btn--base w-100"><i class="fas fa-check"></i> @lang('Filter')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h5>@lang('My Listing Offers')</h5>
            <a href="{{ route('employee.listing.create') }}" class="btn btn--base btn--md"><i class="las la-plus"></i> @lang('Create New')</a>
        </div>
        <div class="col-lg-12">
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Image')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Location')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listings as $listing)
                            <tr>
                                <td data-label="@lang('Image')">
                                    @if($listing->image)
                                        <img src="{{ getImage(getFilePath('listingImage') . '/' . $listing->image) }}" alt="{{ $listing->title }}" class="rounded img-thumb" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">@lang('No image')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Title')">{{ __($listing->title) }}</td>
                                <td data-label="@lang('Type')">{{ $listing->listingType?->name ?: $listing->type }}</td>
                                <td data-label="@lang('Location')">{{ $listing->city }}, {{ $listing->country }}</td>
                                <td data-label="@lang('Price')">{{ $general->cur_sym }}{{ showAmount($listing->price) }}</td>
                                <td data-label="@lang('Status')">
                                    @if($listing->status == 1)
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Pending/Inactive')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('employee.listing.edit', $listing->id) }}" class="btn btn--base btn--sm"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn--danger btn--sm confirmationBtn" data-question="@lang('Delete this listing?')" data-action="{{ route('employee.listing.delete', $listing->id) }}"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No listing found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($listings->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $listings->links() }}
            </div>
        </div>
    @endif

    <x-confirmation-modal></x-confirmation-modal>
@endsection
