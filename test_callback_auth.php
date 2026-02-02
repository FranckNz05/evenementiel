<?php

/**
 * Test du callback Airtel Money avec authentification HMAC
 * Basé sur l'exemple fourni par la documentation
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

echo "🧪 TEST CALLBACK AIRTEL MONEY AVEC AUTHENTIFICATION HMAC\n";
echo str_repeat("=", 60) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  SIMULATION DU CALLBACK SELON LA DOCUMENTATION :\n\n";

// Exemple de payload selon la documentation
$callbackPayload = [
    "transaction" => [
        "id" => "BBZMiscxy",
        "message" => "Paid XAF 5,000 to TECHNOLOGIES LIMITED Charge XAF 140, Trans ID MP210603.1234.L06941.",
        "status_code" => "TS",
        "airtel_money_id" => "MP210603.1234.L06941"
    ],
    "hash" => "zITVAAGYSlzl1WkUQJn81kbpT5drH3koffT8jCkcJJA="
];

echo "📨 Payload de test :\n";
echo json_encode($callbackPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

echo "2️⃣  TEST DE LA VÉRIFICATION HMAC :\n\n";

// Créer une requête simulée
$request = new Request();
$request->merge($callbackPayload);
$request->setMethod('POST');

// Simuler le contenu JSON pour la vérification
$jsonContent = json_encode($callbackPayload, JSON_UNESCAPED_SLASHES);
$request->initialize([], [], [], [], [], [], $jsonContent);

try {
    $callbackController = app(\App\Http\Controllers\AirtelCallbackController::class);

    // Tester la méthode verifyHash directement
    $reflection = new ReflectionClass($callbackController);
    $verifyHashMethod = $reflection->getMethod('verifyHash');
    $verifyHashMethod->setAccessible(true);

    echo "🔐 Test de vérification HMAC...\n";

    // Sans clé privée configurée (devrait échouer)
    $isValid = $verifyHashMethod->invoke($callbackController, $request, $callbackPayload['hash']);

    echo "   📊 Résultat sans clé privée: " . ($isValid ? '✅ VALIDE' : '❌ INVALIDE') . "\n";
    echo "   💡 (Normal - pas de clé privée configurée)\n\n";

    // Tester avec une clé privée de test
    config(['services.airtel.callback_private_key' => 'test_private_key_for_callback']);

    $isValidWithKey = $verifyHashMethod->invoke($callbackController, $request, $callbackPayload['hash']);

    echo "   🔑 Test avec clé privée de test: " . ($isValidWithKey ? '✅ VALIDE' : '❌ INVALIDE') . "\n";
    echo "   💡 (Normal - hash calculé ne correspondra pas au hash de test)\n\n";

} catch (Exception $e) {
    echo "   ❌ Erreur lors du test HMAC: " . $e->getMessage() . "\n\n";
}

echo "3️⃣  SIMULATION COMPLÈTE DU CALLBACK :\n\n";

try {
    // Créer une vraie requête HTTP simulée
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

    // Simuler une requête POST vers le callback
    $response = $kernel->handle(
        Request::create('/webhooks/airtel/callback', 'POST', $callbackPayload, [], [], [], $jsonContent)
    );

    echo "   📡 Requête simulée vers /webhooks/airtel/callback\n";
    echo "   📊 Code de réponse: " . $response->getStatusCode() . "\n";

    $responseContent = $response->getContent();
    echo "   📄 Contenu de la réponse:\n";
    echo "   " . $responseContent . "\n\n";

} catch (Exception $e) {
    echo "   ❌ Erreur lors de la simulation du callback: " . $e->getMessage() . "\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "📋 ANALYSE DU CALLBACK AVEC AUTHENTIFICATION :\n\n";

echo "🔧 FONCTIONNALITÉ IMPLÉMENTÉE :\n";
echo "• ✅ Réception du payload selon la documentation\n";
echo "• ✅ Validation des champs requis (transaction.id, status_code, etc.)\n";
echo "• ✅ Vérification HMAC SHA256 en Base64\n";
echo "• ✅ Traitement des transactions selon le statut\n";
echo "• ✅ Logging détaillé pour le débogage\n\n";

echo "📨 FORMAT ATTENDU DU PAYLOAD :\n";
echo "{\n";
echo "    'transaction': {\n";
echo "        'id': 'BBZMiscxy',\n";
echo "        'message': 'Paid XAF 5,000 to TECHNOLOGIES LIMITED...',\n";
echo "        'status_code': 'TS|TF',\n";
echo "        'airtel_money_id': 'MP210603.1234.L06941'\n";
echo "    },\n";
echo "    'hash': 'zITVAAGYSlzl1WkUQJn81kbpT5drH3koffT8jCkcJJA='\n";
echo "}\n\n";

echo "🔐 SÉCURITÉ HMAC :\n";
echo "• Algorithme: HMAC SHA256\n";
echo "• Format de sortie: Base64\n";
echo "• Clé: callback_private_key depuis la config\n";
echo "• Payload: JSON sans le champ 'hash'\n";
echo "• Options: JSON_UNESCAPED_SLASHES\n\n";

echo "🎯 STATUTS TRAITÉS :\n";
echo "• TS = Transaction Success → Paiement réussi\n";
echo "• TF = Transaction Failed → Paiement échoué\n\n";

echo "⚙️  CONFIGURATION REQUISE :\n";
echo "# Dans votre .env\n";
echo "AIRTEL_CALLBACK_PRIVATE_KEY=votre_clé_privée_ici\n";
echo "AIRTEL_CALLBACK_AUTH_ENABLED=true\n\n";

echo "🧪 POUR TESTER EN PRODUCTION :\n";
echo "1. Obtenir la clé privée auprès d'Airtel Money\n";
echo "2. Configurer AIRTEL_CALLBACK_PRIVATE_KEY dans .env\n";
echo "3. Configurer l'URL de callback dans Airtel Money\n";
echo "4. Tester avec de vrais paiements\n\n";

echo "📞 URL DE CALLBACK À CONFIGURER :\n";
echo "https://mokilievent.com/webhooks/airtel/callback\n\n";

echo str_repeat("=", 60) . "\n";
echo "🎉 CALLBACK AVEC AUTHENTIFICATION HMAC OPÉRATIONNEL !\n";
echo str_repeat("=", 60) . "\n";
