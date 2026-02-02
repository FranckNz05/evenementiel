# 🚀 Guide d'Installation des Correctifs de Sécurité

## Étapes d'Installation

### 1️⃣ Installer la dépendance HTMLPurifier

```bash
composer update
```

Cette commande va installer `ezyang/htmlpurifier` qui est nécessaire pour la sanitisation du HTML.

### 2️⃣ Créer le répertoire de cache

```bash
# Windows PowerShell
New-Item -ItemType Directory -Force -Path "storage\app\purifier"

# Ou si vous utilisez Git Bash / WSL
mkdir -p storage/app/purifier
chmod -R 775 storage/app/purifier
```

### 3️⃣ Recharger l'autoloader Composer

```bash
composer dump-autoload
```

Cette commande va charger le nouveau fichier `app/Helpers/helpers.php` qui contient les fonctions de sécurité.

### 4️⃣ Vider tous les caches Laravel

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 5️⃣ (Optionnel) Mettre en cache pour la production

Si vous êtes en production, optimisez les performances :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6️⃣ Redémarrer le serveur

```bash
# Si vous utilisez php artisan serve
# Arrêtez avec Ctrl+C puis relancez
php artisan serve

# Si vous utilisez nginx/apache
# Redémarrez votre serveur web
```

---

## ✅ Vérification de l'Installation

### Test 1 : Vérifier que les helpers sont chargés

Créez un fichier de test temporaire `routes/web.php` :

```php
Route::get('/test-security', function() {
    $html = '<script>alert("XSS")</script><p>Texte légitime</p>';
    return sanitize_html($html);
});
```

Visitez : `http://votre-domaine.test/test-security`

✅ **Résultat attendu** : Seul "Texte légitime" doit s'afficher (sans le script)

⚠️ **N'oubliez pas de supprimer cette route de test après !**

### Test 2 : Vérifier les headers de sécurité

Visitez n'importe quelle page de votre site et inspectez les headers HTTP :

```bash
# Avec curl
curl -I http://votre-domaine.test

# Vous devriez voir :
# X-Frame-Options: SAMEORIGIN
# X-Content-Type-Options: nosniff
# X-XSS-Protection: 1; mode=block
```

### Test 3 : Vérifier le middleware CSRF

Essayez de soumettre un formulaire sans token CSRF.

✅ **Résultat attendu** : Erreur 419 (Page Expired)

---

## 🔧 Résolution des Problèmes

### Erreur : "Class 'HTMLPurifier' not found"

**Solution :**
```bash
composer install
composer dump-autoload
```

### Erreur : "Call to undefined function sanitize_html()"

**Solution :**
```bash
# Vérifiez que helpers.php est dans composer.json
composer dump-autoload -o
php artisan cache:clear
```

### Erreur : "Permission denied" sur storage/app/purifier

**Solution (Linux/Mac) :**
```bash
chmod -R 775 storage/app/purifier
chown -R www-data:www-data storage/app/purifier
```

**Solution (Windows) :**
- Clic droit sur le dossier → Propriétés → Sécurité
- Donner les permissions complètes à votre utilisateur

### Les headers de sécurité ne s'affichent pas

**Solution :**
```bash
php artisan route:clear
php artisan cache:clear
# Redémarrez le serveur
```

---

## 📋 Checklist Finale

Avant de mettre en production, vérifiez :

- [ ] `composer update` exécuté avec succès
- [ ] Dossier `storage/app/purifier` créé
- [ ] `composer dump-autoload` exécuté
- [ ] Tous les caches vidés
- [ ] Serveur redémarré
- [ ] Test XSS passé (script bloqué)
- [ ] Test CSRF passé (erreur 419 sans token)
- [ ] Headers de sécurité présents dans les réponses HTTP
- [ ] Aucune erreur dans `storage/logs/laravel.log`

---

## 🚨 Important pour la Production

1. **Activez HTTPS** sur votre serveur pour que le header `Strict-Transport-Security` fonctionne
2. **Testez tous les formulaires** pour vous assurer qu'ils fonctionnent toujours
3. **Vérifiez les uploads d'images** (validation stricte ajoutée)
4. **Surveillez les logs** pendant les premiers jours

---

## 🎉 C'est Terminé !

Votre plateforme est maintenant protégée contre :
- ✅ Injections SQL
- ✅ Attaques XSS
- ✅ Attaques CSRF
- ✅ Clickjacking
- ✅ MIME Sniffing

**Bon travail ! 🔒**

