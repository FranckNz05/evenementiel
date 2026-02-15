# 🔍 Diagnostic : Redirection vers n8n signin

## Problème
Quand vous tapez `mokilievent.com`, ça redirige vers `https://mokilievent.com/signin?redirect=%252F` (page de connexion n8n).

## Causes possibles

### 1. Configuration Nginx sur le VPS (le plus probable)
Il y a probablement une configuration nginx sur le VPS qui redirige vers n8n.

### 2. Règle de redirection Cloudflare
Une Page Rule ou Transform Rule dans Cloudflare pourrait rediriger vers n8n.

### 3. n8n écoute sur le port 80/443
n8n pourrait être configuré pour écouter directement sur les ports 80/443 et intercepter toutes les requêtes.

## 🔧 Étapes de diagnostic

### Étape 1 : Vérifier la configuration Nginx sur le VPS

Connectez-vous à votre VPS et vérifiez :

```bash
# Voir toutes les configurations nginx actives
sudo ls -la /etc/nginx/sites-enabled/

# Vérifier la configuration principale
sudo cat /etc/nginx/sites-available/mokilievent.com.conf

# Vérifier s'il y a une configuration par défaut qui pourrait intercepter
sudo cat /etc/nginx/sites-available/default

# Chercher des références à n8n
sudo grep -r "n8n" /etc/nginx/
sudo grep -r "signin" /etc/nginx/
```

**Si vous trouvez une redirection vers n8n**, supprimez-la ou modifiez-la.

### Étape 2 : Vérifier les processus qui écoutent sur les ports 80 et 443

```bash
# Voir ce qui écoute sur le port 80
sudo netstat -tlnp | grep :80
# ou
sudo ss -tlnp | grep :80

# Voir ce qui écoute sur le port 443
sudo netstat -tlnp | grep :443
# ou
sudo ss -tlnp | grep :443

# Vérifier si n8n est en cours d'exécution
sudo systemctl status n8n
# ou
ps aux | grep n8n
```

**Si n8n écoute sur le port 80 ou 443**, vous devez soit :
- Arrêter n8n : `sudo systemctl stop n8n`
- Configurer n8n pour écouter sur un autre port (ex: 5678)
- Configurer nginx pour proxy vers n8n uniquement sur un sous-domaine (ex: `n8n.mokilievent.com`)

### Étape 3 : Vérifier les règles Cloudflare

1. Connectez-vous à votre dashboard Cloudflare
2. Allez dans **Rules** → **Transform Rules** ou **Page Rules**
3. Cherchez des règles qui pourraient rediriger vers `/signin` ou n8n
4. Vérifiez aussi **Redirect Rules** dans **Rules**

**Si vous trouvez une règle**, supprimez-la ou modifiez-la.

### Étape 4 : Vérifier les logs Nginx

```bash
# Logs d'accès
sudo tail -n 100 /var/log/nginx/mokilievent.com.access.log

# Logs d'erreur
sudo tail -n 100 /var/log/nginx/mokilievent.com.error.log

# Logs généraux
sudo tail -n 100 /var/log/nginx/access.log
sudo tail -n 100 /var/log/nginx/error.log
```

Les logs vous diront d'où vient la redirection.

### Étape 5 : Tester directement le serveur (bypass Cloudflare)

```bash
# Depuis le VPS, tester directement
curl -I http://localhost
curl -I http://127.0.0.1

# Depuis votre machine, tester avec l'IP directe du VPS
curl -I http://VOTRE_IP_VPS
```

**Si ça fonctionne avec l'IP directe mais pas avec le domaine**, le problème vient de Cloudflare.
**Si ça ne fonctionne pas même avec l'IP directe**, le problème vient du VPS.

## 🛠️ Solutions selon la cause

### Solution 1 : Si n8n écoute sur le port 80/443

**Option A : Arrêter n8n** (si vous n'en avez pas besoin)
```bash
sudo systemctl stop n8n
sudo systemctl disable n8n
```

**Option B : Configurer n8n sur un autre port**
1. Modifier la configuration n8n pour écouter sur le port 5678
2. Configurer nginx pour proxy vers n8n uniquement sur un sous-domaine :
```nginx
server {
    listen 80;
    server_name n8n.mokilievent.com;
    
    location / {
        proxy_pass http://localhost:5678;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Solution 2 : Si une configuration nginx redirige vers n8n

Modifiez ou supprimez la configuration problématique dans `/etc/nginx/sites-available/`.

Assurez-vous que la configuration pour `mokilievent.com` pointe bien vers Laravel :
```nginx
server {
    listen 80;
    server_name mokilievent.com www.mokilievent.com;
    
    root /var/www/mokilievent/evenementiel/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # ... reste de la configuration
}
```

Puis rechargez nginx :
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Solution 3 : Si c'est une règle Cloudflare

1. Allez dans Cloudflare Dashboard
2. Supprimez ou modifiez la règle problématique
3. Attendez quelques minutes pour que les changements prennent effet

### Solution 4 : Vérifier l'ordre des configurations nginx

Si plusieurs configurations nginx sont actives, l'ordre peut être important. Vérifiez :

```bash
# Voir l'ordre des configurations
ls -la /etc/nginx/sites-enabled/

# La configuration par défaut peut intercepter avant mokilievent.com
# Si c'est le cas, désactivez-la ou modifiez-la
sudo rm /etc/nginx/sites-enabled/default
```

## ✅ Checklist de vérification

- [ ] Vérifié les configurations nginx sur le VPS
- [ ] Vérifié les processus qui écoutent sur les ports 80/443
- [ ] Vérifié les règles Cloudflare (Page Rules, Transform Rules, Redirect Rules)
- [ ] Vérifié les logs nginx
- [ ] Testé directement avec l'IP du VPS (bypass Cloudflare)
- [ ] Vérifié si n8n est en cours d'exécution
- [ ] Vérifié l'ordre des configurations nginx

## 📝 Commandes utiles

```bash
# Vérifier la syntaxe nginx
sudo nginx -t

# Recharger nginx
sudo systemctl reload nginx

# Redémarrer nginx
sudo systemctl restart nginx

# Voir tous les services actifs
sudo systemctl list-units --type=service --state=running

# Chercher n8n dans tous les fichiers de configuration
sudo find /etc -name "*.conf" -exec grep -l "n8n" {} \;
```

## 🆘 Si le problème persiste

1. **Capturez les en-têtes HTTP** :
   ```bash
   curl -v https://mokilievent.com
   ```

2. **Vérifiez les en-têtes de réponse** pour voir où vient la redirection

3. **Contactez le support** avec :
   - Les résultats de `curl -v`
   - Les logs nginx
   - La liste des configurations nginx actives
   - Les règles Cloudflare actives

