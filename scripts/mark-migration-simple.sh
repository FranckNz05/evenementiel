#!/bin/bash

# Script simple pour marquer la migration comme exécutée
# Usage: ./mark-migration-simple.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Marquage de la migration create_views_table comme exécutée..."

# Utiliser une requête SQL directe pour éviter les problèmes avec Tinker
php artisan db --execute="
INSERT IGNORE INTO migrations (migration, batch)
SELECT '2026_02_14_000000_create_views_table', COALESCE(MAX(batch), 0) + 1
FROM migrations;
"

# Alternative avec artisan migrate:status et artisan migrate --pretend
# Ou simplement utiliser la commande migrate avec --pretend pour voir ce qui se passerait

echo ""
echo "Vérification:"
php artisan migrate:status | grep views || echo "Migration non trouvée"

