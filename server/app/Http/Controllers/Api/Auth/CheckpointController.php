<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Google2FA;
use App\Http\Controllers\Controller;
use App\Otp\Otp;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
            'fingerprint' => 'required|string'
        ]);

        if (! Otp::verify($request->otp, $request->fingerprint)) {
            return response()->json([
               'message' => 'Your OTP didn\'t match.'
            ], 422);
        }

        return response()->json([
            'message' => 'OTP verified successfully!'
        ]);
    }

    public function resend()
    {
        Otp::send();

        return response()->json([
            'message' => 'OTP was resent.'
        ]);
    }

    public function activateGoogle2fa()
    {
        $secret = Google2FA::generateSecretKey();

        $user = Auth::user();

        $user->google2fa_secret = $secret;
        $user->is_google2fa_activated = true;
        $user->save();

        $g2faUrl = Google2FA::getQRCodeInline(
            $user->name,
            $user->email,
            $secret
        );

        return response()->json([
            'g2faUrl' => $g2faUrl
        ]);
    }
}
