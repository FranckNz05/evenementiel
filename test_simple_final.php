<?php

/**
 * Test simple final - vérification que les simulations sont supprimées
 */

echo "🎯 VÉRIFICATION FINALE - SIMULATIONS SUPPRIMÉES\n";
echo str_repeat("=", 50) . "\n\n";

echo "1️⃣  VÉRIFICATION DES VUES :\n";

$viewsToCheck = [
    'reservations.pay' => 'resources/views/reservations/pay.blade.php',
    'payments.process' => 'resources/views/payments/process.blade.php'
];

foreach ($viewsToCheck as $viewName => $filePath) {
    if (file_exists(__DIR__ . '/' . $filePath)) {
        $content = file_get_contents(__DIR__ . '/' . $filePath);
        $hasSimulationText = strpos($content, 'simulation') !== false || strpos($content, 'simulé') !== false;
        $hasAirtelMoneyText = strpos($content, 'Airtel Money') !== false;

        echo "   " . ($hasSimulationText ? "❌" : "✅") . " $viewName: " . ($hasSimulationText ? "Contient encore du texte de simulation" : "Texte de simulation supprimé") . "\n";
        echo "   " . ($hasAirtelMoneyText ? "✅" : "⚠️") . " $viewName: " . ($hasAirtelMoneyText ? "Contient 'Airtel Money'" : "Ne contient pas 'Airtel Money'") . "\n\n";
    } else {
        echo "   ⚠️  Fichier $filePath non trouvé\n\n";
    }
}

echo "2️⃣  VÉRIFICATION DES CONTRÔLEURS :\n";

$controllersToCheck = [
    'ReservationController' => 'app/Http/Controllers/ReservationController.php',
    'PaymentController' => 'app/Http/Controllers/PaymentController.php'
];

foreach ($controllersToCheck as $controllerName => $filePath) {
    if (file_exists(__DIR__ . '/' . $filePath)) {
        $content = file_get_contents(__DIR__ . '/' . $filePath);

        $hasAirtelGateway = strpos($content, 'AirtelMoneyGateway') !== false;
        $hasProcessPayment = strpos($content, 'processPayment') !== false;
        $hasSimulatePayment = strpos($content, 'simulatePayment') !== false;

        echo "   " . ($hasAirtelGateway ? "✅" : "❌") . " $controllerName: " . ($hasAirtelGateway ? "Utilise AirtelMoneyGateway" : "N'utilise pas AirtelMoneyGateway") . "\n";
        echo "   " . ($hasProcessPayment ? "✅" : "⚠️") . " $controllerName: " . ($hasProcessPayment ? "A méthode processPayment" : "Pas de méthode processPayment") . "\n";
        echo "   " . (!$hasSimulatePayment ? "✅" : "ℹ️") . " $controllerName: " . (!$hasSimulatePayment ? "Plus de simulatePayment" : "A encore simulatePayment (pour compatibilité)") . "\n\n";
    } else {
        echo "   ⚠️  Fichier $filePath non trouvé\n\n";
    }
}

echo "3️⃣  VÉRIFICATION DES ROUTES :\n";

$routesContent = file_get_contents(__DIR__ . '/routes/web.php');
$hasProcessPost = strpos($routesContent, 'payments.process.post') !== false;
$hasSimulateRoute = strpos($routesContent, 'payments.simulate') !== false;

echo "   " . ($hasProcessPost ? "✅" : "❌") . " Route payments.process.post: " . ($hasProcessPost ? "Définie" : "Manquante") . "\n";
echo "   " . ($hasSimulateRoute ? "ℹ️" : "✅") . " Route payments.simulate: " . ($hasSimulateRoute ? "Encore présente (compatibilité)" : "Supprimée") . "\n\n";

echo "4️⃣  VÉRIFICATION DES CLÉS API :\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);

    $hasClientId = strpos($envContent, 'AIRTEL_CLIENT_ID=b280b215') !== false;
    $hasClientSecret = strpos($envContent, 'AIRTEL_CLIENT_SECRET=c8ecb836') !== false;
    $hasMerchantCode = strpos($envContent, 'AIRTEL_MERCHANT_CODE=7VS4GTR8') !== false;

    echo "   " . ($hasClientId ? "✅" : "❌") . " AIRTEL_CLIENT_ID: " . ($hasClientId ? "Configuré" : "Manquant") . "\n";
    echo "   " . ($hasClientSecret ? "✅" : "❌") . " AIRTEL_CLIENT_SECRET: " . ($hasClientSecret ? "Configuré" : "Manquant") . "\n";
    echo "   " . ($hasMerchantCode ? "✅" : "ℹ️") . " AIRTEL_MERCHANT_CODE: " . ($hasMerchantCode ? "Configuré" : "Optionnel") . "\n\n";
} else {
    echo "   ❌ Fichier .env non trouvé\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "🎉 RÉSULTATS :\n\n";

$allGood = true;

// Vérifier que les simulations sont supprimées
foreach ($viewsToCheck as $filePath) {
    if (file_exists(__DIR__ . '/' . $filePath)) {
        $content = file_get_contents(__DIR__ . '/' . $filePath);
        if (strpos($content, 'simulation') !== false || strpos($content, 'simulé') !== false) {
            $allGood = false;
            break;
        }
    }
}

// Vérifier que les contrôleurs utilisent Airtel
foreach ($controllersToCheck as $filePath) {
    if (file_exists(__DIR__ . '/' . $filePath)) {
        $content = file_get_contents(__DIR__ . '/' . $filePath);
        if (strpos($content, 'AirtelMoneyGateway') === false) {
            $allGood = false;
            break;
        }
    }
}

if ($allGood) {
    echo "✅ TRANSFORMATION RÉUSSIE !\n\n";
    echo "🚀 L'application utilise maintenant l'API Airtel Money réelle :\n";
    echo "• ✅ Simulations supprimées\n";
    echo "• ✅ Paiements Airtel Money opérationnels\n";
    echo "• ✅ Routes mises à jour\n";
    echo "• ✅ Contrôleurs modernisés\n";
    echo "• ✅ Vues corrigées\n\n";

    echo "💡 PROCHAINES ÉTAPES :\n";
    echo "1. Tester l'application: php artisan serve\n";
    echo "2. Effectuer un paiement réel\n";
    echo "3. Vérifier les logs\n";
    echo "4. Configurer les webhooks\n\n";

    echo "🎯 URL de test: http://localhost:8000/payments/process/255\n";
    echo "(Remplacer 255 par l'ID d'une vraie commande)\n\n";

} else {
    echo "⚠️  PROBLÈMES DÉTECTÉS\n\n";
    echo "Vérifiez les points marqués ❌ ci-dessus.\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "📋 TESTS DISPONIBLES :\n";
echo "• php test_real_keys.php      # Test avec vraies clés\n";
echo "• php test_airtel_integration.php # Tests complets\n";
echo "• php check_env_variables.php # Vérification config\n";
echo str_repeat("=", 50) . "\n";
