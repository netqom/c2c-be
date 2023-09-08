<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use Session;
use Illuminate\Support\Facades\Password;
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails {
        // make the trait's method available as traitSendResetLinkEmail
        sendResetLinkEmail as public traitSendResetLinkEmail;
    }

    public function sendResetLinkEmail(Request $request){
        $this->validateEmail($request);
        $user = User::where('email', $request->email)->first();
        
        if (!$user || $user->role != 1 ) {
            Session::put('status','User not exist.');
            return redirect()->back()->withErrors(['email' => '...']);
        }
        return $this->traitSendResetLinkEmail($request); 
    }  
}
