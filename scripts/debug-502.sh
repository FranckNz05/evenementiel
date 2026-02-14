#!/bin/bash

# Script de diagnostic approfondi pour l'erreur 502
# Usage: sudo ./debug-502.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -c 1-3)
NGINX_CONFIG="/etc/nginx/sites-available/mokilievent.com.conf"
PROJECT_DIR="/var/www/mokilievent/evenementiel"
PUBLIC_DIR="$PROJECT_DIR/public"

echo -e "${GREEN}=== Diagnostic approfondi erreur 502 ===${NC}"
echo ""

# 1. Vérifier les sockets PHP-FPM
echo -e "${YELLOW}1. Sockets PHP-FPM disponibles:${NC}"
ls -la /run/php/*.sock 2>/dev/null || echo "Aucun socket dans /run/php/"
ls -la /var/run/php/*.sock 2>/dev/null || echo "Aucun socket dans /var/run/php/"
echo ""

# 2. Vérifier la configuration Nginx
echo -e "${YELLOW}2. Configuration socket dans Nginx:${NC}"
grep "fastcgi_pass" "$NGINX_CONFIG" | head -1
echo ""

# 3. Vérifier PHP-FPM
echo -e "${YELLOW}3. Statut PHP-FPM:${NC}"
systemctl status "php${PHP_VERSION}-fpm" --no-pager | head -5
echo ""

# 4. Vérifier les logs Nginx récents
echo -e "${YELLOW}4. Dernières erreurs Nginx (20 lignes):${NC}"
tail -20 /var/log/nginx/mokilievent.com.error.log 2>/dev/null || echo "Log non trouvé"
echo ""

# 5. Vérifier les logs PHP-FPM récents
echo -e "${YELLOW}5. Dernières erreurs PHP-FPM (20 lignes):${NC}"
tail -20 /var/log/php${PHP_VERSION}-fpm.log 2>/dev/null || echo "Log non trouvé"
echo ""

# 6. Test de connexion au socket
echo -e "${YELLOW}6. Test de connexion au socket:${NC}"
SOCKET_PATH=$(grep "fastcgi_pass" "$NGINX_CONFIG" | grep -o "unix:[^;]*" | cut -d: -f2 | head -1)
if [ -n "$SOCKET_PATH" ]; then
    echo "Socket configuré: $SOCKET_PATH"
    if [ -S "$SOCKET_PATH" ]; then
        echo -e "${GREEN}✓ Socket existe${NC}"
        ls -la "$SOCKET_PATH"
    else
        echo -e "${RED}✗ Socket n'existe pas${NC}"
    fi
else
    echo -e "${RED}✗ Socket non trouvé dans la config${NC}"
fi
echo ""

# 7. Test PHP direct
echo -e "${YELLOW}7. Test PHP direct:${NC}"
if [ -f "$PUBLIC_DIR/index.php" ]; then
    php "$PUBLIC_DIR/index.php" 2>&1 | head -10 || echo "Erreur lors de l'exécution PHP"
else
    echo -e "${RED}✗ index.php non trouvé${NC}"
fi
echo ""

# 8. Vérifier les permissions
echo -e "${YELLOW}8. Permissions du répertoire public:${NC}"
ls -la "$PUBLIC_DIR" | head -5
echo ""

# 9. Test avec curl détaillé
echo -e "${YELLOW}9. Test curl détaillé:${NC}"
curl -v -H "Host: mokilievent.com" http://localhost 2>&1 | grep -E "(Connected|HTTP|502|500|error)" | head -10
echo ""

# 10. Vérifier la configuration PHP-FPM
echo -e "${YELLOW}10. Configuration PHP-FPM (socket):${NC}"
PHP_FPM_CONFIG="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
if [ -f "$PHP_FPM_CONFIG" ]; then
    grep "listen = " "$PHP_FPM_CONFIG" | head -1
    grep "listen.owner\|listen.group\|listen.mode" "$PHP_FPM_CONFIG" | head -3
else
    echo "Config PHP-FPM non trouvée"
fi
echo ""

echo -e "${GREEN}=== Diagnostic terminé ===${NC}"
echo ""
echo -e "${YELLOW}Commandes utiles:${NC}"
echo "sudo tail -f /var/log/nginx/mokilievent.com.error.log"
echo "sudo tail -f /var/log/php${PHP_VERSION}-fpm.log"
echo "sudo systemctl restart php${PHP_VERSION}-fpm"
echo ""

