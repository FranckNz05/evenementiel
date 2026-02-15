#!/bin/bash

# Script de diagnostic et correction pour le VPS
# À exécuter sur le serveur : sudo bash DIAGNOSTIC_ET_CORRECTION_VPS.sh

cd /var/www/mokilievent/evenementiel

echo "=== DIAGNOSTIC ET CORRECTION VPS ==="
echo ""

# 1. Vérifier le lien symbolique storage
echo "1. Vérification du lien symbolique storage..."
if [ ! -L "public/storage" ]; then
    echo "   ❌ Lien symbolique manquant, création..."
    sudo -u www-data php artisan storage:link
else
    echo "   ✅ Lien symbolique existe"
fi
echo ""

# 2. Corriger les permissions
echo "2. Correction des permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache public/storage
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 775 public/storage
sudo chown -R www-data:www-data public/images
sudo chmod -R 755 public/images
echo "   ✅ Permissions corrigées"
echo ""

# 3. Vérifier l'image hero
echo "3. Vérification de l'image hero..."
if [ -f "public/images/foule-humains-copie.jpg" ]; then
    echo "   ✅ Image hero existe"
    ls -lh public/images/foule-humains-copie.jpg
else
    echo "   ❌ Image hero manquante : public/images/foule-humains-copie.jpg"
    echo "   ⚠️  Vous devez copier cette image depuis votre machine locale"
fi
echo ""

# 4. Vérifier le logo par défaut
echo "4. Vérification du logo par défaut..."
if [ -f "public/images/logo.png" ]; then
    echo "   ✅ Logo par défaut existe"
else
    echo "   ❌ Logo par défaut manquant : public/images/logo.png"
fi
echo ""

# 5. Vérifier les fichiers dans storage/settings
echo "5. Vérification des logos dans storage..."
if [ -d "storage/app/public/settings" ]; then
    echo "   ✅ Dossier settings existe"
    ls -lh storage/app/public/settings/ | head -5
else
    echo "   ❌ Dossier settings manquant"
fi
echo ""

# 6. Vérifier les données dans la base de données
echo "6. Vérification des données dans la base de données..."
echo "   - Logo dans settings :"
sudo -u www-data php artisan tinker --execute="echo DB::table('settings')->where('key', 'logo')->value('value') ?? 'AUCUN';"
echo ""
echo "   - Nombre d'annonces actives :"
sudo -u www-data php artisan tinker --execute="echo DB::table('announcements')->where('is_active', 1)->count();"
echo ""
echo "   - Nombre d'événements publiés et approuvés :"
sudo -u www-data php artisan tinker --execute="echo DB::table('events')->where('is_published', 1)->where('is_approved', 1)->count();"
echo ""
echo "   - Nombre d'événements en vedette :"
sudo -u www-data php artisan tinker --execute="echo DB::table('events')->where('is_featured', 1)->where('is_published', 1)->where('is_approved', 1)->count();"
echo ""

# 7. Vérifier APP_DEBUG
echo "7. Vérification de APP_DEBUG..."
if grep -q "APP_DEBUG=true" .env; then
    echo "   ⚠️  APP_DEBUG=true (devrait être false en production)"
else
    echo "   ✅ APP_DEBUG=false ou non défini"
fi
echo ""

# 8. Vider les caches
echo "8. Vidage des caches..."
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear
echo "   ✅ Caches vidés"
echo ""

# 9. Vérifier les erreurs dans les logs
echo "9. Dernières erreurs dans les logs (20 dernières lignes) :"
tail -20 storage/logs/laravel.log | grep -i error | tail -5
echo ""

echo "=== DIAGNOSTIC TERMINÉ ==="
echo ""
echo "Actions recommandées :"
echo "1. Si l'image hero manque, copiez-la depuis votre machine locale"
echo "2. Si le logo manque, copiez-le depuis votre machine locale"
echo "3. Si APP_DEBUG=true, changez-le en false dans .env"
echo "4. Vérifiez les données dans la base de données (annonces, événements)"
echo "5. Testez le site : curl -I https://mokilievent.com"

