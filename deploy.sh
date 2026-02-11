#!/bin/bash

# Script de déploiement pour VPS
# Ce script peut être exécuté manuellement sur le VPS ou via GitHub Actions

set -e

echo "🚀 Démarrage du déploiement..."

# Variables (à ajuster selon votre configuration)
DEPLOY_PATH="${VPS_DEPLOY_PATH:-/var/www/html}"
PHP_VERSION="8.1"

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Le fichier artisan n'existe pas. Êtes-vous dans le répertoire du projet?"
    exit 1
fi

# Mettre l'application en maintenance
echo "📦 Mise en maintenance de l'application..."
php artisan down || true

# Installer/Mettre à jour les dépendances Composer
echo "📥 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# Installer/Mettre à jour les dépendances NPM
echo "📦 Installation des dépendances NPM..."
npm ci --production

# Builder les assets
echo "🔨 Construction des assets..."
npm run build

# Optimiser Laravel
echo "⚡ Optimisation de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

# Exécuter les migrations
echo "🗄️  Exécution des migrations..."
php artisan migrate --force

# Nettoyer les caches
echo "🧹 Nettoyage des caches..."
php artisan cache:clear
php artisan config:clear || true

# Recréer les caches optimisés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fixer les permissions
echo "🔐 Configuration des permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Redémarrer les services si nécessaire (décommentez si vous utilisez Supervisor, Queue Workers, etc.)
# sudo supervisorctl restart laravel-worker:*
# sudo systemctl restart php${PHP_VERSION}-fpm

# Remettre l'application en ligne
echo "✅ Remise en ligne de l'application..."
php artisan up

echo "🎉 Déploiement terminé avec succès!"

