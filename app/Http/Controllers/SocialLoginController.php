<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Sociallink;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
//    public function __construct()
//    {
//        $links = Sociallink::where('redirect', '!=', null)->where('status', 1)->get();
//        foreach ($links as $link){
//            Config::set('services.'.$link->provider.'.client_id', $link->client_id);
//            Config::set('services.'.$link->provider.'.client_secret', $link->client_secret);
//            Config::set('services.'.$link->provider.'.redirect', route('social.callback', $link->provider));
//        }
//    }

    //redirect provider
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    //handle Provider Callback response
    public function handleProviderCallback($provider)
    {

        try {
            if($provider == 'twitter'){
                $socialUser = Socialite::driver('twitter')->user();
            }
            else{
                $socialUser = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            Toastr::error("Something Went wrong. Please try again.");
            return back();
        }

        // check if they're an existing user
        $existingUser = User::where('provider_id', $socialUser->id)->first();

        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
        } else {
            // create a new user
            $newUser                  = new User();
            $newUser->name            = $socialUser->name;
            $newUser->username        = Str::slug($socialUser->name);
            $newUser->email           = $socialUser->email;
            $newUser->phato           = $socialUser->avatar;
            $newUser->role_id           = 1;
            $newUser->email_verified_at = now();
            $newUser->provider    = $provider;
            $newUser->provider_id     = $socialUser->id;
            $success = $newUser->save();

            if($success){
                //log in
                auth()->login($newUser, true);
                //insert notification in database
                Notification::create([
                    'type' => 'register',
                    'fromUser' => Auth::id(),
                    'toUser' => 0,
                    'item_id' => Auth::id(),
                    'notify' => 'register new user',
                ]);
            }
        }
        if(Session::has('redirectLink') != null){
            return redirect(Session::get('redirectLink'));
        }
        else{
            return redirect()->intended(route('user.dashboard'));
        }

    }
}
