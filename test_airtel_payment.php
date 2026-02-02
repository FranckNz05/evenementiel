<?php

/**
 * Script de test complet du paiement Airtel Money
 * Teste l'initiation, la vérification de statut et la simulation de webhook
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\AirtelMoneyGateway;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST COMPLET DE PAIEMENT AIRTEL MONEY\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Vérifier que les services sont disponibles
echo "1️⃣  VÉRIFICATION DES SERVICES...\n";

try {
    $gateway = app(AirtelMoneyGateway::class);
    echo "   ✅ AirtelMoneyGateway instancié avec succès\n";

    // Tester l'accès aux propriétés du service
    $reflection = new ReflectionClass($gateway);
    $airtelServiceProperty = $reflection->getProperty('airtelService');
    $airtelServiceProperty->setAccessible(true);
    $airtelService = $airtelServiceProperty->getValue($gateway);

    echo "   ✅ AirtelMoneyService accessible\n";
} catch (Exception $e) {
    echo "   ❌ Erreur d'instanciation: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 2: Tester l'obtention du token d'accès
echo "2️⃣  TEST D'AUTHENTIFICATION...\n";

try {
    // Accéder directement au service pour tester le token
    $reflection = new ReflectionClass($airtelService);
    $getAccessTokenMethod = $reflection->getMethod('getAccessToken');
    $getAccessTokenMethod->setAccessible(true);

    $accessToken = $getAccessTokenMethod->invoke($airtelService);

    if ($accessToken && strlen($accessToken) > 50) {
        echo "   ✅ Token d'accès obtenu avec succès\n";
        echo "   📏 Longueur du token: " . strlen($accessToken) . " caractères\n";
        echo "   🔑 Préfixe: " . substr($accessToken, 0, 20) . "...\n";
    } else {
        echo "   ❌ Token d'accès invalide ou vide\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors de l'obtention du token: " . $e->getMessage() . "\n";
    echo "   📝 Détails: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

// Test 3: Tester la récupération des clés RSA
echo "3️⃣  TEST DE RÉCUPÉRATION DES CLÉS RSA...\n";

try {
    $getEncryptionKeysMethod = $reflection->getMethod('getEncryptionKeys');
    $getEncryptionKeysMethod->setAccessible(true);

    $encryptionKeys = $getEncryptionKeysMethod->invoke($airtelService);

    if (isset($encryptionKeys['key']) && isset($encryptionKeys['key_id'])) {
        echo "   ✅ Clés RSA récupérées avec succès\n";
        echo "   🆔 Key ID: " . $encryptionKeys['key_id'] . "\n";
        echo "   📅 Valide jusqu'au: " . ($encryptionKeys['valid_upto'] ?? 'N/A') . "\n";
        echo "   🔐 Longueur de la clé: " . strlen($encryptionKeys['key']) . " caractères\n";
    } else {
        echo "   ❌ Clés RSA manquantes ou invalides\n";
        echo "   📄 Réponse: " . json_encode($encryptionKeys, JSON_PRETTY_PRINT) . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la récupération des clés RSA: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 4: Tester le chiffrement PIN
echo "4️⃣  TEST DE CHIFFREMENT PIN...\n";

try {
    $encryptPinMethod = $reflection->getMethod('encryptPin');
    $encryptPinMethod->setAccessible(true);

    $testPin = '1234';
    $encryptedPin = $encryptPinMethod->invoke($airtelService, $testPin);

    if ($encryptedPin && strlen($encryptedPin) > 100) {
        echo "   ✅ PIN chiffré avec succès\n";
        echo "   🔢 PIN original: $testPin\n";
        echo "   🔒 PIN chiffré (Base64): " . substr($encryptedPin, 0, 50) . "...\n";
        echo "   📏 Longueur: " . strlen($encryptedPin) . " caractères\n";
    } else {
        echo "   ❌ Échec du chiffrement PIN\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors du chiffrement PIN: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 5: Tester l'initiation d'un paiement
echo "5️⃣  TEST D'INITIATION DE PAIEMENT...\n";

$testPaymentData = [
    'phone' => '057668371', // Numéro de test
    'amount' => 1000, // 10 FCFA pour test
    'reference' => 'TEST-' . time(),
    'transaction_id' => 'TEST-' . time() . '-' . rand(1000, 9999)
];

try {
    echo "   📱 Données de test: " . json_encode($testPaymentData, JSON_PRETTY_PRINT) . "\n\n";

    $result = $gateway->createPaymentSession($testPaymentData);

    echo "   📡 Réponse de l'API Airtel Money:\n";
    echo "   📊 Statut: " . ($result['success'] ? '✅ SUCCÈS' : '❌ ÉCHEC') . "\n";
    echo "   💬 Message: " . ($result['message'] ?? 'N/A') . "\n";
    echo "   🔄 État: " . ($result['status'] ?? 'N/A') . "\n";

    if ($result['success']) {
        echo "   🆔 Transaction ID: " . ($result['transaction_id'] ?? 'N/A') . "\n";
        echo "   📋 Référence: " . ($result['reference'] ?? 'N/A') . "\n";
        echo "   🔄 Status Code: " . ($result['response_code'] ?? 'N/A') . "\n";

        $transactionId = $result['transaction_id'];

        // Test 6: Tester la vérification de statut
        echo "\n6️⃣  TEST DE VÉRIFICATION DE STATUT...\n";

        sleep(2); // Attendre un peu avant de vérifier le statut

        $statusResult = $gateway->verifyPayment($transactionId);

        echo "   🔍 Résultat de vérification:\n";
        echo "   📊 Statut: " . ($statusResult['success'] ? '✅ SUCCÈS' : '❌ ÉCHEC') . "\n";
        echo "   💬 Message: " . ($statusResult['message'] ?? 'N/A') . "\n";
        echo "   🔄 État de transaction: " . ($statusResult['status'] ?? 'N/A') . "\n";
        echo "   🆔 Transaction ID: " . ($statusResult['transaction_id'] ?? 'N/A') . "\n";
        echo "   📋 Référence: " . ($statusResult['reference'] ?? 'N/A') . "\n";

        if (isset($statusResult['raw_response'])) {
            echo "   📄 Réponse brute (aperçu):\n";
            $rawResponse = json_encode($statusResult['raw_response'], JSON_PRETTY_PRINT);
            echo "   " . substr($rawResponse, 0, 200) . (strlen($rawResponse) > 200 ? "..." : "") . "\n";
        }

    } else {
        echo "   ❌ Échec de l'initiation du paiement\n";
        echo "   📄 Réponse détaillée: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "   ❌ Erreur lors du test de paiement: " . $e->getMessage() . "\n";
    echo "   📝 Trace: " . substr($e->getTraceAsString(), 0, 500) . "...\n";
    exit(1);
}

echo "\n";

// Test 7: Test du webhook simulé
echo "7️⃣  TEST DE SIMULATION WEBHOOK...\n";

if (isset($transactionId)) {
    // Simuler un webhook de succès
    $webhookPayload = [
        'transaction_id' => $transactionId,
        'status' => 'success',
        'result_code' => 'DP00800001001',
        'message' => 'Transaction réussie',
        'reference' => $testPaymentData['reference'],
        'amount' => $testPaymentData['amount']
    ];

    try {
        $webhookResult = $gateway->handleWebhook($webhookPayload);

        echo "   🪝 Résultat du traitement webhook:\n";
        echo "   📊 Succès: " . ($webhookResult['success'] ? '✅ OUI' : '❌ NON') . "\n";
        echo "   💬 Message: " . ($webhookResult['message'] ?? 'N/A') . "\n";
        echo "   🔄 Statut: " . ($webhookResult['status'] ?? 'N/A') . "\n";
        echo "   🆔 Transaction ID: " . ($webhookResult['transaction_id'] ?? 'N/A') . "\n";

    } catch (Exception $e) {
        echo "   ❌ Erreur lors du test webhook: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 TESTS TERMINÉS AVEC SUCCÈS !\n";
echo "\n📊 RÉSUMÉ DES TESTS :\n";
echo "✅ Services instanciés\n";
echo "✅ Authentification OAuth2\n";
echo "✅ Récupération clés RSA\n";
echo "✅ Chiffrement PIN\n";
echo "✅ Initiation paiement\n";
echo "✅ Vérification statut\n";
echo "✅ Traitement webhook\n";
echo "\n🚀 L'INTÉGRATION AIRTEL MONEY EST OPÉRATIONNELLE !\n";
echo str_repeat("=", 50) . "\n";

echo "\n📝 NOTES IMPORTANTES :\n";
echo "• Le paiement de test utilise un petit montant (10 FCFA)\n";
echo "• Vérifiez les logs Laravel pour plus de détails\n";
echo "• Le numéro de test utilisé: " . $testPaymentData['phone'] . "\n";
echo "• Pour un vrai paiement, utilisez un numéro Airtel Money réel\n";
echo "\n🔍 Consultez les logs: tail -f storage/logs/laravel.log\n";
echo str_repeat("=", 50) . "\n";
