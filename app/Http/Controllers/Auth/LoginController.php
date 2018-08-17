<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Users;
use Socialite;
use Auth;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'user_logout']]);
    }

    public function user_logout(Request $request)
    {
        Auth::guard('web')->logout();

        //$this->guard()->logout();

        //$request->session()->invalidate();

        return redirect('/');
    }

    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebook_callback()
    {
        $facebook = Socialite::driver('facebook')->user();
        $facebook_email = $facebook->getEmail();
        $facebook_name = $facebook->getName();

        $user = Users::where('email', $facebook_email)->first();

        if(!$user){
            $user = Users::create([
                        'name' => $facebook_name,
                        'email' => $facebook_email,
                        'password' => bcrypt(str_random(8)),
                    ]);
        }

        Auth::loginUsingId($user->id);

        return redirect()->intended(route('home'));
    }
}
