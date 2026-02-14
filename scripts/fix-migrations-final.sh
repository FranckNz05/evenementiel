#!/bin/bash

# Script pour corriger la table migrations et marquer la migration
# Usage: ./fix-migrations-final.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Vérification et correction de la table migrations..."

# Lire les variables d'environnement Laravel
DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2 | tr -d ' ' | head -1)
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2 | tr -d ' ' | head -1)
DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2 | tr -d ' ' | head -1)

if [ -z "$DB_DATABASE" ]; then
    echo "Erreur: DB_DATABASE non trouvé dans .env"
    exit 1
fi

# Vérifier la structure de la table migrations
echo "Structure de la table migrations:"
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" -e "DESCRIBE migrations;" 2>/dev/null || mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" -e "DESCRIBE migrations;"

echo ""
echo "Vérification si la migration est déjà enregistrée:"
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT * FROM migrations WHERE migration = '2026_02_14_000000_create_views_table';" 2>/dev/null

echo ""
echo "Insertion de la migration (si elle n'existe pas):"
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_DATABASE" << EOF 2>/dev/null
-- Insérer la migration si elle n'existe pas déjà
INSERT INTO migrations (migration, batch)
SELECT '2026_02_14_000000_create_views_table', COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations WHERE migration = '2026_02_14_000000_create_views_table'
);
EOF

echo ""
echo "Vérification finale:"
php artisan migrate:status | grep views || echo "Migration non trouvée"

