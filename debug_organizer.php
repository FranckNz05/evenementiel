<?php

/**
 * Diagnostic pour vérifier le profil organizer de l'utilisateur
 */

echo "🔍 DIAGNOSTIC PROFIL ORGANIZER\n";
echo str_repeat("=", 50) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Organizer;

// Simuler la logique du WithdrawalController
echo "1️⃣ VÉRIFICATION UTILISATEUR CONNECTÉ :\n\n";

// Pour le diagnostic, on va chercher un utilisateur qui pourrait être l'organisateur
// En production, on utiliserait Auth::user()

$users = User::where('email', 'like', '%')->take(5)->get();

foreach ($users as $user) {
    echo "Utilisateur : {$user->name} ({$user->email})\n";
    echo "ID : {$user->id}\n";

    // Vérifier si l'utilisateur a un profil organizer
    $organizer = $user->organizer;

    if ($organizer) {
        echo "✅ Profil organizer trouvé :\n";
        echo "   - Organizer ID : {$organizer->id}\n";
        echo "   - Nom : {$organizer->name}\n";
        echo "   - Status : {$organizer->status}\n";
        echo "   - Créé le : {$organizer->created_at}\n\n";

        // Vérifier les revenus
        $commissionService = app(\App\Services\CommissionService::class);
        $revenueData = $commissionService->calculateOrganizerTotalNetRevenue($organizer->id);

        echo "💰 REVENUS DE L'ORGANIZER :\n";
        echo "   - Revenus bruts : " . number_format($revenueData['gross_revenue'], 0, ',', ' ') . " FCFA\n";
        echo "   - Commissions : " . number_format($revenueData['commissions'], 0, ',', ' ') . " FCFA\n";
        echo "   - Revenus nets : " . number_format($revenueData['net_revenue'], 0, ',', ' ') . " FCFA\n\n";

        // Vérifier les retraits
        $totalWithdrawn = \App\Models\Withdrawal::where('organizer_id', $organizer->id)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('amount');

        $availableBalance = $revenueData['net_revenue'] - $totalWithdrawn;

        echo "💸 SOLDE DISPONIBLE :\n";
        echo "   - Total retiré : " . number_format($totalWithdrawn, 0, ',', ' ') . " FCFA\n";
        echo "   - Solde disponible : " . number_format($availableBalance, 0, ',', ' ') . " FCFA\n\n";

        // Vérifier si le solde permet un retrait
        if ($availableBalance >= 1000) {
            echo "✅ SOLDE SUFFISANT pour retrait minimum (1,000 FCFA)\n\n";
        } else {
            echo "❌ SOLDE INSUFFISANT pour retrait minimum (1,000 FCFA)\n\n";
        }

        break; // On s'arrête au premier organizer trouvé

    } else {
        echo "❌ Aucun profil organizer trouvé pour cet utilisateur\n\n";
    }
}

echo "2️⃣ CRÉATION PROFIL ORGANIZER (si nécessaire) :\n\n";

// Chercher des utilisateurs sans profil organizer
$usersWithoutOrganizer = User::whereDoesntHave('organizer')->get();

if ($usersWithoutOrganizer->count() > 0) {
    echo "Utilisateurs sans profil organizer :\n";
    foreach ($usersWithoutOrganizer as $user) {
        echo "   - {$user->name} ({$user->email})\n";
    }
    echo "\n";

    echo "💡 SOLUTION : Créer un profil organizer\n";
    echo "Vous pouvez :\n";
    echo "1. Aller dans 'Profil organisateur' pour créer le profil\n";
    echo "2. Ou utiliser le seeder pour créer des profils\n\n";

} else {
    echo "Tous les utilisateurs ont un profil organizer ✅\n\n";
}

echo "3️⃣ ROUTES DISPONIBLES :\n\n";
echo "Routes organizer :\n";
echo "   - organizer.dashboard : Tableau de bord\n";
echo "   - organizer.profile.edit : Modifier profil\n";
echo "   - withdrawals.index : Retraits\n\n";

echo str_repeat("=", 50) . "\n";
echo "🎯 DIAGNOSTIC TERMINÉ\n\n";
echo "Si vous n'avez pas de profil organizer, allez dans :\n";
echo "Tableau de bord → Profil organisateur → Créer/Remplir le profil\n\n";

echo "Une fois le profil créé, les retraits fonctionneront !\n";
echo str_repeat("=", 50) . "\n";
