<?php

/**
 * Vérifier les paiements créés pour les événements de test
 */

echo "🔍 VÉRIFICATION PAIEMENTS CRÉÉS\n";
echo str_repeat("=", 50) . "\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

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

echo "📅 ÉVÉNEMENTS TROUVÉS :\n";
echo "Nombre d'événements : " . $events->count() . "\n\n";

$totalExpectedPayments = 0;
foreach ($events as $event) {
    echo "🎪 Événement : {$event->title} (ID: {$event->id})\n";
    $totalExpectedPayments += 50; // On attendait 50 paiements par événement
}

echo "\n💰 PAIEMENTS ATTENDUS : {$totalExpectedPayments}\n\n";

// Vérifier les paiements créés
$payments = DB::table('paiements')->whereIn('evenement_id', $events->pluck('id'))->get();

echo "💳 PAIEMENTS CRÉÉS :\n";
echo "Nombre de paiements trouvés : " . $payments->count() . "\n\n";

if ($payments->count() == 0) {
    echo "❌ AUCUN PAIEMENT CRÉÉ !\n\n";
    echo "🔧 PROBLÈMES POSSIBLES :\n";
    echo "1. Erreur lors de la création des paiements\n";
    echo "2. Problème avec la colonne 'evenement_id'\n";
    echo "3. Les paiements ont été créés dans une autre table\n\n";

    // Vérifier s'il y a des paiements du tout
    $allPayments = DB::table('paiements')->count();
    echo "📊 Total paiements dans la DB : {$allPayments}\n\n";

    exit;
}

$totalRevenue = 0;
$paymentsByEvent = [];

foreach ($payments as $payment) {
    $eventId = $payment->evenement_id;
    if (!isset($paymentsByEvent[$eventId])) {
        $paymentsByEvent[$eventId] = 0;
    }
    $paymentsByEvent[$eventId]++;
    $totalRevenue += $payment->montant;
}

echo "📊 RÉPARTITION PAR ÉVÉNEMENT :\n";
foreach ($events as $event) {
    $eventId = $event->id;
    $count = $paymentsByEvent[$eventId] ?? 0;
    echo "• {$event->title} : {$count} paiements\n";
}

echo "\n💰 REVENUS TOTAUX CALCULÉS : " . number_format($totalRevenue, 0, ',', ' ') . " FCFA\n\n";

// Vérifier le calcul des revenus dans CommissionService
$commissionService = app(\App\Services\CommissionService::class);
$revenueData = $commissionService->calculateOrganizerTotalNetRevenue($organizer->id);

echo "🔢 CALCUL COMMISSION SERVICE :\n";
echo "• Revenus bruts : " . number_format($revenueData['gross_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n";
echo "• Commissions : " . number_format($revenueData['commissions'] ?? 0, 0, ',', ' ') . " FCFA\n";
echo "• Revenus nets : " . number_format($revenueData['net_revenue'] ?? 0, 0, ',', ' ') . " FCFA\n\n";

if (($revenueData['net_revenue'] ?? 0) == 0 && $totalRevenue > 0) {
    echo "⚠️  PROBLÈME DE CALCUL :\n";
    echo "Les paiements existent mais CommissionService ne les trouve pas.\n";
    echo "Vérifiez la logique de calculateOrganizerTotalNetRevenue().\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "🎯 CONCLUSION :\n\n";

if ($payments->count() > 0 && ($revenueData['net_revenue'] ?? 0) > 0) {
    echo "✅ TOUT FONCTIONNE - Les retraits peuvent être testés !\n\n";
} elseif ($payments->count() > 0 && ($revenueData['net_revenue'] ?? 0) == 0) {
    echo "⚠️  PAIEMENTS CRÉÉS mais revenus non calculés\n";
    echo "Il faut corriger CommissionService\n\n";
} else {
    echo "❌ AUCUN PAIEMENT - Il faut les créer\n\n";
}

echo str_repeat("=", 50) . "\n";
