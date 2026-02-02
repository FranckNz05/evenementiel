<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserPreference;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Log;

class UpdateUserPreferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preferences:update {--user-id= : Mettre à jour les préférences d\'un utilisateur spécifique} {--force : Forcer la mise à jour même si pas nécessaire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour les préférences utilisateurs pour les recommandations';

    protected $homeController;

    public function __construct()
    {
        parent::__construct();
        $this->homeController = new HomeController();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $force = $this->option('force');

        if ($userId) {
            // Mettre à jour un utilisateur spécifique
            $user = User::find($userId);
            if (!$user) {
                $this->error("Utilisateur avec ID {$userId} introuvable.");
                return Command::FAILURE;
            }
            $this->updateUserPreferences($user, $force);
        } else {
            // Mettre à jour tous les utilisateurs
            $this->info('Mise à jour des préférences de tous les utilisateurs...');
            $users = User::all(); // Tous les utilisateurs, avec ou sans préférences
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();

            $updated = 0;
            foreach ($users as $user) {
                if ($this->updateUserPreferences($user, $force)) {
                    $updated++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Mise à jour terminée : {$updated} utilisateurs mis à jour sur {$users->count()}.");
        }

        return Command::SUCCESS;
    }

    protected function updateUserPreferences(User $user, bool $force = false): bool
    {
        try {
            $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);

            $updated = false;

            // Mettre à jour les préférences si nécessaire
            if ($force || $preferences->needsUpdate()) {
                $this->homeController->updateUserPreferences($user, $preferences);
                $updated = true;
            }

            // Mettre à jour la similarité si nécessaire
            if ($force || $preferences->needsSimilarityUpdate()) {
                $this->homeController->updateUserSimilarity($user, $preferences);
                $updated = true;
            }

            return $updated;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour des préférences pour l'utilisateur {$user->id}: " . $e->getMessage());
            return false;
        }
    }
}
