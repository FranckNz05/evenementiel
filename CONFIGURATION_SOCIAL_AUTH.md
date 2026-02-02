# Configuration Authentification Sociale (Facebook & Google)

## URLs de callback à configurer

### Pour la production (mokilievent.com)

#### Google OAuth
Dans la [Console Google Cloud](https://console.cloud.google.com/apis/credentials):

**⚠️ IMPORTANT : Les URLs doivent correspondre EXACTEMENT (y compris le port pour localhost)**

**Authorized redirect URIs (ajoutez TOUTES les URLs que vous utilisez) :**
```
https://mokilievent.com/auth/google/callback
http://localhost:8000/auth/google/callback
http://127.0.0.1:8000/auth/google/callback
```

**Authorized JavaScript origins (ajoutez TOUTES les URLs) :**
```
https://mokilievent.com
http://localhost:8000
http://127.0.0.1:8000
```

**Note :** Si vous utilisez un autre port (ex: 8080, 3000), ajoutez-le aussi dans les deux listes ci-dessus.

#### Facebook OAuth
Dans le [Facebook Developers](https://developers.facebook.com/apps/):

**Valid OAuth Redirect URIs:**
```
https://mokilievent.com/auth/facebook/callback
```

**Site URL:**
```
https://mokilievent.com
```

### Pour le développement local (localhost)

**⚠️ IMPORTANT :** Ajoutez ces URLs dans la MÊME configuration OAuth que la production (dans Google Cloud Console, vous pouvez avoir plusieurs URLs autorisées).

#### Google OAuth
**Authorized redirect URIs (à ajouter dans la même configuration) :**
```
http://localhost:8000/auth/google/callback
http://127.0.0.1:8000/auth/google/callback
```

**Authorized JavaScript origins (à ajouter dans la même configuration) :**
```
http://localhost:8000
http://127.0.0.1:8000
```

**Note :** Si vous utilisez `php artisan serve` sans spécifier de port, le port par défaut est 8000. Si vous utilisez un autre port, remplacez `8000` par votre port.

#### Facebook OAuth
**Valid OAuth Redirect URIs:**
```
http://localhost:8000/auth/facebook/callback
```

**Site URL:**
```
http://localhost:8000
```

## Credentials

### Facebook
- **App ID:** 1466916481466764
- **App Secret:** 49d2f9c1cc2ee82d49bd401c28b31006

### Google
- **Client ID:** 96551321667-ka30an7cgreskrleagpee82mli2v3esu.apps.googleusercontent.com
- **Client Secret:** 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
- **Project ID:** mokilievent

## Variables d'environnement (.env)

Ajoutez ces variables dans votre fichier `.env`:

```env
# Facebook OAuth
FACEBOOK_CLIENT_ID=1466916481466764
FACEBOOK_CLIENT_SECRET=49d2f9c1cc2ee82d49bd401c28b31006
FACEBOOK_REDIRECT_URI=https://mokilievent.com/auth/facebook/callback

# Google OAuth
GOOGLE_CLIENT_ID=96551321667-ka30an7cgreskrleagpee82mli2v3esu.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=https://mokilievent.com/auth/google/callback
```

## Installation

1. Installer Laravel Socialite:
```bash
composer require laravel/socialite
```

2. Exécuter la migration:
```bash
php artisan migrate
```

3. Publier la configuration Socialite (optionnel):
```bash
php artisan vendor:publish --provider="Laravel\Socialite\SocialiteServiceProvider"
```

## Routes disponibles

- **Redirection Facebook:** `/auth/facebook` (route: `social.redirect`)
- **Callback Facebook:** `/auth/facebook/callback` (route: `social.callback`)
- **Redirection Google:** `/auth/google` (route: `social.redirect`)
- **Callback Google:** `/auth/google/callback` (route: `social.callback`)

## Fonctionnalités

- ✅ Connexion/Inscription avec Facebook
- ✅ Connexion/Inscription avec Google
- ✅ Liaison automatique si l'email existe déjà
- ✅ Téléchargement automatique de la photo de profil
- ✅ Vérification email automatique pour les comptes sociaux
- ✅ Attribution automatique du rôle "Client"

## Notes importantes

1. **Sécurité:** Les credentials sont déjà configurés par défaut dans `config/services.php`, mais il est recommandé de les mettre dans `.env` pour la production.

2. **URLs de callback:** Assurez-vous que les URLs de callback dans les consoles Google et Facebook correspondent exactement à celles configurées dans votre application.

3. **HTTPS:** En production, utilisez toujours HTTPS pour les URLs de callback.

4. **Permissions Facebook:** 
   - Allez dans votre application Facebook > Settings > Basic
   - Assurez-vous que "Email" est dans la liste des permissions
   - Dans "App Review" > "Permissions and Features", demandez l'accès à "email" si nécessaire
   - **Important:** Ne configurez PAS de scopes dans le code pour Facebook, utilisez uniquement `fields()` pour récupérer les données
   - Les permissions par défaut (public_profile) sont automatiquement incluses

5. **Scopes Google:** L'application demande les scopes `email` et `profile` par défaut.

## Dépannage

### Erreur 400 : redirect_uri_mismatch (Google)

Cette erreur signifie que l'URL de callback utilisée ne correspond pas à celles configurées dans Google Cloud Console.

**Solution :**

1. **Vérifiez l'URL exacte utilisée :**
   - Ouvrez votre navigateur et allez sur `http://localhost:8000/auth/google`
   - Regardez l'URL complète dans la barre d'adresse de Google
   - Notez l'URL de callback exacte (elle devrait être `http://localhost:8000/auth/google/callback`)

2. **Vérifiez dans Google Cloud Console :**
   - Allez sur https://console.cloud.google.com/apis/credentials
   - Cliquez sur votre OAuth 2.0 Client ID
   - Vérifiez que l'URL exacte (avec le bon port) est dans "Authorized redirect URIs"
   - **Important :** L'URL doit correspondre EXACTEMENT, y compris :
     - Le protocole (`http://` ou `https://`)
     - Le domaine (`localhost` ou `127.0.0.1` ou votre domaine)
     - Le port (`:8000` si vous utilisez ce port)
     - Le chemin (`/auth/google/callback`)

3. **Exemples d'URLs valides pour localhost :**
   ```
   http://localhost:8000/auth/google/callback
   http://127.0.0.1:8000/auth/google/callback
   ```

4. **Si vous utilisez un port différent :**
   - Remplacez `8000` par votre port dans les URLs ci-dessus
   - Ajoutez cette URL dans Google Cloud Console

5. **Après modification :**
   - Attendez quelques minutes (les changements peuvent prendre jusqu'à 5 minutes)
   - Videz le cache de votre navigateur
   - Réessayez la connexion

### Erreur Facebook : Invalid Scopes

Si vous voyez "Invalid Scopes: email", cela signifie que vous essayez d'utiliser un scope invalide. Le code a été corrigé pour ne plus utiliser `scopes(['email'])` pour Facebook. Assurez-vous d'avoir la dernière version du code.

