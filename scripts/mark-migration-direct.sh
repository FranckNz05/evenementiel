#!/bin/bash

# Script simple pour marquer la migration directement
# Usage: ./mark-migration-direct.sh

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

echo "Marquage de la migration create_views_table..."

# Insérer directement avec MySQL
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" 2>/dev/null << 'SQL'
-- Vérifier si la migration existe déjà
SET @exists = (SELECT COUNT(*) FROM migrations WHERE migration = '2026_02_14_000000_create_views_table');

-- Si elle n'existe pas, l'insérer
SET @max_batch = (SELECT COALESCE(MAX(batch), 0) FROM migrations);
SET @new_batch = @max_batch + 1;

INSERT INTO migrations (migration, batch)
SELECT '2026_02_14_000000_create_views_table', @new_batch
WHERE @exists = 0;

SELECT 
    CASE 
        WHEN @exists > 0 THEN 'Migration déjà enregistrée'
        ELSE CONCAT('Migration ajoutée (batch: ', @new_batch, ')')
    END AS result;
SQL

echo ""
echo "Vérification:"
php artisan migrate:status 2>/dev/null | grep views || echo "Utilisez: php artisan migrate:status"

