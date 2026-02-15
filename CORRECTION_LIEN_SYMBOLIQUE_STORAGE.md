# Correction du lien symbolique storage

## Problème
Le logo et les fichiers uploadés sont dans `storage/app/public/settings/` mais doivent être accessibles via `public/storage/settings/` grâce à un lien symbolique.

## Solution sur le serveur

### 1. Vérifier si le lien existe
```bash
cd /var/www/mokilievent/evenementiel
ls -la public/storage
```

**Si vous voyez :**
- `public/storage -> /var/www/mokilievent/evenementiel/storage/app/public` → ✅ Le lien existe
- `ls: cannot access 'public/storage': No such file or directory` → ❌ Le lien n'existe pas

### 2. Supprimer l'ancien lien (s'il existe mais est cassé)
```bash
cd /var/www/mokilievent/evenementiel
# Si le lien existe mais est cassé :
sudo rm public/storage
```

### 3. Recréer le lien symbolique
```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan storage:link
```

### 4. Vérifier que le lien fonctionne
```bash
ls -la public/storage
# Devrait afficher :
# lrwxrwxrwx 1 www-data www-data ... public/storage -> /var/www/mokilievent/evenementiel/storage/app/public
```

### 5. Vérifier l'accès aux fichiers
```bash
# Vérifier que les logos sont accessibles
ls -la public/storage/settings/
# Devrait lister les fichiers de logos
```

### 6. Tester l'accès via URL
```bash
# Depuis le serveur, tester l'accès :
curl -I http://localhost/storage/settings/logo.png
# Ou si vous connaissez le nom du fichier :
curl -I http://localhost/storage/settings/NOM_DU_FICHIER.png
```

### 7. Vérifier les permissions
```bash
# S'assurer que les permissions sont correctes
sudo chown -R www-data:www-data storage/app/public
sudo chmod -R 775 storage/app/public
sudo chown -R www-data:www-data public/storage
sudo chmod -R 775 public/storage
```

### 8. Vider les caches Laravel
```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
```

## Commandes complètes à exécuter dans l'ordre

```bash
cd /var/www/mokilievent/evenementiel

# 1. Supprimer l'ancien lien s'il existe
sudo rm -f public/storage

# 2. Recréer le lien symbolique
sudo -u www-data php artisan storage:link

# 3. Vérifier
ls -la public/storage

# 4. Corriger les permissions
sudo chown -R www-data:www-data storage/app/public public/storage
sudo chmod -R 775 storage/app/public public/storage

# 5. Vérifier l'accès aux fichiers
ls -la public/storage/settings/

# 6. Vider les caches
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
```

## Vérification finale

Après avoir créé le lien, le logo devrait être accessible via :
- `https://mokilievent.com/storage/settings/NOM_DU_FICHIER.png`

Où `NOM_DU_FICHIER.png` est le nom du fichier stocké dans la table `settings` avec la clé `logo`.

