# 🔧 Correction : Erreur 500 Internal Server Error

## Progression
✅ Permissions corrigées (plus de 403)  
❌ Erreur 500 - Problème PHP/Laravel

## Diagnostic

### 1. Vérifier les logs Laravel

```bash
# Voir les dernières erreurs Laravel
tail -50 /var/www/mokilievent/evenementiel/storage/logs/laravel.log

# Ou si le fichier n'existe pas encore
ls -la /var/www/mokilievent/evenementiel/storage/logs/
```

### 2. Vérifier les logs PHP-FPM

```bash
# Logs PHP-FPM
sudo tail -30 /var/log/php8.4-fpm.log

# Ou
sudo journalctl -u php8.4-fpm -n 50 --no-pager
```

### 3. Vérifier les logs nginx

```bash
sudo tail -30 /var/log/nginx/mokilievent.com.error.log
```

### 4. Vérifier les permissions de storage

```bash
# Vérifier que storage est accessible en écriture
ls -la /var/www/mokilievent/evenementiel/storage/
ls -la /var/www/mokilievent/evenementiel/storage/logs/
```

### 5. Vérifier le fichier .env

```bash
# Vérifier que .env existe et est lisible
ls -la /var/www/mokilievent/evenementiel/.env
sudo -u www-data cat /var/www/mokilievent/evenementiel/.env | head -10
```

## Solutions courantes

### Solution 1 : Permissions storage et cache

```bash
# S'assurer que storage et bootstrap/cache sont accessibles en écriture
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel/storage
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel/bootstrap/cache
```

### Solution 2 : Vérifier APP_KEY dans .env

```bash
# Vérifier que APP_KEY est défini
grep APP_KEY /var/www/mokilievent/evenementiel/.env

# Si vide, générer une nouvelle clé
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan key:generate
```

### Solution 3 : Vérifier les dépendances

```bash
# Vérifier que composer install a été exécuté
ls -la /var/www/mokilievent/evenementiel/vendor/

# Si manquant
cd /var/www/mokilievent/evenementiel
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### Solution 4 : Vérifier le cache Laravel

```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear
```

### Solution 5 : Vérifier les permissions des fichiers de log

```bash
# Créer le dossier logs s'il n'existe pas
sudo mkdir -p /var/www/mokilievent/evenementiel/storage/logs
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage/logs
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel/storage/logs

# Créer le fichier laravel.log s'il n'existe pas
sudo touch /var/www/mokilievent/evenementiel/storage/logs/laravel.log
sudo chmod 664 /var/www/mokilievent/evenementiel/storage/logs/laravel.log
sudo chown www-data:www-data /var/www/mokilievent/evenementiel/storage/logs/laravel.log
```

