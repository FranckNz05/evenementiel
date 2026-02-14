#!/bin/bash

# Script pour corriger la table migrations avec SQL direct
# Usage: ./fix-migrations-table-sql.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Correction de la table migrations avec SQL direct..."

# Lire les variables d'environnement Laravel
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2 | tr -d ' ')
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | tr -d ' ')
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2 | tr -d ' ')

if [ -z "$DB_DATABASE" ]; then
    echo "Erreur: DB_DATABASE non trouvé dans .env"
    exit 1
fi

# Corriger la table migrations et insérer la migration
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" << EOF
-- Corriger la colonne id si nécessaire
ALTER TABLE migrations MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;

-- Insérer la migration si elle n'existe pas
INSERT IGNORE INTO migrations (migration, batch)
SELECT '2026_02_14_000000_create_views_table', COALESCE(MAX(batch), 0) + 1
FROM migrations;
EOF

echo ""
echo "Vérification:"
php artisan migrate:status | grep views || echo "Migration non trouvée"

