# 🔍 Diagnostic complet : Redirection n8n + Erreur 403

## Situation
- **DNS only (sans proxy Cloudflare)** → Redirection vers `/signin?redirect=%252F` (n8n)
- **Proxy Cloudflare activé** → Erreur 403

Cela signifie que le problème est sur le VPS, pas dans Cloudflare.

## Commandes de diagnostic à exécuter

### 1. Vérifier TOUS les processus qui écoutent sur les ports 80 et 443

```bash
sudo ss -tlnp | grep -E ':(80|443)'
```

### 2. Vérifier si n8n tourne en processus (pas comme service)

```bash
ps aux | grep -i n8n | grep -v grep
```

### 3. Vérifier tous les processus Node.js (n8n est souvent en Node.js)

```bash
ps aux | grep node | grep -v grep
```

### 4. Vérifier les configurations nginx actives

```bash
ls -la /etc/nginx/sites-enabled/
```

### 5. Voir la configuration complète pour mokilievent.com

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

### 6. Vérifier s'il y a une configuration par défaut qui intercepte

```bash
sudo cat /etc/nginx/sites-available/default
```

### 7. Chercher toutes les références à n8n dans nginx

```bash
sudo grep -r "n8n" /etc/nginx/
```

### 8. Chercher des proxy_pass vers d'autres ports

```bash
sudo grep -r "proxy_pass" /etc/nginx/sites-enabled/
```

### 9. Vérifier les logs nginx pour l'erreur 403

```bash
sudo tail -50 /var/log/nginx/mokilievent.com.error.log
```

### 10. Tester avec l'IP directe du VPS (bypass DNS)

```bash
# D'abord, trouvez l'IP du VPS
hostname -I
# Puis testez depuis votre machine locale avec cette IP
```

### 11. Vérifier si n8n écoute sur un autre port et est proxy par nginx

```bash
sudo netstat -tlnp | grep -E ':(5678|3000|8080|8000)'
```

## Hypothèses

1. **n8n tourne en processus** (pas comme service systemd) et écoute sur le port 80
2. **Une configuration nginx** redirige vers n8n
3. **La configuration par défaut** intercepte avant mokilievent.com
4. **Un autre service** (Docker, PM2, etc.) fait tourner n8n

