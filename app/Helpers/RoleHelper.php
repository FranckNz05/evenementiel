<?php

namespace App\Helpers;

use App\Models\User;

class RoleHelper
{
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     *
     * @param User|null $user
     * @param string $role
     * @return bool
     */
    public static function hasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }

        // Vérification via Spatie si disponible
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Fallback sur l'ancienne méthode
        $roleId = config("roles.role_ids.{$role}");
        if (!$roleId) {
            return false;
        }

        return (bool) DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\Models\\User')
            ->where('role_id', $roleId)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a l'un des rôles spécifiés
     *
     * @param User|null $user
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole($user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (self::hasRole($user, $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Récupère le nom du rôle pour l'affichage
     *
     * @param int $roleId
     * @return string
     */
    public static function getRoleName(int $roleId): string
    {
        return config("roles.role_names.{$roleId}", 'Utilisateur');
    }

    /**
     * Vérifie si l'utilisateur a une permission spécifique
     *
     * @param User|null $user
     * @param string $permission
     * @return bool
     */
    public static function can($user, string $permission): bool
    {
        if (!$user) {
            return false;
        }

        // Si l'utilisateur est admin, il a toutes les permissions
        if (self::hasRole($user, 'ADMIN')) {
            return true;
        }

        // Vérification via Spatie si disponible
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Vérification basée sur la configuration
        $userRoles = [];
        if (method_exists($user, 'getRoleNames')) {
            $userRoles = $user->getRoleNames()->toArray();
        } else {
            // Fallback pour l'ancienne méthode
            $roleId = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
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

    /**
     * Récupère le rôle par défaut pour les nouveaux utilisateurs
     *
     * @return string
     */
    public static function getDefaultRole(): string
    {
        return config('roles.default_role', 'USER');
    }

    /**
     * Récupère l'ID du rôle par son nom
     *
     * @param string $roleName
     * @return int|null
     */
    public static function getRoleId(string $roleName): ?int
    {
        return config("roles.role_ids.{$roleName}");
    }

    /**
     * Récupère le nom du rôle par son ID
     *
     * @param int $roleId
     * @return string
     */
    public static function getRoleNameById(int $roleId): string
    {
        return config("roles.role_names.{$roleId}", 'Utilisateur');
    }
}
