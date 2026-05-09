<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.active') ? 'active' : '' }}"
                href="{{route('admin.employees.active')}}">@lang('Active')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.all') ? 'active' : '' }}"
                    href="{{route('admin.employees.all')}}">@lang('All')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.banned') ? 'active' : '' }}"
                    href="{{route('admin.employees.banned')}}">@lang('Banned')
                    @if(isset($bannedEmployeesCount) && $bannedEmployeesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$bannedEmployeesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.email.unverified') ? 'active' : '' }}"
                    href="{{route('admin.employees.email.unverified')}}">@lang('Email Unverified')
                    @if(isset($emailUnverifiedEmployeesCount) && $emailUnverifiedEmployeesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$emailUnverifiedEmployeesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.mobile.unverified') ? 'active' : '' }}"
                    href="{{route('admin.employees.mobile.unverified')}}">@lang('Mobile Unverified')
                    @if(isset($mobileUnverifiedEmployeesCount) && $mobileUnverifiedEmployeesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$mobileUnverifiedEmployeesCount}}</span>
                    @endif
                </a>
            </li>
    

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.kyc.unverified') ? 'active' : '' }}"
                    href="{{route('admin.employees.kyc.unverified')}}">@lang('Kyc Unverified')
                    @if(isset($kycUnverifiedEmployeesCount) && $kycUnverifiedEmployeesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$kycUnverifiedEmployeesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.kyc.pending') ? 'active' : '' }}"
                    href="{{route('admin.employees.kyc.pending')}}">@lang('Kyc Pending')
                    @if(isset($kycPendingEmployeesCount) && $kycPendingEmployeesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$kycPendingEmployeesCount}}</span>
                    @endif
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.with.balance') ? 'active' : '' }}"
                    href="{{route('admin.employees.with.balance')}}">@lang('With Balance')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.subscriber.index') ? 'active' : '' }}"
                    href="{{route('admin.subscriber.index')}}">@lang('Subscribers')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employees.notification.all') ? 'active' : '' }}"
                    href="{{route('admin.employees.notification.all')}}">@lang('Notification to Employees')
                </a>
            </li>
        </ul>
    </div>
</div>
