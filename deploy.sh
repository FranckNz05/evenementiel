#!/bin/bash

# Script de déploiement Laravel - MokiliEvent
# Usage: ./deploy.sh
# S'exécute depuis /var/www/mokilievent

set -e  # Arrêter immédiatement en cas d'erreur

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variables
PROJECT_DIR="/var/www/mokilievent/evenementiel"
CURRENT_DIR=$(pwd)
LOG_FILE="/var/www/mokilievent/deploy.log"

# Fonction de logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERREUR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[ATTENTION]${NC} $1" | tee -a "$LOG_FILE"
}

# Vérifier qu'on est dans le bon répertoire parent
if [ "$CURRENT_DIR" != "/var/www/mokilievent" ]; then
    error "Le script doit être exécuté depuis /var/www/mokilievent (actuellement: $CURRENT_DIR)"
fi

# Vérifier que le répertoire du projet existe
if [ ! -d "$PROJECT_DIR" ]; then
    error "Le répertoire du projet n'existe pas: $PROJECT_DIR"
fi

log "=== Début du déploiement ==="
log "Répertoire de travail: $PROJECT_DIR"

# Se déplacer dans le répertoire du projet
cd "$PROJECT_DIR" || error "Impossible de se déplacer dans $PROJECT_DIR"

# 1. Git pull
log "Mise à jour du code depuis Git..."
git fetch origin main || error "Échec du git fetch"
git pull origin main || error "Échec du git pull"

# 2. Composer install
log "Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader --no-interaction || error "Échec de composer install"

# 3. Migrations
log "Exécution des migrations..."
php artisan migrate --force || error "Échec des migrations"

# 4. Nettoyage et reconstruction des caches
log "Nettoyage des caches..."
php artisan config:clear || error "Échec du nettoyage de config"
php artisan cache:clear || error "Échec du nettoyage de cache"
php artisan route:clear || error "Échec du nettoyage de routes"
php artisan view:clear || error "Échec du nettoyage de vues"

log "Reconstruction des caches..."
php artisan config:cache || error "Échec de la mise en cache de config"
php artisan route:cache || error "Échec de la mise en cache de routes"
php artisan view:cache || error "Échec de la mise en cache de vues"

# 5. Optimisations supplémentaires
log "Optimisation de l'autoloader..."
composer dump-autoload --optimize --no-dev || warning "Échec de l'optimisation de l'autoloader (non bloquant)"

# 6. Vérification des permissions (optionnel mais recommandé)
log "Vérification des permissions..."
if [ -d "storage" ]; then
    # Essayer avec sudo d'abord, puis sans sudo
    if command -v sudo &> /dev/null; then
        sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || chmod -R 775 storage bootstrap/cache 2>/dev/null || true
        sudo chown -R cursor:www-data storage bootstrap/cache 2>/dev/null || chown -R cursor:www-data storage bootstrap/cache 2>/dev/null || true
    else
        chmod -R 775 storage bootstrap/cache 2>/dev/null || true
        chown -R cursor:www-data storage bootstrap/cache 2>/dev/null || true
    fi
    log "Permissions vérifiées"
fi

# 7. Rechargement de Nginx
log "Rechargement de Nginx..."
if command -v sudo &> /dev/null; then
    # Essayer avec sudo d'abord
    if sudo systemctl reload nginx 2>/dev/null; then
        log "Nginx rechargé avec succès (sudo)"
    elif sudo nginx -s reload 2>/dev/null; then
        log "Nginx rechargé avec succès (nginx -s reload)"
    else
        warning "Impossible de recharger Nginx automatiquement. Rechargez manuellement avec: sudo systemctl reload nginx"
    fi
else
    # Essayer sans sudo (si l'utilisateur a les permissions)
    if systemctl reload nginx 2>/dev/null; then
        log "Nginx rechargé avec succès"
    elif nginx -s reload 2>/dev/null; then
        log "Nginx rechargé avec succès (nginx -s reload)"
    else
        warning "Impossible de recharger Nginx automatiquement. Rechargez manuellement avec: sudo systemctl reload nginx"
    fi
fi

# 8. Vérification finale
log "Vérification de l'application..."
if php artisan --version &>/dev/null; then
    log "Laravel fonctionne correctement"
else
    error "Laravel ne répond pas correctement"
fi

log "=== Déploiement terminé avec succès ==="
log "Heure: $(date +'%Y-%m-%d %H:%M:%S')"
echo ""
