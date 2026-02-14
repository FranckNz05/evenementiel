#!/bin/bash

# Script de diagnostic et correction de l'erreur 502 Bad Gateway
# Usage: sudo ./fix-502-error.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/mokilievent/evenementiel"
PUBLIC_DIR="$PROJECT_DIR/public"
NGINX_CONFIG="/etc/nginx/sites-available/mokilievent.com.conf"

echo -e "${GREEN}=== Diagnostic et correction de l'erreur 502 ===${NC}"

# Vérifier que le script est exécuté avec sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Erreur: Ce script doit être exécuté avec sudo${NC}"
    exit 1
fi

# 1. Détecter la version PHP
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -c 1-3)
echo -e "${YELLOW}Version PHP détectée: ${PHP_VERSION}${NC}"

# 2. Vérifier PHP-FPM
echo -e "${YELLOW}1. Vérification de PHP-FPM...${NC}"
PHP_FPM_SERVICE="php${PHP_VERSION}-fpm"
PHP_FPM_SOCKET="/var/run/php/php${PHP_VERSION}-fpm.sock"

if systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
    echo -e "${GREEN}✓ PHP-FPM est actif${NC}"
else
    echo -e "${RED}✗ PHP-FPM n'est pas actif, démarrage...${NC}"
    systemctl start "$PHP_FPM_SERVICE"
    systemctl enable "$PHP_FPM_SERVICE"
    sleep 2
fi

# 3. Vérifier le socket PHP-FPM
echo -e "${YELLOW}2. Vérification du socket PHP-FPM...${NC}"
if [ -S "$PHP_FPM_SOCKET" ]; then
    echo -e "${GREEN}✓ Socket trouvé: $PHP_FPM_SOCKET${NC}"
    ls -la "$PHP_FPM_SOCKET"
