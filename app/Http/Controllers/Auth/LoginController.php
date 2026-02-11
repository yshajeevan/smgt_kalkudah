<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, HasRoles;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */


    protected function authenticated(Request $request, $user){
        return redirect()->route('/') ;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]+$/|min:9|max:15',
        ]);

        $phone = $request->input('phone');
        // Format the phone number to start with '94' if it starts with '0'
        $phone = preg_replace('/^0/', '94', $phone);
            
        // Ensure the phone number has the country code prefix even if the user omitted it
        if (!str_starts_with($phone, '94')) {
            $phone = '94' . $phone;
        }
        // Check if the phone number exists in the database
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return redirect()->back()->with('message', 'Phone number not registered.');
        }

        // Set a limit and cooldown time (e.g., 3 requests within 10 minutes)
        $otpRequestLimit = 3;
        $coolDownMinutes = 10;
        $cacheKey = 'otp_requests_' . $phone;
        
        // Check the current request count
        $requestCount = Cache::get($cacheKey, 0);
        
        if ($requestCount >= $otpRequestLimit) {
            return redirect()->back()->with('error', 'OTP request limit reached. Please try again later.');
        }
        
        // Increment the request count and set the cooldown timer if it's the first request
        Cache::put($cacheKey, $requestCount + 1, now()->addMinutes($coolDownMinutes));

        $otp = rand(100000, 999999);

        // Save OTP in cache with a 10-minute expiration time
        Cache::put('otp_' . $phone, $otp, 600);

        try {
            error_reporting(E_ALL);
            date_default_timezone_set('Asia/Colombo');
            $now = date("Y-m-d\TH:i:s");
            $username = "zeo_batti";
            $password = "@q123456";
            $digest=md5($password);
            
            $body = '{
            
            "messages": [
            {
            "clientRef": "",
            "number": "'.$phone.'",
            "mask": "BATWESTZEO",
            "text": "'.$otp.'",
            "campaignName":"TestPromo"
            }
            ]
            }';
            // return $body;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://richcommunication.dialog.lk/api/sms/send");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$body); //Post Fields
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = [
            'Content-Type: application/json',
            'USER: '.$username,
            'DIGEST: '.$digest,
            'CREATED: '.$now
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $server_output = curl_exec ($ch);
            
            curl_close ($ch);
            var_dump($server_output);

            // Store OTP in a way that it can be verified later (e.g., in the database)
            $user = User::where('phone', $phone)->first();
            if ($user) {
                $user->otp = $otp;
                $user->otp_expires_at = now()->addMinutes(10);
                $user->save();
            }
            session(['phone' => $phone]);
            return redirect()->route('otp.verify.view')->with('message', 'OTP sent to your phone.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed sending OTP code.');
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid data.');
        }

        $user = User::where('phone', $request->input('phone'))
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid OTP or session expired.');
        }
        return redirect()->route('password.reset.view')->with('message', 'Validation Success! Now you can reset your password.');
    }
    
    public function showResetForm()
    {
        
        return view('auth.password_reset_form');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'phone.required' => 'Phone session not fount, try again.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        try {
            $user = User::where('phone', $request->input('phone'))->first();

            if (!$user) {
                return redirect()->back()->with('error', 'No user found with the provided phone number.');
            }

            $user->password = Hash::make($request->password);
            $user->save();

            Session::forget('phone');
            return redirect()->route('login')->with('message', 'Password reset successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while resetting the password. Please try again.');
        }
    }

}
