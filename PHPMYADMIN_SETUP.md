# Installation et configuration de phpMyAdmin

## 📋 Installation

### 1. Installer phpMyAdmin

```bash
sudo apt update
sudo apt install phpmyadmin php-mbstring php-zip php-gd php-json php-curl -y
```

**Pendant l'installation, choisir :**
- Serveur web : **nginx** (ou apache2 selon votre config)
- Configurer la base de données : **Oui**
- Mot de passe pour phpmyadmin : (choisir un mot de passe sécurisé)

### 2. Créer le lien symbolique (si nécessaire)

```bash
# Pour Nginx, créer un lien symbolique
sudo ln -s /usr/share/phpmyadmin /var/www/html/phpmyadmin
```

## 🔧 Configuration Nginx

### Option 1 : Ajouter à la configuration existante

Ajoutez cette section dans `/etc/nginx/sites-available/mokilievent.com.conf` :

```nginx
location /phpmyadmin {
    alias /usr/share/phpmyadmin;
    index index.php;
    
    location ~ ^/phpmyadmin/(.+\.php)$ {
        alias /usr/share/phpmyadmin/$1;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $request_filename;
    }
    
    location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
        alias /usr/share/phpmyadmin/$1;
    }
}
```

Puis :
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Option 2 : Sous-domaine séparé

Créer un fichier `/etc/nginx/sites-available/phpmyadmin.conf` :

```nginx
server {
    listen 80;
    server_name phpmyadmin.mokilievent.com;
    
    root /usr/share/phpmyadmin;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

Activer :
```bash
sudo ln -s /etc/nginx/sites-available/phpmyadmin.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Sécurité

### 1. Restreindre l'accès par IP (recommandé)

```nginx
location /phpmyadmin {
    # Autoriser seulement certaines IPs
    allow VOTRE_IP;
    deny all;
    
    alias /usr/share/phpmyadmin;
    # ... reste de la config
}
```

### 2. Utiliser HTTPS

```bash
# Installer SSL avec Certbot
sudo certbot --nginx -d phpmyadmin.mokilievent.com
```

### 3. Changer l'URL par défaut

```bash
# Renommer le répertoire pour plus de sécurité
sudo mv /usr/share/phpmyadmin /usr/share/pma_secret
# Mettre à jour la config Nginx en conséquence
```

## 🌐 Accès

Une fois configuré :

- **Via le domaine principal** : `https://mokilievent.com/phpmyadmin`
- **Via sous-domaine** : `https://phpmyadmin.mokilievent.com`

**Identifiants :**
- Utilisateur : `root` ou l'utilisateur MySQL configuré
- Mot de passe : Le mot de passe MySQL (pas celui de phpmyadmin)

## 🔍 Vérification

```bash
# Vérifier que phpMyAdmin est installé
ls -la /usr/share/phpmyadmin

# Vérifier la configuration Nginx
sudo nginx -t

# Tester l'accès
curl -I http://localhost/phpmyadmin
```

## ⚠️ Important

- **Ne pas exposer phpMyAdmin publiquement** sans protection
- Utiliser HTTPS
- Restreindre l'accès par IP si possible
- Utiliser un mot de passe fort pour l'utilisateur MySQL

