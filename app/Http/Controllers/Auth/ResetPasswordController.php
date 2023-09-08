<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
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


    protected $redirectTo = RouteServiceProvider::HOME;

    
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

     /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => trans($response)], 200);
        }
        if($request->user()->role == '2'){
            return redirect($this->redirectPath())->with('status', trans($response));
        }
        return redirect('/admin/dashboard')->with('status', trans($response));
    }
}
