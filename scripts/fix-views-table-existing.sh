#!/bin/bash

# Script pour corriger la table views existante
# Usage: sudo ./fix-views-table-existing.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/mokilievent/evenementiel"

echo -e "${GREEN}=== Correction de la table views existante ===${NC}"

cd "$PROJECT_DIR" || exit 1

# Option 1 : Marquer la migration comme exécutée
echo -e "${YELLOW}Option 1 : Marquer la migration comme exécutée...${NC}"
php artisan migrate:status | grep views || echo "Migration non trouvée dans le statut"

# Vérifier si la migration est déjà marquée
if php artisan migrate:status 2>/dev/null | grep -q "2026_02_14_000000_create_views_table.*Ran"; then
    echo -e "${GREEN}✓ Migration déjà marquée comme exécutée${NC}"
else
    echo -e "${YELLOW}Marquage de la migration comme exécutée...${NC}"
    php artisan migrate --pretend 2>&1 | grep views || true
    
    # Insérer manuellement dans la table migrations
    php artisan tinker --execute="
    try {
        DB::table('migrations')->insertOrIgnore([
            'migration' => '2026_02_14_000000_create_views_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        echo 'Migration marquée comme exécutée' . PHP_EOL;
    } catch (Exception \$e) {
        echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
    }
    "
fi

# Option 2 : Vérifier la structure de la table
echo ""
echo -e "${YELLOW}Option 2 : Vérification de la structure de la table...${NC}"
php artisan tinker --execute="
try {
    \$columns = Schema::getColumnListing('views');
    echo 'Colonnes de la table views: ' . implode(', ', \$columns) . PHP_EOL;
    
    // Vérifier les colonnes nécessaires
    \$required = ['id', 'user_id', 'viewable_id', 'viewable_type', 'viewed_type', 'update_at', 'created_at', 'updated_at'];
    \$missing = array_diff(\$required, \$columns);
    
    if (empty(\$missing)) {
        echo '✓ Toutes les colonnes nécessaires sont présentes' . PHP_EOL;
    } else {
        echo '⚠ Colonnes manquantes: ' . implode(', ', \$missing) . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo -e "${GREEN}=== Correction terminée ===${NC}"
echo ""
echo -e "${YELLOW}Si la table est correcte, la migration devrait maintenant passer.${NC}"
echo "Test: php artisan migrate --force"
echo ""

