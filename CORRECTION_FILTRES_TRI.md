# ✅ Correction des Filtres et du Tri

## 🐛 Problème

Les filtres ne fonctionnaient pas sur `/direct-events?sort=title` car :
1. Le paramètre `sort` n'était pas traité dans le contrôleur
2. Les liens de tri ne conservaient pas les autres filtres actifs

## 🔧 Solution Appliquée

### 1. Ajout du Tri dans le Contrôleur

**Fichier** : `app/Http/Controllers/EventController.php`

```php
->when($request->filled('sort'), function ($query) use ($request) {
    switch ($request->sort) {
        case 'date_asc':
            $query->orderBy('start_date', 'asc');
            break;
        case 'date_desc':
            $query->orderBy('start_date', 'desc');
            break;
        case 'title':
            $query->orderBy('title', 'asc');
            break;
        case 'price_asc':
            $query->orderByRaw('(SELECT MIN(prix) FROM tickets WHERE tickets.event_id = events.id) asc');
            break;
        case 'price_desc':
            $query->orderByRaw('(SELECT MIN(prix) FROM tickets WHERE tickets.event_id = events.id) desc');
            break;
        default:
            // Tri par défaut
            $query->orderByRaw("CASE WHEN CAST(end_date AS DATETIME) >= NOW() THEN 0 ELSE 1 END")
                  ->orderBy('start_date', 'asc');
            break;
    }
}, function ($query) {
    // Tri par défaut si aucun sort n'est spécifié
    $query->orderByRaw("CASE WHEN CAST(end_date AS DATETIME) >= NOW() THEN 0 ELSE 1 END")
          ->orderByRaw('CAST(start_date AS DATETIME) asc');
})
```

### 2. Conservation des Filtres dans les Liens

**Avant** :
```blade
href="{{ url('/direct-events') }}?sort=title{{ request('search') ? '&search='.request('search') : '' }}"
```

**Après** :
```blade
href="{{ url('/direct-events') }}?{{ http_build_query(array_merge(request()->except('sort'), ['sort' => 'title'])) }}"
```

Cette méthode :
- Prend tous les paramètres de l'URL actuelle (`request()`)
- Retire le paramètre `sort` existant (`except('sort')`)
- Ajoute le nouveau paramètre `sort`
- Reconstruit l'URL avec tous les paramètres

## 📋 Options de Tri Disponibles

1. **`date_asc`** : Date croissante (du plus ancien au plus récent)
2. **`date_desc`** : Date décroissante (du plus récent au plus ancien)
3. **`title`** : Par titre alphabétique
4. **`price_asc`** : Prix croissant
5. **`price_desc`** : Prix décroissant

## 🎯 Résultat

✅ Le tri fonctionne maintenant correctement  
✅ Les filtres sont conservés lors du changement de tri  
✅ Tous les paramètres de l'URL sont préservés  
✅ Le tri par défaut affiche les événements actifs en premier

---

**✨ Les filtres et le tri fonctionnent maintenant parfaitement !**

