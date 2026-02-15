# 🔧 Corrections finales

## Problème 1 : Fonction format_number() manquante

Le fichier `app/Helpers/helpers.php` existe mais n'est pas chargé.

### Solution : Ajouter dans composer.json

Modifiez `composer.json` pour ajouter :

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/helpers.php"
    ]
}
```

Puis sur le serveur :
```bash
cd /var/www/mokilievent/evenementiel
composer dump-autoload
```

### Solution alternative : Charger dans un Service Provider

Ou ajouter dans `app/Providers/AppServiceProvider.php` dans la méthode `boot()` :

```php
require_once app_path('Helpers/helpers.php');
```

## Problème 2 : Redirection n8n depuis le navigateur

Si `curl` fonctionne mais que le navigateur redirige vers n8n, c'est **Cloudflare** qui fait la redirection.

### Vérifications Cloudflare

1. **Dashboard Cloudflare** → **Rules** → **Page Rules**
   - Chercher une règle avec pattern `*mokilievent.com*` ou `mokilievent.com/*`
   - Vérifier si elle redirige vers `/signin`

2. **Rules** → **Transform Rules** → **Redirect Rules**
   - Chercher des règles qui redirigent vers `/signin`

3. **Security** → **WAF**
   - Vérifier s'il y a des blocages récents

4. **Vider le cache Cloudflare** :
   - **Caching** → **Configuration** → **Purge Everything**

### Test pour confirmer

```bash
# Depuis le serveur (devrait fonctionner)
curl -I -H "Host: mokilievent.com" http://localhost

# Depuis votre machine (bypass Cloudflare)
curl -I http://IP_DU_VPS -H "Host: mokilievent.com"

# Si ça fonctionne avec l'IP mais pas avec le domaine, c'est Cloudflare
```

## Commandes complètes sur le serveur

```bash
cd /var/www/mokilievent/evenementiel

# 1. Ajouter helpers.php dans composer.json (éditer le fichier)
# Puis :
composer dump-autoload

# 2. Nettoyer le cache Laravel
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear

# 3. Tester
curl -I -H "Host: mokilievent.com" http://localhost
```

