<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OTPVerificationController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)
                  ->where('otp', $request->otp)
                  ->where('otp_expires_at', '>', now())
                  ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré'
            ], 400);
        }

        $user->update([
            'is_active' => true,
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compte vérifié avec succès'
        ]);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expires_at = now()->addMinutes(4);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $otp_expires_at
        ]);

        Mail::to($user->email)->send(new OTPVerification($user));

        return response()->json([
            'success' => true,
            'message' => 'Nouveau code OTP envoyé'
        ]);
    }
}