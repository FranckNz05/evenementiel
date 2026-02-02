<?php

/**
 * Vérifier l'utilisateur organizer1@mokilievent.com et son profil organizer
 */

echo "🔍 VÉRIFICATION UTILISATEUR organizer1@mokilievent.com\n";
echo str_repeat("=", 60) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "1️⃣ RECHERCHE UTILISATEUR :\n\n";

// Rechercher l'utilisateur organizer1@mokilievent.com
$user = DB::table('users')->where('email', 'organizer1@mokilievent.com')->first();

if (!$user) {
    echo "❌ UTILISATEUR NON TROUVÉ : organizer1@mokilievent.com\n\n";
    echo "Utilisateurs avec 'organizer' dans l'email :\n";
    $organizerUsers = DB::table('users')->where('email', 'like', '%organizer%')->get();
    foreach ($organizerUsers as $orgUser) {
        echo "   - {$orgUser->email} (ID: {$orgUser->id})\n";
    }
    echo "\n";
    exit;
}

echo "✅ UTILISATEUR TROUVÉ :\n";
echo "   - ID : {$user->id}\n";
echo "   - Nom : " . ($user->name ?? 'N/A') . "\n";
echo "   - Email : {$user->email}\n\n";

echo "2️⃣ VÉRIFICATION PROFIL ORGANIZER :\n\n";

// Vérifier si cet utilisateur a un profil organizer
$organizer = DB::table('organizers')->where('user_id', $user->id)->first();

if (!$organizer) {
    echo "❌ AUCUN PROFIL ORGANIZER TROUVÉ\n\n";
    echo "🔧 SOLUTIONS POSSIBLES :\n";
    echo "1. Créer un profil organizer pour cet utilisateur\n";
    echo "2. Vérifier les routes d'accès au dashboard organizer\n";
    echo "3. Vérifier les middlewares\n\n";
    exit;
}

echo "✅ PROFIL ORGANIZER TROUVÉ :\n";
echo "   - Organizer ID : {$organizer->id}\n";
echo "   - Company Name : {$organizer->company_name}\n";
echo "   - Slug : {$organizer->slug}\n";
echo "   - Email : {$organizer->email}\n";
echo "   - Is Verified : " . ($organizer->is_verified ? '✅ OUI' : '❌ NON') . "\n\n";

echo "3️⃣ VÉRIFICATION REVENUS ET RETRAITS :\n\n";

// Calculer les revenus (simulation simple)
$eventsCount = DB::table('events')->where('user_id', $user->id)->count();
echo "   - Nombre d'événements : {$eventsCount}\n";

$totalPayments = DB::table('paiements')
    ->where('evenement_id', 'in', function($query) use ($user) {
        $query->select('id')->from('events')->where('user_id', $user->id);
    })
    ->where('statut', 'payé')
    ->sum('montant');

echo "   - Total paiements reçus : " . number_format($totalPayments, 0, ',', ' ') . " FCFA\n";

// Vérifier les retraits
$withdrawalsCount = DB::table('withdrawals')->where('organizer_id', $organizer->id)->count();
$totalWithdrawn = DB::table('withdrawals')
    ->where('organizer_id', $organizer->id)
    ->whereIn('status', ['completed', 'processing'])
    ->sum('amount');

echo "   - Nombre de retraits : {$withdrawalsCount}\n";
echo "   - Total retiré : " . number_format($totalWithdrawn, 0, ',', ' ') . " FCFA\n";

$availableBalance = $totalPayments - $totalWithdrawn;
echo "   - Solde disponible : " . number_format($availableBalance, 0, ',', ' ') . " FCFA\n\n";

echo "4️⃣ DIAGNOSTIC FINAL :\n\n";

if ($organizer->is_verified) {
    echo "✅ Profil organizer vérifié\n";
} else {
    echo "❌ Profil organizer non vérifié\n";
}

if ($availableBalance >= 1000) {
    echo "✅ Solde suffisant pour retrait minimum (1,000 FCFA)\n";
} else {
    echo "❌ Solde insuffisant pour retrait minimum (1,000 FCFA)\n";
}

echo "\n🎯 CONCLUSION :\n";
if ($organizer && $organizer->is_verified) {
    echo "L'utilisateur devrait pouvoir accéder aux retraits.\n";
    echo "Si l'erreur persiste, le problème vient d'ailleurs.\n\n";
} else {
    echo "L'utilisateur ne peut pas accéder aux retraits pour le moment.\n\n";
}

echo str_repeat("=", 60) . "\n";
