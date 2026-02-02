<?php

/**
 * Créer un profil organizer pour l'utilisateur organizer1@mokilievent.com (ID 47)
 */

echo "👤 CRÉATION PROFIL ORGANIZER POUR organizer1@mokilievent.com\n";
echo str_repeat("=", 60) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organizer;

// Trouver l'utilisateur organizer1@mokilievent.com
$user = DB::table('users')->where('email', 'organizer1@mokilievent.com')->first();

if (!$user) {
    echo "❌ UTILISATEUR organizer1@mokilievent.com NON TROUVÉ\n\n";
    exit;
}

echo "Utilisateur trouvé :\n";
echo "   - ID : {$user->id}\n";
echo "   - Email : {$user->email}\n\n";

// Vérifier si l'utilisateur a déjà un profil organizer
$existingOrganizer = DB::table('organizers')->where('user_id', $user->id)->first();

if ($existingOrganizer) {
    echo "✅ L'utilisateur a déjà un profil organizer :\n";
    echo "   - Organizer ID : {$existingOrganizer->id}\n";
    echo "   - Company : {$existingOrganizer->company_name}\n\n";
    echo "🎯 PAS BESOIN DE CRÉER UN NOUVEAU PROFIL\n\n";
    exit;
}

echo "❌ Aucun profil organizer trouvé\n\n";

// Créer un profil organizer pour cet utilisateur
echo "🏗️ CRÉATION DU PROFIL ORGANIZER...\n\n";

try {
    // Créer l'organizer avec les champs requis
    $organizerData = [
        'user_id' => $user->id,
        'company_name' => 'Organizer 1 Company',
        'slug' => 'organizer-1-company-' . time(),
        'email' => $user->email,
        'phone_primary' => '+242064088868',
        'address' => '123 Rue de l\'Organisation, Brazzaville',
        'description' => 'Organisateur professionnel créé automatiquement',
        'is_verified' => 1, // Marquer comme vérifié pour permettre les retraits
        'created_at' => now(),
        'updated_at' => now(),
    ];

    $organizerId = DB::table('organizers')->insertGetId($organizerData);

    echo "✅ PROFIL ORGANIZER CRÉÉ AVEC SUCCÈS !\n\n";
    echo "Détails du profil créé :\n";
    echo "   - Organizer ID : {$organizerId}\n";
    echo "   - Company Name : {$organizerData['company_name']}\n";
    echo "   - Email : {$organizerData['email']}\n";
    echo "   - Is Verified : ✅ OUI\n\n";

    // Vérifier que la relation fonctionne maintenant
    $userModel = User::find($user->id);
    $organizerFromUser = $userModel->organizer;

    if ($organizerFromUser) {
        echo "✅ RELATION UTILISATEUR → ORGANIZER : OK\n\n";
    } else {
        echo "❌ RELATION UTILISATEUR → ORGANIZER : ÉCHEC\n\n";
    }

    echo "🎯 RÉSULTAT :\n";
    echo "Vous pouvez maintenant accéder aux retraits !\n";
    echo "Allez dans : Dashboard Organizer → Paiements → Retraits\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR LORS DE LA CRÉATION :\n";
    echo "   - Message : {$e->getMessage()}\n\n";
}

echo str_repeat("=", 60) . "\n";
