<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming API authentication request
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // Valider les credentials
        $request->authenticate();

        // Récupérer l'utilisateur avec les relations nécessaires
        $user = User::with(['roles', 'permissions'])
                   ->where('email', $request->email)
                   ->firstOrFail();

        // Vérifier si le compte est activé (si vous avez ce champ)
        if (isset($user->is_active)) {
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte est désactivé'
                ], 403);
            }
        }

        // Créer le token d'authentification
        $token = $user->createToken('flutter-mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'avatar']),
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'token' => $token,
                'token_type' => 'Bearer'
            ],
            'message' => 'Connexion réussie'
        ]);
    }

    /**
     * Destroy an authenticated API session (logout)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            // Révoquer tous les tokens de l'utilisateur
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion'
            ], 500);
        }
    }

    /**
     * Get authenticated user data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['roles', 'permissions']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'avatar']),
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]
        ]);
    }
}