#!/bin/bash

# Script pour vérifier si la table users existe
# Usage: ./check-users-table.sh

PROJECT_DIR="/var/www/mokilievent/evenementiel"

cd "$PROJECT_DIR" || exit 1

echo "Vérification de la table users..."

php artisan tinker --execute="
try {
    \$exists = Schema::hasTable('users');
    echo 'Table users existe: ' . (\$exists ? 'OUI' : 'NON') . PHP_EOL;
    if (\$exists) {
        \$count = DB::table('users')->count();
        echo 'Nombre d\'utilisateurs: ' . \$count . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
}
"

