# 🔒 Configuration HTTPS finale

## Certificat obtenu ✅

Le certificat SSL a été créé avec succès :
- `/etc/letsencrypt/live/mokilievent.com/fullchain.pem`
- `/etc/letsencrypt/live/mokilievent.com/privkey.pem`

## Configuration nginx HTTPS

### Étape 1 : Vérifier la configuration actuelle

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

### Étape 2 : Modifier la configuration

```bash
sudo nano /etc/nginx/sites-available/mokilievent.com.conf
```

### Étape 3 : Configuration complète

Remplacez tout le contenu par la configuration HTTPS complète (voir le fichier `nginx/mokilievent.com.conf.https`).

### Étape 4 : Vérifier la syntaxe

```bash
sudo nginx -t
```

### Étape 5 : Recharger nginx

```bash
sudo systemctl reload nginx
```

### Étape 6 : Tester HTTPS

```bash
curl -I https://mokilievent.com
```

## Note sur le renouvellement automatique

Le certificat obtenu avec `--manual` ne se renouvelle pas automatiquement. Pour le renouvellement automatique, vous devrez soit :
1. Utiliser l'API Cloudflare avec un hook script
2. Ou renouveler manuellement tous les 3 mois
3. Ou utiliser le challenge HTTP (si Hostinger ne bloque plus)

