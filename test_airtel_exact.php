<?php

/**
 * Test exact basé sur l'exemple de documentation Airtel Money
 * Utilise les mêmes paramètres que leur exemple
 */

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

echo "🧪 TEST EXACT SUIVANT LA DOCUMENTATION AIRTEL MONEY\n";
echo str_repeat("=", 60) . "\n\n";

// Configuration selon l'exemple de la doc
$headers = array(
    'Accept' => '*/* ',
    'Content-Type' => 'application/json',
    'X-Country' => 'CG',
    'X-Currency' => 'XAF',
    'Authorization' => 'Bearer UC*******2w' // Token fictif pour test
);

$client = new Client();

// Corps de la requête selon l'exemple exact
$request_body = array(
    "reference" => "Testing transaction",
    "subscriber" => array(
        "country" => "CG",
        "currency" => "XAF",
        "msisdn" => "12****89"
    ),
    "transaction" => array(
        "amount" => 1000,
        "country" => "CG",
        "currency" => "XAF",
        "id" => "random-unique-id"
    )
);

echo "📡 Test 1: URL de la documentation (.cg)\n";
echo "🔗 URL: https://openapiuat.airtel.cg/merchant/v1/payments/\n\n";

try {
    $response = $client->request('POST', 'https://openapiuat.airtel.cg/merchant/v1/payments/', array(
        'headers' => $headers,
        'json' => $request_body
    ));

    echo "✅ RÉPONSE REÇUE:\n";
    echo $response->getBody()->getContents();
    echo "\n";

} catch (BadResponseException $e) {
    echo "❌ ERREUR HTTP:\n";
    echo "📊 Status: " . $e->getResponse()->getStatusCode() . "\n";
    echo "💬 Message: " . $e->getMessage() . "\n";
    echo "📄 Body: " . $e->getResponse()->getBody()->getContents() . "\n";
} catch (Exception $e) {
    echo "❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

echo "📡 Test 2: Notre URL actuelle (.africa)\n";
echo "🔗 URL: https://openapiuat.airtel.africa/merchant/v2/payments/\n\n";

// Test avec notre configuration actuelle
$ourHeaders = array(
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
    'X-Country' => 'CG',
    'X-Currency' => 'XAF',
    'Authorization' => 'Bearer UC*******2w' // Token fictif pour test
);

$ourRequestBody = array(
    "reference" => "Testing transaction",
    "subscriber" => array(
        "country" => "CG",
        "currency" => "XAF",
        "msisdn" => "1266789" // Format sans les ***
    ),
    "transaction" => array(
        "amount" => 1000,
        "country" => "CG",
        "currency" => "XAF",
        "id" => "random-unique-id"
    )
);

try {
    $response = $client->request('POST', 'https://openapiuat.airtel.africa/merchant/v2/payments/', array(
        'headers' => $ourHeaders,
        'json' => $ourRequestBody
    ));

    echo "✅ RÉPONSE REÇUE:\n";
    echo $response->getBody()->getContents();
    echo "\n";

} catch (BadResponseException $e) {
    echo "❌ ERREUR HTTP:\n";
    echo "📊 Status: " . $e->getResponse()->getStatusCode() . "\n";
    echo "💬 Message: " . $e->getMessage() . "\n";
    echo "📄 Body: " . $e->getResponse()->getBody()->getContents() . "\n";
} catch (Exception $e) {
    echo "❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

echo "📡 Test 3: Test de connectivité simple\n";
echo "🔗 URL: https://openapiuat.airtel.cg/\n\n";

// Test de connectivité de base
try {
    $response = $client->request('GET', 'https://openapiuat.airtel.cg/', array(
        'headers' => array('Accept' => '*/*'),
        'timeout' => 10
    ));

    echo "✅ CONNECTIVITÉ OK\n";
    echo "📊 Status: " . $response->getStatusCode() . "\n";

} catch (Exception $e) {
    echo "❌ PROBLÈME DE CONNECTIVITÉ: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 ANALYSE DES RÉSULTATS:\n\n";

echo "🔍 DIFFÉRENCES IDENTIFIÉES:\n";
echo "• URL documentation: openapiuat.airtel.cg (v1)\n";
echo "• Notre URL: openapiuat.airtel.africa (v2)\n";
echo "• Header Accept: '*/* ' vs 'application/json'\n";
echo "• MSISDN format: 12****89 vs 1266789\n\n";

echo "💡 RECOMMANDATIONS:\n";
echo "1. Tester avec l'URL .cg de la documentation\n";
echo "2. Vérifier si c'est v1 ou v2 qui est correct\n";
echo "3. Adapter les headers selon la documentation\n";
echo "4. Obtenir un vrai token d'accès pour tester\n\n";

echo "🔑 POUR TESTER AVEC VRAI TOKEN:\n";
echo "• Remplacer 'UC*******2w' par un vrai token OAuth2\n";
echo "• Obtenir le token via POST /auth/oauth2/token\n";
echo "• Utiliser vos vraies clés API\n\n";

echo str_repeat("=", 60) . "\n";
