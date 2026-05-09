@extends($activeTemplate . 'layouts.employee.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20">
                <form action="">
                    <div class="row d-flex justify-content-end">
                        <div class="col-lg-5 col-6">
                            <div class="form-group">
                                <input type="text" name="search" class="form--control" placeholder="@lang('Search by title')">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="flex-grow-1 align-self-end">
                                <button type="submit" class="btn btn--lg btn--base w-100"><i class="fas fa-check"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Image')</th>
                            <th>@lang('Gellery/Artwork Count')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $collection)
                            <tr>
                                <td data-label="@lang('Name')">{{__($collection->name)}}</td>
                                <td data-label="@lang('Image')">
                                    <img src="{{ getImage(getFilePath('collection') . '/' . $collection->image) }}" alt="@lang('image')" class="rounded img-thumb" style="width: 60px;height:60px;">
                                </td>
                                <td data-label="@lang('Artwork Coun')">{{$collection->artworks->count()}}</td>
                                <td data-label="@lang('Status')">
                                    @php
                                        echo $collection->statusBadge($collection->status);
                                    @endphp
                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{route('employee.collection.edit', $collection->id)}}" class="btn btn--base" title="@lang('Edit')"><i class="fas fa-edit"></i></a>
                                    <a href="javascript:void(0)" class="btn btn--danger changeStatusBtn" data-id="{{$collection->id}}" title="@lang('Status Change')"><i class="fa-solid fa-rotate"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" data-label="@lang('Collection Table')" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mx-xxl-5 mx-lg-0 my-4">
        <div class="col-lg-12 justify-content-end d-flex">
            @if($collections->hasPages())
                {{ $collections->links() }}
            @endif
        </div>
    </div>

    <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusModalLabel">@lang('Status Change Confirmation')</h5>
                    <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('employee.collection.change.status') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to change the status?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn--lg">@lang('Change')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        'use strict';
        $('.changeStatusBtn').on('click', function () {
            var modal = $('#changeStatusModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.modal('show');
        });

    </script>
@endpush


