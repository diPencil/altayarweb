<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = employee();
        if (! $user) {
            return to_route('employee.login');
        }
        $user->address = $user->address ?? (object) [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'city' => '',
        ];
        return view($this->activeTemplate. 'employee.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
            'cover_image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);

        $user = employee();
        if (! $user) {
            return to_route('employee.login');
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $country = $user->address->country ?? '';
        $user->address = (object)[
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $country,
            'city' => $request->city,
        ];

        if ($request->hasFile('image'))
        {
            $path = getFilePath('EmployeeProfile');
            fileManager()->removeFile($path.'/'.$user->image);
            $directory = $user->username."/". $user->id;
            $path = getFilePath('EmployeeProfile').'/'.$directory;
            $filename = $directory.'/'.fileUploader($request->image, $path, getFileSize('EmployeeProfile'));
            $user->image = $filename;
        }

        if ($request->hasFile('cover_image')) {
            try {
                $old = $user->cover_image;
                $user->cover_image = fileUploader($request->cover_image, getFilePath('coverImage'), getFileSize('coverImage'),$old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->save();
        $notify[] = ['success', 'Profile has been updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        if (! employee()) {
            return to_route('employee.login');
        }
        return view($this->activeTemplate . 'employee.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        $general = gs();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = employee();
        if (! $user) {
            return to_route('employee.login');
        }
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
