<?php

/**
 * Créer un retrait en statut "pending" pour tester le système d'approbation
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CRÉATION D'UN RETRAIT PENDING POUR TEST\n";
echo str_repeat("=", 50) . "\n\n";

// Trouver l'utilisateur organizer1
$user = DB::table('users')->where('email', 'organizer1@mokilievent.com')->first();
if (!$user) {
    echo "❌ Utilisateur organizer1@mokilievent.com non trouvé\n\n";
    exit;
}

$organizer = DB::table('organizers')->where('user_id', $user->id)->first();
if (!$organizer) {
    echo "❌ Profil organizer non trouvé\n\n";
    exit;
}

echo "✅ Organisateur trouvé - ID: {$organizer->id}\n\n";

// Vérifier s'il y a déjà un retrait en pending
$existingPending = DB::table('withdrawals')->where('organizer_id', $organizer->id)->where('status', 'pending')->first();

if ($existingPending) {
    echo "✅ Il y a déjà un retrait en attente - ID: {$existingPending->id}\n";
    echo "   - Montant: " . number_format($existingPending->amount, 0, ',', ' ') . " FCFA\n";
    echo "   - Méthode: {$existingPending->payment_method}\n";
    echo "   - Téléphone: {$existingPending->phone_number}\n\n";
} else {
    // Créer un retrait en pending pour tester
    $withdrawalId = DB::table('withdrawals')->insertGetId([
        'organizer_id' => $organizer->id,
        'amount' => 50000, // 50,000 FCFA pour test
        'payment_method' => 'Airtel Money',
        'phone_number' => '+242064088868',
        'status' => 'pending',
        'transaction_reference' => 'WD-TEST-' . time(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Retrait de test créé - ID: {$withdrawalId}\n";
    echo "   - Montant: 50,000 FCFA\n";
    echo "   - Méthode: Airtel Money\n";
    echo "   - Statut: pending\n\n";
}

echo "🎯 INSTRUCTIONS POUR TEST :\n\n";
echo "1. Connectez-vous en tant qu'administrateur\n";
echo "2. Allez dans : Dashboard → Retraits\n";
echo "3. Vous devriez voir le retrait en statut 'En attente'\n";
echo "4. Cliquez sur le bouton vert (✓) pour approuver\n";
echo "5. Saisissez le PIN Airtel Money (test)\n";
echo "6. Validez l'approbation\n\n";

echo "📱 RÉSULTAT ATTENDU :\n";
echo "• L'API Airtel Money sera appelée\n";
echo "• L'argent sera envoyé au numéro +242064088868\n";
echo "• Le statut passera à 'completed'\n\n";

echo str_repeat("=", 50) . "\n";
echo "🧪 SYSTÈME D'APPROBATION ADMIN PRÊT !\n";
echo str_repeat("=", 50) . "\n";
