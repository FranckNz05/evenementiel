<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CheckUserRoles extends Command
{
    protected $signature = 'user:check-roles {email}';
    protected $description = 'Vérifie les rôles d\'un utilisateur';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Utilisateur non trouvé avec l'email: {$email}");
            return;
        }

        $this->info("Rôles de l'utilisateur {$email}:");
        $roles = $user->roles()->get();
        
        foreach ($roles as $role) {
            $this->line("- {$role->name} (ID: {$role->id})");
            
            // Afficher les permissions du rôle
            $this->info("  Permissions:");
            $permissions = $role->permissions()->get();
            foreach ($permissions as $permission) {
                $this->line("  - {$permission->name} (ID: {$permission->id})");
            }
        }
    }
} 