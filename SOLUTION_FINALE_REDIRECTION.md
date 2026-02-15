# ✅ Solution finale : Corriger la redirection n8n

## Problèmes identifiés

1. ✅ **n8n tourne** : Processus Node.js (PID 1720)
2. ✅ **Configuration `default` active** : Intercepte toutes les requêtes non matchées
3. ✅ **n8n écoute sur port 5678** via Docker (normal, pas le problème)

## 🔧 Solution : Désactiver la configuration default

La configuration `default` avec `default_server` intercepte toutes les requêtes avant que `mokilievent.com.conf` ne soit évaluée.

### Étape 1 : Voir la configuration n8n complète

```bash
sudo cat /etc/nginx/sites-available/n8n
```

### Étape 2 : Désactiver la configuration default

```bash
# Supprimer le lien symbolique
sudo rm /etc/nginx/sites-enabled/default

# Vérifier que c'est bien supprimé
ls -la /etc/nginx/sites-enabled/
```

### Étape 3 : Vérifier la configuration mokilievent.com.conf

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

### Étape 4 : Vérifier la syntaxe nginx

```bash
sudo nginx -t
```

### Étape 5 : Recharger nginx

```bash
sudo systemctl reload nginx
```

### Étape 6 : Tester

```bash
curl -I http://localhost
curl -I -H "Host: mokilievent.com" http://localhost
```

## 🎯 Explication

Le problème est que nginx évalue les configurations dans un ordre spécifique, et la configuration `default` avec `default_server` est évaluée en premier pour toutes les requêtes qui ne correspondent pas exactement à un `server_name`.

Quand vous accédez à `mokilievent.com` :
- Sans proxy Cloudflare : La config default intercepte et redirige vers n8n (probablement via une règle dans default)
- Avec proxy Cloudflare : La config default intercepte et cause le 403

En désactivant `default`, nginx utilisera uniquement `mokilievent.com.conf` pour les requêtes vers mokilievent.com.

## ⚠️ Si n8n doit rester accessible

Si vous avez besoin d'accéder à n8n, vous pouvez :
1. L'accéder via `automate.zoomhorizoncg.com` (déjà configuré)
2. Ou créer un sous-domaine comme `n8n.mokilievent.com`

Mais la configuration `default` ne doit PAS être active car elle intercepte tout.

