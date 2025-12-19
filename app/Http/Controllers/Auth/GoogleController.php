<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use Exception;
use App\User;
use Session;
use App\UsersDeviceHistory;

require(base_path() . '/public/device-detector/vendor/autoload.php');
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

class GoogleController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
      
    /**
     * Create a new controller instance.
     *
     * @return void
     */
public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        $finduser = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

        if ($finduser) {
            Auth::login($finduser);
            $user_id = $finduser->id;
        } else {
            $newUser = User::create([
                'name'     => $googleUser->name,
                'email'    => $googleUser->email,
                'password' => bcrypt('123456dummy'),
                'google_id'=> $googleUser->id
            ]);
            Auth::login($newUser);
            $user_id = $newUser->id;
        }

        // Save Device Info
        $this->saveDeviceHistory($user_id);

        return redirect('/dashboard');

    } catch (Exception $e) {
        \Log::error('Google login failed: '.$e->getMessage());
        return redirect('/login')->with('error', 'Google authentication failed. Please try again.');
    }
}

private function saveDeviceHistory($user_id)
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $dd = new DeviceDetector($userAgent);
    $dd->parse();

    if (!$dd->isBot()) {
        $osInfo = $dd->getOs();
        $device = $dd->getDeviceName();
        $brand  = $dd->getBrandName();
        $model  = $dd->getModel();

        $user_device_name = $brand
            ? $brand.' '.$model.' '.$osInfo['platform'].' '.$device
            : $osInfo['name'].' '.$osInfo['version'].' '.$osInfo['platform'].' '.$device;

        $user_device_obj = new UsersDeviceHistory;
        $user_device_obj->user_id = $user_id;
        $user_device_obj->user_device_name = $user_device_name;
        $user_device_obj->user_session_name = Session::getId();
        $user_device_obj->save();
    }
}
}
