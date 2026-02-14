#!/bin/bash

# Script pour corriger le chemin du socket PHP-FPM dans Nginx
# Usage: sudo ./fix-socket-path.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -c 1-3)
NGINX_CONFIG="/etc/nginx/sites-available/mokilievent.com.conf"

echo -e "${GREEN}=== Correction du chemin du socket PHP-FPM ===${NC}"

# Vérifier les deux chemins possibles
SOCKET_VAR="/var/run/php/php${PHP_VERSION}-fpm.sock"
SOCKET_RUN="/run/php/php${PHP_VERSION}-fpm.sock"

echo -e "${YELLOW}Vérification des sockets...${NC}"

if [ -S "$SOCKET_RUN" ]; then
    echo -e "${GREEN}✓ Socket trouvé: $SOCKET_RUN${NC}"
    USE_SOCKET="$SOCKET_RUN"
elif [ -S "$SOCKET_VAR" ]; then
    echo -e "${GREEN}✓ Socket trouvé: $SOCKET_VAR${NC}"
    USE_SOCKET="$SOCKET_VAR"
else
    echo -e "${RED}✗ Aucun socket trouvé${NC}"
    exit 1
fi

# Vérifier si ce sont des liens symboliques
if [ -L "$SOCKET_RUN" ] || [ -L "$SOCKET_VAR" ]; then
    echo -e "${YELLOW}Les sockets sont des liens symboliques, vérification...${NC}"
    if [ -L "$SOCKET_RUN" ]; then
        REAL_PATH=$(readlink -f "$SOCKET_RUN")
        echo -e "${YELLOW}$SOCKET_RUN → $REAL_PATH${NC}"
    fi
    if [ -L "$SOCKET_VAR" ]; then
        REAL_PATH=$(readlink -f "$SOCKET_VAR")
        echo -e "${YELLOW}$SOCKET_VAR → $REAL_PATH${NC}"
    fi
fi

# Corriger la configuration Nginx
echo -e "${YELLOW}Correction de la configuration Nginx...${NC}"

# Remplacer tous les chemins possibles
sed -i "s|fastcgi_pass unix:/var/run/php/php[0-9.]*-fpm.sock|fastcgi_pass unix:$USE_SOCKET|g" "$NGINX_CONFIG"
sed -i "s|fastcgi_pass unix:/run/php/php[0-9.]*-fpm.sock|fastcgi_pass unix:$USE_SOCKET|g" "$NGINX_CONFIG"

echo -e "${GREEN}✓ Configuration mise à jour${NC}"

# Vérifier la configuration
echo -e "${YELLOW}Vérification de la configuration...${NC}"
if nginx -t; then
    echo -e "${GREEN}✓ Configuration Nginx valide${NC}"
else
    echo -e "${RED}✗ Erreur dans la configuration${NC}"
    exit 1
fi

# Recharger Nginx
echo -e "${YELLOW}Rechargement de Nginx...${NC}"
systemctl reload nginx
echo -e "${GREEN}✓ Nginx rechargé${NC}"

echo ""
echo -e "${GREEN}=== Correction terminée ===${NC}"
echo -e "${YELLOW}Socket utilisé: $USE_SOCKET${NC}"
echo ""

