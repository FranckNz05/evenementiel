<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La table views existe déjà, on ajoute seulement les nouvelles colonnes
        Schema::table('views', function (Blueprint $table) {
            // Ajouter ip_address si elle n'existe pas
            if (!Schema::hasColumn('views', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('user_id');
            }
            // Ajouter viewed_at si elle n'existe pas
            if (!Schema::hasColumn('views', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('ip_address');
            }
        });
        
        // Ajouter les index pour améliorer les performances (si ils n'existent pas)
        // On utilise DB::raw pour éviter les erreurs si l'index existe déjà
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        // Index composite pour vérifier rapidement les vues par user/jour
        $indexExists1 = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, 'views', 'views_user_id_viewed_at_index']
        );
        
        if ($indexExists1[0]->count == 0) {
            Schema::table('views', function (Blueprint $table) {
                $table->index(['user_id', 'viewed_at'], 'views_user_id_viewed_at_index');
            });
        }
        
        // Index composite pour vérifier rapidement les vues par IP/jour
        $indexExists2 = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, 'views', 'views_ip_address_viewed_at_index']
        );
        
        if ($indexExists2[0]->count == 0) {
            Schema::table('views', function (Blueprint $table) {
                $table->index(['ip_address', 'viewed_at'], 'views_ip_address_viewed_at_index');
            });
        }
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            // Supprimer les index s'ils existent
            try {
                $table->dropIndex('views_user_id_viewed_at_index');
            } catch (\Exception $e) {
                // L'index n'existe pas, on continue
            }
            try {
                $table->dropIndex('views_ip_address_viewed_at_index');
            } catch (\Exception $e) {
                // L'index n'existe pas, on continue
            }
            
            // Supprimer les colonnes
            if (Schema::hasColumn('views', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            if (Schema::hasColumn('views', 'viewed_at')) {
                $table->dropColumn('viewed_at');
            }
        });
    }
};
