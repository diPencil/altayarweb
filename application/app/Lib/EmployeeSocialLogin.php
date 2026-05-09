<?php

namespace App\Lib;

use Exception;
use App\Models\User;
use App\Constants\Status;
use App\Models\UserLogin;
use App\Models\AdminNotification;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;


class EmployeeSocialLogin
{
    private $provider;
    public function __construct($provider)
    {
        $this->provider = $provider;
        $this->configuration();
    }

    public function redirectDriver()
    {
        return Socialite::driver($this->provider)->redirect();
    }

    private function configuration()
    {
        $provider = $this->provider;
        $configuration = gs()->Employee_socialite_credentials->$provider;
        Config::set('services.' . $provider, [
            'client_id'     => $configuration->client_id,
            'client_secret' => $configuration->client_secret,
            'redirect'      => route('employee.social.login.callback', $provider),
        ]);

    }

    public function login()
    {
        $user = Socialite::driver($this->provider)->user();
        $username = $this->generateUsername($user->name);
        $userData = Employee::where('username', $username)->first();

        if (!$userData) {
            $emailExists = Employee::where('email', $user->email)->exists();
            if ($emailExists) {
                throw new Exception('Email already exists');
            }
            $userData = $this->createUser($user, $this->provider);
        }

        auth()->guard('employee')->login($userData);
        $this->loginLog($userData);

        return to_route('employee.home');
    }

    private function createUser($user, $provider)
    {
        $general = gs();
        $password = getTrx(8);
        $firstName = preg_replace('/\W\w+\s*(\W*)$/', '$1', $user->name);
        $pieces = explode(' ', $user->name);
        $lastName = array_pop($pieces);
        $username = $this->generateUsername($user->name);

        if ($firstName) {
            $firstName = $firstName;
        }
        if ($lastName) {
            $lastName = $lastName;
        }

        $newUser = new Employee();
        $newUser->username = $username;
        $newUser->email = $user->email;
        $newUser->password = Hash::make($password);
        $newUser->firstname = $firstName;
        $newUser->lastname = $lastName;

        $newUser->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'city' => ''
        ];

        $newUser->status = 1;
        $newUser->kv = $general->kv ? 0 :01;
        $newUser->ev = 1;
        $newUser->sv = 1;
        $newUser->ts = 0;
        $newUser->tv = 1;
        $newUser->reg_step = 1;
        $newUser->login_by = $provider;

        if ($user->avatar) {
            $fileName = uniqid() . time() . '.jpg';
            file_put_contents(getFilePath('EmployeeProfile') . '/' . $fileName, file_get_contents($this->provider != 'facebook' ? $user->avatar : $user->avatar . '&access_token=' . $user->token));
            $newUser->image = $fileName;
        }

        $newUser->save();

        $adminNotification = new AdminNotification();
        $adminNotification->agent_id = $newUser->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.employees.detail', $newUser->id);
        $adminNotification->save();

        return $newUser;
    }

    private function loginLog($user)
    {
        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userEmployee = osBrowser();
        $userLogin->agent_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = $userEmployee['browser'];
        $userLogin->os = $userEmployee['os_platform'];
        $userLogin->save();
    }

    protected function generateUsername($name)
    {
        // get first word into name
        $firstName = preg_replace('/\W\w+\s*(\W*)$/', '$1', $name);
        // get first and last word array wise set
        $pieces = explode(' ', $name);
        // get last word into name
        $lastName = array_pop($pieces);
        $username = preg_replace("/[^a-z0-9_]/", '', strtolower($firstName . $lastName));

        return $username;
    }
}
