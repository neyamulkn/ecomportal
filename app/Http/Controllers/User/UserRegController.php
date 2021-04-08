<?php

namespace App\Http\Controllers\User;

use App\Models\SiteSetting;
use App\Models\Notification;
use App\Traits\CreateSlug;
use App\Traits\Sms;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserRegController extends Controller
{
    use Sms;
    use CreateSlug;
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function RegisterForm() {
        return view('users.register');
    }

    public function register(Request $request) {

        if (config('siteSetting.registration') == 0) {
            Toastr::error('Registration is closed by Admin.');
            Session::flash('alert', 'Registration is closed by Admin');
            return back();
        }

        $reCaptcha = SiteSetting::where('type', 'google_recaptcha')->first();
        if($reCaptcha->status == 1 && isset($_POST['g-recaptcha-response'])){
            $secretKey = $reCaptcha->secret_key;
            $captcha = $_POST['g-recaptcha-response'];
            
            if(!$captcha){
                Toastr::error('Please check the robot check.');
                return back();
            }
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
            $responseKeys = json_decode($response,true);


            if(intval($responseKeys["success"]) !== 1) {
                Toastr::error('Please check the robot check.');
                return back();
            }
        }
        

        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:users|min:11|numeric|regex:/(01)[0-9]/',
            'password' => 'required|min:6'
        ]);

        if($request->email){
            $request->validate([
               'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        }

        $mobile = trim($request->mobile);
        $email = trim($request->email);
        $password = trim($request['password']);

        $username = $this->createSlug('users', $request->name, 'username');
        $username = trim($username, '-');
        $user = new User;
        $user->name = $request->name;
        $user->username = $username;
        $user->email =  $email;
        $user->mobile = $mobile;
        $user->password = Hash::make($password);
        $success = $user->save();
        if($success) {

            $fieldType = ($request->email ? 'email' : 'mobile');
            $emailOrMobile = ($request->email ? $request->email : $request->mobile);

            Cookie::queue('emailOrMobile',$mobile, time() + (86400));
            Cookie::queue('password', $password, time() + (86400));

            if (Auth::attempt([$fieldType => $emailOrMobile, 'password' => $password])) {
                //send mobile notify
                if(Auth::user()->mobile){
                    $customer_mobile = Auth::user()->mobile;
                    $msg = 'Hello '.Auth::user()->name.', Thank you for registering with '.$_SERVER['SERVER_NAME'].'.';
                    $this->sendSms($customer_mobile, $msg);
                }

                //insert notification in database
                Notification::create([
                    'type' => 'register',
                    'fromUser' => Auth::id(),
                    'toUser' => null,
                    'item_id' => Auth::id(),
                    'notify' => 'register new user',
                ]);
                Toastr::success('Registration in success.');
                if(Session::has('redirectLink')){
                    return redirect(Session::get('redirectLink'));
                }
                return redirect()->intended(route('user.dashboard'));
            }
        }else{
            Toastr::error('Registration failed try again.');
            return back()->withInput();
        }
    }
}
