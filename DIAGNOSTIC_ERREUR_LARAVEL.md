# 🔍 Diagnostic de l'erreur Laravel

## Commande pour voir l'erreur complète

```bash
# Voir les 100 dernières lignes avec l'erreur complète
tail -100 /var/www/mokilievent/evenementiel/storage/logs/laravel.log | grep -A 20 "ERROR\|Exception\|Error"

# Ou voir la dernière erreur complète
tail -200 /var/www/mokilievent/evenementiel/storage/logs/laravel.log | head -100

# Voir juste la dernière entrée d'erreur
grep -A 50 "local.ERROR" /var/www/mokilievent/evenementiel/storage/logs/laravel.log | tail -60
```

## Erreurs courantes et solutions

### Erreur : Permission denied sur storage
```bash
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel/storage
```

### Erreur : APP_KEY manquant
```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan key:generate
```

### Erreur : View not found
```bash
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan config:clear
```

### Erreur : Database connection
```bash
# Vérifier .env
grep DB_ /var/www/mokilievent/evenementiel/.env
```

