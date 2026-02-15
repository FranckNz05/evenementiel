# Diagnostic de la page d'accueil

## Problème
Les événements et organisateurs ne s'affichent pas sur la page d'accueil.

## Commandes de diagnostic sur le serveur

### 1. Vérifier les erreurs dans les logs Laravel
```bash
cd /var/www/mokilievent/evenementiel
tail -50 storage/logs/laravel.log | grep -i error
```

### 2. Vérifier les erreurs récentes
```bash
tail -100 storage/logs/laravel.log | grep -A 10 "production.ERROR"
```

### 3. Tester la route home directement
```bash
curl -v https://mokilievent.com 2>&1 | head -50
```

### 4. Vérifier les données dans la base de données

#### Événements
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
echo "Total événements: " . DB::table('events')->count();
echo "\nPubliés et approuvés: " . DB::table('events')->where('is_published', 1)->where('is_approved', 1)->count();
echo "\nFuturs: " . DB::table('events')->where('start_date', '>=', now())->count();
echo "\nÉligibles: " . DB::table('events')->where('is_published', 1)->where('is_approved', 1)->where('start_date', '>=', now())->count();
exit
```

#### Organisateurs
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
echo "Total organisateurs: " . DB::table('organizers')->count();
echo "\nVérifiés: " . DB::table('organizers')->where('is_verified', 1)->count();
echo "\nAvec événements: " . DB::table('organizers')->whereHas('events', function($q) {
    $q->where('start_date', '>=', now())
      ->where('is_published', 1)
      ->where('is_approved', 1);
})->count();
exit
```

### 5. Tester le HomeController directement
```bash
sudo -u www-data php artisan tinker
# Dans tinker :
$controller = new \App\Http\Controllers\HomeController();
$result = $controller->index();
# Vérifier les données
$data = $result->getData();
echo "Popular Events: " . $data['popularEvents']->count();
echo "\nOrganizers: " . $data['organizers']->count();
echo "\nCategories: " . $data['categories']->count();
exit
```

### 6. Vérifier les erreurs JavaScript dans le navigateur
Ouvrez la console du navigateur (F12) et vérifiez s'il y a des erreurs JavaScript qui empêchent l'affichage.

### 7. Vérifier si Owl Carousel est chargé
```bash
# Vérifier dans les logs si Owl Carousel cause des erreurs
tail -100 storage/logs/laravel.log | grep -i "owl\|carousel\|javascript"
```

### 8. Vérifier les permissions sur les fichiers
```bash
ls -la resources/views/home.blade.php
ls -la app/Http/Controllers/HomeController.php
```

### 9. Vérifier la configuration de la base de données
```bash
grep DB_ .env
```

### 10. Tester avec une requête directe
```bash
sudo -u www-data php artisan route:list | grep home
```

## Solutions possibles

### Si les données existent mais ne s'affichent pas :
1. Vérifier les erreurs JavaScript dans la console du navigateur
2. Vérifier si Owl Carousel est correctement chargé
3. Vérifier les permissions sur les fichiers de vue

### Si les données n'existent pas :
1. Créer des événements de test
2. Créer des organisateurs de test
3. Vérifier que les événements sont publiés et approuvés

### Si c'est une erreur PHP :
1. Vérifier les logs Laravel
2. Vérifier les permissions
3. Vider les caches

