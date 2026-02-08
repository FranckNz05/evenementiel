<?php
// test_encryption_fix.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AirtelMoneyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

echo "🧪 TEST DE CHIFFREMENT RSA AVEC PARAMÈTRES OAEP\n";
echo str_repeat("=", 50) . "\n\n";

$service = new AirtelMoneyService();
$pin = "1234";

// Récupérer la clé publique de fallback
$publicKey = config('services.airtel.rsa_public_key');

if (!$publicKey) {
    echo "❌ Erreur: AIRTEL_RSA_PUBLIC_KEY non configurée dans .env\n";
    exit(1);
}

$variations = [
    [
        'hash' => 'sha256',
        'mgf' => 'sha256',
        'desc' => 'SHA-256 / MGF1-SHA256 (Ancienne config)'
    ],
    [
        'hash' => 'sha256',
        'mgf' => 'sha1',
        'desc' => 'SHA-256 / MGF1-SHA1 (Nouvelle config par défaut)'
    ],
    [
        'hash' => 'sha1',
        'mgf' => 'sha1',
        'desc' => 'SHA-1 / MGF1-SHA1 (Standard historique)'
    ]
];

foreach ($variations as $v) {
    echo "🔹 Test: {$v['desc']}\n";
    
    // Forcer la configuration pour le test
    Config::set('services.airtel.oaep_hash', $v['hash']);
    Config::set('services.airtel.oaep_mgf', $v['mgf']);
    
    try {
        $encrypted = $service->encryptWithRSA($pin, $publicKey);
        echo "   ✅ Succès ! Longueur: " . strlen($encrypted) . " chars\n";
        echo "   📄 Aperçu: " . substr($encrypted, 0, 40) . "...\n\n";
    } catch (Exception $e) {
        echo "   ❌ Échec: " . $e->getMessage() . "\n\n";
    }
}

echo str_repeat("=", 50) . "\n";
echo "💡 Note: Essayez de demander à l'utilisateur de tester la 'Nouvelle config par défaut' en premier.\n";
