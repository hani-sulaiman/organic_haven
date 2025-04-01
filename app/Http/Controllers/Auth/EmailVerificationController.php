<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified.'
            ], 400);
        }

        // Generate new OTP
        $otp = Str::random(6);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send verification email
        Mail::to($user->email)->send(new VerifyEmail($user));

        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully.',
        ], 200);
    }
}
