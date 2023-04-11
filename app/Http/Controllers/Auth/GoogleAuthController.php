<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use App\Providers\RouteServiceProvider;
use Exception;


class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        //check if google auth is enabled
        if(!config('services.google.enabled')) {
            return redirect()->route('login')->withErrors(['error' => __('Google authentication is not enabled.')]);
        }
        
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            Log::error('Error in Google Auth Callback: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => __('Unable to authenticate with Google. Please try again.')]);
        }
        //write to log google user
        Log::info("Google User Login", [$googleUser]);

        $user = User::firstOrNew(['email' => $googleUser->getEmail()]);
        if($user->exists) {
            if(!empty($user->auth_provider) && $user->auth_provider != 'google') {
                return redirect()->route('login')->withErrors(['error' => __('This email is already registered with another authentication provider.')]);
            }
            if(!empty($user->auth_provider_id) && $user->auth_provider_id != $googleUser->getId()) {
                return redirect()->route('login')->withErrors(['error' => __('This email seems to be already registered with another Google account.')]);
            }
            //if user registered manually and then tries to login with google, let's update the auth_provider and auth_provider_id
            if(empty($user->auth_provider)) {
                $user->auth_provider = 'google';
            }
            if(empty($user->auth_provider_id)) {
                $user->auth_provider_id = $googleUser->getId();
            }
            $user->save();
        } else {
            $user->name = $googleUser->getName();
            $user->email = $googleUser->getEmail();
            $user->password = Hash::make(Str::random(24));
            $user->auth_provider = 'google';
            $user->auth_provider_id = $googleUser->getId();
            $user->save();
            //assign 'registered' role to users as default
            $role = Role::findByName('registered');
            $user->assignRole($role);
            event(new Registered($user));
        }

        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}
