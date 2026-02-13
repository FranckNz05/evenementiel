#!/bin/bash

# Script d'installation de la configuration Nginx pour mokilievent.com
# Usage: sudo ./setup-nginx-domain.sh

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Variables
DOMAIN="mokilievent.com"
CONFIG_FILE="nginx/mokilievent.com.conf"
NGINX_AVAILABLE="/etc/nginx/sites-available/${DOMAIN}.conf"
NGINX_ENABLED="/etc/nginx/sites-enabled/${DOMAIN}.conf"
PROJECT_DIR="/var/www/mokilievent/evenementiel"

echo -e "${GREEN}=== Configuration Nginx pour ${DOMAIN} ===${NC}"

# Vérifier que le script est exécuté avec sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Erreur: Ce script doit être exécuté avec sudo${NC}"
    exit 1
fi

# Vérifier que le répertoire du projet existe
if [ ! -d "$PROJECT_DIR" ]; then
    echo -e "${RED}Erreur: Le répertoire du projet n'existe pas: $PROJECT_DIR${NC}"
    exit 1
fi

# Vérifier que le fichier de configuration existe
if [ ! -f "$PROJECT_DIR/$CONFIG_FILE" ]; then
    echo -e "${RED}Erreur: Le fichier de configuration n'existe pas: $PROJECT_DIR/$CONFIG_FILE${NC}"
    exit 1
fi

# Détecter la version PHP
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -c 1-3)
echo -e "${YELLOW}Version PHP détectée: ${PHP_VERSION}${NC}"

# Vérifier que PHP-FPM est installé
PHP_FPM_SOCKET="/var/run/php/php${PHP_VERSION}-fpm.sock"
if [ ! -S "$PHP_FPM_SOCKET" ]; then
    echo -e "${YELLOW}Attention: Le socket PHP-FPM n'existe pas: $PHP_FPM_SOCKET${NC}"
    echo -e "${YELLOW}Vérifiez que PHP-FPM est installé et démarré${NC}"
    read -p "Continuer quand même? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Copier la configuration
echo -e "${GREEN}Copie de la configuration Nginx...${NC}"
cp "$PROJECT_DIR/$CONFIG_FILE" "$NGINX_AVAILABLE"

# Mettre à jour le socket PHP-FPM dans la configuration
sed -i "s|unix:/var/run/php/php8.1-fpm.sock|unix:/var/run/php/php${PHP_VERSION}-fpm.sock|g" "$NGINX_AVAILABLE"

# Créer le lien symbolique
echo -e "${GREEN}Activation du site...${NC}"
if [ -L "$NGINX_ENABLED" ]; then
    rm "$NGINX_ENABLED"
fi
ln -s "$NGINX_AVAILABLE" "$NGINX_ENABLED"

# Vérifier la configuration Nginx
echo -e "${GREEN}Vérification de la configuration Nginx...${NC}"
if nginx -t; then
    echo -e "${GREEN}✓ Configuration Nginx valide${NC}"
else
    echo -e "${RED}✗ Erreur dans la configuration Nginx${NC}"
    exit 1
fi

# Vérifier les permissions
echo -e "${GREEN}Vérification des permissions...${NC}"
chown -R cursor:www-data "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Permissions configurées${NC}"

# Recharger Nginx
echo -e "${GREEN}Rechargement de Nginx...${NC}"
systemctl reload nginx
echo -e "${GREEN}✓ Nginx rechargé${NC}"

# Vérifier le statut de PHP-FPM
echo -e "${GREEN}Vérification de PHP-FPM...${NC}"
if systemctl is-active --quiet "php${PHP_VERSION}-fpm"; then
    echo -e "${GREEN}✓ PHP-FPM est actif${NC}"
else
    echo -e "${YELLOW}⚠ PHP-FPM n'est pas actif, démarrage...${NC}"
    systemctl start "php${PHP_VERSION}-fpm"
    systemctl enable "php${PHP_VERSION}-fpm"
    echo -e "${GREEN}✓ PHP-FPM démarré${NC}"
fi

echo ""
echo -e "${GREEN}=== Configuration terminée avec succès ===${NC}"
echo ""
echo -e "${YELLOW}Prochaines étapes:${NC}"
echo "1. Configurez les enregistrements DNS pour ${DOMAIN} et www.${DOMAIN}"
echo "2. Pointez-les vers l'IP de ce serveur: $(hostname -I | awk '{print $1}')"
echo "3. Attendez la propagation DNS (peut prendre jusqu'à 48h)"
echo "4. Testez avec: curl -I http://${DOMAIN}"
echo "5. Installez SSL avec: sudo certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
echo ""

