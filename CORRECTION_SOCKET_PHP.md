# 🔧 Correction du socket PHP-FPM

## Situation
- Socket symbolique : `/run/php/php-fpm.sock` → `/etc/alternatives/php-fpm.sock`
- Sockets réels disponibles : `php8.1-fpm.sock` et `php8.4-fpm.sock`

## Vérifications à faire

### 1. Vérifier si le lien symbolique fonctionne

```bash
ls -la /etc/alternatives/php-fpm.sock
```

### 2. Vérifier quelle version PHP est utilisée par Laravel

```bash
cd /var/www/mokilievent/evenementiel
php -v
```

### 3. Vérifier les logs nginx pour l'erreur exacte

```bash
sudo tail -20 /var/log/nginx/mokilievent.com.error.log
```

## Solution

Si le lien symbolique ne fonctionne pas, utilisez directement le socket de la version PHP active.

### Option 1 : Utiliser php8.4-fpm.sock (si PHP 8.4 est actif)

Modifier la configuration :
```nginx
fastcgi_pass unix:/run/php/php8.4-fpm.sock;
```

### Option 2 : Utiliser php8.1-fpm.sock (si PHP 8.1 est actif)

```nginx
fastcgi_pass unix:/run/php/php8.1-fpm.sock;
```

### Option 3 : Vérifier et corriger le lien symbolique

```bash
# Voir où pointe le lien
readlink -f /run/php/php-fpm.sock

# Vérifier si le fichier existe
ls -la /etc/alternatives/php-fpm.sock
```

