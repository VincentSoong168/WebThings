<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Auth;
use Password;

class AdminResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/panel/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admins');
    }

    protected function guard()
    {
        return Auth::guard('admins');
    }

    protected function broker()
    {
        return Password::broker('admins');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.admin_reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    protected function rules()
    {
        return [
            'token'     => 'required',
            'name'      => 'required|min:3',
            'password'  => 'required|confirmed|min:6',
        ];
    }

    protected function validationErrorMessages()
    {
        return [];
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'name', 'password', 'password_confirmation', 'token'
        );
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                    ->withInput($request->only('name'))
                    ->withErrors(['name' => trans($response)]);
    }
}
