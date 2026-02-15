# 🔧 Correction : Échec de vérification Certbot

## Problème
Let's Encrypt ne peut pas accéder à `/.well-known/acme-challenge/` pour vérifier le domaine.

## Causes possibles

1. **DNS pointe encore vers Cloudflare** (même en DNS only)
2. **Port 80 bloqué** par un firewall
3. **Configuration nginx bloque `.well-known`**
4. **Le domaine n'est pas accessible depuis l'extérieur**

## Solutions

### 1. Vérifier que le DNS pointe vers votre VPS

```bash
# Vérifier où pointe le DNS
nslookup mokilievent.com
nslookup www.mokilievent.com

# Devrait retourner l'IP de votre VPS, pas les IPs Cloudflare
```

### 2. Vérifier que le port 80 est accessible

```bash
# Vérifier que nginx écoute sur le port 80
sudo ss -tlnp | grep :80

# Vérifier le firewall
sudo ufw status
# Si le port 80 est bloqué, l'ouvrir :
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 3. Vérifier la configuration nginx pour .well-known

```bash
# Vérifier que .well-known n'est pas bloqué
sudo grep -i "well-known" /etc/nginx/sites-available/mokilievent.com.conf
```

La configuration doit permettre l'accès à `.well-known` :

```nginx
# Bloquer l'accès aux fichiers cachés (sauf .well-known pour Let's Encrypt)
location ~ /\.(?!well-known) {
    deny all;
}
```

### 4. Tester l'accès manuellement

```bash
# Créer un fichier de test
sudo mkdir -p /var/www/mokilievent/evenementiel/public/.well-known/acme-challenge
echo "test" | sudo tee /var/www/mokilievent/evenementiel/public/.well-known/acme-challenge/test.txt

# Tester l'accès
curl http://mokilievent.com/.well-known/acme-challenge/test.txt
```

### 5. Si le DNS pointe encore vers Cloudflare

Si `nslookup` retourne les IPs Cloudflare, vous devez :
- Soit attendre que le DNS se propage (peut prendre jusqu'à 48h)
- Soit utiliser le mode "DNS challenge" au lieu de "HTTP challenge"

### 6. Utiliser le DNS challenge (si HTTP ne fonctionne pas)

```bash
sudo certbot certonly --manual --preferred-challenges dns -d mokilievent.com -d www.mokilievent.com
```

Cela vous demandera d'ajouter un enregistrement TXT dans votre DNS.

