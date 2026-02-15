# Correction des problèmes sur le VPS

## Problèmes identifiés :
1. Pas de barre d'annonces
2. Mauvais logo (le logo est chargé depuis la table `settings`)
3. Erreur Carbon affichée
4. Pas d'image hero section (foule-humains-copie.jpg)
5. Aucune donnée sur la page d'accueil

## Solutions à appliquer sur le serveur :

### 1. Vérifier et créer le lien symbolique storage
```bash
cd /var/www/mokilievent/evenementiel
ls -la public/storage
# Si le lien n'existe pas :
php artisan storage:link
# Vérifier :
ls -la public/storage
```

### 2. Corriger les permissions storage
```bash
cd /var/www/mokilievent/evenementiel
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 775 public/storage
```

### 3. Vérifier que l'image hero existe
```bash
ls -la public/images/foule-humains-copie.jpg
# Si elle n'existe pas, copiez-la depuis votre machine locale
```

### 4. Vérifier les données dans la base de données

#### Vérifier le logo dans settings :
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
DB::table('settings')->where('key', 'logo')->first();
exit
```

#### Vérifier les annonces :
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
DB::table('announcements')->where('is_active', 1)->get();
exit
```

#### Vérifier les événements :
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
DB::table('events')->where('is_published', 1)->where('is_approved', 1)->count();
exit
```

### 5. Masquer l'erreur Carbon (si pas déjà fait)
```bash
sudo nano /etc/php/8.4/fpm/php.ini
# Cherchez error_reporting et modifiez :
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
# Vérifiez aussi :
display_errors = Off
# Sauvegardez et redémarrez :
sudo systemctl restart php8.4-fpm
```

### 6. Vérifier APP_DEBUG dans .env
```bash
cd /var/www/mokilievent/evenementiel
grep APP_DEBUG .env
# Si APP_DEBUG=true, changez en :
# APP_DEBUG=false
sudo nano .env
# Puis :
sudo -u www-data php artisan config:clear
```

### 7. Vider tous les caches
```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear
```

### 8. Vérifier les permissions sur public/images
```bash
sudo chown -R www-data:www-data public/images
sudo chmod -R 755 public/images
```

### 9. Vérifier que les fichiers existent
```bash
# Logo par défaut
ls -la public/images/logo.png

# Image hero
ls -la public/images/foule-humains-copie.jpg

# Storage settings
ls -la storage/app/public/settings/
```

### 10. Si le logo dans settings pointe vers storage/app/public/settings
Le chemin doit être accessible via public/storage/settings/ après le lien symbolique.

### 11. Vérifier les logs pour plus d'informations
```bash
tail -50 /var/www/mokilievent/evenementiel/storage/logs/laravel.log
```

## Ordre d'exécution recommandé :
1. Lien symbolique storage (étape 1)
2. Permissions (étape 2 et 8)
3. Vérifier les fichiers (étape 9)
4. Vérifier les données BD (étape 4)
5. Masquer erreur Carbon (étape 5)
6. APP_DEBUG=false (étape 6)
7. Vider les caches (étape 7)
8. Tester le site

