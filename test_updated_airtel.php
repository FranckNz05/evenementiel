<?php

/**
 * Test avec les modifications basées sur la documentation Airtel
 * Utilise l'URL .cg et les headers corrects
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🧪 TEST AVEC CONFIGURATION MISE À JOUR\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$config = config('services.airtel');

echo "1️⃣  CONFIGURATION MISE À JOUR :\n";
echo "   🌐 Production: " . ($config['production'] ? 'OUI' : 'NON') . "\n";
echo "   🔗 URL de base: " . ($config['production'] ? 'https://openapi.airtel.cg' : 'https://openapiuat.airtel.cg') . "\n";
echo "   🏛️  Pays: " . ($config['country'] ?? 'N/A') . "\n";
echo "   💰 Devise: " . ($config['currency'] ?? 'N/A') . "\n";
echo "   🔑 Client ID: " . substr($config['client_id'] ?? '', 0, 20) . "...\n\n";

echo "2️⃣  TEST DE L'AIRTEL SERVICE MIS À JOUR :\n";

try {
    $airtelService = app(\App\Services\AirtelMoneyService::class);

    // Tester la récupération du token
    $reflection = new ReflectionClass($airtelService);
    $getAccessTokenMethod = $reflection->getMethod('getAccessToken');
    $getAccessTokenMethod->setAccessible(true);

    echo "   🔑 Tentative de récupération du token...\n";
    $token = $getAccessTokenMethod->invoke($airtelService);

    if ($token) {
        echo "   ✅ Token obtenu avec la nouvelle configuration\n";
        echo "   📏 Longueur: " . strlen($token) . " caractères\n\n";
    } else {
        echo "   ❌ Échec récupération token\n\n";
    }

} catch (Exception $e) {
    echo "   ❌ Erreur avec la nouvelle configuration: " . $e->getMessage() . "\n\n";
}

echo "3️⃣  TEST DE PAIEMENT AVEC NOUVELLE CONFIG :\n";

try {
    $gateway = app(\App\Services\AirtelMoneyGateway::class);

    $testData = [
        'phone' => '057668371',
        'amount' => 100,
        'reference' => 'TEST-UPDATED-' . time(),
        'transaction_id' => 'TEST-UPDATED-' . time()
    ];

    echo "   📱 Données de test: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

    $result = $gateway->createPaymentSession($testData);

    echo "   📡 RÉSULTAT:\n";
    echo "   ✅ Succès: " . ($result['success'] ? 'OUI' : 'NON') . "\n";
    echo "   💬 Message: " . ($result['message'] ?? 'N/A') . "\n";
    echo "   🔄 Status: " . ($result['status'] ?? 'N/A') . "\n";

    if (!$result['success']) {
        echo "   📄 Détails de l'erreur:\n";
        echo "   " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }

} catch (Exception $e) {
    echo "   ❌ Exception lors du test: " . $e->getMessage() . "\n";
    echo "   📝 Trace: " . substr($e->getTraceAsString(), 0, 300) . "...\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 ANALYSE APRÈS MODIFICATIONS :\n\n";

echo "🔧 MODIFICATIONS APPORTÉES :\n";
echo "• URL: openapiuat.airtel.africa → openapiuat.airtel.cg\n";
echo "• Endpoint: /merchant/v2/payments/ → /merchant/v1/payments/\n";
echo "• Header Accept: 'application/json' → '*/* '\n\n";

echo "🎯 PROBLÈME PRINCIPAL :\n";
echo "Les clés API semblent ne pas être reconnues par Airtel.\n";
echo "Cela peut signifier :\n";
echo "• Clés incorrectes ou expirées\n";
echo "• Application non approuvée\n";
echo "• Compte développeur non activé\n\n";

echo "💡 SOLUTIONS RECOMMANDÉES :\n";
echo "1. Vérifier les clés API avec le support Airtel\n";
echo "2. S'assurer que l'application est approuvée\n";
echo "3. Tester avec des clés de test officielles\n";
echo "4. Contacter le support développeur Airtel Money\n\n";

echo "📞 POUR OBTENIR DES CLÉS VALIDES :\n";
echo "• Aller sur le portail développeur Airtel Money\n";
echo "• Créer une application pour le Congo (CG)\n";
echo "• Obtenir les clés API de test/production\n";
echo "• Configurer l'URL de callback\n\n";

echo str_repeat("=", 50) . "\n";
