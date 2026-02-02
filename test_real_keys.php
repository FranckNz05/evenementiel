<?php

/**
 * Test avec les vraies clés API Airtel Money
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🔑 TEST AVEC VRAIES CLÉS API AIRTEL MONEY\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  VÉRIFICATION DES CLÉS CONFIGURÉES :\n";

// Vérifier directement dans le fichier .env
$envFile = __DIR__ . '/.env';
$envContent = file_get_contents($envFile);

$clientId = null;
$clientSecret = null;

foreach (file($envFile, FILE_IGNORE_NEW_LINES) as $line) {
    $line = trim($line);
    if (preg_match('/^AIRTEL_CLIENT_ID=(.+)$/', $line, $matches)) {
        $clientId = $matches[1];
    }
    if (preg_match('/^AIRTEL_CLIENT_SECRET=(.+)$/', $line, $matches)) {
        $clientSecret = $matches[1];
    }
}

echo "🔑 Client ID: " . substr($clientId ?? 'N/A', 0, 20) . "...\n";
echo "🔐 Client Secret: " . substr($clientSecret ?? 'N/A', 0, 20) . "...\n\n";

echo "2️⃣  TEST D'AUTHENTIFICATION DIRECT :\n";

if ($clientId && $clientSecret) {
    $baseUrl = 'https://openapiuat.airtel.cg'; // URL selon la documentation

    echo "📡 Test OAuth2 vers: $baseUrl/auth/oauth2/token\n\n";

    try {
        $response = \Illuminate\Support\Facades\Http::asForm()->post($baseUrl . '/auth/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        echo "📊 Code HTTP: " . $response->status() . "\n";

        if ($response->successful()) {
            $data = $response->json();
            echo "✅ AUTHENTIFICATION RÉUSSIE !\n\n";

            echo "🎫 Token Type: " . ($data['token_type'] ?? 'N/A') . "\n";
            echo "⏰ Expires In: " . ($data['expires_in'] ?? 'N/A') . " secondes\n";

            $accessToken = $data['access_token'] ?? null;
            if ($accessToken) {
                echo "🔑 Token obtenu (aperçu): " . substr($accessToken, 0, 30) . "...\n\n";

                echo "3️⃣  TEST DE PAIEMENT AVEC TOKEN RÉEL :\n";

                $testPaymentData = [
                    'phone' => '057668371',
                    'amount' => 100,
                    'reference' => 'TESTREAL' . time(),
                    'transaction_id' => 'TESTREAL' . time()
                ];

                // Créer le payload selon la documentation
                $payload = [
                    'reference' => $testPaymentData['reference'],
                    'subscriber' => [
                        'country' => 'CG',
                        'currency' => 'XAF',
                        'msisdn' => $testPaymentData['phone']
                    ],
                    'transaction' => [
                        'amount' => $testPaymentData['amount'],
                        'country' => 'CG',
                        'currency' => 'XAF',
                        'id' => $testPaymentData['transaction_id']
                    ]
                ];

                $headers = [
                    'Accept' => '*/* ',
                    'Content-Type' => 'application/json',
                    'X-Country' => 'CG',
                    'X-Currency' => 'XAF',
                    'Authorization' => 'Bearer ' . $accessToken,
                ];

                echo "💰 Tentative de paiement de {$testPaymentData['amount']} FCFA...\n";
                echo "📱 Numéro: {$testPaymentData['phone']}\n\n";

                $paymentResponse = \Illuminate\Support\Facades\Http::withHeaders($headers)
                    ->post($baseUrl . '/merchant/v1/payments/', $payload);

                echo "📊 Code HTTP paiement: " . $paymentResponse->status() . "\n";

                if ($paymentResponse->successful()) {
                    $paymentData = $paymentResponse->json();
                    echo "✅ PAIEMENT INITIÉ AVEC SUCCÈS !\n\n";

                    echo "📄 Réponse de l'API:\n";
                    echo json_encode($paymentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

                    if (isset($paymentData['data']['transaction']['id'])) {
                        $transactionId = $paymentData['data']['transaction']['id'];
                        echo "🆔 Transaction ID: $transactionId\n";
                        echo "🎯 Statut: " . ($paymentData['data']['transaction']['status'] ?? 'N/A') . "\n\n";

                        echo "4️⃣  TEST DE VÉRIFICATION DE STATUT :\n";

                        $statusResponse = \Illuminate\Support\Facades\Http::withHeaders($headers)
                            ->get($baseUrl . '/standard/v1/payments/' . $transactionId);

                        echo "📊 Code HTTP vérification: " . $statusResponse->status() . "\n";

                        if ($statusResponse->successful()) {
                            $statusData = $statusResponse->json();
                            echo "✅ STATUT RÉCUPÉRÉ !\n\n";
                            echo json_encode($statusData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
                        } else {
                            echo "❌ ÉCHEC VÉRIFICATION STATUT\n";
                            echo "📄 Erreur: " . $statusResponse->body() . "\n";
                        }
                    }

                } else {
                    $errorData = $paymentResponse->json();
                    echo "❌ ÉCHEC PAIEMENT\n";
                    echo "📄 Réponse d'erreur:\n";
                    echo json_encode($errorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

                    if (isset($errorData['status'])) {
                        echo "🔍 Code d'erreur: " . ($errorData['status']['response_code'] ?? $errorData['status']['result_code'] ?? 'N/A') . "\n";
                        echo "💬 Message: " . ($errorData['status']['message'] ?? 'N/A') . "\n";
                    }
                }

            } else {
                echo "❌ Aucun token d'accès reçu\n";
            }

        } else {
            $errorData = $response->json();
            echo "❌ ÉCHEC AUTHENTIFICATION\n";
            echo "📄 Erreur détaillée:\n";
            echo json_encode($errorData, JSON_PRETTY_PRINT) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
    }

} else {
    echo "❌ CLÉS API NON TROUVÉES DANS .env\n";
    echo "Vérifiez que AIRTEL_CLIENT_ID et AIRTEL_CLIENT_SECRET sont configurés.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 RÉSULTATS :\n\n";

if (isset($paymentResponse) && $paymentResponse->successful()) {
    echo "🎉 SUCCÈS ! L'intégration Airtel Money fonctionne avec les vraies clés !\n";
    echo "🚀 L'application peut maintenant accepter des paiements réels.\n\n";

    echo "💡 PROCHAINES ÉTAPES :\n";
    echo "1. Configurer l'URL de webhook dans Airtel Money\n";
    echo "2. Tester avec un vrai numéro de téléphone\n";
    echo "3. Surveiller les logs pour les transactions\n";
    echo "4. Passer en production quand prêt\n\n";

} else {
    echo "⚠️  LES CLÉS API SONT CONFIGURÉES MAIS L'AUTHENTIFICATION ÉCHOUE\n\n";

    echo "🔧 POSSIBLES RAISONS :\n";
    echo "• Clés pour le mauvais environnement (devraient être pour TEST/UAT)\n";
    echo "• Application pas encore approuvée par Airtel\n";
    echo "• Compte développeur pas activé\n";
    echo "• Limites ou restrictions sur le compte\n\n";

    echo "📞 CONTACTER AIRTEL MONEY :\n";
    echo "• Fournir les clés API pour vérification\n";
    echo "• Demander l'activation du compte développeur\n";
    echo "• Vérifier l'approbation de l'application\n\n";
}

echo str_repeat("=", 50) . "\n";
