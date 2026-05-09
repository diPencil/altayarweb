@php
  $user = employee();
  $languages = App\Models\Language::where('code', '!=', 'es')->get();
  $currentLang = $languages->firstWhere('code', session('lang', 'en')) ?? $languages->first();
@endphp
<div class="row mb-4 mx-0">
    <div class="dashboard-header d-flex justify-content-between align-items-center radius--20">
        <div class="navigator-text d-flex justify-content-center align-items-center">
            <div class="dashboard-body__bar">
                <span class="dashboard-body__bar-icon"><i class="las la-bars"></i></span>
            </div>
            <h6>{{__($pageTitle)}}</h6>
        </div>
        <div class="user-info--wrap d-flex align-items-center gap-3">
          <div class="dropdown">
              <button type="button" class="btn btn-sm btn--base dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="{{ getImage(getFilePath('language') . '/' . @$currentLang->icon, getFileSize('language')) }}" alt="@lang('Icon')" style="width: 20px; height: 20px; border-radius: 50%;">
                  <span class="d-none d-sm-inline">{{ __(@$currentLang->name) }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-end">
                  @foreach ($languages as $language)
                      <li>
                          <a class="dropdown-item d-flex align-items-center px-3 py-2 lang-change" href="javascript:void(0)" data-lang="{{ $language->code }}">
                              <img src="{{ getImage(getFilePath('language') . '/' . $language->icon, getFileSize('language')) }}" alt="@lang('Icon')" style="width: 20px; height: 20px; border-radius: 50%; margin-inline-end: 10px;">
                              <span class="dropdown-menu__caption">{{ __($language->name) }}</span>
                          </a>
                      </li>
                  @endforeach
              </ul>
          </div>

          <a title="@lang('Visit Site')" href="{{ route('home') }}" target="_blank" class="btn btn-sm btn--base text-white">
              <i class="fas fa-globe"></i>
          </a>

          <a href="javascript:void(0)" class="u-info dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-thumb">
                <img src="{{ getImage(getFilePath('employeeProfile').'/'.$user->image,getFileSize('employeeProfile')) }}" alt="@lang('image')">
              </div>
              <div class="user--name d-flex align-items-center gap-2">
                  <i class="fa-solid fa-circle-chevron-down"></i>
              </div>
          </a>

          <ul class="dropdown-menu dropdown-menu-end">
                        @if (employeeMenuCan('profile'))
                        <li><a class="dropdown-item" href="{{ route('employee.profile.setting') }}"><i class="fa-regular fa-user"></i> @lang('Profile')</a></li>
                        @endif
                        @if (employeeMenuCan('password'))
                        <li><a class="dropdown-item" href="{{ route('employee.change.password') }}"><i class="fa-solid fa-key"></i> @lang('Password')</a></li>
                        @endif
                        @if (employeeMenuCan('twofactor'))
                        <li><a class="dropdown-item" href="{{ route('employee.twofactor') }}"><i class="fa-solid fa-user-ninja"></i> @lang('2FA Security')</a></li>
                        @endif
            <li><a class="dropdown-item" href="{{route('employee.logout')}}"><i class="fa-solid fa-arrow-right-from-bracket"></i> @lang('Logout')</a> </li>
        </ul>
      </div>
    </div>
</div>
