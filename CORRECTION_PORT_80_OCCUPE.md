# 🔧 Correction : Port 80 déjà utilisé

## Problème
Nginx ne peut pas redémarrer car le port 80 est déjà utilisé.

## Diagnostic

### 1. Vérifier ce qui écoute sur le port 80

```bash
sudo ss -tlnp | grep :80
# ou
sudo netstat -tlnp | grep :80
```

### 2. Vérifier les configurations nginx actives

```bash
ls -la /etc/nginx/sites-enabled/
```

### 3. Vérifier s'il y a plusieurs configurations qui écoutent sur le port 80

```bash
sudo grep -r "listen 80" /etc/nginx/sites-enabled/
```

## Solutions

### Solution 1 : Vérifier la configuration nginx

Il peut y avoir plusieurs blocs `server` qui écoutent sur le port 80. Vérifiez :

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf | grep -A 2 "listen 80"
```

### Solution 2 : Arrêter nginx temporairement

```bash
sudo systemctl stop nginx
sudo certbot --nginx -d mokilievent.com -d www.mokilievent.com
```

### Solution 3 : Vérifier s'il y a d'autres processus

```bash
# Voir tous les processus qui écoutent sur le port 80
sudo lsof -i :80
```

### Solution 4 : Vérifier la configuration par défaut

Si la configuration `default` est toujours active et écoute sur le port 80, elle peut causer un conflit :

```bash
# Vérifier si default est actif
ls -la /etc/nginx/sites-enabled/ | grep default

# Si oui, vérifier son contenu
sudo cat /etc/nginx/sites-available/default | grep "listen 80"
```

