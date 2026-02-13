# Configuration Cloudflare pour mokilievent.com

## 📋 Situation actuelle

Votre domaine pointe vers les IPs Cloudflare (proxy activé) :
- `188.114.97.2`
- `188.114.96.2`

## 🔧 Options de configuration

### Option 1 : Désactiver le proxy Cloudflare (Simple)

**Avantages** :
- Configuration simple
- Pas de modification serveur nécessaire
- IP réelle des visiteurs directement visible

**Inconvénients** :
- Pas de protection DDoS Cloudflare
- Pas de CDN Cloudflare

**Étapes** :
1. Cloudflare Dashboard → DNS → Records
2. Pour chaque enregistrement A, cliquer sur l'icône orange (proxy)
3. Passer en gris (DNS only)
4. Attendre 5-10 minutes
5. Vérifier : `nslookup mokilievent.com` devrait retourner `72.61.161.141`

### Option 2 : Garder le proxy Cloudflare (Recommandé pour production)

**Avantages** :
- Protection DDoS
- CDN pour les assets statiques
- SSL automatique
- Analytics Cloudflare

**Configuration requise** :
1. Utiliser la configuration Nginx avec support Cloudflare
2. Configurer SSL dans Cloudflare

## 🚀 Configuration avec proxy Cloudflare activé

### 1. Mettre à jour la configuration Nginx

```bash
# Sur le VPS
cd /var/www/mokilievent/evenementiel
sudo cp nginx/mokilievent-cloudflare.conf /etc/nginx/sites-available/mokilievent.com.conf
sudo nginx -t
sudo systemctl reload nginx
```

### 2. Configurer SSL dans Cloudflare

**SSL/TLS → Overview** :
- Mode : **Flexible** (HTTP entre Cloudflare et serveur, HTTPS entre client et Cloudflare)
- Ou **Full** (HTTPS partout, nécessite certificat SSL sur le serveur)

**Pour SSL Flexible** (le plus simple) :
- Cloudflare gère automatiquement le SSL
- Pas besoin de certificat sur le serveur
- Le serveur reste en HTTP

**Pour SSL Full** (plus sécurisé) :
- Nécessite un certificat SSL sur le serveur
- Installer avec Certbot : `sudo certbot --nginx -d mokilievent.com -d www.mokilievent.com`
- Puis configurer Cloudflare en mode "Full"

### 3. Vérifier la configuration

```bash
# Vérifier que le site répond
curl -I http://mokilievent.com

# Vérifier les logs
sudo tail -f /var/log/nginx/mokilievent.com.error.log
```

## 🔒 Configuration SSL recommandée

### SSL Flexible (Recommandé pour commencer)

**Cloudflare** :
- SSL/TLS → Overview → Mode : **Flexible**

**Serveur** :
- Reste en HTTP (port 80)
- Pas de certificat SSL nécessaire

### SSL Full (Recommandé pour production)

**Serveur** :
```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtenir le certificat
sudo certbot --nginx -d mokilievent.com -d www.mokilievent.com

# Mettre à jour la configuration Nginx pour HTTPS
# Décommenter la section HTTPS dans mokilievent-cloudflare.conf
```

**Cloudflare** :
- SSL/TLS → Overview → Mode : **Full**
- SSL/TLS → Edge Certificates → Always Use HTTPS : **On**

## 📊 Vérification

### Vérifier la résolution DNS

```bash
# Devrait retourner les IPs Cloudflare si proxy activé
nslookup mokilievent.com

# Ou les IPs du VPS si proxy désactivé
nslookup mokilievent.com
```

### Tester l'accès

```bash
# Depuis le VPS
curl -I http://mokilievent.com

# Depuis votre navigateur
https://mokilievent.com
```

## ⚙️ Paramètres Cloudflare recommandés

### Performance
- **Auto Minify** : CSS, JavaScript activés
- **Brotli** : Activé
- **HTTP/2** : Activé
- **HTTP/3 (QUIC)** : Activé

### Sécurité
- **Security Level** : Medium
- **Bot Fight Mode** : Activé
- **Always Use HTTPS** : Activé (si SSL configuré)
- **Automatic HTTPS Rewrites** : Activé

### Caching
- **Caching Level** : Standard
- **Browser Cache TTL** : Respect Existing Headers
- **Purge Cache** : Utiliser si nécessaire après déploiement

## 🔍 Dépannage

### Le site ne charge pas

1. **Vérifier les logs Nginx** :
```bash
sudo tail -f /var/log/nginx/mokilievent.com.error.log
```

2. **Vérifier PHP-FPM** :
```bash
sudo systemctl status php8.4-fpm
```

3. **Vérifier les permissions** :
```bash
ls -la /var/www/mokilievent/evenementiel/public
```

### Erreur 502 Bad Gateway

**Cause** : PHP-FPM n'est pas démarré ou socket incorrect.

**Solution** :
```bash
sudo systemctl start php8.4-fpm
sudo systemctl enable php8.4-fpm
```

### Erreur SSL

**Si SSL Flexible** :
- Vérifier que le mode est bien "Flexible" dans Cloudflare
- Le serveur doit être accessible en HTTP (port 80)

**Si SSL Full** :
- Vérifier que le certificat SSL est installé sur le serveur
- Vérifier que le mode est "Full" dans Cloudflare

## ✅ Checklist

- [ ] DNS configuré dans Cloudflare
- [ ] Proxy Cloudflare activé ou désactivé selon choix
- [ ] Configuration Nginx mise à jour (si proxy activé)
- [ ] SSL configuré dans Cloudflare
- [ ] Site accessible via https://mokilievent.com
- [ ] Logs vérifiés
- [ ] Performance optimisée dans Cloudflare

