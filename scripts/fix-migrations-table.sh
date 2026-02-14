#!/bin/bash

# Script pour corriger la table migrations
# Usage: ./fix-migrations-table.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Correction de la table migrations..."

# Vérifier et corriger la structure de la table migrations
php artisan tinker --execute="
try {
    // Vérifier la structure de la table migrations
    \$columns = Schema::getColumnListing('migrations');
    echo 'Colonnes actuelles: ' . implode(', ', \$columns) . PHP_EOL;
    
    // Vérifier si la colonne id est auto-increment
    \$result = DB::select('SHOW CREATE TABLE migrations');
    \$createTable = \$result[0]->{'Create Table'};
    
    if (strpos(\$createTable, 'AUTO_INCREMENT') === false) {
        echo 'Correction de la colonne id...' . PHP_EOL;
        DB::statement('ALTER TABLE migrations MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        echo '✓ Colonne id corrigée' . PHP_EOL;
    } else {
        echo '✓ Colonne id est déjà auto-increment' . PHP_EOL;
    }
    
    // Maintenant, insérer la migration manuellement
    \$maxBatch = DB::table('migrations')->max('batch') ?? 0;
    \$newBatch = \$maxBatch + 1;
    
    // Utiliser une requête SQL directe avec AUTO_INCREMENT
    DB::statement(\"INSERT INTO migrations (migration, batch) VALUES ('2026_02_14_000000_create_views_table', \$newBatch)\");
    
    echo '✓ Migration marquée comme exécutée (batch: ' . \$newBatch . ')' . PHP_EOL;
    
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
    echo 'Tentative alternative...' . PHP_EOL;
    
    // Alternative: utiliser SQL direct
    try {
        \$maxBatch = DB::selectOne('SELECT MAX(batch) as max_batch FROM migrations');
        \$newBatch = (\$maxBatch->max_batch ?? 0) + 1;
        DB::statement(\"INSERT INTO migrations (migration, batch) VALUES ('2026_02_14_000000_create_views_table', \$newBatch)\");
        echo '✓ Migration marquée (méthode alternative)' . PHP_EOL;
    } catch (Exception \$e2) {
        echo 'Erreur alternative: ' . \$e2->getMessage() . PHP_EOL;
    }
}
"

echo ""
echo "Vérification:"
php artisan migrate:status | grep views || echo "Migration non trouvée"

