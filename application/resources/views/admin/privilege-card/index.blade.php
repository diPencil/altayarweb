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
                                <input type="text" name="search" class="form-control" value="{{ request()->search }}" placeholder="@lang('Search by name or subtitle')">
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
                <h5 class="mb-1">@lang('Privilege Cards')</h5>
                <p class="mb-0 text-muted">@lang('Manage premium cards, benefits, PDFs and featured promotions.')</p>
            </div>
            <a href="{{ route('admin.privilege.cards.create') }}" class="btn btn--primary"><i class="las la-plus me-1"></i>@lang('Create Card')</a>
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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Benefits')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cards as $card)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($card->image_file)
                                                <img src="{{ asset(getFilePath('privilegeCardImage') . '/' . $card->image_file) }}" alt="{{ $card->name }}" class="rounded img-thumb" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <span class="text-muted">@lang('No image')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ is_rtl() && $card->name_ar ? $card->name_ar : $card->name }}</div>
                                            <small class="text-muted">{{ is_rtl() && $card->subtitle_ar ? $card->subtitle_ar : $card->subtitle }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $general->cur_sym }}{{ showAmount($card->price) }}</div>
                                            @if($card->original_price)
                                                <small class="text-muted text-decoration-line-through">{{ $general->cur_sym }}{{ showAmount($card->original_price) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($card->benefits)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($card->benefits as $benefit)
                                                        <span class="badge rounded-pill bg--info">{{ $benefit }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">@lang('No benefits added yet')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $card->is_featured ? 'badge--success' : 'badge--warning' }}">
                                                {{ $card->is_featured ? __('Featured') : __('Regular') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $card->status ? 'badge--success' : 'badge--danger' }}">
                                                {{ $card->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.privilege.cards.edit', $card->id) }}" class="btn btn-sm btn--primary"><i class="las la-edit"></i></a>
                                            <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Delete this card?')" data-action="{{ route('admin.privilege.cards.delete', $card->id) }}"><i class="las la-trash"></i></button>
                                            <button class="btn btn-sm btn--warning confirmationBtn" data-question="@lang('Change status of this card?')" data-action="{{ route('admin.privilege.cards.status.change', $card->id) }}"><i class="las la-sync"></i></button>
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
                @if($cards->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cards) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.privilege.cards.create') }}" class="btn btn-sm btn--primary"><i class="fas fa-plus"></i> @lang('Add New')</a>
@endpush
