# Guide de Déploiement UAT - MokiliEvent

## 📋 Vue d'Ensemble

Ce guide explique comment déployer et configurer l'environnement UAT (User Acceptance Testing) pour MokiliEvent.

## 🎯 Prérequis

- Serveur avec PHP 8.2+, MySQL, Nginx/Apache
- Accès SSH au serveur
- Nom de domaine configuré (ex: uat.mokilievent.com)
- Mailtrap ou service email de test
- Comptes sandbox pour passerelles de paiement

## 🚀 Étapes de Déploiement

### 1. Préparation de la Base de Données

```bash
# Se connecter au serveur MySQL
mysql -u root -p

# Créer la base de données UAT
CREATE DATABASE mokilievent_uat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Créer un utilisateur dédié (optionnel)
CREATE USER 'mokilievent_uat'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON mokilievent_uat.* TO 'mokilievent_uat'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Configuration de l'Environnement

```bash
# Copier le fichier .env.uat vers .env
cp .env.uat .env

# Générer une nouvelle clé d'application
php artisan key:generate

# Configurer les permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Configuration du Fichier .env

Éditer `.env` et configurer :

```env
# Base de données
DB_DATABASE=mokilievent_uat
DB_USERNAME=mokilievent_uat
DB_PASSWORD=votre_mot_de_passe_db

# URL de l'application
APP_URL=https://uat.mokilievent.com

# Email (Mailtrap pour les tests)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap

# Paiement (mode sandbox)
FEDAPAY_PUBLIC_KEY=pk_sandbox_xxxxx
FEDAPAY_SECRET_KEY=sk_sandbox_xxxxx
FEDAPAY_ENVIRONMENT=sandbox
```

### 4. Migration et Seeding

```bash
# Exécuter les migrations
php artisan migrate:fresh

# Charger les données de test UAT
php artisan db:seed --class=UATSeeder

# Vérifier que tout est créé
php artisan tinker
>>> User::count(); // Devrait retourner 8+ utilisateurs
>>> Event::count(); // Devrait retourner 5+ événements
>>> exit
```

### 5. Configuration du Serveur Web

#### Pour Nginx

```nginx
server {
    listen 80;
    server_name uat.mokilievent.com;
    root /var/www/mokilievent-uat/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Activer le site
sudo ln -s /etc/nginx/sites-available/uat.mokilievent.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 6. Configuration SSL (Let's Encrypt)

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx

# Obtenir un certificat SSL
sudo certbot --nginx -d uat.mokilievent.com

# Vérifier le renouvellement automatique
sudo certbot renew --dry-run
```

### 7. Optimisations Laravel

```bash
# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compiler les assets
npm install
npm run build
```

### 8. Vérifications Post-Déploiement

```bash
# Vérifier l'état de l'application
php artisan about

# Tester les emails
php artisan tinker
>>> Mail::raw('Test UAT', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Vérifier les logs
tail -f storage/logs/laravel.log
```

## ✅ Checklist de Validation

- [ ] Application accessible via https://uat.mokilievent.com
- [ ] Connexion avec admin@uat.test fonctionne
- [ ] Connexion avec orga@uat.test fonctionne
- [ ] Connexion avec user@uat.test fonctionne
- [ ] 5 événements visibles sur la page d'accueil
- [ ] Emails envoyés vers Mailtrap
- [ ] Aucune erreur dans storage/logs/laravel.log
- [ ] Assets (CSS/JS) chargent correctement
- [ ] Images d'événements s'affichent

## 🔧 Maintenance

### Réinitialiser les Données de Test

```bash
# Effacer et recréer toutes les données
php artisan migrate:fresh --seed --class=UATSeeder

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Monitorer les Logs

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/error.log

# Logs PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

## 📊 Comptes de Test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@uat.test | Admin123! |
| Organisateur | orga@uat.test | Orga123! |
| Utilisateur | user@uat.test | User123! |
| User 1-5 | user1@uat.test | Test123! |

## 🧪 Lancer les Tests

```bash
# Tests fonctionnels
php artisan test

# Tests de performance (optionnel)
# Installer hey: https://github.com/rakyll/hey
hey -n 100 -c 10 https://uat.mokilievent.com
```

## 📚 Documentation de Test

- **Plan de Test**: [docs/UAT_TEST_PLAN.md](docs/UAT_TEST_PLAN.md)
- **Guide Utilisateur**: [docs/UAT_USER_GUIDE.md](docs/UAT_USER_GUIDE.md)
- **Scénarios Détaillés**: [docs/TEST_SCENARIOS.md](docs/TEST_SCENARIOS.md)

## 🆘 Dépannage

### Erreur 500
```bash
# Vérifier les permissions
sudo chown -R www-data:www-data storage bootstrap/cache

# Vérifier les logs
tail -f storage/logs/laravel.log
```

### Base de données non accessible
```bash
# Tester la connexion
php artisan tinker
>>> DB::connection()->getPdo();
```

### Emails non envoyés
- Vérifier les credentials Mailtrap dans `.env`
- Vérifier les logs: `tail -f storage/logs/laravel.log`
- Tester manuellement: `php artisan tinker`

## 📞 Support

En cas de problème:
- **Email**: dev@mokilievent.com
- **Documentation**: Ce fichier README
- **Logs**: `storage/logs/laravel.log`

---

**Dernière mise à jour**: 2026-02-08
