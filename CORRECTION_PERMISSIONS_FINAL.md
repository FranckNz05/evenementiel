# 🔧 Correction finale des permissions

## Problème identifié

Les fichiers appartiennent à `cursor:cursor` mais nginx s'exécute avec l'utilisateur `www-data`.

## Solution

### Option 1 : Changer le propriétaire vers www-data (recommandé)

```bash
# Changer le propriétaire de tout le projet
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel

# Corriger les permissions
sudo find /var/www/mokilievent/evenementiel -type d -exec chmod 755 {} \;
sudo find /var/www/mokilievent/evenementiel -type f -exec chmod 644 {} \;

# Permissions spéciales pour storage et bootstrap/cache (Laravel)
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache

# Vérifier
ls -la /var/www/mokilievent/evenementiel/public/ | head -5
```

### Option 2 : Garder cursor comme propriétaire mais ajouter www-data au groupe

```bash
# Ajouter www-data au groupe cursor
sudo usermod -a -G cursor www-data

# Changer le groupe du projet
sudo chgrp -R cursor /var/www/mokilievent/evenementiel

# Donner les permissions de lecture au groupe
sudo chmod -R 750 /var/www/mokilievent/evenementiel
sudo chmod -R 770 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 770 /var/www/mokilievent/evenementiel/bootstrap/cache
sudo chmod 755 /var/www/mokilievent/evenementiel/public
```

## Commande complète (Option 1 - recommandée)

```bash
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel
sudo find /var/www/mokilievent/evenementiel -type d -exec chmod 755 {} \;
sudo find /var/www/mokilievent/evenementiel -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
sudo chmod 755 /var/www/mokilievent/evenementiel/public

# Vérifier que nginx peut maintenant lire
sudo -u www-data ls -la /var/www/mokilievent/evenementiel/public/index.php

# Tester
curl -I -H "Host: mokilievent.com" http://localhost
```

## Note importante

Si vous utilisez `cursor` pour éditer les fichiers, vous devrez peut-être utiliser `sudo` pour certaines opérations, ou configurer votre utilisateur pour qu'il puisse écrire dans le dossier.

Pour permettre à `cursor` d'écrire sans sudo :
```bash
# Ajouter cursor au groupe www-data
sudo usermod -a -G www-data cursor

# Donner les permissions d'écriture au groupe
sudo chmod -R g+w /var/www/mokilievent/evenementiel
```

Puis reconnectez-vous pour que les changements de groupe prennent effet.

