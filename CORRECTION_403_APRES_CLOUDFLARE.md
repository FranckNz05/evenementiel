# 🔧 Correction : Erreur 403 après désactivation Cloudflare

## Diagnostic

Maintenant que Cloudflare ne proxy plus, les requêtes arrivent directement au serveur. L'erreur 403 peut venir de :

1. **Permissions incorrectes** des fichiers
2. **Configuration nginx** qui bloque
3. **SELinux** (si activé)
4. **Permissions du dossier parent**

## Commandes de diagnostic

### 1. Vérifier les logs nginx

```bash
sudo tail -30 /var/log/nginx/mokilievent.com.error.log
```

### 2. Vérifier les permissions

```bash
# Vérifier le dossier public
ls -la /var/www/mokilievent/evenementiel/public/

# Vérifier le dossier parent
ls -ld /var/www/mokilievent/evenementiel/
ls -ld /var/www/mokilievent/
```

### 3. Vérifier que nginx peut lire

```bash
sudo -u www-data ls -la /var/www/mokilievent/evenementiel/public/index.php
```

### 4. Vérifier SELinux (si activé)

```bash
getenforce
# Si "Enforcing", désactiver temporairement pour tester
sudo setenforce 0
```

## Solutions

### Solution 1 : Corriger les permissions

```bash
# Corriger le propriétaire
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel

# Corriger les permissions
sudo find /var/www/mokilievent/evenementiel -type d -exec chmod 755 {} \;
sudo find /var/www/mokilievent/evenementiel -type f -exec chmod 644 {} \;

# Permissions spéciales pour Laravel
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
sudo chmod 755 /var/www/mokilievent/evenementiel/public
sudo chmod 644 /var/www/mokilievent/evenementiel/public/index.php
```

### Solution 2 : Vérifier la configuration nginx

```bash
# Vérifier qu'il n'y a pas de "deny all"
sudo grep -i "deny\|allow" /etc/nginx/sites-available/mokilievent.com.conf
```

### Solution 3 : Vérifier les permissions du dossier parent

```bash
# S'assurer que les dossiers parents sont accessibles
sudo chmod 755 /var/www
sudo chmod 755 /var/www/mokilievent
sudo chmod 755 /var/www/mokilievent/evenementiel
```

