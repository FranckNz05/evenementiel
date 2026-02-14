#!/bin/bash

# Script pour marquer une migration comme exécutée
# Usage: ./mark-migration-done.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Marquage de la migration create_views_table comme exécutée..."

php artisan tinker --execute="
try {
    // Vérifier si la migration est déjà enregistrée
    \$exists = DB::table('migrations')
        ->where('migration', '2026_02_14_000000_create_views_table')
        ->exists();
    
    if (!\$exists) {
        // Obtenir le batch actuel
        \$maxBatch = DB::table('migrations')->max('batch') ?? 0;
        \$newBatch = \$maxBatch + 1;
        
        // Insérer la migration avec un ID auto-incrémenté
        // Laravel gère automatiquement l'ID, donc on ne le spécifie pas
        DB::table('migrations')->insert([
            'migration' => '2026_02_14_000000_create_views_table',
            'batch' => \$newBatch
        ]);
        
        echo '✓ Migration marquée comme exécutée (batch: ' . \$newBatch . ')' . PHP_EOL;
    } else {
        echo '✓ Migration déjà enregistrée' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "Vérification:"
php artisan migrate:status | grep views || echo "Migration non trouvée"

