<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Hash;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admins', ['except' => ['admin_logout']]);
    }

    public function login()
    {
    	return view('back.admin_login');
    }

    public function login_check(Request $request)
    {
    	$rules = [
	        'username' 	=> 'required',
	        'password'	=> 'required|min:6',
	    ];

	    $messages = [
	        'username.required' => '名稱不可為空',
	        'password.required'	=> '密碼不可為空',
	        'password.min'		=> '密碼至少六個字元',
	    ];

	    $this->validate($request, $rules, $messages);
	    //------------------------------------------------------
        
	    if( Auth::guard('admins')->attempt(['name' => $request->username, 'password' => $request->password], $request->remember) ){
	    	return redirect()->intended(route('admin.home'));
	    }else{
	    	return redirect()->back()->withInput($request->only('username', 'remember'));
	    }
    }

    public function admin_logout(Request $request)
    {
        Auth::guard('admins')->logout();

        //$this->guard()->logout();

        //$request->session()->invalidate();

        return redirect('/');
    }
}
