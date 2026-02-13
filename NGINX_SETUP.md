# Configuration Nginx pour mokilievent.com

## 📋 Instructions d'installation

### 1. Copier la configuration sur le VPS

```bash
# Sur le VPS
sudo cp /var/www/mokilievent/evenementiel/nginx/mokilievent.com.conf /etc/nginx/sites-available/mokilievent.com.conf
```

### 2. Créer le lien symbolique

```bash
# Activer le site
sudo ln -s /etc/nginx/sites-available/mokilievent.com.conf /etc/nginx/sites-enabled/mokilievent.com.conf
```

### 3. Vérifier la version PHP

```bash
# Vérifier la version PHP installée
php -v

# Vérifier le socket PHP-FPM
ls -la /var/run/php/
```

**Important** : Si votre version PHP est différente de 8.1, modifiez la ligne dans le fichier de configuration :
```nginx
fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
```
Par exemple, pour PHP 8.2 :
```nginx
fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
```

### 4. Tester la configuration Nginx

```bash
# Vérifier la syntaxe
sudo nginx -t

# Si tout est OK, recharger Nginx
sudo systemctl reload nginx
```

### 5. Vérifier les permissions

```bash
# Vérifier que les permissions sont correctes
ls -la /var/www/mokilievent/evenementiel/public

# Si nécessaire, ajuster les permissions
sudo chown -R cursor:www-data /var/www/mokilievent/evenementiel
sudo chmod -R 755 /var/www/mokilievent/evenementiel
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
```

### 6. Configurer le DNS

Sur votre registraire de domaine, configurez les enregistrements DNS :

**Type A** :
```
mokilievent.com     → IP_DU_VPS
www.mokilievent.com → IP_DU_VPS
```

**Exemple** :
```
mokilievent.com     → 123.456.789.012
www.mokilievent.com → 123.456.789.012
```

### 7. Vérifier la connexion

```bash
# Tester depuis le VPS
curl -I http://localhost

# Tester depuis votre machine locale (après configuration DNS)
curl -I http://mokilievent.com
```

## 🔒 Configuration SSL (HTTPS) avec Let's Encrypt

### 1. Installer Certbot

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

### 2. Obtenir le certificat SSL

```bash
sudo certbot --nginx -d mokilievent.com -d www.mokilievent.com
```

Certbot va :
- Générer les certificats SSL
- Modifier automatiquement la configuration Nginx
- Configurer le renouvellement automatique

### 3. Tester le renouvellement automatique

```bash
sudo certbot renew --dry-run
```

### 4. Activer HTTPS dans la configuration

Après l'installation SSL, décommenter la section HTTPS dans `/etc/nginx/sites-available/mokilievent.com.conf` et commenter la redirection HTTP.

## 🔧 Dépannage

### Erreur 502 Bad Gateway

**Cause** : PHP-FPM n'est pas démarré ou le socket est incorrect.

**Solution** :
```bash
# Vérifier le statut de PHP-FPM
sudo systemctl status php8.1-fpm

# Démarrer PHP-FPM si nécessaire
sudo systemctl start php8.1-fpm
sudo systemctl enable php8.1-fpm

# Vérifier le socket
ls -la /var/run/php/php8.1-fpm.sock
```

### Erreur 403 Forbidden

**Cause** : Permissions incorrectes.

**Solution** :
```bash
sudo chown -R cursor:www-data /var/www/mokilievent/evenementiel
sudo chmod -R 755 /var/www/mokilievent/evenementiel
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
```

### Erreur 404 Not Found

**Cause** : Document root incorrect ou fichier index.php manquant.

**Solution** :
```bash
# Vérifier que le fichier index.php existe
ls -la /var/www/mokilievent/evenementiel/public/index.php

# Vérifier la configuration Nginx
sudo nginx -t
```

### Vérifier les logs

```bash
# Logs Nginx
sudo tail -f /var/log/nginx/mokilievent.com.error.log
sudo tail -f /var/log/nginx/mokilievent.com.access.log

# Logs PHP-FPM
sudo tail -f /var/log/php8.1-fpm.log

# Logs Laravel
tail -f /var/www/mokilievent/evenementiel/storage/logs/laravel.log
```

## 📝 Notes importantes

1. **Version PHP** : Assurez-vous que la version PHP dans la configuration correspond à celle installée sur le VPS.

2. **Permissions** : Les répertoires `storage` et `bootstrap/cache` doivent être accessibles en écriture par le serveur web.

3. **DNS** : La propagation DNS peut prendre jusqu'à 48 heures. Utilisez `dig mokilievent.com` pour vérifier.

4. **Firewall** : Assurez-vous que les ports 80 (HTTP) et 443 (HTTPS) sont ouverts :
```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

5. **SELinux** (si activé) : Désactiver ou configurer SELinux pour permettre à Nginx d'accéder aux fichiers.

## ✅ Checklist de vérification

- [ ] Configuration Nginx copiée dans `/etc/nginx/sites-available/`
- [ ] Lien symbolique créé dans `/etc/nginx/sites-enabled/`
- [ ] Version PHP vérifiée et socket PHP-FPM correct
- [ ] Configuration Nginx testée (`nginx -t`)
- [ ] Nginx rechargé (`systemctl reload nginx`)
- [ ] Permissions des fichiers vérifiées
- [ ] DNS configuré (A record pointant vers l'IP du VPS)
- [ ] Ports 80 et 443 ouverts dans le firewall
- [ ] SSL configuré (optionnel mais recommandé)
- [ ] Site accessible via `http://mokilievent.com`

