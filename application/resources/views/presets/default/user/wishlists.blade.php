@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <form action="" method="GET">
            <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                <div class="input-group">
                    <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                        placeholder="@lang('Search by tour title')">
                    <button type="submit" class="input-group-text bg--base text-white border-0">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>
        </form>
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th class="text-center">@lang('Tour Title')</th>
                    <th class="text-center">@lang('Location')</th>
                    <th class="text-center">@lang('Time')</th>
                    <th class="text-center">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wishlists as $wishlist)
                    <tr>
                        <td class="text-center" data-label="@lang('Tour Title')">
                            <span class="fw--500">
                                <a href="{{ route('tour.package.details', [slug($wishlist->tour_package->title), $wishlist->tour_package->id]) }}" class="text--base">
                                    {{__($wishlist->tour_package->title) }}
                                </a>
                            </span>
                        </td>
                        <td class="text-center" data-label="@lang('Location')">
                            @php
                                $locationParts = array_filter([$wishlist->tour_package->city, $wishlist->tour_package->state, $wishlist->tour_package->country]);
                                $location = implode(', ', $locationParts);
                            @endphp
                            @if($location)
                            <p title="{{ $location }}" class="fs--14">
                                <i class="fa-regular fa-compass"></i>
                                {{ $location}}
                            </p>
                        @endif
                        </td>
                        <td class="text-center" data-label="@lang('Time')">
                            {{ showDateTime($wishlist->created_at) }}
                        </td>
                        <td class="text-center" data-label="@lang('Action')">
                            <a href="javascript:void(0)" class="btn btn-md btn--danger confirmationBtn action--btn text--white" title="@lang('Remove')"
                                data-question="@lang('Did you revieve the order')?"
                                data-action="{{ route('user.remove.wishlist', $wishlist->id) }}">
                                <i class="la la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td data-label="@lang('Tour Title')" class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>


@include($activeTemplate . 'components.pagination_controls', ['data' => $wishlists])
<x-confirmation-modal />
@endsection
