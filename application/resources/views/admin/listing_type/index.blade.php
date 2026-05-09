@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive admin-table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S/N')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listingTypes as $key=>$type)
                                <tr>
                                    <td data-label="@lang('S/N')"> {{$listingTypes->firstItem() + $key}}</td>
                                    <td data-label="@lang('Name')"> {{__($type->name)}}</td>
                                  
                                    <td data-label="@lang('Created At')"> {{showDateTime($type->created_at)}}</td>
                                    <td data-label="@lang('Status')">
                                        @php
                                            echo $type->statusBadge($type->status);
                                        @endphp
                                    </td>
                                    <td data-label="Action">
                                        <button type="button" class="btn btn-sm btn--primary edit" data-id="{{$type->id}}" data-name="{{$type->name}}" data-name_ar="{{$type->name_ar}}" data-status="{{$type->status}}">
                                            <i class="las la-edit text--shadow"></i>
                                        </button>
                                        <button class="btn btn--danger btn-sm me-3 confirmationBtn"
                                        data-question="@lang('Are you sure to change this status?')"
                                        data-action="{{ route('admin.listing.type.status.change',$type->id) }}"><i class="las la-sync-alt"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{__($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($listingTypes->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($listingTypes) }}
                </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

<!--add modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{route('admin.listing.type.store')}}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add New Listing Type')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name"> @lang('Name'):</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    placeholder="@lang('Name')" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name_ar"> @lang('Name (Arabic)'):</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}"
                                    placeholder="@lang('Name (Arabic)')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--edit modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{route('admin.listing.type.update')}}" method="POST">
            @csrf
            <input type="hidden" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Update Listing Type')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ad_name"> @lang('Name'):</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                    placeholder="@lang('Name')" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="ad_name_ar"> @lang('Name (Arabic)'):</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}"
                                    placeholder="@lang('Name (Arabic)')">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="status"> @lang('Status'):</label>
                                <select name="status" class="form-select">
                                    <option value="1">@lang("Active")</option>
                                    <option value="0">@lang("Disable")</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-confirmation-modal></x-confirmation-modal>
@endsection

@push('breadcrumb-plugins')
<div class="d-flex flex-wrap justify-content-end">
    <button type="button" class="btn btn--primary addModal me-2" data-toggle="modal"><i class="fas fa-plus"></i>
        @lang('Add New')
    </button>
    <form action="" method="GET" class="form-inline">
        <div class="input-group justify-content-end">
            <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search...')"
                value="{{ request()->search }}">
            <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>
</div>
@endpush


@push('script')
    <script>
        'use strict';
        $('.addModal').on('click', function () {
            $('#addModal').modal('show');
        });

        var modal = $('#editModal');
        $('.edit').on('click', function () {

            var name = $(this).data('name');
            var name_ar = $(this).data('name_ar');
            var status = $(this).data('status');
            var id = $(this).data('id');
    
            modal.find('input[name=id]').val(id);
            modal.find('input[name=name]').val(name);
            modal.find('input[name=name_ar]').val(name_ar);
            modal.find('select[name=status]').val(status);
            modal.modal('show');
        })

    </script>
@endpush
