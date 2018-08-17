<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Password;

class AdminForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admins');
    }

    public function broker()
    {
    	return Password::broker('admins');
    }

    public function showLinkRequestForm()
    {
    	return view('auth.passwords.admin_email');
    }

    public function sendResetLinkEmail(Request $request)
    {
    	$rules = [
    		'name'	=> ['required'],
    	];

    	$messages = [
    		'name.required'   => '名稱必填',
    	];

    	$this->validate($request, $rules, $messages);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('name')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    public function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            ['name' => trans($response)]
        );
    }
}
