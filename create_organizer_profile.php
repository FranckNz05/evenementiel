<?php

/**
 * Script pour créer un profil organizer pour l'utilisateur
 */

echo "👤 CRÉATION PROFIL ORGANIZER\n";
echo str_repeat("=", 40) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organizer;

// Trouver l'utilisateur qui n'a pas de profil organizer
$user = User::find(25); // L'utilisateur qui a le problème

if (!$user) {
    echo "❌ Utilisateur avec ID 25 introuvable\n\n";
    exit;
}

echo "Utilisateur trouvé :\n";
echo "   - Nom : {$user->name}\n";
echo "   - Email : {$user->email}\n";
echo "   - ID : {$user->id}\n\n";

// Vérifier si l'utilisateur a déjà un profil organizer
$existingOrganizer = $user->organizer;

if ($existingOrganizer) {
    echo "✅ L'utilisateur a déjà un profil organizer :\n";
    echo "   - Organizer ID : {$existingOrganizer->id}\n";
    echo "   - Nom : {$existingOrganizer->name}\n\n";
    echo "🎯 PAS BESOIN DE CRÉER UN NOUVEAU PROFIL\n\n";
    exit;
}

echo "❌ Aucun profil organizer trouvé\n\n";

// Créer un profil organizer pour cet utilisateur
echo "🏗️ CRÉATION DU PROFIL ORGANIZER...\n\n";

try {
    $organizer = new Organizer();
    $organizer->user_id = $user->id;
    $organizer->company_name = $user->name ?: 'Mon Organisateur';
    $organizer->slug = 'organizer-' . $user->id . '-' . time();
    $organizer->email = $user->email;
    $organizer->phone_primary = $user->phone ?: '+242064088868';
    $organizer->address = 'Adresse à définir';
    $organizer->description = 'Organisateur créé automatiquement - Veuillez compléter votre profil';
    $organizer->is_verified = 1; // Marquer comme vérifié pour permettre les retraits
    $organizer->save();

    echo "✅ PROFIL ORGANIZER CRÉÉ AVEC SUCCÈS !\n\n";
    echo "Détails du profil créé :\n";
    echo "   - Organizer ID : {$organizer->id}\n";
    echo "   - Nom : {$organizer->name}\n";
    echo "   - Email : {$organizer->email}\n";
    echo "   - Status : {$organizer->status}\n";
    echo "   - Créé le : {$organizer->created_at}\n\n";

    // Vérifier que la relation fonctionne maintenant
    $user->refresh();
    $organizerFromUser = $user->organizer;

    if ($organizerFromUser) {
        echo "✅ RELATION UTILISATEUR → ORGANIZER : OK\n\n";
    } else {
        echo "❌ RELATION UTILISATEUR → ORGANIZER : ÉCHEC\n\n";
    }

    echo "🎯 RÉSULTAT :\n";
    echo "Vous pouvez maintenant accéder aux retraits !\n";
    echo "Allez dans : Tableau de bord → Paiements → Retraits\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR LORS DE LA CRÉATION :\n";
    echo "   - Message : {$e->getMessage()}\n\n";
}

echo str_repeat("=", 40) . "\n";
echo "📝 NOTE :\n";
echo "Si ce script ne fonctionne pas, vous pouvez :\n";
echo "1. Aller dans 'Profil organisateur'\n";
echo "2. Remplir et sauvegarder le formulaire\n";
echo "3. Cela créera automatiquement le profil\n\n";

echo str_repeat("=", 40) . "\n";
