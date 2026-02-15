# 📋 Installation via Git

## Étapes en local (Windows)

```bash
# Ajouter le fichier
git add nginx/mokilievent.com.conf.final

# Commiter
git commit -m "Ajout configuration nginx complète pour mokilievent.com"

# Pousser vers le repo
git push
```

## Étapes sur le serveur

```bash
# Aller dans le dossier du projet
cd /var/www/mokilievent/evenementiel

# Récupérer les changements
git pull

# Copier le fichier vers sites-available
sudo cp nginx/mokilievent.com.conf.final /etc/nginx/sites-available/mokilievent.com.conf

# Vérifier la syntaxe
sudo nginx -t

# Si OK, recharger nginx
sudo systemctl reload nginx

# Tester
curl -I -H "Host: mokilievent.com" http://localhost
```

## Alternative : Renommer directement

Si vous préférez, vous pouvez aussi renommer le fichier avant de le commiter :

```bash
# En local
mv nginx/mokilievent.com.conf.final nginx/mokilievent.com.conf
git add nginx/mokilievent.com.conf
git commit -m "Configuration nginx complète pour mokilievent.com"
git push
```

Puis sur le serveur :
```bash
cd /var/www/mokilievent/evenementiel
git pull
sudo cp nginx/mokilievent.com.conf /etc/nginx/sites-available/mokilievent.com.conf
sudo nginx -t
sudo systemctl reload nginx
```

