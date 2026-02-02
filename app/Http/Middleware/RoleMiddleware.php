<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Administrateur\DashboardController as AdminDashboardController;

class RoleMiddleware
{

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // 1. Vérification de l'authentification
        if (!$user) {
            Log::warning('Unauthenticated user attempted to access role-protected route');
            abort(403, 'Accès non autorisé. Veuillez vous connecter.');
        }

        // 2. Validation des rôles demandés
        // Convertir les rôles séparés par des | en tableau
        $rolesList = [];
        foreach ($roles as $role) {
            if (strpos($role, '|') !== false) {
                $rolesList = array_merge($rolesList, explode('|', $role));
            } else {
                $rolesList[] = $role;
            }
        }

        // Utiliser les noms de rôles au lieu des IDs
        if (empty($rolesList)) {
            Log::error('No roles specified in route middleware');
            abort(500, 'Configuration de sécurité invalide : aucun rôle spécifié');
        }

        // 3. Vérification des rôles utilisateur par nom plutôt que par ID
        $hasRequiredRole = false;
        foreach ($rolesList as $role) {
            if ($user->hasRole($role)) {
                $hasRequiredRole = true;
                break;
            }
        }

        if (!$hasRequiredRole) {
            Log::warning('Role access denied', [
                'user_id' => $user->id,
                'user_roles' => $user->roles()->pluck('name')->toArray(),
                'required_roles' => $rolesList
            ]);

            abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}


