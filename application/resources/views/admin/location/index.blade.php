@extends('admin.layouts.app')
@section('panel')
    <div class="d-flex flex-wrap justify-content-end mb-3">
        <div class="d-inline">
            <div class="input-group justify-content-end">
                <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search Location')...">
                <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('SL')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Destination')</th>
                                    <th>@lang('Count')</th>
                                    <th>@lang('status')</th>
                                    <th>@lang('Action')</th>

                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($locations as $key=>$location)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ __($location->name) }}</td>
                                        <td>
                                            <img src="{{ getImage(getFilePath('location') . '/' . $location->image) }}"
                                                alt="@lang('Image')" class="shadow rounded img-thumbnail"
                                                style="width:60px">
                                        </td>
                                        <td>{{ __($location->location) }}</td>
                                        <td>{{ $location->displayCountLabel() }}</td>

                                        <td>
                                            @php
                                                echo $location->statusBadge($location->status);
                                            @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.location.edit', $location->id) }}"
                                                class="btn btn-sm btn-outline--primary" title="@lang('Edit')"
                                                data-data="{{ json_encode($location->only('name', 'status', 'id', 'location')) }}">
                                                <i class="la la-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-question="@lang('Are you sure to delete this location?')"
                                                data-action="{{ route('admin.location.delete', $location->id) }}"
                                                title="@lang('Delete')">
                                                <i class="la la-trash"></i>
                                            </button>
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
        </div>
    </div>

    <x-confirmation-modal></x-confirmation-modal>

    @push('breadcrumb-plugins')
        <a href="{{ route('admin.location.create') }}" class="btn btn-sm btn--primary addCity">@lang('Add New')</a>
    @endpush
@endsection
