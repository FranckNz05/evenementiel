#!/bin/bash

# Script de diagnostic et correction de l'erreur 403
# Usage: sudo ./fix-403-error.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/mokilievent/evenementiel"
PUBLIC_DIR="$PROJECT_DIR/public"

echo -e "${GREEN}=== Diagnostic et correction de l'erreur 403 ===${NC}"

# Vérifier que le script est exécuté avec sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Erreur: Ce script doit être exécuté avec sudo${NC}"
    exit 1
fi

# 1. Vérifier que le répertoire public existe
echo -e "${YELLOW}1. Vérification du répertoire public...${NC}"
if [ ! -d "$PUBLIC_DIR" ]; then
    echo -e "${RED}✗ Le répertoire public n'existe pas: $PUBLIC_DIR${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Répertoire public existe${NC}"

# 2. Vérifier que index.php existe
echo -e "${YELLOW}2. Vérification de index.php...${NC}"
if [ ! -f "$PUBLIC_DIR/index.php" ]; then
    echo -e "${RED}✗ Le fichier index.php n'existe pas${NC}"
    exit 1
fi
echo -e "${GREEN}✓ index.php existe${NC}"

# 3. Vérifier et corriger les permissions
echo -e "${YELLOW}3. Correction des permissions...${NC}"

# Propriétaire des fichiers
chown -R cursor:www-data "$PROJECT_DIR"
echo -e "${GREEN}✓ Propriétaire configuré: cursor:www-data${NC}"

# Permissions des répertoires
find "$PROJECT_DIR" -type d -exec chmod 755 {} \;
echo -e "${GREEN}✓ Permissions des répertoires: 755${NC}"

# Permissions des fichiers
find "$PROJECT_DIR" -type f -exec chmod 644 {} \;
echo -e "${GREEN}✓ Permissions des fichiers: 644${NC}"

# Permissions spéciales pour storage et bootstrap/cache
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Permissions storage et cache: 775${NC}"

# Permissions spéciales pour artisan
if [ -f "$PROJECT_DIR/artisan" ]; then
    chmod 755 "$PROJECT_DIR/artisan"
    echo -e "${GREEN}✓ Permissions artisan: 755${NC}"
fi

# 4. Vérifier la configuration Nginx
echo -e "${YELLOW}4. Vérification de la configuration Nginx...${NC}"
NGINX_CONFIG="/etc/nginx/sites-available/mokilievent.com.conf"

if [ ! -f "$NGINX_CONFIG" ]; then
    echo -e "${RED}✗ Configuration Nginx non trouvée: $NGINX_CONFIG${NC}"
    exit 1
fi

# Vérifier le document root dans la config
ROOT_DIR=$(grep -E "^\s*root\s+" "$NGINX_CONFIG" | head -1 | awk '{print $2}' | tr -d ';')
if [ "$ROOT_DIR" != "$PUBLIC_DIR" ]; then
    echo -e "${YELLOW}⚠ Document root dans Nginx: $ROOT_DIR${NC}"
    echo -e "${YELLOW}⚠ Document root attendu: $PUBLIC_DIR${NC}"
    read -p "Corriger la configuration Nginx? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        sed -i "s|root .*;|root $PUBLIC_DIR;|g" "$NGINX_CONFIG"
        echo -e "${GREEN}✓ Configuration Nginx corrigée${NC}"
    fi
else
    echo -e "${GREEN}✓ Document root correct: $ROOT_DIR${NC}"
fi

# 5. Vérifier PHP-FPM
echo -e "${YELLOW}5. Vérification de PHP-FPM...${NC}"
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -c 1-3)
PHP_FPM_SOCKET="/var/run/php/php${PHP_VERSION}-fpm.sock"

if [ ! -S "$PHP_FPM_SOCKET" ]; then
    echo -e "${RED}✗ Socket PHP-FPM non trouvé: $PHP_FPM_SOCKET${NC}"
    echo -e "${YELLOW}Démarrage de PHP-FPM...${NC}"
    systemctl start "php${PHP_VERSION}-fpm"
    systemctl enable "php${PHP_VERSION}-fpm"
fi

if systemctl is-active --quiet "php${PHP_VERSION}-fpm"; then
    echo -e "${GREEN}✓ PHP-FPM est actif${NC}"
else
    echo -e "${RED}✗ PHP-FPM n'est pas actif${NC}"
    systemctl start "php${PHP_VERSION}-fpm"
    echo -e "${GREEN}✓ PHP-FPM démarré${NC}"
fi

# Vérifier le socket dans la config Nginx
if grep -q "fastcgi_pass.*php${PHP_VERSION}-fpm.sock" "$NGINX_CONFIG"; then
    echo -e "${GREEN}✓ Socket PHP-FPM correct dans la config${NC}"
else
    echo -e "${YELLOW}⚠ Socket PHP-FPM à vérifier dans la config${NC}"
fi

# 6. Vérifier SELinux (si activé)
echo -e "${YELLOW}6. Vérification de SELinux...${NC}"
if command -v getenforce &> /dev/null; then
    SELINUX_STATUS=$(getenforce)
    if [ "$SELINUX_STATUS" != "Disabled" ]; then
        echo -e "${YELLOW}⚠ SELinux est activé: $SELINUX_STATUS${NC}"
        echo -e "${YELLOW}Exécution de: setsebool -P httpd_can_network_connect 1${NC}"
        setsebool -P httpd_can_network_connect 1 2>/dev/null || true
        echo -e "${YELLOW}Exécution de: chcon -R -t httpd_sys_content_t $PROJECT_DIR${NC}"
        chcon -R -t httpd_sys_content_t "$PROJECT_DIR" 2>/dev/null || true
    else
        echo -e "${GREEN}✓ SELinux est désactivé${NC}"
    fi
else
    echo -e "${GREEN}✓ SELinux n'est pas installé${NC}"
fi

# 7. Tester la configuration Nginx
echo -e "${YELLOW}7. Test de la configuration Nginx...${NC}"
if nginx -t; then
    echo -e "${GREEN}✓ Configuration Nginx valide${NC}"
else
    echo -e "${RED}✗ Erreur dans la configuration Nginx${NC}"
    exit 1
fi

# 8. Recharger Nginx
echo -e "${YELLOW}8. Rechargement de Nginx...${NC}"
systemctl reload nginx
echo -e "${GREEN}✓ Nginx rechargé${NC}"

# 9. Test de connexion
echo -e "${YELLOW}9. Test de connexion...${NC}"
if curl -I http://localhost 2>/dev/null | head -1 | grep -q "200\|301\|302"; then
    echo -e "${GREEN}✓ Site accessible en local${NC}"
else
    echo -e "${YELLOW}⚠ Test local non concluant, vérifiez les logs${NC}"
fi

# 10. Afficher les logs récents
echo -e "${YELLOW}10. Dernières erreurs Nginx...${NC}"
if [ -f "/var/log/nginx/mokilievent.com.error.log" ]; then
    echo -e "${YELLOW}Dernières lignes du log d'erreur:${NC}"
    tail -5 /var/log/nginx/mokilievent.com.error.log
else
    echo -e "${YELLOW}Log d'erreur non trouvé${NC}"
fi

echo ""
echo -e "${GREEN}=== Diagnostic terminé ===${NC}"
echo ""
echo -e "${YELLOW}Commandes de test:${NC}"
echo "curl -I http://localhost"
echo "curl -I http://mokilievent.com"
echo "sudo tail -f /var/log/nginx/mokilievent.com.error.log"
echo ""

