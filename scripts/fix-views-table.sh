#!/bin/bash

# Script pour créer la table views manquante
# Usage: sudo ./fix-views-table.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/mokilievent/evenementiel"

echo -e "${GREEN}=== Correction de la table views manquante ===${NC}"

# Vérifier qu'on est dans le bon répertoire
if [ ! -d "$PROJECT_DIR" ]; then
    echo -e "${RED}Erreur: Le répertoire du projet n'existe pas${NC}"
    exit 1
fi

cd "$PROJECT_DIR" || exit 1

# Vérifier si la migration existe
if [ -f "database/migrations/2026_02_14_000000_create_views_table.php" ]; then
    echo -e "${GREEN}✓ Migration trouvée${NC}"
else
    echo -e "${YELLOW}⚠ Migration non trouvée, création...${NC}"
fi

# Exécuter les migrations
echo -e "${YELLOW}Exécution des migrations...${NC}"
php artisan migrate --force

echo ""
echo -e "${GREEN}=== Migration terminée ===${NC}"
echo ""

