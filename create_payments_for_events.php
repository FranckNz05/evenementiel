<?php

/**
 * Créer les paiements manquants pour les événements de test
 */

echo "💳 CRÉATION PAIEMENTS POUR ÉVÉNEMENTS DE TEST\n";
echo str_repeat("=", 60) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

// Trouver les événements créés pour cet organizer
$events = DB::table('events')->where('organizer_id', $organizer->id)->get();

if ($events->count() == 0) {
    echo "❌ Aucun événement trouvé pour cet organizer\n\n";
    exit;
}

echo "📅 ÉVÉNEMENTS À TRAITER :\n";
foreach ($events as $event) {
    echo "• {$event->title} (ID: {$event->id})\n";
}
echo "\n";

// Configuration des événements avec leurs prix
$eventConfigs = [
    435 => ['price' => 15000, 'tickets' => 67], // Concert Jazz
    436 => ['price' => 10000, 'tickets' => 78], // Théâtre
    437 => ['price' => 8000, 'tickets' => 56],  // Art
];

$totalRevenue = 0;

foreach ($events as $event) {
    $eventId = $event->id;
    $config = $eventConfigs[$eventId] ?? ['price' => 10000, 'tickets' => 50];

    echo "🎫 CRÉATION PAIEMENTS POUR : {$event->title}\n";
    echo "   - Prix ticket : " . number_format($config['price'], 0, ',', ' ') . " FCFA\n";
    echo "   - Tickets à créer : {$config['tickets']}\n\n";

    $eventRevenue = 0;

    for ($i = 0; $i < $config['tickets']; $i++) {
        try {
            // Générer des données réalistes pour le paiement
            $paymentDate = now()->subDays(rand(1, 30));
            $phoneNumber = '+24206' . rand(1000000, 9999999);
            $userId = rand(1, 50); // Utilisateur aléatoire existant

            $paymentId = DB::table('paiements')->insertGetId([
                'matricule' => 'PAY-' . strtoupper(Str::random(8)),
                'user_id' => $userId,
                'order_id' => null,
                'reservation_id' => null,
                'evenement_id' => $eventId,
                'order_ticket_id' => null,
                'montant' => $config['price'],
                'statut' => 'payé',
                'methode_paiement' => 'Airtel Money',
                'date_paiement' => $paymentDate,
                'reference_paiement' => 'REF-' . strtoupper(Str::random(10)),
                'numero_telephone' => $phoneNumber,
                'details' => json_encode([
                    'event_title' => $event->title,
                    'ticket_type' => 'Standard',
                    'payment_method' => 'Airtel Money',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12))
                ]),
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);

            $eventRevenue += $config['price'];

        } catch (\Exception $e) {
            echo "❌ Erreur paiement {$i} pour {$event->title}: {$e->getMessage()}\n";
        }
    }

    echo "   ✅ Créé {$config['tickets']} paiements\n";
    echo "   💰 Revenus générés : " . number_format($eventRevenue, 0, ',', ' ') . " FCFA\n\n";

    $totalRevenue += $eventRevenue;
}

echo "🎯 RÉCAPITULATIF FINAL :\n\n";
echo "✅ Événements traités : " . $events->count() . "\n";
echo "💰 Revenus totaux générés : " . number_format($totalRevenue, 0, ',', ' ') . " FCFA\n\n";

// Vérifier le calcul des revenus
$commissionService = app(\App\Services\CommissionService::class);
$revenueData = $commissionService->calculateOrganizerTotalNetRevenue($organizer->id);

echo "🔢 VÉRIFICATION REVENUS :\n";
echo "• Revenus bruts calculés : " . number_format($revenueData['gross_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n";
echo "• Revenus nets : " . number_format($revenueData['net_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n\n";

if (($revenueData['net_revenue'] ?? 0) > 0) {
    echo "✅ LES REVENUS SONT MAINTENANT VISIBLES !\n\n";

    // Calculer le solde disponible
    $totalWithdrawn = DB::table('withdrawals')->where('organizer_id', $organizer->id)
        ->whereIn('status', ['completed', 'processing'])
        ->sum('amount');

    $availableBalance = ($revenueData['net_revenue'] ?? 0) - $totalWithdrawn;

    echo "💸 SOLDE DISPONIBLE :\n";
    echo "• Revenus nets : " . number_format($revenueData['net_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n";
    echo "• Retraits : " . number_format($totalWithdrawn, 0, ',', ' ') . " FCFA\n";
    echo "• Solde disponible : " . number_format($availableBalance, 0, ',', ' ') . " FCFA ✅\n\n";
} else {
    echo "❌ PROBLÈME : Les revenus ne sont toujours pas calculés\n";
    echo "Il faut vérifier CommissionService\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "🎪 PAIEMENTS CRÉÉS AVEC SUCCÈS !\n";
echo "Vous pouvez maintenant tester les retraits Airtel Money !\n";
echo str_repeat("=", 60) . "\n";