else
    echo -e "${RED}✗ Socket non trouvé: $PHP_FPM_SOCKET${NC}"
    echo -e "${YELLOW}Recherche d'autres sockets PHP-FPM...${NC}"
    ls -la /var/run/php/*.sock 2>/dev/null || echo "Aucun socket trouvé"
    
    # Vérifier la configuration PHP-FPM
    PHP_FPM_CONFIG="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
    if [ -f "$PHP_FPM_CONFIG" ]; then
        echo -e "${YELLOW}Configuration PHP-FPM trouvée: $PHP_FPM_CONFIG${NC}"
        echo -e "${YELLOW}Socket configuré:${NC}"
        grep "listen = " "$PHP_FPM_CONFIG" | head -1
    fi
fi

# 4. Vérifier la configuration Nginx
echo -e "${YELLOW}3. Vérification de la configuration Nginx...${NC}"
if [ ! -f "$NGINX_CONFIG" ]; then
    echo -e "${RED}✗ Configuration Nginx non trouvée: $NGINX_CONFIG${NC}"
    exit 1
fi

# Vérifier le socket dans la config Nginx
NGINX_SOCKET=$(grep "fastcgi_pass" "$NGINX_CONFIG" | grep -o "php[0-9.]*-fpm.sock" | head -1)
if [ -z "$NGINX_SOCKET" ]; then
    echo -e "${RED}✗ Socket PHP-FPM non trouvé dans la configuration Nginx${NC}"
else
    echo -e "${GREEN}✓ Socket dans Nginx: $NGINX_SOCKET${NC}"
    
    # Vérifier si le socket correspond
    EXPECTED_SOCKET="php${PHP_VERSION}-fpm.sock"
    if [[ "$NGINX_SOCKET" == "$EXPECTED_SOCKET" ]]; then
        echo -e "${GREEN}✓ Le socket correspond à la version PHP${NC}"
    else
        echo -e "${YELLOW}⚠ Le socket ne correspond pas, correction...${NC}"
        sed -i "s|fastcgi_pass unix:/var/run/php/php[0-9.]*-fpm.sock|fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock|g" "$NGINX_CONFIG"
        echo -e "${GREEN}✓ Configuration corrigée${NC}"
    fi
fi

# 5. Vérifier les permissions du socket
echo -e "${YELLOW}4. Vérification des permissions du socket...${NC}"
if [ -S "$PHP_FPM_SOCKET" ]; then
    SOCKET_PERMS=$(stat -c "%a %U:%G" "$PHP_FPM_SOCKET")
    echo -e "${YELLOW}Permissions actuelles: $SOCKET_PERMS${NC}"
    
    # S'assurer que www-data peut accéder au socket
    chown www-data:www-data "$PHP_FPM_SOCKET" 2>/dev/null || true
    chmod 666 "$PHP_FPM_SOCKET" 2>/dev/null || true
    echo -e "${GREEN}✓ Permissions du socket vérifiées${NC}"
fi

# 6. Vérifier les permissions des fichiers
echo -e "${YELLOW}5. Vérification des permissions des fichiers...${NC}"
chown -R cursor:www-data "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Permissions configurées${NC}"

# 7. Vérifier la configuration PHP-FPM
echo -e "${YELLOW}6. Vérification de la configuration PHP-FPM...${NC}"
PHP_FPM_CONFIG="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
if [ -f "$PHP_FPM_CONFIG" ]; then
    # Vérifier que le socket est configuré
    if grep -q "listen = /var/run/php/php${PHP_VERSION}-fpm.sock" "$PHP_FPM_CONFIG"; then
        echo -e "${GREEN}✓ Socket configuré dans PHP-FPM${NC}"
    else
        echo -e "${YELLOW}⚠ Configuration du socket à vérifier${NC}"
        grep "listen = " "$PHP_FPM_CONFIG" | head -1
    fi
    
    # Vérifier les permissions du socket dans PHP-FPM
    if grep -q "listen.owner = www-data" "$PHP_FPM_CONFIG" && grep -q "listen.group = www-data" "$PHP_FPM_CONFIG"; then
        echo -e "${GREEN}✓ Permissions socket configurées dans PHP-FPM${NC}"
    else
        echo -e "${YELLOW}⚠ Configuration des permissions à vérifier${NC}"
    fi
fi

# 8. Redémarrer PHP-FPM
echo -e "${YELLOW}7. Redémarrage de PHP-FPM...${NC}"
systemctl restart "$PHP_FPM_SERVICE"
sleep 2
if systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
    echo -e "${GREEN}✓ PHP-FPM redémarré avec succès${NC}"
else
    echo -e "${RED}✗ Erreur lors du redémarrage de PHP-FPM${NC}"
    systemctl status "$PHP_FPM_SERVICE" --no-pager | head -10
fi

# 9. Tester la configuration Nginx
echo -e "${YELLOW}8. Test de la configuration Nginx...${NC}"
if nginx -t; then
    echo -e "${GREEN}✓ Configuration Nginx valide${NC}"
else
    echo -e "${RED}✗ Erreur dans la configuration Nginx${NC}"
    exit 1
fi

# 10. Recharger Nginx
echo -e "${YELLOW}9. Rechargement de Nginx...${NC}"
systemctl reload nginx
echo -e "${GREEN}✓ Nginx rechargé${NC}"

# 11. Test de connexion
echo -e "${YELLOW}10. Test de connexion...${NC}"
sleep 1
if curl -I -H "Host: mokilievent.com" http://localhost 2>/dev/null | head -1 | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✓ Site accessible${NC}"
else
    echo -e "${YELLOW}⚠ Test non concluant, vérifiez les logs${NC}"
    echo -e "${YELLOW}Dernières erreurs:${NC}"
    tail -5 /var/log/nginx/mokilievent.com.error.log 2>/dev/null || echo "Log non trouvé"
    tail -5 /var/log/php${PHP_VERSION}-fpm.log 2>/dev/null || echo "Log PHP-FPM non trouvé"
fi

echo ""
echo -e "${GREEN}=== Diagnostic terminé ===${NC}"
echo ""
echo -e "${YELLOW}Commandes de test:${NC}"
echo "curl -I -H 'Host: mokilievent.com' http://localhost"
echo "curl -I http://72.61.161.141"
echo "sudo tail -f /var/log/nginx/mokilievent.com.error.log"
echo "sudo tail -f /var/log/php${PHP_VERSION}-fpm.log"
echo ""

