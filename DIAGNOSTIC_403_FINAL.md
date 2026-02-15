# 🔍 Diagnostic final : Erreur 403

## Vérifications à faire

### 1. Tester depuis le serveur (bypass Cloudflare)

```bash
# Test direct depuis le serveur
curl -I -H "Host: mokilievent.com" http://localhost

# Test avec l'IP directe (si vous connaissez l'IP du VPS)
curl -I http://IP_DU_VPS
```

### 2. Vérifier les logs nginx en temps réel

```bash
# Voir les requêtes en temps réel
sudo tail -f /var/log/nginx/mokilievent.com.access.log

# Dans un autre terminal, faire une requête depuis votre navigateur
# Puis regarder ce qui apparaît dans les logs
```

### 3. Vérifier si c'est Cloudflare qui bloque

Si vous testez depuis votre navigateur avec `https://mokilievent.com`, l'erreur 403 peut venir de Cloudflare, pas du serveur.

**Vérifications Cloudflare :**
- Allez dans Cloudflare Dashboard
- Rules → WAF (Web Application Firewall)
- Vérifiez s'il y a des règles qui bloquent
- Security → WAF → Voir les événements récents

### 4. Vérifier les permissions une dernière fois

```bash
# Vérifier les permissions du dossier public
ls -la /var/www/mokilievent/evenementiel/public/

# Vérifier que nginx peut lire
sudo -u www-data ls -la /var/www/mokilievent/evenementiel/public/index.php

# Vérifier les permissions du dossier parent
ls -ld /var/www/mokilievent/evenementiel/
ls -ld /var/www/mokilievent/
```

### 5. Vérifier la configuration nginx

```bash
# Vérifier qu'il n'y a pas de "deny all" qui bloque
sudo grep -i "deny\|allow" /etc/nginx/sites-available/mokilievent.com.conf
```

### 6. Tester avec curl en mode verbeux

```bash
# Voir exactement ce qui se passe
curl -v -H "Host: mokilievent.com" http://localhost
```

## Solutions selon la source

### Si l'erreur vient de Cloudflare

1. **Désactiver temporairement le WAF** :
   - Cloudflare Dashboard → Security → WAF
   - Désactiver temporairement pour tester

2. **Vérifier les Page Rules** :
   - Rules → Page Rules
   - Chercher des règles qui bloquent

3. **Vérifier le Security Level** :
   - Security → Settings → Security Level
   - Mettre temporairement sur "Essentially Off" pour tester

### Si l'erreur vient du serveur

```bash
# Vérifier les permissions complètes
sudo chown -R www-data:www-data /var/www/mokilievent/evenementiel
sudo find /var/www/mokilievent/evenementiel -type d -exec chmod 755 {} \;
sudo find /var/www/mokilievent/evenementiel -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/mokilievent/evenementiel/storage
sudo chmod -R 775 /var/www/mokilievent/evenementiel/bootstrap/cache
```

