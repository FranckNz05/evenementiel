<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => 'Lien de réinitialisation envoyé'])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }

    /**
     * Reset password
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé'])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }
}