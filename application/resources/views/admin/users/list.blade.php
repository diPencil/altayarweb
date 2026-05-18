@extends('admin.layouts.app')
@section('panel')
    @include('admin.components.tabs.user')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive admin-table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Member ID')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Member Plan')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Joined At')</th>
                                    <th>@lang('Balance')</th>
                                    <th>@lang('Cashback')</th>
                                    <th>@lang('Points')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $loop->index }}</td>

                                        <td>
                                            {{ $user->currentMembership?->member_code ?? __('-') }}
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->fullname }}
                                                ({{ $user->username }})
                                            </a>
                                        </td>

                                        <td>
                                            {{ $user->currentMembership?->plan ? (is_rtl() && $user->currentMembership->plan?->name_ar ? $user->currentMembership->plan?->name_ar : $user->currentMembership->plan?->name) : __('No Plan') }}
                                        </td>

                                        <td>
                                            {{ $user->email }}
                                        </td>



                                        <td>
                                            {{ showDateTime($user->created_at) }}
                                        </td>


                                        <td>
                                            <span class="fw-bold">
                                                {{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">
                                                {{ $general->cur_sym }}{{ showAmount($user->cashback_balance) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">
                                                {{ $user->membership_points_balance }}
                                            </span>
                                        </td>

                                        <td>
                                            @if (Request::routeIs('admin.users.kyc.pending'))
                                                <a title="@lang('Kyc Details')"
                                                    href="{{ route('admin.users.kyc.details', $user->id) }}"
                                                    class="btn btn-sm btn--primary">
                                                    <i class="las la-info-circle text--shadow"></i>
                                                </a>
                                            @endif
                                            <a title="{{ $user->status == 1 ? __('Ban User') : __('Unban User') }}"
                                                href="javascript:void(0)"
                                                class="btn btn-sm {{ $user->status == 1 ? 'btn--warning' : 'btn--success' }} userStatus"
                                                data-bs-toggle="modal" data-bs-target="#userStatusModal"
                                                data-action="{{ route('admin.users.status', $user->id) }}"
                                                data-status="{{ $user->status }}"
                                                data-ban-reason="{{ e($user->ban_reason) }}">
                                                <i class="las {{ $user->status == 1 ? 'la-ban' : 'la-undo' }} text--shadow"></i>
                                            </a>
                                            @php($deleteBlockReason = $user->deleteBlockReason())
                                            @if (!$deleteBlockReason)
                                                <button type="button" class="btn btn-sm btn--danger confirmationBtn"
                                                    data-question="{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}"
                                                    data-action="{{ route('admin.users.delete', $user->id) }}"
                                                    aria-label="{{ __('Delete User') }}">
                                                    <i class="las la-trash text--shadow"></i>
                                                </button>
                                            @else
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $deleteBlockReason }}">
                                                    <button type="button" class="btn btn-sm btn--danger opacity-50" aria-disabled="true" tabindex="-1">
                                                        <i class="las la-trash text--shadow"></i>
                                                    </button>
                                                </span>
                                            @endif
                                            <a title="@lang('User Profile')" href="{{ route('admin.users.detail', $user->id) }}"
                                                class="btn btn-sm btn--primary">
                                                <i class="las la-eye text--shadow"></i>
                                            </a>
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
                @if ($users->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($users) }}
                    </div>
                @endif
            </div>
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-outline--primary"><i class="las la-plus"></i>@lang('Add New User')</a>
        <form action="" method="GET" class="form-inline">
            <div class="input-group justify-content-end">
                <input type="text" name="search" class="form-control bg--white" placeholder="@lang('Search by Username')"
                    value="{{ request()->search }}">
                <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </form>
    </div>
@endpush

<div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title status-modal-title"></h5>
                <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="status-ban-block">
                        <h6 class="mb-2">@lang('If you ban this user he/she won\'t be able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="status-unban-block d-none">
                        <p class="mb-2"><span>@lang('Ban reason was'):</span></p>
                        <p class="status-ban-reason mb-3"></p>
                        <h4 class="text-center mt-3 status-unban-question"></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark status-unban-block-btn d-none" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary btn-global status-ban-submit">@lang('Save')</button>
                    <button type="submit" class="btn btn--primary status-unban-submit d-none">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function ($) {
            "use strict";

            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
                new bootstrap.Tooltip(element);
            });

            $(document).on('click', '.userStatus', function () {
                const modal = $('#userStatusModal');
                const status = Number($(this).data('status'));
                const action = $(this).data('action');
                const banReason = $(this).data('ban-reason') || @json(__('No reason provided'));

                modal.find('form').attr('action', action);
                modal.find('textarea[name=reason]').val('').prop('required', status === 1);

                if (status === 1) {
                    modal.find('.status-modal-title').text(@json(__('Ban User')));
                    modal.find('.status-ban-block').removeClass('d-none');
                    modal.find('.status-unban-block').addClass('d-none');
                    modal.find('.status-ban-submit').removeClass('d-none');
                    modal.find('.status-unban-submit').addClass('d-none');
                    modal.find('.status-unban-block-btn').addClass('d-none');
                } else {
                    modal.find('.status-modal-title').text(@json(__('Unban User')));
                    modal.find('.status-ban-block').addClass('d-none');
                    modal.find('.status-unban-block').removeClass('d-none');
                    modal.find('.status-ban-submit').addClass('d-none');
                    modal.find('.status-unban-submit').removeClass('d-none');
                    modal.find('.status-unban-block-btn').removeClass('d-none');
                    modal.find('.status-ban-reason').text(banReason);
                    modal.find('.status-unban-question').text(@json(__('Are you sure to unban this user?')));
                }
            });
        })(jQuery);
    </script>
@endpush
