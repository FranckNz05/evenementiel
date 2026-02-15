# 🔒 Configuration HTTPS pour Nginx

## Étapes

### 1. Vérifier que les certificats existent

```bash
# Vérifier que les certificats ont été créés
sudo ls -la /etc/letsencrypt/live/mokilievent.com/
```

Vous devriez voir :
- `fullchain.pem`
- `privkey.pem`

### 2. Mettre à jour la configuration nginx

**Option A : Utiliser le fichier que j'ai créé**

```bash
# Sur le serveur, copiez le fichier
sudo nano /etc/nginx/sites-available/mokilievent.com.conf
```

Puis copiez-collez le contenu de `nginx/mokilievent.com.conf.https`

**Option B : Modifier manuellement**

Éditez `/etc/nginx/sites-available/mokilievent.com.conf` et :

1. **Ajoutez la redirection HTTP vers HTTPS au début :**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mokilievent.com www.mokilievent.com;
    return 301 https://$server_name$request_uri;
}
```

2. **Ajoutez la configuration HTTPS après :**
```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name mokilievent.com www.mokilievent.com;
    
    ssl_certificate /etc/letsencrypt/live/mokilievent.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mokilievent.com/privkey.pem;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # ... (reste de la configuration comme avant)
}
```

### 3. Vérifier la syntaxe nginx

```bash
sudo nginx -t
```

### 4. Recharger nginx

```bash
sudo systemctl reload nginx
```

### 5. Tester HTTPS

```bash
curl -I https://mokilievent.com
```

## Si Certbot a déjà modifié la configuration

Certbot peut avoir déjà modifié votre configuration. Vérifiez :

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

Si Certbot a déjà ajouté la configuration HTTPS, vous n'avez peut-être qu'à vérifier qu'elle est correcte.

