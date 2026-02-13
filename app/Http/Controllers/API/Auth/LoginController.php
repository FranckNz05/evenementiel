<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        $user = User::with(['roles', 'permissions'])
                  ->where('email', $request->email)
                  ->first();

        // Vérification du statut du compte
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est désactivé ou non vérifié'
            ], 403);
        }

        // Vérification OTP si nécessaire
        if ($user->otp && $user->otp_expires_at > now()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez vérifier votre email avec le code OTP',
                'requires_otp' => true
            ], 403);
        }

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'genre' => $user->genre,
                    'tranche_age' => $user->tranche_age,
                    'profil_image' => $user->profil_image,
                    'is_profile_complete' => $user->is_profile_complete,
                ],
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }
}