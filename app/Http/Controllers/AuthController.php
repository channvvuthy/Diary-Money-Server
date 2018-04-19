<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rules = [
            'email' => 'required|min:3|unique:users',
            'password' => 'required'
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }
        $email = $request['email'];
        $password = $request->password;
        $name=$email;
        $user =new User();
        $user->password=bcrypt($password);
        $user->email=$email;
        $user->save();
        $verification_code = str_random(30); //Generate verification code
        if ($user->save()) {
            $subject = "Please verify your email address.";
            Mail::send('email.verify', ['email' => $email, 'verification_code' => $verification_code],
                function ($mail) use ($email, $name, $subject) {
                    $mail->from(getenv('FROM_EMAIL_ADDRESS'), "channvuthyit@gmail.com");
                    $mail->to($email, $name);
                    $mail->subject($subject);
                });
        }
        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);
        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration.']);
    }
    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token',$verification_code)->first();
        if(!is_null($check)){
            $user = User::find($check->user_id);
            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'Account already verified..'
                ]);
            }
            $user->is_verified=1;
            $user->save();
            DB::table('user_verifications')->where('token',$verification_code)->delete();
            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully verified your email address.'
            ]);
        }
        return response()->json(['success'=> false, 'error'=> "Verification code is invalid."]);
    }
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }


}
