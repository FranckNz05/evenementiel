# 🚀 Configuration CI/CD - Guide Rapide

## ✅ Ce qui a été créé

1. **`.github/workflows/ci-cd.yml`** - Pipeline automatique (tests + déploiement)
2. **`.github/workflows/deploy-manual.yml`** - Déploiement manuel depuis GitHub
3. **`deploy.sh`** - Script de déploiement pour exécution manuelle sur VPS
4. **`DEPLOYMENT.md`** - Documentation complète du déploiement

## 📋 Ordre d'installation (IMPORTANT - Suivez cet ordre !)

**⚠️ L'ordre est crucial ! Ne configurez pas les secrets GitHub avant d'avoir préparé le VPS.**

1. ✅ Installer les dépendances sur le VPS (PHP, Composer, Node.js, etc.)
2. ✅ **Créer le répertoire de déploiement** (ex: `/var/www/html`)
3. ✅ Cloner le projet et configurer `.env` (optionnel mais recommandé)
4. ✅ Générer la clé SSH sur le VPS
5. ✅ **Configurer les secrets GitHub** (avec les infos du VPS)
6. ✅ Tester le pipeline

---

### ÉTAPE 1 : Installer les dépendances sur le VPS

Connectez-vous à votre VPS via SSH et exécutez :

```bash
# 1. Mettre à jour le système
sudo apt update && sudo apt upgrade -y

# 2. Installer PHP 8.1 et extensions
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-bcmath php8.1-gd php8.1-zip php8.1-curl

# 3. Installer Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 4. Installer Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# 5. Installer MySQL (si pas déjà fait)
sudo apt install -y mysql-server

# 6. Installer Nginx (si pas déjà fait)
sudo apt install -y nginx

# 7. Installer Git (si pas déjà fait)
sudo apt install -y git
```

### ÉTAPE 2 : Créer le répertoire de déploiement

**⚠️ IMPORTANT : Créez d'abord le répertoire avant de configurer les secrets GitHub !**

```bash
# Créer le répertoire de déploiement
sudo mkdir -p /var/www/html

# Donner les permissions à votre utilisateur
sudo chown -R $USER:$USER /var/www/html

# OU utiliser un autre chemin selon votre configuration (ex: /home/user/app)
# Dans ce cas, notez le chemin exact pour l'utiliser dans VPS_DEPLOY_PATH
```

**Notez le chemin exact** que vous avez choisi (ex: `/var/www/html` ou `/home/user/app`) - vous en aurez besoin pour le secret `VPS_DEPLOY_PATH`.

### ÉTAPE 3 : Cloner le projet une première fois (optionnel mais recommandé)

```bash
cd /var/www/html  # ou votre chemin choisi

# Cloner votre repo
git clone https://github.com/VOTRE_USERNAME/VOTRE_REPO.git .

# Copier et configurer .env
cp .env.example .env
nano .env  # Configurez vos variables d'environnement (DB, APP_URL, etc.)

# Installer les dépendances
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Gérer les permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Générer la clé d'application
php artisan key:generate
```

### ÉTAPE 4 : Générer une clé SSH pour GitHub Actions

**Sur votre VPS**, générez une clé SSH :

```bash
# Générer la clé SSH
ssh-keygen -t ed25519 -C "github-actions-deploy"
# Appuyez sur Entrée pour accepter l'emplacement par défaut
# Ne mettez PAS de passphrase (ou notez-la si vous en mettez une)

# Afficher la clé privée (à copier dans GitHub Secrets)
cat ~/.ssh/id_ed25519

# Afficher la clé publique (à ajouter dans authorized_keys)
cat ~/.ssh/id_ed25519.pub
```

**Ajoutez la clé publique dans authorized_keys** :
```bash
cat ~/.ssh/id_ed25519.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### ÉTAPE 5 : Configurer les secrets GitHub

**Maintenant** que tout est prêt sur le VPS, configurez les secrets GitHub :

Allez sur GitHub → Votre dépôt → **Settings** → **Secrets and variables** → **Actions** → **Repository secrets** → **New repository secret**

Ajoutez ces secrets (dans cet ordre) :

| Nom du Secret | Valeur | Comment obtenir |
|---------------|--------|-----------------|
| `VPS_HOST` | L'adresse IP ou domaine de votre VPS | Ex: `192.168.1.100` ou `vps.example.com` |
| `VPS_USER` | Votre nom d'utilisateur SSH | Ex: `ubuntu`, `root`, ou votre utilisateur (celui que vous utilisez pour SSH) |
| `VPS_SSH_KEY` | Le contenu complet de votre clé privée | `cat ~/.ssh/id_ed25519` sur le VPS (copiez TOUT le contenu) |
| `VPS_DEPLOY_PATH` | Chemin où déployer sur le VPS | Le chemin que vous avez créé à l'ÉTAPE 2 (ex: `/var/www/html`) |
| `VPS_PORT` | Port SSH (optionnel) | `22` par défaut, changez si différent |

**Exemple de VPS_SSH_KEY** (copiez tout, y compris les lignes BEGIN/END) :
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAACFwAAAAdzc2gtcn
... (tout le contenu) ...
-----END OPENSSH PRIVATE KEY-----
```

### ÉTAPE 6 : Configurer Nginx (optionnel pour tester)

Créez `/etc/nginx/sites-available/laravel` (voir `DEPLOYMENT.md` pour le contenu complet)

### ÉTAPE 7 : Tester le pipeline

Une fois toutes les étapes précédentes terminées :

1. **Poussez vos changements sur GitHub** (depuis votre machine locale) :
   ```bash
   git add .
   git commit -m "Ajout du pipeline CI/CD"
   git push origin main
   ```

2. **Allez sur GitHub → Actions** pour voir le pipeline s'exécuter

3. Le déploiement se fera automatiquement si les tests passent

**⚠️ Si c'est la première fois**, vous pouvez d'abord tester avec le déploiement manuel (voir section ci-dessous)

## 🔄 Utilisation

### Déploiement automatique
- Se déclenche automatiquement à chaque push sur `main` ou `master`
- Exécute d'abord les tests
- Si les tests passent, déploie sur le VPS

### Déploiement manuel
- Allez sur GitHub → **Actions** → **Deploy Manual**
- Cliquez sur **Run workflow**
- Choisissez la branche à déployer
- Cliquez sur **Run workflow**

### Déploiement via script
Sur votre VPS:
```bash
cd /var/www/html
chmod +x deploy.sh
./deploy.sh
```

## ⚠️ Important

- ✅ Ne commitez **JAMAIS** le fichier `.env`
- ✅ Vérifiez que tous les secrets GitHub sont configurés
- ✅ Testez la connexion SSH avant le premier déploiement
- ✅ Configurez les permissions correctement sur le VPS
- ✅ Utilisez HTTPS en production (Let's Encrypt)

## 📚 Documentation complète

Consultez `DEPLOYMENT.md` pour:
- Configuration détaillée de Nginx
- Dépannage des erreurs courantes
- Configuration de sécurité
- Optimisations avancées

## 🆘 Besoin d'aide?

Si vous rencontrez des problèmes:
1. Vérifiez les logs GitHub Actions
2. Vérifiez les logs sur le VPS: `tail -f storage/logs/laravel.log`
3. Consultez la section Dépannage dans `DEPLOYMENT.md`

