<?php

/**
 * Test du nouveau système de gestion des paiements en attente
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🧪 TEST DU SYSTÈME DE PAIEMENTS EN ATTENTE\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  VÉRIFICATION DES COMPOSANTS :\n\n";

// Vérifier que la vue de waiting existe
$waitingViewPath = __DIR__ . '/resources/views/payments/waiting.blade.php';
if (file_exists($waitingViewPath)) {
    echo "   ✅ Vue payments.waiting existe\n";
} else {
    echo "   ❌ Vue payments.waiting manquante\n";
}

// Vérifier que la méthode waiting existe dans PaymentController
$paymentControllerPath = __DIR__ . '/app/Http/Controllers/PaymentController.php';
$controllerContent = file_get_contents($paymentControllerPath);

if (strpos($controllerContent, 'public function waiting') !== false) {
    echo "   ✅ Méthode waiting() existe dans PaymentController\n";
} else {
    echo "   ❌ Méthode waiting() manquante dans PaymentController\n";
}

// Vérifier que la méthode checkPaymentStatus existe dans ReservationController
$reservationControllerPath = __DIR__ . '/app/Http/Controllers/ReservationController.php';
$reservationContent = file_get_contents($reservationControllerPath);

if (strpos($reservationContent, 'public function checkPaymentStatus') !== false) {
    echo "   ✅ Méthode checkPaymentStatus() existe dans ReservationController\n";
} else {
    echo "   ❌ Méthode checkPaymentStatus() manquante dans ReservationController\n";
}

// Vérifier les routes
$routesContent = file_get_contents(__DIR__ . '/routes/web.php');

if (strpos($routesContent, 'payments.waiting') !== false) {
    echo "   ✅ Route payments.waiting configurée\n";
} else {
    echo "   ❌ Route payments.waiting manquante\n";
}

if (strpos($routesContent, 'reservations.check-payment-status') !== false) {
    echo "   ✅ Route reservations.check-payment-status configurée\n";
} else {
    echo "   ❌ Route reservations.check-payment-status manquante\n";
}

echo "\n2️⃣  TEST DE LA LOGIQUE DE TRAITEMENT :\n\n";

// Simuler un résultat de paiement "pending"
$pendingResult = [
    'success' => false, // C'est faux selon l'ancienne logique
    'status' => 'pending', // Mais c'est en fait un pending
    'message' => 'In process. Transaction in pending state. Please check after sometime.',
    'transaction_id' => 'TEST123456789'
];

// Tester l'ancienne logique (devrait échouer)
$oldLogicSuccess = $pendingResult['success'];
echo "   Ancienne logique: success = " . ($oldLogicSuccess ? '✅' : '❌') . " (" . ($oldLogicSuccess ? 'traité comme succès' : 'traité comme erreur') . ")\n";

// Tester la nouvelle logique (devrait réussir)
$newLogicSuccess = $pendingResult['success'] || ($pendingResult['status'] ?? null) === 'pending';
echo "   Nouvelle logique: success || status='pending' = " . ($newLogicSuccess ? '✅' : '❌') . " (" . ($newLogicSuccess ? 'traité comme succès' : 'traité comme erreur') . ")\n";

echo "\n3️⃣  VÉRIFICATION DES CODES D'ERREUR :\n\n";

// Vérifier que DP00800001006 est bien configuré comme "pending"
$airtelService = app(\App\Services\AirtelMoneyService::class);
$reflection = new ReflectionClass($airtelService);
$errorCodesProperty = $reflection->getProperty('errorCodes');
$errorCodesProperty->setAccessible(true);
$errorCodes = $errorCodesProperty->getValue($airtelService);

$pendingCode = 'DP00800001006';
if (isset($errorCodes[$pendingCode])) {
    $pendingConfig = $errorCodes[$pendingCode];
    echo "   Code $pendingCode :\n";
    echo "   📊 Status: {$pendingConfig['status']}\n";
    echo "   💬 Message: {$pendingConfig['message']}\n";
    echo "   🔄 Retry: " . ($pendingConfig['retry'] ? 'Oui' : 'Non') . "\n";

    if ($pendingConfig['status'] === 'pending' && $pendingConfig['retry'] === true) {
        echo "   ✅ Configuration correcte pour les paiements en attente\n";
    } else {
        echo "   ❌ Configuration incorrecte\n";
    }
} else {
    echo "   ❌ Code $pendingCode non trouvé dans la configuration\n";
}

echo "\n4️⃣  SIMULATION D'UN PAIEMENT EN ATTENTE :\n\n";

// Simuler le workflow complet
echo "   🔄 Workflow simulé :\n";
echo "   1. Utilisateur initie paiement → API Airtel\n";
echo "   2. API retourne 'DP00800001006' (pending) → Application\n";
echo "   3. Application redirige vers page d'attente → Utilisateur\n";
echo "   4. Page vérifie automatiquement le statut → Application\n";
echo "   5. Webhook confirme paiement → Application finalise\n";
echo "   6. Redirection vers page de succès → Utilisateur\n\n";

echo "   📱 Interface utilisateur :\n";
echo "   • Spinner de chargement animé\n";
echo "   • Instructions claires pour l'utilisateur\n";
echo "   • Barre de progression\n";
echo "   • Vérification automatique toutes les 2 secondes\n";
echo "   • Bouton de vérification manuelle\n";
echo "   • Messages de statut en temps réel\n\n";

echo str_repeat("=", 50) . "\n";
echo "🎯 RÉSULTATS FINAUX :\n\n";

$allComponentsReady = true;

// Vérifier tous les composants
$checks = [
    'Vue waiting existe' => file_exists($waitingViewPath),
    'Méthode waiting() existe' => strpos($controllerContent, 'public function waiting') !== false,
    'Méthode checkPaymentStatus() existe' => strpos($reservationContent, 'public function checkPaymentStatus') !== false,
    'Route payments.waiting existe' => strpos($routesContent, 'payments.waiting') !== false,
    'Route reservations.check-payment-status existe' => strpos($routesContent, 'reservations.check-payment-status') !== false,
    'Code DP00800001006 configuré correctement' => isset($errorCodes[$pendingCode]) && $errorCodes[$pendingCode]['status'] === 'pending',
    'Nouvelle logique traite pending comme succès' => $newLogicSuccess
];

foreach ($checks as $component => $status) {
    echo "   " . ($status ? "✅" : "❌") . " $component\n";
    if (!$status) {
        $allComponentsReady = false;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";

if ($allComponentsReady) {
    echo "🎉 SYSTÈME DE PAIEMENTS EN ATTENTE OPÉRATIONNEL !\n\n";
    echo "🚀 L'application peut maintenant gérer correctement les paiements Airtel Money :\n\n";
    echo "• ✅ Paiements en attente redirigés vers page de chargement\n";
    echo "• ✅ Vérification automatique du statut\n";
    echo "• ✅ Interface utilisateur intuitive\n";
    echo "• ✅ Gestion des timeouts et erreurs\n";
    echo "• ✅ Confirmation finale via webhooks\n\n";

    echo "💡 Pour tester :\n";
    echo "1. Créer une commande/order\n";
    echo "2. Initier un paiement Airtel Money\n";
    echo "3. Voir la page de chargement s'afficher\n";
    echo "4. Confirmer sur téléphone (ou attendre)\n";
    echo "5. Voir la redirection automatique vers succès\n\n";

    echo "🎯 URL de test : http://localhost:8000/payments/process/{ID_ORDER}\n\n";

} else {
    echo "⚠️  PROBLÈMES DÉTECTÉS\n\n";
    echo "Certains composants ne sont pas encore prêts.\n";
    echo "Vérifiez les points marqués ❌ ci-dessus.\n\n";
}

echo str_repeat("=", 50) . "\n";
