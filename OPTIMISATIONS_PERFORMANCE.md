# Optimisations Performance Mobile - MokiliEvent

## Objectif
Passer la performance mobile de ~59 à 80+ (Lighthouse) en optimisant pour mobile 4G lente (Afrique).

## Métriques cibles
- **FCP** : < 2s (actuellement ~5s)
- **LCP** : < 2.5s (actuellement ~10s)
- **Performance Score** : > 80 (actuellement ~59)
- **TBT** : Maintenir le bon score actuel
- **CLS** : Maintenir 0

---

## ✅ Optimisations Implémentées

### 1. Image Héro (LCP Element)
**Fichier modifié** : `resources/views/home.blade.php`

- ✅ Ajout de `<picture>` avec support WebP/AVIF et fallback JPG
- ✅ Ajout de `fetchpriority="high"` sur l'image héro
- ✅ Ajout de `loading="eager"` (pas de lazy loading pour l'héro)
- ✅ Dimensions explicites (`width="1200" height="500"`)
- ✅ Structure optimisée avec z-index pour overlay

**Action requise** : Convertir `public/images/foule-humains-copie.jpg` en WebP et AVIF
- Largeur max : 1200px
- Poids cible : < 150 Ko
- Formats : WebP (fallback) et AVIF (prioritaire)

**Commande de conversion** :
```bash
# WebP (qualité 80)
cwebp -q 80 -resize 1200 0 public/images/foule-humains-copie.jpg -o public/images/foule-humains-copie.webp

# AVIF (qualité 50, meilleure compression)
avifenc --min 0 --max 50 --speed 4 -d 8 public/images/foule-humains-copie.jpg public/images/foule-humains-copie.avif
```

---

### 2. Lazy Loading Images
**Fichiers modifiés** :
- `resources/views/components/event-card.blade.php`
- `resources/views/home.blade.php`

- ✅ Ajout de `loading="lazy"` sur toutes les images sauf l'héro
- ✅ Ajout de `decoding="async"` pour le décodage asynchrone
- ✅ Dimensions explicites (`width` et `height`) pour éviter le layout shift

**Images optimisées** :
- Images des événements (event-card)
- Images des organisateurs
- Images des catégories

---

### 3. CSS Critique (Above the Fold)
**Fichier modifié** : `resources/views/layouts/app.blade.php`

- ✅ CSS critique inline dans le `<head>` pour le rendu initial
- ✅ Chargement différé des CSS non critiques avec `preload` + `onload`
- ✅ Support noscript pour les navigateurs sans JavaScript

**CSS chargés en différé** :
- Bootstrap CSS
- Custom CSS
- Theme CSS
- Playfair Display (police secondaire)
- Font Awesome
- OwlCarousel CSS

---

### 4. Scripts JavaScript
**Fichiers modifiés** :
- `resources/views/layouts/app.blade.php`
- `resources/views/home.blade.php`

- ✅ Tous les scripts en `defer` (jQuery, Bootstrap, custom.js, ajax-actions.js)
- ✅ OwlCarousel en `defer`
- ✅ Scripts non critiques chargés après le rendu initial

**Scripts optimisés** :
- jQuery (defer)
- Bootstrap (defer)
- Custom.js (defer)
- Ajax-actions.js (defer)
- OwlCarousel (defer)

---

### 5. Polices (Fonts)
**Fichier modifié** : `resources/views/layouts/app.blade.php`

- ✅ Preload de la police principale (Inter)
- ✅ Chargement différé avec `preload` + `onload`
- ✅ Playfair Display (police secondaire) chargée en différé
- ✅ Support noscript

**Polices optimisées** :
- Inter (400, 500, 600, 700) - Police principale
- Playfair Display (700) - Police secondaire (chargement différé)

---

### 6. Headers Cache (Assets Statiques)
**Fichiers créés/modifiés** :
- `app/Http/Middleware/CacheStaticAssets.php` (nouveau)
- `app/Http/Kernel.php` (ajout du middleware)
- `public/.htaccess` (optimisations Apache)

**Optimisations** :
- ✅ Cache-Control : `public, max-age=31536000, immutable` (1 an)
- ✅ Headers Expires pour les assets statiques
- ✅ Compression Gzip activée
- ✅ Support Brotli si disponible
- ✅ Vary: Accept-Encoding pour la compression

**Assets mis en cache** :
- Images (JPG, PNG, WebP, AVIF, SVG, ICO) : 1 an
- CSS : 1 an
- JavaScript : 1 an
- Fonts (WOFF, WOFF2, TTF, OTF) : 1 an

---

## 📋 Actions Requises (Manuelles)

### 1. Convertir l'image héro
```bash
# Installer les outils si nécessaire
# Ubuntu/Debian:
sudo apt-get install webp libavif-bin

# macOS:
brew install webp libavif

# Conversion WebP
cwebp -q 80 -resize 1200 0 public/images/foule-humains-copie.jpg -o public/images/foule-humains-copie.webp

# Conversion AVIF
avifenc --min 0 --max 50 --speed 4 -d 8 public/images/foule-humains-copie.jpg public/images/foule-humains-copie.avif

# Vérifier le poids
ls -lh public/images/foule-humains-copie.*
```

### 2. Configuration Serveur (OPcache)
Ajouter dans `php.ini` ou `.user.ini` :
```ini
[opcache]
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 3. Vérifier HTTP/2
- Activer HTTP/2 sur le serveur web (Nginx/Apache)
- Vérifier avec : `curl -I --http2 https://mokilievent.com`

### 4. Vérifier TTFB
- TTFB cible : < 500ms
- Vérifier avec Chrome DevTools ou PageSpeed Insights
- Optimiser les requêtes DB si nécessaire

---

## 📊 Résultats Attendus

### Avant
- **Performance** : ~59
- **FCP** : ~5s
- **LCP** : ~10s
- **Poids total** : ~3-5 MB

### Après (estimé)
- **Performance** : 80-85
- **FCP** : < 2s
- **LCP** : < 2.5s
- **Poids total** : ~1-2 MB (avec compression)

---

## 🔍 Fichiers Modifiés

1. `resources/views/home.blade.php` - Optimisation héro et images
2. `resources/views/components/event-card.blade.php` - Lazy loading images
3. `resources/views/layouts/app.blade.php` - CSS critique, fonts, scripts
4. `app/Http/Middleware/CacheStaticAssets.php` - Nouveau middleware cache
5. `app/Http/Kernel.php` - Ajout middleware
6. `public/.htaccess` - Compression et cache Apache

---

## 🧪 Tests à Effectuer

1. **Lighthouse Mobile** : Vérifier le score performance
2. **PageSpeed Insights** : Tester sur mobile 4G lente
3. **Chrome DevTools** : Vérifier FCP, LCP, TBT
4. **Network Tab** : Vérifier le poids total et les temps de chargement
5. **Cache** : Vérifier que les assets sont bien mis en cache

---

## 📝 Notes Importantes

- Les images WebP/AVIF doivent être créées manuellement
- OPcache doit être activé sur le serveur de production
- HTTP/2 doit être activé sur le serveur web
- Tester sur un réseau 4G lent pour valider les optimisations
- Surveiller les Core Web Vitals après déploiement

---

## 🚀 Prochaines Étapes (Optionnelles)

1. **CDN** : Mettre les assets statiques sur un CDN
2. **Service Worker** : Cache des assets en local
3. **Image CDN** : Utiliser un service comme Cloudinary pour l'optimisation automatique
4. **Minification** : Minifier le CSS/JS personnalisé
5. **Tree-shaking** : Supprimer le CSS/JS non utilisé

---

**Date** : {{ date('Y-m-d') }}
**Version** : 1.0

