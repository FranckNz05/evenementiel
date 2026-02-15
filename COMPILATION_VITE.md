# 🔧 Compilation des assets Vite

## Problème
Le manifest Vite n'est pas trouvé car les assets n'ont pas été compilés.

## Solution 1 : Compiler les assets (Production)

```bash
cd /var/www/mokilievent/evenementiel

# Installer les dépendances npm si nécessaire
npm install

# Compiler les assets pour la production
npm run build
```

## Solution 2 : Mode développement (si vous avez Node.js)

```bash
# Démarrer Vite en mode watch (développement)
npm run dev
```

**Note :** Cette commande doit rester active. Pour la production, utilisez `npm run build`.

## Solution 3 : Désactiver temporairement Vite (si vous n'avez pas Node.js)

Si vous n'avez pas Node.js installé sur le serveur, vous pouvez temporairement modifier les vues pour ne pas utiliser Vite.

Modifier `resources/views/layouts/app.blade.php` :
- Remplacer `@vite(['resources/js/app.js', 'resources/css/app.css'])` 
- Par des liens directs vers les fichiers CSS/JS compilés ou utiliser des CDN

## Vérification

Après compilation, vérifiez que le fichier existe :
```bash
ls -la /var/www/mokilievent/evenementiel/public/build/manifest.json
```

## Installation Node.js sur le serveur (si nécessaire)

```bash
# Installer Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Vérifier l'installation
node -v
npm -v
```

