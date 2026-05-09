<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.report.notification.history')||Request::routeIs('admin.users.notification.log') ? 'active' : '' }}"
                    href="{{route('admin.report.notification.history')}}">@lang('User')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.employee.report.notification.history')|| Request::routeIs('admin.employees.notification.log') ? 'active' : '' }}"
                    href="{{route('admin.employee.report.notification.history')}}">@lang('Employee')
                </a>
            </li>
            
        </ul>
    </div>
</div>
