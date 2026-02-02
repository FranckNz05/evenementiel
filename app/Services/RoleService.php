<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    /**
     * Attribue un rôle à un utilisateur
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function assignRole(User $user, string $role): bool
    {
        try {
            // Vérifier si le rôle existe
            $roleId = config("roles.role_ids.{$role}");
            if (!$roleId) {
                Log::error("Tentative d'attribution d'un rôle invalide: {$role}");
                return false;
            }

            // Vérifier si l'utilisateur a déjà ce rôle
            if ($this->hasRole($user, $role)) {
                return true;
            }

            // Si l'utilisateur n'a pas de rôle, on lui en attribue un
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'model_id' => $user->id,
                    'model_type' => get_class($user)
                ],
                ['role_id' => $roleId]
            );

            // Rafraîchir le modèle utilisateur
            $user->load('roles');

            Log::info("Rôle {$role} attribué à l'utilisateur {$user->email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'attribution du rôle: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un rôle à un utilisateur
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function removeRole(User $user, string $role): bool
    {
        try {
            $roleId = config("roles.role_ids.{$role}");
            if (!$roleId) {
                return false;
            }

            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', get_class($user))
                ->where('role_id', $roleId)
                ->delete();

            // Rafraîchir le modèle utilisateur
            $user->load('roles');

            Log::info("Rôle {$role} retiré à l'utilisateur {$user->email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression du rôle: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur a un rôle spécifique
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool
    {
        $roleId = config("roles.role_ids.{$role}");
        if (!$roleId) {
            return false;
        }

        // Vérification via Spatie si disponible
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Fallback sur l'ancienne méthode
        return DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('role_id', $roleId)
            ->exists();
    }

    /**
     * Synchronise les rôles d'un utilisateur
     *
     * @param User $user
     * @param array $roles
     * @return bool
     */
    public function syncRoles(User $user, array $roles): bool
    {
        try {
            $roleIds = [];
            
            // Convertir les noms de rôles en IDs
            foreach ($roles as $role) {
                $roleId = config("roles.role_ids.{$role}");
                if ($roleId) {
                    $roleIds[] = $roleId;
                }
            }

            if (empty($roleIds)) {
                return false;
            }

            // Supprimer les rôles existants
            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', get_class($user))
                ->delete();

            // Ajouter les nouveaux rôles
            $data = array_map(function ($roleId) use ($user) {
                return [
                    'role_id' => $roleId,
                    'model_id' => $user->id,
                    'model_type' => get_class($user)
                ];
            }, $roleIds);

            DB::table('model_has_roles')->insert($data);

            // Rafraîchir le modèle utilisateur
            $user->load('roles');

            Log::info("Rôles synchronisés pour l'utilisateur {$user->email}", ['roles' => $roles]);
            return true;

        } catch (\Exception $e) {
            Log::error("Erreur lors de la synchronisation des rôles: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les rôles disponibles
     *
     * @return array
     */
    public function getAllRoles(): array
    {
        return config('roles.role_names', []);
    }

    /**
     * Vérifie si un utilisateur a une permission spécifique
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(User $user, string $permission): bool
    {
        // L'administrateur a toutes les permissions
        if ($this->hasRole($user, 'ADMIN')) {
            return true;
        }

        // Vérification via Spatie si disponible
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Récupérer les rôles de l'utilisateur
        $userRoles = [];
        if (method_exists($user, 'getRoleNames')) {
            $userRoles = $user->getRoleNames()->toArray();
        } else {
            // Fallback pour l'ancienne méthode
            $roleId = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', get_class($user))
                ->value('role_id');
            
            if ($roleId) {
                $userRoles = [array_search($roleId, config('roles.role_ids', []))];
            }
        }

        // Vérifier si l'un des rôles de l'utilisateur a la permission
        foreach ($userRoles as $role) {
            $rolePermissions = config("roles.permissions.{$role}", []);
            if (in_array($permission, $rolePermissions)) {
                return true;
            }
        }

        return false;
    }
}
