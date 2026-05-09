<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.list') ? 'active' : '' }}"
                    href="{{route('admin.deposit.list')}}">@lang('All')
                    @if($allDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$allDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.approved') ? 'active' : '' }}"
                    href="{{route('admin.deposit.approved')}}">@lang('Approved')
                    @if($approvedDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$approvedDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.pending') ? 'active' : '' }}"
                    href="{{route('admin.deposit.pending')}}">@lang('Pending')
                    @if($pendingDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$pendingDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.successful') ? 'active' : '' }}"
                    href="{{route('admin.deposit.successful')}}">@lang('Successful')
                    @if($successfulDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$successfulDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.rejected') ? 'active' : '' }}"
                    href="{{route('admin.deposit.rejected')}}">@lang('Rejected')
                    @if($rejectedDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$rejectedDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.deposit.initiated') ? 'active' : '' }}"
                    href="{{route('admin.deposit.initiated')}}">@lang('Initiated')
                    @if($initiatedDepositsCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$initiatedDepositsCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.gateway.automatic.index') ? 'active' : '' }}"
                    href="{{route('admin.gateway.automatic.index')}}">@lang('Payment Gateways')
                </a>
            </li>
        </ul>
    </div>
</div>
