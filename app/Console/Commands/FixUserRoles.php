<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class FixUserRoles extends Command
{
    protected $signature = 'user:fix-roles {email? : Email de l\'utilisateur} {--list : Afficher la liste des utilisateurs avec leurs rôles} {--role= : Définir un rôle spécifique (Administrateur, Organizer, Client)}';
    protected $description = 'Gérer les rôles des utilisateurs';

    public function handle()
    {
        if ($this->option('list')) {
            return $this->listUsersWithRoles();
        }

        $email = $this->argument('email');
        $role = $this->option('role');

        if (!$email) {
            $this->error('Veuillez spécifier un email utilisateur');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email: {$email}");
            return 1;
        }

        if ($role) {
            return $this->setUserRole($user, $role);
        }

        $this->showUserRoles($user);
        return 0;
    }

    protected function listUsersWithRoles()
    {
        $users = User::with('roles')->get();
        
        $this->info("Liste des utilisateurs et leurs rôles :\n");
        
        $headers = ['ID', 'Nom', 'Email', 'Rôles'];
        $rows = [];
        
        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->roles->pluck('name')->implode(', ') ?: 'Aucun rôle'
            ];
        }
        
        $this->table($headers, $rows);
        return 0;
    }

    protected function showUserRoles(User $user)
    {
        $this->info("Informations de l'utilisateur :");
        $this->line("ID: {$user->id}");
        $this->line("Nom: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Rôles: " . ($user->roles->isNotEmpty() ? $user->getRoleNames()->implode(', ') : 'Aucun rôle'));
        
        // Afficher aussi l'ancienne méthode de vérification pour référence
        $legacyRoleId = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\Models\\User')
            ->value('role_id');
            
        $this->line("Ancien ID de rôle: " . ($legacyRoleId ?: 'Non défini'));
    }

    protected function setUserRole(User $user, string $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Le rôle '{$roleName}' n'existe pas. Rôles disponibles: " . Role::pluck('name')->implode(', '));
            return 1;
        }
        
        // Supprimer tous les rôles existants
        $user->syncRoles([$role->name]);
        
        $this->info("Le rôle de l'utilisateur a été mis à jour avec succès !");
        $this->showUserRoles($user);
        
        return 0;
    }
}
