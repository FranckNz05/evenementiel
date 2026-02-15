# 🔧 Correction des permissions Git

## Problème
`error: cannot open '.git/FETCH_HEAD': Permission denied`

## Solution

### Option 1 : Corriger les permissions (recommandé)

```bash
# Vérifier le propriétaire actuel
ls -la .git/FETCH_HEAD

# Corriger les permissions pour l'utilisateur cursor
sudo chown -R cursor:cursor /var/www/mokilievent/evenementiel/.git
sudo chown -R cursor:cursor /var/www/mokilievent/evenementiel

# Vérifier
ls -la .git/ | head -5
```

### Option 2 : Utiliser sudo (temporaire)

```bash
sudo git pull
```

Mais cela peut créer d'autres problèmes de permissions.

### Option 3 : Copier directement le fichier

Si git ne fonctionne pas, vous pouvez copier directement le fichier :

```bash
# Créer le fichier directement
sudo nano /etc/nginx/sites-available/mokilievent.com.conf
```

Puis copier-coller le contenu de `nginx/mokilievent.com.conf.final`.

## Après correction des permissions

```bash
# Vérifier que vous pouvez faire git pull
git pull

# Si ça fonctionne, copier le fichier
sudo cp nginx/mokilievent.com.conf.final /etc/nginx/sites-available/mokilievent.com.conf

# Vérifier la syntaxe
sudo nginx -t

# Recharger
sudo systemctl reload nginx
```

