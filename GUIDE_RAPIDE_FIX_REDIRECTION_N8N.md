# 🚀 Guide Rapide : Corriger la redirection vers n8n

## ⚡ Actions immédiates (5 minutes)

### 1. Connectez-vous à votre VPS

```bash
ssh votre_utilisateur@votre_vps_ip
```

### 2. Exécutez le script de diagnostic

```bash
cd /var/www/mokilievent/evenementiel
sudo bash scripts/diagnostic_redirection_n8n.sh
```

Le script va vous montrer exactement où est le problème.

### 3. Solutions rapides selon le résultat

#### Si n8n est actif sur le port 80/443 :

```bash
# Arrêter n8n temporairement
sudo systemctl stop n8n

# Vérifier que nginx fonctionne maintenant
curl -I http://localhost

# Si ça fonctionne, désactiver n8n au démarrage
sudo systemctl disable n8n
```

#### Si une configuration nginx redirige vers n8n :

```bash
# Chercher la configuration problématique
sudo grep -r "n8n\|signin" /etc/nginx/

# Éditer la configuration
sudo nano /etc/nginx/sites-available/mokilievent.com.conf

# Vérifier la syntaxe
sudo nginx -t

# Recharger nginx
sudo systemctl reload nginx
```

#### Si c'est une règle Cloudflare :

1. Allez sur https://dash.cloudflare.com
2. Sélectionnez votre domaine `mokilievent.com`
3. Allez dans **Rules** → **Page Rules** ou **Transform Rules**
4. Cherchez une règle qui redirige vers `/signin`
5. Supprimez ou modifiez la règle

## 🔍 Vérification rapide

### Test depuis le VPS :
```bash
curl -I http://localhost
```

**Résultat attendu** : `HTTP/1.1 200 OK` ou redirection vers votre page d'accueil Laravel

**Si vous voyez une redirection vers `/signin`** : Le problème est sur le VPS (nginx ou n8n)

### Test depuis votre machine (bypass Cloudflare) :
```bash
curl -I http://VOTRE_IP_VPS
```

**Si ça fonctionne avec l'IP mais pas avec le domaine** : Le problème vient de Cloudflare

## 📋 Checklist rapide

- [ ] Exécuté le script de diagnostic
- [ ] Vérifié si n8n est actif : `sudo systemctl status n8n`
- [ ] Vérifié les configurations nginx : `sudo ls -la /etc/nginx/sites-enabled/`
- [ ] Vérifié les règles Cloudflare dans le dashboard
- [ ] Testé avec `curl -I http://localhost` sur le VPS
- [ ] Rechargé nginx après modifications : `sudo systemctl reload nginx`

## 🆘 Si ça ne fonctionne toujours pas

1. **Vérifiez les logs en temps réel** :
   ```bash
   sudo tail -f /var/log/nginx/mokilievent.com.error.log
   ```

2. **Testez avec curl en mode verbeux** :
   ```bash
   curl -v https://mokilievent.com
   ```

3. **Vérifiez l'ordre des configurations nginx** :
   ```bash
   ls -la /etc/nginx/sites-enabled/
   ```
   La configuration par défaut peut intercepter avant mokilievent.com

4. **Consultez le guide complet** : `DIAGNOSTIC_REDIRECTION_N8N.md`

## 💡 Solution la plus probable

Dans 90% des cas, le problème est que **n8n écoute sur le port 80** et intercepte toutes les requêtes avant nginx.

**Solution** :
```bash
# Arrêter n8n
sudo systemctl stop n8n
sudo systemctl disable n8n

# Vérifier que nginx fonctionne
sudo systemctl status nginx
sudo nginx -t
sudo systemctl reload nginx

# Tester
curl -I http://localhost
```

Si vous avez besoin de n8n, configurez-le pour écouter sur un autre port (ex: 5678) et accédez-y via `n8n.mokilievent.com`.

