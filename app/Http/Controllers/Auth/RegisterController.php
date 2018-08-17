<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Users;
use App\RegisterVerify;
use App\Mail\Register_Verify;
use App\Jobs\Delete_Verify_Row;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Socialite;
use Mail;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    public function showVerifyForm()
    {
        return view('auth.verify_form');
    }

    public function sendVerifyForm(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255|unique:users',
        ];

        $this->validate($request, $rules);
        //------------------------------------------------------------

        $token = str_random(40);
        $email = $request->email;

        $pre_verify = RegisterVerify::where('email', $email)->first();
        if ($pre_verify!=null) {
            Session::flash('fail', 'token尚未失效');

            return redirect()->route('login');
        }

        $verify = RegisterVerify::create([
            'email' =>  $email,
            'token' =>  $token,
        ]);

        //驗證有效時間為10分鐘 發送SQL刪除到佇列 delay十分鐘後執行
        $del_job = (new Delete_Verify_Row($token, $email))->delay(60*10);
        dispatch($del_job);

        Mail::to($email)->queue(new Register_Verify($token, $email));

        return redirect()->route('login');
    }

    public function showRegistrationForm(Request $request, $token, $email)
    {
        $result = RegisterVerify::where('email', $email)->where('token', $token)->first();

        if ($result) {
            $email = $result->email;

            Session::put('register_email', $email);
            Session::put('register_token', $token);

            return view('auth.register', compact('email'));
        } else {
            Session::flash('fail', '註冊驗證碼已過期');
            return redirect()->route('login');
        }
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ];

        $this->validate($request, $rules);
        //------------------------------------------------------------

        $email = Session::get('register_email');
        $token = Session::get('register_token');

        $user = Users::create([
            'name' => $request->name,
            'email' => $email,
            'password' => bcrypt($request->password),
        ]);

        RegisterVerify::where('email', $email)->where('token', $token)->delete();

        Session::forget('register_email');
        Session::forget('register_token');
        Session::save();

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
