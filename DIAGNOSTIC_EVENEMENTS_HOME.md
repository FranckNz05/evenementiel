# Diagnostic des événements sur la page d'accueil

## Problème
Les événements ne s'affichent pas sur la page d'accueil.

## Conditions requises pour qu'un événement s'affiche :
1. `is_published = true`
2. `is_approved = true`
3. `start_date >= maintenant` (événements futurs uniquement)
4. Avoir au moins une vue (pour le tri par popularité)

## Commandes de diagnostic sur le serveur

### 1. Vérifier le nombre total d'événements
```bash
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan tinker --execute="echo 'Total: ' . DB::table('events')->count();"
```

### 2. Vérifier les événements publiés et approuvés
```bash
sudo -u www-data php artisan tinker --execute="echo 'Publiés et approuvés: ' . DB::table('events')->where('is_published', 1)->where('is_approved', 1)->count();"
```

### 3. Vérifier les événements futurs (start_date >= maintenant)
```bash
sudo -u www-data php artisan tinker --execute="echo 'Futurs: ' . DB::table('events')->where('start_date', '>=', now())->count();"
```

### 4. Vérifier les événements qui répondent à TOUTES les conditions
```bash
sudo -u www-data php artisan tinker --execute="echo 'Éligibles: ' . DB::table('events')->where('is_published', 1)->where('is_approved', 1)->where('start_date', '>=', now())->count();"
```

### 5. Voir les détails des événements éligibles
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
DB::table('events')
    ->where('is_published', 1)
    ->where('is_approved', 1)
    ->where('start_date', '>=', now())
    ->select('id', 'title', 'start_date', 'is_published', 'is_approved')
    ->get();
exit
```

### 6. Vérifier s'il y a des événements avec des dates passées
```bash
sudo -u www-data php artisan tinker --execute="echo 'Passés: ' . DB::table('events')->where('start_date', '<', now())->count();"
```

### 7. Vérifier les événements non publiés
```bash
sudo -u www-data php artisan tinker --execute="echo 'Non publiés: ' . DB::table('events')->where('is_published', 0)->count();"
```

### 8. Vérifier les événements non approuvés
```bash
sudo -u www-data php artisan tinker --execute="echo 'Non approuvés: ' . DB::table('events')->where('is_approved', 0)->count();"
```

## Solutions possibles

### Solution 1 : Modifier le HomeController pour afficher aussi les événements en cours
Si vous voulez afficher les événements en cours (pas seulement futurs), modifiez le HomeController.

### Solution 2 : Créer des événements de test avec des dates futures
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
\App\Models\Event::create([
    'title' => 'Événement Test',
    'slug' => 'evenement-test-' . time(),
    'description' => 'Description de test',
    'start_date' => now()->addDays(7),
    'end_date' => now()->addDays(8),
    'is_published' => true,
    'is_approved' => true,
    'category_id' => 1,
    'organizer_id' => 1,
    'status' => 'Gratuit',
    'etat' => 'En cours',
]);
exit
```

### Solution 3 : Modifier la requête pour inclure les événements en cours
Modifier le HomeController pour inclure les événements dont la date de fin est dans le futur.

