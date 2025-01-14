<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use App\Mail\PasswordReset as MailPasswordReset;
use App\Models\PasswordReset;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// use Laravel\Socialite\Facades\Socialite;

// use Laravel\Socialite\Facades\Socialite;

class loginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->only('email', 'password'),
            [
                'email'    => 'required|email',
                'password' => 'required|min:8',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()->all(),
            ]);
        }

        $validated = $validator->validated();

        $user = User::where('email', $validated['email'])->first();
        $role = User::where('email', $validated['email'])->with('role')->get();
        // dd($role);
        if (!$user) {
            return response()->json([
                'success' => 0,
                'message' => 'Email not found!',
            ]);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => 0,
                'message' => 'Password does not match!',
            ]);
        }

        $token_exists = DB::table('personal_access_tokens')->where('tokenable_id', '=', $user['_id'])->first();


        if ($token_exists) {
            DB::table('personal_access_tokens')->where('tokenable_id', $user['_id'])->delete();
            $token = $user->createToken('authToken')->plainTextToken;
            DB::table('personal_access_tokens')->where('tokenable_id', $user['_id'])->update(['token' => $token]);
        } else {
            $token = $user->createToken('authToken')->plainTextToken;
        }
        return response()->json([
            'success' => 1,
            'message' => 'Login successful!',
            'token'   => $token,
            'role'   => $role,
            'user'    => $user
        ], 200);
    }

    public function Signup(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name'    => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'number'   => 'required',
                'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()->all(),
            ]);
        }


        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path(), $fileName);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->number = $request->number;

        if (isset($fileName)) {
            $user->file = $fileName;
        }


        if ($user->save()) {
            return response()->json([
                'success' => 1,
                'message' => 'User Created Successfully!',
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'message' => 'Error While Creating User!',
            ]);
        }
    }

    public function sendOTP(Request $request)
    {
        $validated = Validator::make(
            $request->only(['email']),
            [
                'email'   => 'required|email|exists:users,email'
            ],
            [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.exists' => 'This email does not exist in our records.',
            ]
        );

        if ($validated->fails()) {
            return response()->json([
                'success' => 0,
                'error' => $validated->errors()->all()
            ], 422);
        }
        $otp = random_int(1000, 9999);

        if (User::where('email', $request->email)->exists()) {
            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(5),
                ]
            );

            $user = User::where('email', $request->email)->first();
            $url = env('FRONTEND_URL') . 'updatePassword?email=' . urlencode($request->email) . '&otp=' . $otp;

            Mail::to($request->email)->send(new MailPasswordReset($otp, $user, $url));
            return response()->json(
                [
                    'success' => 1,
                    'message' => 'OTP has been sent to your email. Check your email to update password!'
                ]
            );
        }

        return response()->json(['message' => 'Email does not exist in our records.'], 404);
    }

    public function updatePassword(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required',
            'otp'   => 'required',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'success'   => 0,
                'error'    => $validated->errors()->all()
            ]);
        }

        $exist_email = User::where('email', $request->email)->exists();

        if ($exist_email) {
            $otp = (int)$request->otp;
            $otpRecord = PasswordReset::where('email', $request->email)
                ->where('otp', $otp)
                ->first();
            if ($otpRecord) {
                $otpTime = $otpRecord->expires_at;
                $currentTime = now();

                if ($otpTime > $currentTime) {
                    $user = User::where('email', $request->email)->first();

                    if ($user) {
                        $user->update([
                            'password' => Hash::make($request->password)
                        ]);

                        $otpRecord->delete();

                        return response()->json([
                            'success' => 1,
                            'message' => 'Password Updated Successfully!'
                        ]);
                    } else {
                        return response()->json([
                            'success' => 0,
                            'message' => 'User Not Found!'
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => 0,
                        'message' => 'OTP has expired'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => 0,
                    'message' => 'Incorrect OTP!'
                ]);
            }
        } else {
            return response()->json([
                'success'   => 0,
                'message'    => 'Invalid Email!'
            ]);
        }
    }
}
