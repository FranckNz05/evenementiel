<?php
// test_rsa_keys.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AirtelMoneyService;
use Illuminate\Support\Facades\Log;

echo "Test de récupération des clés RSA Airtel Money...\n\n";

try {
    $service = new AirtelMoneyService();
    
    // Forcer l'appel API (ignorer le cache si possible ou juste appeler la méthode publie)
    // Mais getEncryptionKeys est publique.
    
    // On va aussi effacer le cache pour être sûr
    $country = env('AIRTEL_COUNTRY', 'CG');
    $cacheKey = 'airtel_encryption_keys_' . $country;
    Illuminate\Support\Facades\Cache::forget($cacheKey);
    
    echo "Cache effacé. Tentative d'appel API...\n";
    
    $keys = $service->getEncryptionKeys();
    
    echo "SUCCESS !\n";
    echo "Key ID: " . ($keys['key_id'] ?? 'N/A') . "\n";
    echo "Valid Upto: " . ($keys['valid_upto'] ?? 'N/A') . "\n";
    echo "Public Key:\n" . substr($keys['key'], 0, 50) . "...\n";
    
    // Test de chiffrement avec la clé récupérée
    $data = "1234";
    $encrypted = $service->encryptPin($data);
    echo "Test chiffrement PIN: OK (" . strlen($encrypted) . " chars)\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
