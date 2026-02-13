# Instructions de Déploiement - MokiliEvent

## Installation du script deploy.sh

### 1. Copier le script sur le serveur

```bash
# Depuis votre machine locale (si vous avez accès SSH)
scp deploy.sh cursor@votre-serveur:/var/www/mokilievent/

# OU depuis le serveur directement
# Télécharger le fichier ou le créer avec nano/vim
```

### 2. Rendre le script exécutable

```bash
# Se connecter au serveur
ssh cursor@votre-serveur

# Aller dans le répertoire
cd /var/www/mokilievent

# Rendre exécutable
chmod +x deploy.sh
```

### 3. Vérifier les permissions

```bash
# Vérifier que le script appartient à l'utilisateur cursor
ls -la deploy.sh

# Devrait afficher quelque chose comme:
# -rwxr-xr-x 1 cursor cursor 1234 date deploy.sh
```

## Configuration des permissions Nginx (optionnel)

Si vous voulez que le script recharge Nginx sans sudo, configurez sudoers :

```bash
# Éditer sudoers (nécessite root)
sudo visudo

# Ajouter cette ligne (remplacer cursor par votre utilisateur si différent)
cursor ALL=(ALL) NOPASSWD: /bin/systemctl reload nginx, /usr/sbin/nginx -s reload
```

**⚠️ Attention** : Cette configuration donne des permissions spécifiques. Si vous préférez, le script fonctionnera sans mais affichera un avertissement.

## Test manuel

### Test complet

```bash
# Depuis /var/www/mokilievent
cd /var/www/mokilievent
./deploy.sh
```

### Test étape par étape (debug)

Si vous voulez tester chaque étape manuellement :

```bash
cd /var/www/mokilievent/evenementiel

# 1. Git pull
git pull origin main

# 2. Composer
composer install --no-dev --optimize-autoloader

# 3. Migrations
php artisan migrate --force

# 4. Caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Nginx
sudo systemctl reload nginx
```

## Utilisation quotidienne

### Déploiement standard

```bash
cd /var/www/mokilievent
./deploy.sh
```

### Voir les logs

```bash
tail -f /var/www/mokilievent/deploy.log
```

### Déploiement avec vérification Git

```bash
cd /var/www/mokilievent/evenementiel
git status
git log -1
cd ..
./deploy.sh
```

## Automatisation (optionnel)

### Cron pour déploiement automatique

```bash
# Éditer le crontab
crontab -e

# Ajouter (exemple: tous les jours à 2h du matin)
0 2 * * * cd /var/www/mokilievent && ./deploy.sh >> /var/www/mokilievent/deploy-cron.log 2>&1
```

### Webhook GitHub/GitLab (recommandé)

Créer un endpoint Laravel pour recevoir les webhooks :

```php
// routes/web.php
Route::post('/deploy', function() {
    $output = [];
    exec('cd /var/www/mokilievent && ./deploy.sh 2>&1', $output);
    return response()->json(['output' => $output]);
})->middleware('auth'); // Protéger avec un token
```

## Dépannage

### Erreur: "Permission denied"

```bash
chmod +x deploy.sh
```

### Erreur: "Le script doit être exécuté depuis /var/www/mokilievent"

```bash
# Vérifier le répertoire actuel
pwd

# Se déplacer au bon endroit
cd /var/www/mokilievent
./deploy.sh
```

### Erreur: "Échec de composer install"

```bash
# Vérifier que Composer est installé
composer --version

# Vérifier les permissions
ls -la composer.json
```

### Erreur: "Échec des migrations"

```bash
# Vérifier la connexion à la base de données
php artisan migrate:status

# Vérifier le fichier .env
cat .env | grep DB_
```

### Nginx ne se recharge pas

```bash
# Recharger manuellement
sudo systemctl reload nginx

# OU
sudo nginx -s reload

# Vérifier le statut
sudo systemctl status nginx
```

## Vérification post-déploiement

```bash
# Vérifier que l'application fonctionne
curl -I https://mokilievent.com

# Vérifier les logs Laravel
tail -f /var/www/mokilievent/evenementiel/storage/logs/laravel.log

# Vérifier les logs Nginx
sudo tail -f /var/log/nginx/error.log
```

## Sécurité

- ✅ Le script utilise `set -e` pour arrêter en cas d'erreur
- ✅ Les migrations utilisent `--force` (nécessaire en production)
- ✅ Composer utilise `--no-dev` pour ne pas installer les dépendances de dev
- ⚠️ Le script nécessite des permissions sur le répertoire du projet
- ⚠️ Les logs sont stockés dans `/var/www/mokilievent/deploy.log`

## Notes importantes

1. **Backup** : Toujours faire un backup avant un déploiement majeur
2. **Maintenance** : Mettre l'application en maintenance si nécessaire avec `php artisan down`
3. **Tests** : Tester sur un environnement de staging avant la production
4. **Rollback** : Garder une branche de backup ou utiliser `git revert`

