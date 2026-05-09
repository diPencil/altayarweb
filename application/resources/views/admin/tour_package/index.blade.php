@extends('admin.layouts.app')

@section('panel')
    <div class="row">
         <div class="col-12">
                        <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Title')</label>
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}"
                                    placeholder="@lang('Search by title')">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Category')</label>
                                <select name="category_id" class="form-control">
                                    <option value="">@lang('Select Category')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request()->category_id == $category->id ? 'selected' : '' }}>
                                            {{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-grow-1 align-self-end">
                                <button type="submit" class="btn btn--primary h-40 w-100"><i class="fas fa-check"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 d-flex flex-wrap justify-content-between">
            <div>
                @include('admin.components.tabs.tour_package')
            </div>
        </div>
       
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--primary showFilterBtn btn-sm">
                    <i class="las la-filter"></i>
                    @lang('Filter')
                </button>
            </div>

            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('SI')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Person')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Tour Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tourPackages as $item)
                                    <tr>
                                        @php $primaryImage = optional($item->TourPackagePrimaryImage)->image; @endphp
                                        <td>{{ $loop->iteration }}</td>

                                        <td class="text--primary">
                                            @if ($item->user_type == 'admin')
                                                @lang('Admin')
                                            @else
                                            <a href="{{route('admin.employees.detail',$item?->agent->id)}}">
                                                @lang('Employee') ({{$item?->agent->username}})
                                            </a>
                                            @endif
                                        </td>
                                        <td>{{ __(strLimit($item->title, 30)) }}</td>
                                        <td>
                                            <a href="{{ route('tour.package.details', [slug($item->title), $item->id]) }}">
                                                <img src="{{ $primaryImage ? getImage(getFilePath('tourPackageImage') . '/' . $primaryImage) : asset('assets/images/general/default.png') }}"
                                                    alt="@lang('image')" class="rounded img-thumb"
                                                    style="width: 60px;height:60px;">
                                            </a>
                                        </td>

                                        <td>{{ __($item->category->name) }}</td>
                                        <td> {{ $general->cur_sym }}{{ showAmount($item->price) }}</td>
                                        <td> {{ $item->person_capability }}</td>
                                        <td> {{ $item->country }}</td>

                                        <td>
                                            @php
                                                echo $item->statusBadge($item->status);
                                            @endphp
                                        </td>
                                        <td>
                                            <a href="{{ route('tour.package.details', [slug($item->title), $item->id]) }}"
                                                class="btn btn--primary btn-sm">
                                                <i class="fas fa-eye"></i></a>

                                            @if ($item->user_type == 'admin')
                                                <a href="{{ route('admin.tour.package.edit', $item->id) }}"
                                                    class="btn btn--primary btn-sm">
                                                    <i class="fas fa-edit"></i></a>
                                           
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($tourPackages->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($tourPackages) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>


@endsection

@push('breadcrumb-plugins')
<div class="d-flex flex-wrap justify-content-end">
    <div >
                <a href="{{ route('admin.tour.package.create') }}" class="btn btn--primary mb-1">@lang('Add New')</a>
            </div>
</div>
@endpush
