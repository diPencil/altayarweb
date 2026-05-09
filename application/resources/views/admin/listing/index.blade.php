@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4 align-items-end">
                            <div class="flex-grow-1">
                                <label>@lang('Search')</label>
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Search by title, type or location')">
                            </div>
                            <div class="flex-shrink-0">
                                <button type="submit" class="btn btn--primary h-40 w-100"><i class="fas fa-check"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h5 class="mb-1">@lang('Listing Offers')</h5>
                <p class="mb-0 text-muted">@lang('Manage hotels, trips and seasonal offers from one place.')</p>
            </div>
            <a href="{{ route('admin.listing.create') }}" class="btn btn--primary"><i class="las la-plus me-1"></i>@lang('Create Listing')</a>
        </div>

        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('SL')</th>
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
                                        @php
                                            $listingTypeLabel = $listing->listingType?->name ?: ($listing->type ?: __('-'));
                                        @endphp
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($listing->image)
                                                <img src="{{ getImage(getFilePath('listingImage') . '/' . $listing->image) }}" alt="{{ $listing->title }}" class="rounded img-thumb" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <span class="text-muted">@lang('No image')</span>
                                            @endif
                                        </td>
                                        <td>{{ __(strLimit($listing->title, 32)) }}</td>
                                        <td>
                                            <span class="badge text-uppercase fw--600" style="background-color: #0d6efd; color: #fff; padding: 8px 14px; border-radius: 999px;">
                                                {{ $listingTypeLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ trim(($listing->city ? $listing->city . ', ' : '') . ($listing->country ?? '')) ?: __('-') }}
                                            @if($listing->start_date && $listing->end_date)
                                                <br>
                                                <small class="text-muted">
                                                    {{ showDateTime($listing->start_date, 'd M Y') }} - {{ showDateTime($listing->end_date, 'd M Y') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ __($listing->currency ?? $general->cur_text) }} {{ showAmount($listing->price) }}</td>
                                        <td>
                                            <span class="badge {{ $listing->status ? 'badge--success' : 'badge--danger' }}">
                                                {{ $listing->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.listing.edit', $listing->id) }}" class="btn btn-sm btn--primary"><i class="las la-edit"></i></a>
                                            <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Delete this listing?')" data-action="{{ route('admin.listing.delete', $listing->id) }}"><i class="las la-trash"></i></button>
                                            <button class="btn btn-sm btn--warning confirmationBtn" data-question="@lang('Change status of this listing?')" data-action="{{ route('admin.listing.status.change', $listing->id) }}"><i class="las la-sync"></i></button>
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
                @if($listings->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($listings) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.listing.create') }}" class="btn btn-sm btn--primary"><i class="fas fa-plus"></i> @lang('Add New')</a>
@endpush
