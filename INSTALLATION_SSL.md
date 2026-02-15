# 🔒 Installation SSL avec Let's Encrypt

## Problème
Le site essaie d'accéder en HTTPS mais il n'y a pas de certificat SSL configuré.

## Solution : Installer un certificat SSL avec Certbot

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
- Modifier automatiquement la configuration Nginx pour HTTPS
- Configurer le renouvellement automatique

### 3. Vérifier la configuration HTTPS

```bash
# Vérifier que la configuration HTTPS a été ajoutée
sudo cat /etc/nginx/sites-available/mokilievent.com.conf | grep -A 5 "listen 443"
```

### 4. Tester le renouvellement automatique

```bash
sudo certbot renew --dry-run
```

### 5. Vérifier que HTTPS fonctionne

```bash
curl -I https://mokilievent.com
```

## Alternative : Accéder en HTTP temporairement

Si vous ne pouvez pas installer SSL maintenant, vous pouvez temporairement accéder en HTTP :
- `http://mokilievent.com` (sans le 's')

Mais le navigateur peut toujours rediriger vers HTTPS à cause de HSTS.

## Désactiver HSTS (si nécessaire)

Si HSTS est activé et cause des problèmes, vous pouvez le désactiver dans la configuration Nginx en supprimant la ligne :
```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

Mais il est préférable d'installer le certificat SSL.

