# 🔧 Correction : Erreur 403 Forbidden

## Causes possibles

1. **Permissions incorrectes** sur `/var/www/mokilievent/evenementiel/public`
2. **Propriétaire incorrect** des fichiers
3. **Document root incorrect** dans la configuration nginx
4. **Index files manquants**

## Diagnostic

### 1. Vérifier les permissions

```bash
# Vérifier les permissions du dossier public
ls -la /var/www/mokilievent/evenementiel/public

# Vérifier si index.php existe
ls -la /var/www/mokilievent/evenementiel/public/index.php
```

### 2. Vérifier le propriétaire

```bash
# Voir qui est le propriétaire
ls -ld /var/www/mokilievent/evenementiel
ls -ld /var/www/mokilievent/evenementiel/public
```

### 3. Vérifier les logs nginx

```bash
sudo tail -30 /var/log/nginx/mokilievent.com.error.log
```

## Solutions

### Solution 1 : Corriger les permissions (la plus probable)

```bash
# Corriger le propriétaire (remplacer 'cursor' par votre utilisateur si différent)
sudo chown -R cursor:www-data /var/www/mokilievent/evenementiel

# Corriger les permissions
sudo chmod -R 755 /var/www/mokilievent/evenementiel
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache

# Vérifier que le dossier public est accessible
sudo chmod 755 /var/www/mokilievent/evenementiel/public
sudo chmod 644 /var/www/mokilievent/evenementiel/public/index.php
```

### Solution 2 : Vérifier que nginx peut lire les fichiers

```bash
# Tester si nginx peut lire le fichier
sudo -u www-data cat /var/www/mokilievent/evenementiel/public/index.php | head -5
```

Si ça échoue, c'est un problème de permissions.

### Solution 3 : Vérifier la configuration nginx

```bash
# Vérifier que le document root est correct
sudo grep -A 2 "root" /etc/nginx/sites-available/mokilievent.com.conf
```

### Solution 4 : Vérifier SELinux (si activé)

```bash
# Vérifier si SELinux est activé
getenforce

# Si c'est "Enforcing", désactiver temporairement pour tester
sudo setenforce 0
```

## Commande complète de correction

```bash
# Corriger tout d'un coup
sudo chown -R cursor:www-data /var/www/mokilievent/evenementiel
sudo find /var/www/mokilievent/evenementiel -type d -exec chmod 755 {} \;
sudo find /var/www/mokilievent/evenementiel -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
sudo chmod 755 /var/www/mokilievent/evenementiel/public
sudo chmod 644 /var/www/mokilievent/evenementiel/public/index.php

# Vérifier
ls -la /var/www/mokilievent/evenementiel/public/ | head -10
```

