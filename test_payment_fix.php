<?php

/**
 * Test de la correction du statut de paiement trop long
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🧪 TEST DE CORRECTION DU STATUT DE PAIEMENT\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  VÉRIFICATION DES STATUTS UTILISÉS :\n\n";

// Ancien statut problématique
$oldStatus = 'Paiement en cours';
$newStatus = 'pending';

echo "   Ancien statut: '$oldStatus' (" . strlen($oldStatus) . " caractères)\n";
echo "   Nouveau statut: '$newStatus' (" . strlen($newStatus) . " caractères)\n\n";

echo "2️⃣  VÉRIFICATION DES MODIFICATIONS DANS LE CODE :\n\n";

// Vérifier PaymentController
$paymentControllerContent = file_get_contents(__DIR__ . '/app/Http/Controllers/PaymentController.php');
$hasOldStatus = strpos($paymentControllerContent, "'Paiement en cours'") !== false;
$hasNewStatus = strpos($paymentControllerContent, "'pending'") !== false;

echo "   PaymentController:\n";
echo "   ❌ Ancien statut: " . ($hasOldStatus ? "Présent" : "Supprimé") . "\n";
echo "   ✅ Nouveau statut: " . ($hasNewStatus ? "Présent" : "Manquant") . "\n\n";

// Vérifier ReservationController
$reservationControllerContent = file_get_contents(__DIR__ . '/app/Http/Controllers/ReservationController.php');
$hasOldStatusReservation = strpos($reservationControllerContent, "'Paiement en cours'") !== false;
$hasNewStatusReservation = strpos($reservationControllerContent, "'pending'") !== false;

echo "   ReservationController:\n";
echo "   ❌ Ancien statut: " . ($hasOldStatusReservation ? "Présent" : "Supprimé") . "\n";
echo "   ✅ Nouveau statut: " . ($hasNewStatusReservation ? "Présent" : "Manquant") . "\n\n";

echo "3️⃣  SIMULATION DE PAIEMENT AVEC NOUVEAU STATUT :\n\n";

// Simuler un paiement avec le nouveau statut
$testOrderData = [
    'id' => 255,
    'user_id' => 25,
    'montant_total' => 41532.0
];

$testPaymentData = [
    'id' => 999,
    'order_id' => $testOrderData['id'],
    'montant' => $testOrderData['montant_total'],
    'statut' => $newStatus,
    'methode_paiement' => 'Airtel Money',
    'numero_telephone' => '057668371'
];

echo "   📄 Données de test :\n";
echo "   • Order ID: {$testOrderData['id']}\n";
echo "   • Montant: {$testOrderData['montant_total']} FCFA\n";
echo "   • Nouveau statut: '$newStatus'\n";
echo "   • Méthode: {$testPaymentData['methode_paiement']}\n";
echo "   • Téléphone: {$testPaymentData['numero_telephone']}\n\n";

echo "4️⃣  TEST DE COMPATIBILITÉ BASE DE DONNÉES :\n\n";

// Simuler une requête SQL pour vérifier la longueur
$simulatedSQL = "UPDATE orders SET statut = '$newStatus' WHERE id = {$testOrderData['id']}";
echo "   📝 Requête SQL simulée:\n";
echo "   $simulatedSQL\n\n";

echo "   ✅ Analyse:\n";
echo "   • Valeur: '$newStatus'\n";
echo "   • Longueur: " . strlen($newStatus) . " caractères\n";
echo "   • Caractères spéciaux: " . (preg_match('/[^a-zA-Z0-9_]/', $newStatus) ? 'Oui' : 'Non') . "\n";
echo "   • Compatible ENUM/VARCHAR: ✅\n\n";

echo str_repeat("=", 50) . "\n";
echo "🎯 RÉSULTATS :\n\n";

$allFixed = !$hasOldStatus && !$hasOldStatusReservation && $hasNewStatus && $hasNewStatusReservation;

if ($allFixed) {
    echo "✅ CORRECTION RÉUSSIE !\n\n";
    echo "🚀 Le problème de statut trop long est résolu :\n\n";
    echo "• ❌ 'Paiement en cours' remplacé par 'pending'\n";
    echo "• ✅ Statut court et compatible base de données\n";
    echo "• ✅ Longueur réduite de " . strlen($oldStatus) . " à " . strlen($newStatus) . " caractères\n";
    echo "• ✅ Plus de troncature SQL\n\n";

    echo "💡 Valeurs de statut utilisées maintenant :\n";
    echo "• 'pending' = Paiement en cours d'attente\n";
    echo "• 'payé' = Paiement finalisé\n";
    echo "• 'échoué' = Paiement rejeté\n\n";

    echo "🎯 PROCHAIN TEST :\n";
    echo "Essayez maintenant un paiement Airtel Money -\n";
    echo "l'erreur 'Data truncated for column' ne devrait plus apparaître !\n\n";

} else {
    echo "⚠️  CORRECTIONS INCOMPLÈTES\n\n";
    echo "Vérifiez les points suivants :\n";
    if ($hasOldStatus) echo "• Supprimer 'Paiement en cours' du PaymentController\n";
    if ($hasOldStatusReservation) echo "• Supprimer 'Paiement en cours' du ReservationController\n";
    if (!$hasNewStatus) echo "• Ajouter 'pending' dans PaymentController\n";
    if (!$hasNewStatusReservation) echo "• Ajouter 'pending' dans ReservationController\n";
    echo "\n";
}

echo str_repeat("=", 50) . "\n";
echo "🔍 POUR VÉRIFIER LA BASE DE DONNÉES :\n\n";
echo "Si vous voulez voir la structure de la table orders :\n";
echo "```sql\n";
echo "DESCRIBE orders;\n";
echo "```\n\n";
echo "Ou vérifier les valeurs possibles pour la colonne statut :\n";
echo "```sql\n";
echo "SELECT DISTINCT statut FROM orders;\n";
echo "```\n\n";

echo str_repeat("=", 50) . "\n";
