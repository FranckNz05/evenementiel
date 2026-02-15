# 📋 Guide d'installation de la configuration Nginx

## Fichier créé
`nginx/mokilievent.com.conf.final`

## Étapes de transfert et installation

### Option 1 : Via SCP (depuis votre machine Windows)

```powershell
# Depuis PowerShell sur Windows
scp nginx/mokilievent.com.conf.final cursor@VOTRE_IP_VPS:/tmp/mokilievent.com.conf
```

### Option 2 : Via Git (si le fichier est dans le repo)

```bash
# Sur le serveur
cd /var/www/mokilievent/evenementiel
git pull
```

### Option 3 : Copier-coller le contenu

1. Ouvrez le fichier `nginx/mokilievent.com.conf.final` en local
2. Copiez tout le contenu
3. Sur le serveur, créez le fichier :
```bash
sudo nano /etc/nginx/sites-available/mokilievent.com.conf
```
4. Collez le contenu
5. Sauvegardez (Ctrl+O, Enter, Ctrl+X)

## Installation sur le serveur

Une fois le fichier transféré, exécutez ces commandes :

```bash
# 1. Vérifier que le fichier est bien présent
sudo cat /etc/nginx/sites-available/mokilievent.com.conf

# 2. Vérifier la syntaxe nginx
sudo nginx -t

# 3. Si la syntaxe est correcte, recharger nginx
sudo systemctl reload nginx

# 4. Tester
curl -I -H "Host: mokilievent.com" http://localhost
```

## Vérifications finales

```bash
# Vérifier que nginx fonctionne
sudo systemctl status nginx

# Vérifier que PHP-FPM fonctionne
sudo systemctl status php8.4-fpm

# Tester avec curl
curl -I http://localhost
curl -I -H "Host: mokilievent.com" http://localhost

# Vérifier les logs en cas d'erreur
sudo tail -20 /var/log/nginx/mokilievent.com.error.log
```

## Si vous utilisez SCP depuis Windows

```powershell
# Dans PowerShell, naviguez vers le dossier du projet
cd C:\Users\ronel\OneDrive\Documents\html_public\html_public

# Transférez le fichier
scp nginx/mokilievent.com.conf.final cursor@VOTRE_IP_VPS:/tmp/

# Puis sur le serveur
ssh cursor@VOTRE_IP_VPS
sudo cp /tmp/mokilievent.com.conf.final /etc/nginx/sites-available/mokilievent.com.conf
sudo nginx -t
sudo systemctl reload nginx
```

## Résumé des changements

✅ Configuration complète avec tous les paramètres FastCGI  
✅ Socket PHP 8.4 correct (`php8.4-fpm.sock`)  
✅ Headers Cloudflare configurés  
✅ Buffers FastCGI optimisés pour Laravel  
✅ Security headers ajoutés  
✅ Gzip compression activée  
✅ Cache pour les assets statiques  

