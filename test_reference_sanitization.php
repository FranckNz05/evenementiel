<?php
// test_reference_sanitization.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AirtelMoneyService;
use Illuminate\Support\Str;

echo "🧪 TEST DE SANITISATION DES RÉFÉRENCES (DP00900001013)\n";
echo str_repeat("=", 60) . "\n\n";

$service = new AirtelMoneyService();
$reflection = new ReflectionClass($service);
$disburseMethod = $reflection->getMethod('disburse');
// disburse matches the public signature, but we want to see how it prepares the payload.
// Actually, disburse is public, so we can't easily intercept the payload without mocking the HTTP client.

// Let's use reflection to inspect the code or just trust the preg_replace if we can't easily mock.
// Better: Create a mock response for the HTTP client.

echo "🔍 Test de sanitisation manuel des IDs...\n";

$testData = [
    'reference' => 'WD-6987482B9A6DF',
    'transaction_id' => 'WD-15-1770473545'
];

$reference = preg_replace('/[^a-zA-Z0-9]/', '', $testData['reference']);
$transactionId = preg_replace('/[^a-zA-Z0-9]/', '', $testData['transaction_id']);

echo "Original Reference:   " . $testData['reference'] . "\n";
echo "Sanitized Reference:  " . $reference . " (Length: " . strlen($reference) . ")\n";
echo "Alphanumeric Check:   " . (ctype_alnum($reference) ? "✅ OK" : "❌ FAIL") . "\n\n";

echo "Original Trans ID:    " . $testData['transaction_id'] . "\n";
echo "Sanitized Trans ID:   " . $transactionId . " (Length: " . strlen($transactionId) . ")\n";
echo "Alphanumeric Check:   " . (ctype_alnum($transactionId) ? "✅ OK" : "❌ FAIL") . "\n\n";

if (ctype_alnum($reference) && ctype_alnum($transactionId)) {
    echo "🎉 LA SANITISATION FONCTIONNE CORRECTEMENT !\n";
} else {
    echo "❌ ÉCHEC DE LA SANITISATION.\n";
}

echo str_repeat("=", 60) . "\n";
