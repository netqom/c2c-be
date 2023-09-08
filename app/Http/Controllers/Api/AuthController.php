<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Product};
use Auth,Mail,DB;
use Illuminate\Support\Facades\{Lang,Hash,Validator}; 
use Carbon\Carbon; 
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $user;
    public function __construct(User $user){
        $this->user    = $user;
    }


    /**
     * create new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'address' => ['required'],
        ]);
        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $result = $this->user->createUpdateItem($request);
        if($result){
            //$token = $result->createToken('web')->plainTextToken;
            return response()->json(['status' => true,'message' => Lang::get('auth.accountCreated') ]);
        }else{
            return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
        }
    }


    /**
     * Login for normal user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'email' => 'required|string', 'password' => 'required|string' ]);
        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $request->request->add(['role' => 2,'status' => 1]); //add request
        $credentials = $request->only('email', 'password','role','status');
        if(auth()->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();
            if( $user->email_verified_at == null ){
                return response()->json(['status' => false, 'message' => Lang::get('auth.verifyEmailFirst')]);
            }
            $token = $user->createToken('web')->plainTextToken;
            return response()->json(['status' => true, 'message' => Lang::get('auth.loggedIn'), 'user' => $user, 'access_token' => $token ]);
        }

        $user = User::where(['email' => $request->email])->first();
        if (!empty($user)) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json(['success'=>false, 'message' => Lang::get('auth.accountDeactive')]);
            }
        }
        return response()->json(['status' => false, 'message' => Lang::get('auth.invalidCredentials')]);
    }

    /**
     * Logout Functioanlity Start From Here
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json(['status' => true, 'message' => Lang::get('auth.logout')]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitForgetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => ['required', 'string', 'email', 'max:255', 'exists:users']]);
        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $token = Str::random(64);
        $url = env('FRONTEND_APP_URL').'/reset-password/'. $token;
        $user = User::where(['email' => $request->email])->first();
        DB::table('password_resets')->where(['email' => $request->email])->delete();
        DB::table('password_resets')->insert(['email' => $request->email, 'token' => $token]);
        if(Mail::send('emails.forgetPassword', ['token' => $token, 'email' => $request->email,'url' => $url , 'name' => $user['name']], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        })){
            return response()->json(['status' => true, 'message' => Lang::get('auth.passwordResetEmailSent')]);
        }
        return response()->json(['status' => true, 'message' => Lang::get('auth.passwordResetEmailSent')]);
        
    }

    /** Reset Password Form Submit */
    public function submitResetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => '', 'errors' => $validator->errors()->first()]);
        }
        $user = DB::table('password_resets')->where(['token' => $request->resetToken])->latest()->first();
        if(empty($user)){
            return response()->json(['status' => false, 'message' => Lang::get('auth.expireToken') ]);
        }
        User::where(['email' => $user->email])->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('email', $user->email)->delete();
        return response()->json(['status' => true, 'message' => Lang::get('auth.resetPassword')]);
    }

    /** Verify Email Address **/
    public function verifyEmail(Request $request)
    {
        $user = User::where(['email_verification_code' => $request->verifyCode])->first();
        if(empty($user)){
            return response()->json(['status' => false, 'message' => Lang::get('auth.verificationCode') ]);
        }
        User::where(['email_verification_code' => $request->verifyCode])->update([
            'email_verified_at' => date('Y-m-d h:i:s'),
            'email_verification_code' => ''
        ]);
        return response()->json(['status' => true, 'message' => Lang::get('auth.emailVerfied') , 'user' => $user]);

    }
}
