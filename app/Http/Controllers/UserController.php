<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Http\Controllers\Controller;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class UserController extends Controller
{
    public function userRegistration(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'mobile' => 'nullable',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => $request->password
            ]);

            // return response()->json([
            //     'status' => true,
            //     'message' => "User registration successfull",
            //     'data' => $user,
            // ]);
            $data = ['message' => 'User registration successfull', 'status' => true,  'error' => ''];
            return redirect('/login')->with($data);
        } catch (Exception $e) {
            // return response()->json([
            //     'status' => false,
            //     'message' => 'Registration failed'
            // ]);
            $data = ['message' => 'Registration failed', 'status' => false,  'error' => $e->getMessage()];
            return redirect('/login')->with($data);
        }
    }

    public function UserLogin(Request $request)
    {
        $count = User::where('email', $request->input('email'))->where('password', $request->input('password'))->select('id')->first();

        if ($count != null) {
            //$token = JWTToken::CreateToken($request->input('email'), $count->id);

            $email = $request->input('email');
            $user_id = $count->id;
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'User login successfull',
            //     'token' => $token
            // ], 200)->cookie('token', $token, time() + 60);
            $request->session()->put('email', $email);
            $request->session()->put('user_id', $user_id);

            $data = ['message' => 'User login successful', 'status' => true,  'error' => ''];

            return redirect('/dashboard')->with($data);
        } else {
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'User login failed'
            // ], 200);

            $data = ['message' => 'Invalid email or password', 'status' => false,  'error' => ''];

            return redirect('/login')->with($data);
        }
    }

    public function UserLogout()
    {
        return response()->json([
            'status' => 'success',
            'message' => "User logout successfull"
        ], 200)->cookie('token', '', -1);
    }

    public function SendOtpCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', $email)->count();

        if ($count == 1) {
            Mail::to($email)->send(new OTPMail($otp));
            User::where('email', $email)->update(['otp' => $otp]);

            $request->session()->put('email', $email);

            $data = [
                'status' => true,
                'message' => "4 digit {$otp} Code has been sent to your email",
                'error' => ''
            ];
            return redirect('/verify_otp')->with($data);
            // return response()->json([
            //     'status' => 'success',
            //     'meesage' => "4 digit {$otp} Code has been sent to your email"
            // ], 200);
        } else {
            // return response()->json([
            //     'status' => 'failed',
            //     'meesage' => "Unauthorized"
            // ]);
            $data = [
                'status' => false,
                'message' => "Unauthorized",
                'error' => ''
            ];
            return redirect('/registration')->with($data);
        }
    }

    public function VerifyOtp(Request $request)
    {
        //$email = $request->input('email');
        $email = $request->session()->get('email', 'default');
        $otp = $request->input('otp');

        $count = User::where('email', $email)->where('otp', $otp)->count();

        if ($count == 1) {
            User::where('email', $email)->update(['otp' => '0']);
            //$token = JWTToken::CreateToeknForSetPassword($email);
            $request->session()->put('otp_verified', 'yes');

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'OTP Verification successfull'
            // ], 200)->cookie('token', $token, 60 * 24 * 30);
            $data = ['status' => true, 'message' => 'OTP Verification successfull', 'error' => ''];
            return redirect('/reset_password')->with($data);
        } else {
            // return response()->json([
            //     'status' => 'failed',
            //     'meesage' => "OTP Verification failed"
            // ], 200);
            $data = ['status' => false, 'message' => 'OTP Verification failed', 'error' => ''];
            return redirect('/login')->with($data);
        }
    }

    public function ResetPassword(Request $request)
    {
        try {
            //$email = $request->header('email');
            $email = $request->session()->get('email', 'default');
            $password = $request->input('password');

            $otp_verified = $request->session()->get('otp_verified', 'default');

            if ($otp_verified === 'yes') {
                User::where('email', $email)->update(['password' => $password]);
                $request->session()->flush();

                $data = [
                    'status' => true,
                    'message' => 'Password Reset successful',
                    'error' => ''
                ];

                return redirect('/login')->with($data);
            } else {
                $data = [
                    'status' => false,
                    'message' => 'Requst fail',
                    'error' => ''
                ];
                return redirect('/reset_password')->with($data);
            }

            // return response()->json([
            //     'status' => 'success',
            //     'meesage' => "Password Reset successfull."
            // ], 200);
        } catch (\Exception $e) {
            $data = [
                'status' => false,
                'message' => 'Requst fail',
                'error' => ''
            ];
            return redirect('/reset_password')->with($data);
        }
    }

    public function UserUpdate(Request $request)
    {
        $user_email = $request->header('email');
        $new_email = $request->input('email');

        $user = User::where('email', $user_email)->first();

        $user->update([
            'name' => $request->input('name'),
            'email' => $new_email,
            'mobile' => $request->input('mobile')
        ]);

        if ($user_email !== $new_email) {
            return response()->json([
                'status' => 'success',
                'meesage' => "User updated successfull, you have been logged out due to email change"
            ])->cookie('token', '', -1);
        }

        return response()->json([
            'status' => 'success',
            'meesage' => "User updated successfull"
        ]);
    }

    public function LoginPage()
    {
        return Inertia::render('LoginPage');
    }

    public function RegistrationPage()
    {
        return Inertia::render('RegistrationPage');
    }

    public function SendOtpPage()
    {
        return Inertia::render('SentOtpPage');
    }

    public function VerifyOtpPage()
    {
        return Inertia::render('VerifyOtpPage');
    }

    public function ResetPasswordPage()
    {
        return Inertia::render('ResetPasswordPage');
    }
}
