# 👨‍💻 Guide de Sécurité pour les Développeurs

Ce guide présente les bonnes pratiques de sécurité à suivre lors du développement sur cette plateforme.

---

## 📖 Table des Matières

1. [Affichage de Contenu Utilisateur](#affichage-de-contenu-utilisateur)
2. [Validation des Formulaires](#validation-des-formulaires)
3. [Requêtes Base de Données](#requêtes-base-de-données)
4. [Upload de Fichiers](#upload-de-fichiers)
5. [Authentification et Autorisation](#authentification-et-autorisation)
6. [Gestion des Sessions](#gestion-des-sessions)

---

## 🎨 Affichage de Contenu Utilisateur

### Règle d'Or : TOUJOURS échapper ou sanitiser le contenu utilisateur

### Option 1 : Contenu Texte Simple (Recommandé par défaut)

```blade
<!-- ✅ BON : Échappe automatiquement le HTML -->
<h1>{{ $user->name }}</h1>
<p>{{ $event->title }}</p>
```

### Option 2 : Contenu HTML Enrichi (Blogs, Descriptions)

```blade
<!-- ✅ BON : Sanitise le HTML pour supprimer les scripts -->
<div class="content">
    {!! sanitize_html($blog->content) !!}
</div>

<!-- ❌ DANGEREUX : N'utilisez JAMAIS ceci avec du contenu utilisateur -->
<div class="content">
    {!! $blog->content !!}
</div>
```

### Option 3 : Contenu Iframe (Google Maps, YouTube)

```blade
<!-- ✅ BON : La fonction sanitize_html() autorise uniquement les iframes sûrs -->
<div class="map-container">
    {!! sanitize_html($event->adresse_map) !!}
</div>
```

### Tableau de Décision

| Type de Contenu | Syntaxe Blade | Fonction Helper |
|-----------------|---------------|-----------------|
| Nom, Titre, Email | `{{ $var }}` | - |
| Description courte | `{{ $var }}` | - |
| HTML riche (blog) | `{!! sanitize_html($var) !!}` | ✅ |
| Iframe (maps) | `{!! sanitize_html($var) !!}` | ✅ |
| URL | `{{ secure_url($var) }}` | `secure_url()` |

---

## 📝 Validation des Formulaires

### Toujours utiliser des FormRequest pour les opérations critiques

#### Créer un FormRequest

```bash
php artisan make:request StoreProductRequest
```

#### Exemple de FormRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-]+$/' // Seulement lettres, chiffres, espaces, tirets
            ],
            'description' => [
                'required',
                'string',
                'max:5000'
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB
                'dimensions:min_width=100,min_height=100'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Le nom contient des caractères non autorisés.',
            // ... autres messages
        ];
    }

    protected function prepareForValidation(): void
    {
        // Nettoyer les données avant validation
        if ($this->has('name')) {
            $this->merge([
                'name' => strip_tags($this->name)
            ]);
        }
    }
}
```

#### Utiliser le FormRequest dans le Contrôleur

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    // ✅ BON : Utilise FormRequest
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        
        // Les données sont déjà validées et sécurisées
        $product = Product::create($validated);
        
        return redirect()->route('products.show', $product);
    }
    
    // ❌ ÉVITER : Validation inline (pour les cas simples uniquement)
    public function storeSimple(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        // ...
    }
}
```

---

## 🗄️ Requêtes Base de Données

### Utiliser TOUJOURS Eloquent ou Query Builder

```php
// ✅ BON : Eloquent ORM
$user = User::where('email', $email)->first();
$events = Event::where('ville', $city)->get();

// ✅ BON : Query Builder avec paramètres liés
$results = DB::table('users')
    ->where('email', $email)
    ->where('active', true)
    ->get();

// ✅ BON : Requêtes avec jointures
$events = DB::table('events')
    ->join('categories', 'events.category_id', '=', 'categories.id')
    ->where('events.status', 'published')
    ->select('events.*', 'categories.name')
    ->get();

// ⚠️ ATTENTION : DB::raw() - Utilisez uniquement avec des données sûres
$users = User::select('name', DB::raw('COUNT(*) as total'))
    ->groupBy('name')
    ->get();

// ❌ DANGEREUX : JAMAIS de concaténation avec DB::raw()
$results = DB::select("SELECT * FROM users WHERE email = '$email'"); // ❌
$results = DB::raw("DELETE FROM users WHERE id = " . $id); // ❌
```

### Protection pour les recherches LIKE

```php
use App\Helpers\SecurityHelper;

// ✅ BON : Échapper les caractères spéciaux de LIKE
$search = SecurityHelper::escapeLike($request->search);
$events = Event::where('title', 'LIKE', "%{$search}%")->get();

// ❌ MAUVAIS : Injection possible via %, _
$events = Event::where('title', 'LIKE', "%{$request->search}%")->get();
```

---

## 📤 Upload de Fichiers

### Validation stricte des uploads

```php
use App\Helpers\SecurityHelper;

public function uploadImage(Request $request)
{
    // ✅ BON : Validation complète
    $validated = $request->validate([
        'image' => [
            'required',
            'file',
            'mimes:jpeg,png,jpg,webp',
            'max:5120', // 5MB
            'dimensions:min_width=300,min_height=300,max_width=5000,max_height=5000'
        ]
    ]);
    
    // Nettoyer le nom de fichier
    $filename = SecurityHelper::sanitizeFilename($request->file('image')->getClientOriginalName());
    
    // Stocker avec un nom unique
    $path = $request->file('image')->storeAs(
        'images',
        time() . '_' . $filename,
        'public'
    );
    
    return $path;
}

// ❌ DANGEREUX : Pas de validation
public function uploadDangerous(Request $request)
{
    $path = $request->file('image')->store('images'); // ❌
    return $path;
}
```

### Types MIME autorisés par catégorie

| Catégorie | Extensions | MIME Types |
|-----------|-----------|------------|
| Images | jpg, jpeg, png, webp | image/jpeg, image/png, image/webp |
| Documents | pdf | application/pdf |
| Archives | zip | application/zip |

---

## 🔐 Authentification et Autorisation

### Vérifier TOUJOURS les autorisations

```php
// ✅ BON : Utiliser les Policies
public function update(Request $request, Event $event)
{
    $this->authorize('update', $event);
    
    // L'utilisateur est autorisé
    $event->update($request->validated());
}

// ✅ BON : Vérifier manuellement
public function delete(Event $event)
{
    if (auth()->id() !== $event->user_id && !auth()->user()->hasRole('Administrateur')) {
        abort(403, 'Non autorisé');
    }
    
    $event->delete();
}

// ❌ DANGEREUX : Pas de vérification
public function deleteUnsafe(Event $event)
{
    $event->delete(); // N'importe qui peut supprimer !
}
```

### Utiliser les middlewares d'authentification

```php
// Dans le contrôleur
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
    $this->middleware('role:Administrateur')->only(['destroy', 'approve']);
}

// Ou dans les routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('events', EventController::class);
});
```

---

## 🔑 Gestion des Sessions

### Régénérer l'ID de session après login

```php
// ✅ BON : Régénération de session
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // Important !
        
        return redirect()->intended('dashboard');
    }
    
    return back()->withErrors(['email' => 'Identifiants invalides']);
}
```

### Limiter les tentatives de connexion

```php
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

public function login(Request $request)
{
    $key = 'login-attempt:' . $request->ip();
    
    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes."
        ]);
    }
    
    if (Auth::attempt($request->only('email', 'password'))) {
        RateLimiter::clear($key);
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }
    
    RateLimiter::hit($key, 300); // 5 minutes de blocage
    
    return back()->withErrors(['email' => 'Identifiants invalides']);
}
```

---

## 🛡️ Helpers de Sécurité Disponibles

| Fonction | Usage | Exemple |
|----------|-------|---------|
| `sanitize_html($html)` | Nettoie HTML | `{!! sanitize_html($blog->content) !!}` |
| `secure_output($text)` | Échappe HTML | `{{ secure_output($text) }}` |
| `SecurityHelper::escapeLike($text)` | Échappe LIKE | `where('title', 'LIKE', "%{$safe}%")` |
| `SecurityHelper::sanitizeUrl($url)` | Valide URL | `$url = SecurityHelper::sanitizeUrl($input)` |
| `SecurityHelper::sanitizeFilename($name)` | Nettoie nom fichier | `$safe = SecurityHelper::sanitizeFilename($name)` |

---

## ⚠️ Checklist de Sécurité pour Chaque Fonctionnalité

Avant de commit votre code, vérifiez :

- [ ] ✅ Toutes les entrées utilisateur sont validées
- [ ] ✅ Les données affichées utilisent `{{ }}` ou `sanitize_html()`
- [ ] ✅ Pas de `DB::raw()` avec des données utilisateur
- [ ] ✅ Les uploads de fichiers sont validés (type, taille, dimensions)
- [ ] ✅ Les autorisations sont vérifiées (Policies ou middleware)
- [ ] ✅ Les formulaires contiennent `@csrf`
- [ ] ✅ Pas de données sensibles dans les logs ou messages d'erreur
- [ ] ✅ Les routes API ont un rate limiting

---

## 🚨 Signaux d'Alarme (Code Smell Sécurité)

Si vous voyez ces patterns dans le code, corrigez-les immédiatement :

```php
// 🚨 DANGER : Affichage non échappé
{!! $user_input !!}

// 🚨 DANGER : SQL brut avec concaténation
DB::select("SELECT * FROM users WHERE id = " . $id);

// 🚨 DANGER : Pas de validation
$user->update($request->all());

// 🚨 DANGER : Upload sans validation
$request->file('doc')->store('documents');

// 🚨 DANGER : Pas d'autorisation
public function delete(Event $event) {
    $event->delete();
}

// 🚨 DANGER : eval() ou exec()
eval($code); // Ne JAMAIS utiliser !
```

---

## 📚 Ressources Supplémentaires

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)

---

**🔒 La sécurité est l'affaire de tous ! Codez avec prudence.**

