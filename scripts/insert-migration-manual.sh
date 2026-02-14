#!/bin/bash

# Script pour insérer manuellement la migration
# Usage: ./insert-migration-manual.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

# Lire les variables d'environnement
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2 | tr -d ' ' | head -1)
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2 | tr -d ' ' | head -1)
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2 | tr -d ' ' | head -1)

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USER" ]; then
    echo "Erreur: Variables DB non trouvées dans .env"
    exit 1
fi

echo "Vérification de la table migrations..."
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT COUNT(*) as total FROM migrations;" 2>/dev/null

echo ""
echo "Recherche de la migration..."
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT * FROM migrations WHERE migration = '2026_02_14_000000_create_views_table';" 2>/dev/null

echo ""
echo "Insertion de la migration..."
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" << 'SQL'
-- Obtenir le batch maximum
SET @max_batch = (SELECT COALESCE(MAX(batch), 0) FROM migrations);
SET @new_batch = @max_batch + 1;

-- Insérer la migration
INSERT INTO migrations (migration, batch)
VALUES ('2026_02_14_000000_create_views_table', @new_batch)
ON DUPLICATE KEY UPDATE batch = batch;

SELECT CONCAT('Migration insérée avec batch: ', @new_batch) AS result;
SQL

echo ""
echo "Vérification finale:"
php artisan migrate:status 2>/dev/null | grep views || echo "Utilisez: php artisan migrate:status"

