<?php

/**
 * Test de la correction du format de référence
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🧪 TEST DE CORRECTION DU FORMAT DE RÉFÉRENCE\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  TEST DE FORMAT DE RÉFÉRENCE :\n";

// Fonction pour nettoyer la référence comme dans le service
function cleanReference($ref) {
    // S'assurer que la référence est alphanumérique uniquement
    $cleanRef = preg_replace('/[^A-Za-z0-9]/', '', $ref);
    // Tronquer à 64 caractères max si nécessaire
    return substr($cleanRef, 0, 64);
}

$testReferences = [
    'TEST-UPDATED-1769692922',  // Ancien format avec tirets
    'TXN' . strtoupper(\Illuminate\Support\Str::random(12)),  // Nouveau format
    'REF_123-ABC',  // Avec underscore et tiret
    'REF 123 ABC DEF GHI JKL MNO PQR STU VWX YZ 123456789',  // Trop long avec espaces
];

foreach ($testReferences as $i => $ref) {
    $cleaned = cleanReference($ref);
    $isValid = preg_match('/^[A-Za-z0-9]{4,64}$/', $cleaned);

    echo "   Test " . ($i + 1) . ":\n";
    echo "   📝 Original: '$ref'\n";
    echo "   🔧 Nettoyé: '$cleaned'\n";
    echo "   ✅ Valide: " . ($isValid ? 'OUI' : 'NON') . " (longueur: " . strlen($cleaned) . ")\n\n";
}

echo "2️⃣  TEST DE PAIEMENT AVEC RÉFÉRENCE CORRECTE :\n";

try {
    $gateway = app(\App\Services\AirtelMoneyGateway::class);

    $testData = [
        'phone' => '057668371',
        'amount' => 100,
        'reference' => 'TXNABC123DEF456',  // Format alphanumérique propre
        'transaction_id' => 'TXNABC123DEF456'
    ];

    echo "   📱 Données de test: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

    $result = $gateway->createPaymentSession($testData);

    echo "   📡 RÉSULTAT:\n";
    echo "   ✅ Succès: " . ($result['success'] ? 'OUI' : 'NON') . "\n";
    echo "   💬 Message: " . ($result['message'] ?? 'N/A') . "\n";
    echo "   🔄 Status: " . ($result['status'] ?? 'N/A') . "\n";

    if ($result['success']) {
        echo "   🆔 Transaction ID: " . ($result['transaction_id'] ?? 'N/A') . "\n";
        echo "   📋 Référence: " . ($result['reference'] ?? 'N/A') . "\n";
        echo "   🎉 PAIEMENT INITIÉ AVEC SUCCÈS !\n";
    } else {
        echo "   📄 Détails de l'erreur:\n";
        if (isset($result['raw_response']['status']['message'])) {
            echo "   🔍 Message API: " . $result['raw_response']['status']['message'] . "\n";
        }
        echo "   💡 Code d'erreur: " . ($result['response_code'] ?? 'N/A') . "\n";
    }

} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 RÉSUMÉ :\n\n";

echo "🔧 CORRECTION APPORTÉE :\n";
echo "• Format de référence: alphanumérique uniquement\n";
echo "• Longueur: 4-64 caractères\n";
echo "• Suppression des tirets et caractères spéciaux\n\n";

echo "🎯 PROCHAINES ÉTAPES :\n";
echo "1. Tester avec une référence valide\n";
echo "2. Si ça fonctionne, l'intégration est prête\n";
echo "3. Si ça échoue encore, contacter Airtel pour les clés\n\n";

echo "📝 NOTE :\n";
echo "Même si l'API retourne une erreur d'authentification,\n";
echo "nous avons maintenant le bon format de données !\n\n";

echo str_repeat("=", 50) . "\n";
