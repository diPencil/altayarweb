@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="base--card radius--20">
            <form action="#" method="POST">
                @csrf
                <div class="row gy-3">
                    <div class="col-sm-12">
                        <h4>@lang('Change Password')</h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form--label mb-3">@lang('Current Password')</label>
                            <div class="input--group position-relative">
                                <input type="password" class="form--control" id="current_password" name="current_password" placeholder="@lang('Current Password')" required>
                                <div class="password-show-hide fas fa-eye-slash toggle-password-change text--black" data-target="current_password"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form--label mb-3">@lang('New Password')</label>
                            <div class="input--group position-relative"> 
                                <input type="password" class="form--control" id="new_password" name="password" placeholder="@lang('Password')" required>
                                <div class="password-show-hide fas fa-eye-slash toggle-password-change text--black" data-target="new_password"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="password_confirmation" class="form--label mb-3">@lang('Confirm Password')</label>
                            <div class="input--group position-relative">
                                <input type="password" class="form--control"  id="password_confirmation" name="password_confirmation" placeholder="@lang('Confirm Password')" required>
                                <div class="password-show-hide fas fa-eye-slash toggle-password-change text--black" data-target="password_confirmation"></div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn--base btn--md pills ">@lang('Update')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
