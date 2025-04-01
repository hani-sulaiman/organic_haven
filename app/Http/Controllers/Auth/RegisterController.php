<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'birthdate' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create user with hashed password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => "customer",
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);

        // Generate OTP
        $otp = Str::random(6); // You can use a numeric OTP if preferred
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send verification email
        Mail::to($user->email)->send(new VerifyEmail($user));

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify your email.',
        ], 201);
    }

    /**
     * Verify OTP code
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.'
            ], 400);
        }

        if ($user->otp_code !== $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired.'
            ], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
        ], 200);
    }
}
