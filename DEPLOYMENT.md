# Guide de Configuration CI/CD

Ce guide vous explique comment configurer le pipeline CI/CD pour déployer automatiquement votre application Laravel sur votre VPS.

## 📋 Prérequis

### Sur votre VPS

1. **PHP 8.1+** avec les extensions suivantes:
   ```bash
   sudo apt update
   sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-gd php8.1-zip php8.1-curl
   ```

2. **Composer**
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

3. **Node.js 18+ et NPM**
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt install -y nodejs
   ```

4. **MySQL/MariaDB**
   ```bash
   sudo apt install mysql-server
   ```

5. **Nginx** (ou Apache)
   ```bash
   sudo apt install nginx
   ```

6. **Git**
   ```bash
   sudo apt install git
   ```

7. **Clé SSH** pour l'accès au serveur
   ```bash
   ssh-keygen -t ed25519 -C "github-actions"
   # Copiez la clé publique dans ~/.ssh/authorized_keys
   ```

### Sur GitHub

1. Accédez à votre dépôt GitHub
2. Allez dans **Settings** → **Secrets and variables** → **Actions**
3. Ajoutez les secrets suivants:

## 🔐 Configuration des Secrets GitHub

Ajoutez ces secrets dans GitHub (Settings → Secrets → Actions):

| Secret | Description | Exemple |
|--------|-------------|---------|
| `VPS_HOST` | Adresse IP ou domaine de votre VPS | `192.168.1.100` ou `vps.example.com` |
| `VPS_USER` | Nom d'utilisateur SSH | `ubuntu` ou `root` |
| `VPS_SSH_KEY` | Clé privée SSH (complète avec `-----BEGIN ...`) | Contenu de `~/.ssh/id_rsa` |
| `VPS_PORT` | Port SSH (optionnel, défaut: 22) | `22` |
| `VPS_DEPLOY_PATH` | Chemin de déploiement sur le VPS | `/var/www/html` ou `/home/user/app` |

### Comment obtenir la clé SSH privée

Sur votre machine locale ou VPS:
```bash
cat ~/.ssh/id_rsa
# ou
cat ~/.ssh/id_ed25519
```

Copiez tout le contenu, y compris les lignes `-----BEGIN ...` et `-----END ...`.

## 🚀 Configuration du VPS

### 1. Préparer le répertoire de déploiement

```bash
# Créer le répertoire
sudo mkdir -p /var/www/html
sudo chown -R $USER:$USER /var/www/html

# Ou utiliser un autre chemin selon votre configuration
```

### 2. Configuration Nginx (exemple)

Créez un fichier de configuration Nginx `/etc/nginx/sites-available/laravel`:

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/html/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activez le site:
```bash
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 3. Configuration des permissions

```bash
cd /var/www/html
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Fichier .env sur le VPS

Créez le fichier `.env` sur votre VPS avec vos configurations de production:

```bash
cd /var/www/html
cp .env.example .env
nano .env
```

Configurez au minimum:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://votre-domaine.com`
- `DB_*` (paramètres de base de données)
- Toutes les autres variables d'environnement nécessaires

Générez la clé d'application:
```bash
php artisan key:generate
```

## 🔄 Workflow du Pipeline

Le pipeline GitHub Actions fait ceci:

1. **Tests** (sur chaque push/PR):
   - Installe PHP 8.1 et les extensions
   - Installe les dépendances Composer et NPM
   - Build les assets
   - Lance les tests PHPUnit

2. **Déploiement** (uniquement sur `main`/`master`):
   - Build les assets de production
   - Transfère les fichiers sur le VPS via SCP
   - Exécute les commandes de déploiement sur le VPS:
     - Mise en maintenance
     - Installation des dépendances
     - Optimisation des caches
     - Exécution des migrations
     - Remise en ligne

## 🛠️ Déploiement Manuel

Si vous préférez déployer manuellement, vous pouvez utiliser le script `deploy.sh`:

```bash
# Sur votre VPS
cd /var/www/html
chmod +x deploy.sh
./deploy.sh
```

## 🔍 Vérification

Après le déploiement, vérifiez:

1. L'application est accessible: `https://votre-domaine.com`
2. Les logs Laravel: `tail -f storage/logs/laravel.log`
3. Les logs Nginx: `sudo tail -f /var/log/nginx/error.log`
4. Les permissions: `ls -la storage bootstrap/cache`

## 🐛 Dépannage

### Erreur de connexion SSH
- Vérifiez que la clé SSH est correctement configurée
- Testez la connexion: `ssh -i ~/.ssh/id_rsa user@vps-host`
- Vérifiez que le port SSH est correct

### Erreur de permissions
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 775 /var/www/html/storage
sudo chmod -R 775 /var/www/html/bootstrap/cache
```

### Erreur de base de données
- Vérifiez les credentials dans `.env`
- Vérifiez que MySQL est en cours d'exécution: `sudo systemctl status mysql`
- Testez la connexion: `mysql -u user -p`

### Erreur de build
- Vérifiez que Node.js est installé: `node --version`
- Vérifiez que NPM est installé: `npm --version`
- Vérifiez les logs GitHub Actions pour plus de détails

## 📝 Notes Importantes

- ⚠️ Ne commitez **JAMAIS** le fichier `.env` (il est dans `.gitignore`)
- 🔒 Assurez-vous que les secrets GitHub sont bien configurés
- 🧪 Les tests sont exécutés avant chaque déploiement
- 🔄 Le déploiement ne se fait que sur la branche `main` ou `master`
- 📦 Les migrations sont exécutées automatiquement avec `--force`

## 🔐 Sécurité

- Utilisez HTTPS (Let's Encrypt avec Certbot)
- Configurez un firewall (UFW)
- Limitez l'accès SSH par IP si possible
- Utilisez des mots de passe forts pour la base de données
- Activez `APP_DEBUG=false` en production

