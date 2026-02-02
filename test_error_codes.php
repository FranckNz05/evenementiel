<?php

/**
 * Test des codes d'erreur Airtel Money selon la documentation officielle
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

echo "🧪 TEST DES CODES D'ERREUR AIRTEL MONEY\n";
echo str_repeat("=", 50) . "\n\n";

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "1️⃣  VÉRIFICATION DES CODES D'ERREUR OFFICIELS :\n\n";

// Codes d'erreur selon la documentation officielle
$officialErrorCodes = [
    'DP00800001000' => [
        'expected' => 'ambiguous',
        'description' => 'The transaction is still processing and is in ambiguous state. Please do the transaction enquiry to fetch the transaction status.',
        'retry' => true
    ],
    'DP00800001001' => [
        'expected' => 'success',
        'description' => 'Transaction is successful.',
        'retry' => false
    ],
    'DP00800001002' => [
        'expected' => 'failed',
        'description' => 'Incorrect pin has been entered.',
        'retry' => true
    ],
    'DP00800001003' => [
        'expected' => 'failed',
        'description' => 'Exceeds withdrawal amount limit(s) / Withdrawal amount limit exceeded. The User has exceeded their wallet allowed transaction limit.',
        'retry' => false
    ],
    'DP00800001004' => [
        'expected' => 'failed',
        'description' => 'Invalid Amount. The amount User is trying to transfer is less than the minimum amount allowed.',
        'retry' => false
    ],
    'DP00800001005' => [
        'expected' => 'failed',
        'description' => 'Transaction ID is invalid. User didn\'t enter the pin.',
        'retry' => true
    ],
    'DP00800001006' => [
        'expected' => 'pending',
        'description' => 'In process. Transaction in pending state. Please check after sometime.',
        'retry' => true
    ],
    'DP00800001007' => [
        'expected' => 'failed',
        'description' => 'Not enough balance. User wallet does not have enough money to cover the payable amount.',
        'retry' => false
    ],
    'DP00800001008' => [
        'expected' => 'refused',
        'description' => 'Refused. The transaction was refused.',
        'retry' => false
    ],
    'DP00800001010' => [
        'expected' => 'failed',
        'description' => 'Transaction not permitted to Payee. Payee is already initiated for churn or barred or not registered on Airtel Money platform.',
        'retry' => false
    ],
    'DP00800001024' => [
        'expected' => 'timeout',
        'description' => 'Transaction Timed Out. The transaction was timed out.',
        'retry' => true
    ],
    'DP00800001025' => [
        'expected' => 'not_found',
        'description' => 'Transaction Not Found. The transaction was not found.',
        'retry' => false
    ]
];

try {
    $airtelService = app(\App\Services\AirtelMoneyService::class);

    // Accéder aux codes d'erreur via reflection
    $reflection = new ReflectionClass($airtelService);
    $errorCodesProperty = $reflection->getProperty('errorCodes');
    $errorCodesProperty->setAccessible(true);
    $implementedErrorCodes = $errorCodesProperty->getValue($airtelService);

    echo "📋 COMPARAISON AVEC L'IMPLÉMENTATION :\n\n";

    $allCorrect = true;
    $missingCodes = [];
    $incorrectCodes = [];

    foreach ($officialErrorCodes as $code => $expectedData) {
        if (!isset($implementedErrorCodes[$code])) {
            $missingCodes[] = $code;
            echo "❌ $code : MANQUANT\n";
            $allCorrect = false;
            continue;
        }

        $implemented = $implementedErrorCodes[$code];

        $statusCorrect = $implemented['status'] === $expectedData['expected'];
        $messageCorrect = $implemented['message'] === $expectedData['description'];
        $retryCorrect = $implemented['retry'] === $expectedData['retry'];

        if ($statusCorrect && $messageCorrect && $retryCorrect) {
            echo "✅ $code : {$implemented['status']} - {$implemented['message']}\n";
        } else {
            $incorrectCodes[] = $code;
            echo "⚠️  $code : PARTIELLEMENT CORRECT\n";

            if (!$statusCorrect) {
                echo "   📊 Status: {$implemented['status']} (attendu: {$expectedData['expected']})\n";
            }
            if (!$messageCorrect) {
                echo "   💬 Message: {$implemented['message']} \n   (attendu: {$expectedData['description']})\n";
            }
            if (!$retryCorrect) {
                echo "   🔄 Retry: " . ($implemented['retry'] ? 'true' : 'false') . " (attendu: " . ($expectedData['retry'] ? 'true' : 'false') . ")\n";
            }

            $allCorrect = false;
        }
    }

    // Vérifier s'il y a des codes supplémentaires dans l'implémentation
    $extraCodes = array_diff(array_keys($implementedErrorCodes), array_keys($officialErrorCodes));
    if (!empty($extraCodes)) {
        echo "\n🔍 CODES SUPPLÉMENTAIRES DANS L'IMPLÉMENTATION :\n";
        foreach ($extraCodes as $extraCode) {
            echo "ℹ️  $extraCode : {$implementedErrorCodes[$extraCode]['status']} - {$implementedErrorCodes[$extraCode]['message']}\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 RÉSULTATS :\n";

    if ($allCorrect && empty($missingCodes) && empty($incorrectCodes)) {
        echo "🎉 TOUS LES CODES D'ERREUR SONT CORRECTEMENT IMPLÉMENTÉS !\n";
    } else {
        echo "⚠️  PROBLÈMES DÉTECTÉS :\n";
        if (!empty($missingCodes)) {
            echo "   • Codes manquants: " . implode(', ', $missingCodes) . "\n";
        }
        if (!empty($incorrectCodes)) {
            echo "   • Codes incorrects: " . implode(', ', $incorrectCodes) . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 LISTE COMPLÈTE DES CODES D'ERREUR OFFICIELS :\n\n";

foreach ($officialErrorCodes as $code => $data) {
    echo "🔸 $code\n";
    echo "   📊 Status: {$data['expected']}\n";
    echo "   💬 Description: {$data['description']}\n";
    echo "   🔄 Retry: " . ($data['retry'] ? 'Oui' : 'Non') . "\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "🎯 UTILISATION DANS LE CODE :\n\n";
echo "// Pour obtenir les informations d'une erreur :\n";
echo "\$airtelService = new AirtelMoneyService();\n";
echo "\$reflection = new ReflectionClass(\$airtelService);\n";
echo "\$errorCodes = \$reflection->getProperty('errorCodes')->getValue(\$airtelService);\n";
echo "\$errorInfo = \$errorCodes['DP00800001001']; // Par exemple\n\n";

echo "// Structure de retour :\n";
echo "\$errorInfo = [\n";
echo "    'status' => 'success|failed|pending|ambiguous|timeout|not_found|refused',\n";
echo "    'message' => 'Description détaillée de l\'erreur',\n";
echo "    'retry' => true/false // Si l'opération peut être retentée\n";
echo "];\n\n";

echo str_repeat("=", 50) . "\n";
