<?php
// test_airtel_disbursement_codes.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AirtelMoneyService;

echo "Démarrage des tests des codes d'erreur Disbursement Airtel Money...\n\n";

$service = new AirtelMoneyService();
$reflection = new ReflectionClass(AirtelMoneyService::class);

// Méthodes à tester via Reflection
$getErrorInfo = $reflection->getMethod('getErrorInfo');
$getErrorInfo->setAccessible(true);

$isPendingStatus = $reflection->getMethod('isPendingStatus');
$isPendingStatus->setAccessible(true);

$isAmbiguousStatus = $reflection->getMethod('isAmbiguousStatus');
$isAmbiguousStatus->setAccessible(true);

$isRealSuccess = $reflection->getMethod('isRealSuccess');
$isRealSuccess->setAccessible(true);

// Scénarios de test
$tests = [
    [
        'code' => 'DP00900001000',
        'expected' => [
            'status' => 'ambiguous',
            'is_ambiguous' => true,
            'is_pending' => false,
            'is_success' => false
        ],
        'desc' => 'Disbursement Ambiguous'
    ],
    [
        'code' => 'DP00900001001',
        'expected' => [
            'status' => 'success',
            'is_ambiguous' => false,
            'is_pending' => false,
            'is_success' => true
        ],
        'desc' => 'Disbursement Success'
    ],
    [
        'code' => 'DP00900001006',
        'expected' => [
            'status' => 'pending',
            'is_ambiguous' => false,
            'is_pending' => true,
            'is_success' => false // Pending is not "Real Success" (completed)
        ],
        'desc' => 'Disbursement In Progress (Pending)'
    ],
    [
        'code' => 'DP00900001003', // Limit reached
        'expected' => [
            'status' => 'failed',
            'is_ambiguous' => false,
            'is_pending' => false,
            'is_success' => false
        ],
        'desc' => 'Disbursement Failed (Limit Reached)'
    ],
    // Legacy tests (Collections)
    [
        'code' => 'DP00800001000',
        'expected' => [
            'status' => 'ambiguous',
            'is_ambiguous' => true,
            'is_pending' => false,
            'is_success' => false
        ],
        'desc' => 'Collection Ambiguous'
    ],
    [
        'code' => 'DP00800001001',
        'expected' => [
            'status' => 'success',
            'is_ambiguous' => false,
            'is_pending' => false,
            'is_success' => true
        ],
        'desc' => 'Collection Success'
    ],
];

$failedCount = 0;

foreach ($tests as $test) {
    echo "Test: {$test['desc']} ({$test['code']})... ";
    
    $info = $getErrorInfo->invoke($service, $test['code']);
    $ambiguous = $isAmbiguousStatus->invoke($service, $test['code']);
    $pending = $isPendingStatus->invoke($service, $test['code']);
    $success = $isRealSuccess->invoke($service, $test['code']);

    $errors = [];
    if ($info['status'] !== $test['expected']['status']) {
        $errors[] = "Status mismatch: got '{$info['status']}', expected '{$test['expected']['status']}'";
    }
    if ($ambiguous !== $test['expected']['is_ambiguous']) {
        $errors[] = "isAmbiguous mismatch: got " . ($ambiguous ? 'true' : 'false');
    }
    if ($pending !== $test['expected']['is_pending']) {
        $errors[] = "isPending mismatch: got " . ($pending ? 'true' : 'false');
    }
    if ($success !== $test['expected']['is_success']) {
        $errors[] = "isRealSuccess mismatch: got " . ($success ? 'true' : 'false');
    }

    if (empty($errors)) {
        echo "OK\n";
    } else {
        echo "FAIL\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
        $failedCount++;
    }
}

echo "\nRésultat: " . ($failedCount === 0 ? "TOUS LES TESTS ONT RÉUSSI" : "$failedCount tests échoués") . "\n";
