<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Handle password reset request by sending OTP to user's email.
     */
    public function sendOtp(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        // Generate OTP
        $otp = Str::random(6); // Alternatively, use numeric OTP: mt_rand(100000, 999999)

        // Create or update password reset record
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $email],
            [
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        // Send OTP via email
        Mail::to($email)->send(new PasswordResetMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email address.',
        ], 200);
    }

    /**
     * Verify OTP and reset the password.
     */
    public function resetPassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp_code;
        $newPassword = $request->password;

        // Retrieve the password reset record
        $passwordReset = PasswordReset::where('email', $email)->first();

        if (!$passwordReset) {
            return response()->json([
                'success' => false,
                'message' => 'No password reset request found for this email.',
            ], 404);
        }

        if ($passwordReset->otp_code !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.',
            ], 400);
        }

        if ($passwordReset->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired.',
            ], 400);
        }

        // Reset the password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($newPassword);
        $user->save();

        // Delete the password reset record
        $passwordReset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
        ], 200);
    }
}
