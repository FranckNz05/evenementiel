<?php

/**
 * Script de diagnostic pour l'API Airtel Money
 * Teste la connectivité de base et diagnostique les problèmes d'authentification
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 DIAGNOSTIC API AIRTEL MONEY\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Vérifier la configuration
echo "1️⃣  CONFIGURATION ACTUELLE :\n";
$config = config('services.airtel');
echo "   🌐 Production: " . ($config['production'] ? 'OUI' : 'NON') . "\n";
echo "   🏛️  Pays: " . ($config['country'] ?? 'N/A') . "\n";
echo "   💰 Devise: " . ($config['currency'] ?? 'N/A') . "\n";
echo "   🔑 Client ID: " . substr($config['client_id'] ?? '', 0, 20) . "...\n";
echo "   🔐 Client Secret: " . substr($config['client_secret'] ?? '', 0, 20) . "...\n\n";

// Test 2: Déterminer l'URL de base
echo "2️⃣  URL DE L'API :\n";
$isProduction = $config['production'] ?? false;
$baseUrl = $isProduction
    ? 'https://openapi.airtel.africa'
    : 'https://openapiuat.airtel.africa';

echo "   🔗 URL de base: $baseUrl\n";
echo "   🎯 Environnement: " . ($isProduction ? 'PRODUCTION' : 'TEST/UAT') . "\n\n";

// Test 3: Tester la connectivité réseau de base
echo "3️⃣  TEST DE CONNECTIVITÉ RÉSEAU :\n";

try {
    $startTime = microtime(true);
    $response = Http::timeout(10)->get($baseUrl . '/health'); // Essayer un endpoint de santé
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    echo "   📡 Requête GET vers $baseUrl/health\n";
    echo "   ⏱️  Temps de réponse: {$duration}ms\n";
    echo "   📊 Statut: " . $response->status() . "\n";

    if ($response->successful()) {
        echo "   ✅ Connectivité réseau OK\n";
    } else {
        echo "   ⚠️  Réponse inattendue: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur de connectivité: " . $e->getMessage() . "\n";
    echo "   💡 Vérifiez votre connexion internet\n";
}

echo "\n";

// Test 4: Tester l'authentification OAuth2 détaillée
echo "4️⃣  DIAGNOSTIC AUTHENTIFICATION OAUTH2 :\n";

$clientId = $config['client_id'];
$clientSecret = $config['client_secret'];

echo "   🔑 Tentative d'authentification...\n";
echo "   📝 Client ID: " . substr($clientId, 0, 20) . "...\n";
echo "   🔐 Client Secret: " . substr($clientSecret, 0, 20) . "...\n\n";

try {
    $authUrl = $baseUrl . '/auth/oauth2/token';

    echo "   📡 Requête POST vers: $authUrl\n";

    $startTime = microtime(true);
    $response = Http::asForm()
        ->timeout(15)
        ->post($authUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    echo "   ⏱️  Temps de réponse: {$duration}ms\n";
    echo "   📊 Code HTTP: " . $response->status() . "\n\n";

    if ($response->successful()) {
        $data = $response->json();
        echo "   ✅ AUTHENTIFICATION RÉUSSIE !\n";
        echo "   🎫 Token Type: " . ($data['token_type'] ?? 'N/A') . "\n";
        echo "   ⏰ Expires In: " . ($data['expires_in'] ?? 'N/A') . " secondes\n";
        echo "   🔑 Token (aperçu): " . substr($data['access_token'] ?? '', 0, 30) . "...\n";

        // Test 5: Tester les clés de chiffrement
        echo "\n5️⃣  TEST DES CLÉS DE CHIFFREMENT :\n";

        $accessToken = $data['access_token'];
        $headers = [
            'Accept' => 'application/json',
            'X-Country' => $config['country'],
            'X-Currency' => $config['currency'],
            'Authorization' => 'Bearer ' . $accessToken,
        ];

        $encryptionUrl = $baseUrl . '/v1/rsa/encryption-keys';
        echo "   📡 Requête GET vers: $encryptionUrl\n";

        $startTime = microtime(true);
        $response = Http::withHeaders($headers)->get($encryptionUrl);
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        echo "   ⏱️  Temps de réponse: {$duration}ms\n";
        echo "   📊 Code HTTP: " . $response->status() . "\n";

        if ($response->successful()) {
            $encryptionData = $response->json();
            echo "   ✅ CLÉS RSA RÉCUPÉRÉES !\n";
            echo "   🆔 Key ID: " . ($encryptionData['data']['key_id'] ?? 'N/A') . "\n";
            echo "   📅 Valid Until: " . ($encryptionData['data']['valid_upto'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ ÉCHEC RÉCUPÉRATION CLÉS RSA\n";
            echo "   📄 Réponse: " . $response->body() . "\n";
        }

    } else {
        echo "   ❌ ÉCHEC AUTHENTIFICATION\n";
        echo "   📄 Réponse complète: " . $response->body() . "\n";

        $errorData = $response->json();
        if ($errorData) {
            echo "   🔍 Détails de l'erreur:\n";
            echo "      - Error: " . ($errorData['error'] ?? 'N/A') . "\n";
            echo "      - Description: " . ($errorData['error_description'] ?? 'N/A') . "\n";
        }

        echo "\n🔧 DIAGNOSTIC :\n";

        if ($response->status() === 401) {
            echo "   🚫 ERREUR 401: Authentification invalide\n";
            echo "   💡 Causes possibles:\n";
            echo "      • Clés API incorrectes ou expirées\n";
            echo "      • Clés pour le mauvais environnement (prod/test)\n";
            echo "      • Application non approuvée par Airtel\n";
            echo "      • Compte développeur non activé\n";
        } elseif ($response->status() === 400) {
            echo "   🚫 ERREUR 400: Requête malformée\n";
            echo "   💡 Vérifiez le format des paramètres\n";
        } elseif ($response->status() === 403) {
            echo "   🚫 ERREUR 403: Accès refusé\n";
            echo "   💡 Vérifiez les permissions de l'application\n";
        } else {
            echo "   🚫 ERREUR " . $response->status() . ": " . $response->statusText() . "\n";
        }
    }

} catch (Exception $e) {
    echo "   ❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
    echo "   💡 Vérifiez votre connexion internet et la configuration réseau\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 RECOMMANDATIONS :\n";

if (isset($response) && $response->status() === 401) {
    echo "🔑 PROBLÈME D'AUTHENTIFICATION :\n";
    echo "• Vérifiez que vos clés API sont correctes\n";
    echo "• Assurez-vous d'utiliser les bonnes clés pour l'environnement choisi\n";
    echo "• Contactez le support Airtel Money pour vérifier votre compte développeur\n";
    echo "• Vérifiez que votre application est approuvée et activée\n";
}

echo "\n🔍 PROCHAINES ÉTAPES :\n";
echo "1. Corrigez les problèmes identifiés ci-dessus\n";
echo "2. Relancez ce diagnostic: php diagnose_airtel_api.php\n";
echo "3. Si le problème persiste, contactez le support Airtel Money\n";

echo "\n📞 SUPPORT AIRTEL MONEY :\n";
echo "Consultez la documentation développeur sur le portail Airtel\n";

echo str_repeat("=", 50) . "\n";
