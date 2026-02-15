# 🔧 Correction : Erreur 502 Bad Gateway

## Problème
Après avoir supprimé la config `default`, on obtient un 502 Bad Gateway. Cela signifie que nginx ne peut pas communiquer avec PHP-FPM.

## Diagnostic

### 1. Vérifier la configuration mokilievent.com.conf

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

### 2. Vérifier quel socket PHP-FPM est disponible

```bash
ls -la /var/run/php/
# ou
ls -la /run/php/
```

### 3. Vérifier le statut de PHP-FPM

```bash
sudo systemctl status php*-fpm
```

### 4. Vérifier les logs nginx pour l'erreur exacte

```bash
sudo tail -20 /var/log/nginx/mokilievent.com.error.log
```

## Solution probable

Le socket PHP-FPM dans la configuration est probablement incorrect. Il faut le corriger.

### Socket probable selon la version PHP :
- PHP 8.1 : `/run/php/php8.1-fpm.sock`
- PHP 8.2 : `/run/php/php8.2-fpm.sock`
- PHP 8.4 : `/run/php/php8.4-fpm.sock`

