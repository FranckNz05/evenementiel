<?php

/**
 * Script pour mettre à jour les clés API Airtel Money dans le fichier .env
 */

echo "🔑 MISE À JOUR DES CLÉS API AIRTEL MONEY\n";
echo str_repeat("=", 50) . "\n\n";

// Clés API fournies par l'utilisateur
$newKeys = [
    'AIRTEL_CLIENT_ID' => 'b280b215-8b00-4be4-bfbb-02f9b2a155c5',
    'AIRTEL_CLIENT_SECRET' => 'c8ecb836-657e-429f-ae34-d4e646cde2f1',
    'AIRTEL_MERCHANT_CODE' => '7VS4GTR8' // Nouveau champ pour le code marchand
];

$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    echo "❌ ERREUR : Le fichier .env n'existe pas dans " . __DIR__ . "\n";
    exit(1);
}

echo "📄 Lecture du fichier .env actuel...\n";

// Lire le contenu actuel
$envContent = file_get_contents($envFile);
$envLines = file($envFile, FILE_IGNORE_NEW_LINES);

echo "🔄 Mise à jour des variables...\n\n";

// Variables mises à jour
$updated = false;
$added = [];

foreach ($newKeys as $key => $value) {
    $found = false;

    // Chercher la variable existante
    foreach ($envLines as $index => $line) {
        $line = trim($line);

        // Ignorer les commentaires et lignes vides
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Chercher la variable
        if (preg_match('/^' . preg_quote($key, '/') . '\s*=\s*(.+)$/', $line, $matches)) {
            $oldValue = trim($matches[1], '"\'');

            if ($oldValue !== $value) {
                // Mettre à jour la valeur
                $envLines[$index] = "$key=$value";
                echo "✅ $key mis à jour : $oldValue → $value\n";
                $updated = true;
            } else {
                echo "ℹ️  $key déjà à jour : $value\n";
            }

            $found = true;
            break;
        }
    }

    // Si la variable n'existe pas, l'ajouter
    if (!$found) {
        $envLines[] = "$key=$value";
        echo "➕ $key ajouté : $value\n";
        $added[] = $key;
        $updated = true;
    }
}

if ($updated) {
    // Écrire le fichier mis à jour
    $newContent = implode("\n", $envLines) . "\n";
    file_put_contents($envFile, $newContent);

    echo "\n💾 Fichier .env mis à jour avec succès !\n\n";

    // Recharger le cache de configuration
    echo "🔄 Rechargement du cache de configuration...\n";
    shell_exec('php artisan config:clear');
    shell_exec('php artisan config:cache');

    echo "✅ Cache rechargé !\n\n";

} else {
    echo "\nℹ️  Aucune mise à jour nécessaire - toutes les clés sont déjà à jour.\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "🔍 VÉRIFICATION DES CLÉS INSTALLÉES :\n\n";

// Vérifier que les clés sont correctement configurées
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$config = config('services.airtel');

echo "🌐 Production: " . ($config['production'] ? 'OUI' : 'NON') . "\n";
echo "🏛️  Pays: " . ($config['country'] ?? 'N/A') . "\n";
echo "💰 Devise: " . ($config['currency'] ?? 'N/A') . "\n";
echo "🔑 Client ID: " . substr($config['client_id'] ?? '', 0, 20) . "...\n";
echo "🔐 Client Secret: " . substr($config['client_secret'] ?? '', 0, 20) . "...\n";

if (isset($config['merchant_code'])) {
    echo "🏪 Code Marchand: " . $config['merchant_code'] . "\n";
}

echo "\n🎯 STATUT :\n";
$clientIdOk = ($config['client_id'] ?? '') === $newKeys['AIRTEL_CLIENT_ID'];
$clientSecretOk = ($config['client_secret'] ?? '') === $newKeys['AIRTEL_CLIENT_SECRET'];

if ($clientIdOk && $clientSecretOk) {
    echo "✅ CLÉS API CORRECTEMENT CONFIGURÉES !\n";
    echo "🚀 L'intégration Airtel Money est maintenant opérationnelle avec les vraies clés !\n\n";

    echo "🧪 TESTS DISPONIBLES :\n";
    echo "• php test_airtel_integration.php    # Test complet\n";
    echo "• php test_airtel_payment.php        # Test de paiement\n";
    echo "• php diagnose_airtel_api.php       # Diagnostic API\n";
    echo "• php check_env_variables.php       # Vérification config\n\n";

    echo "💡 PROCHAINES ÉTAPES :\n";
    echo "1. Tester l'application : php artisan serve\n";
    echo "2. Effectuer un paiement de test\n";
    echo "3. Vérifier les logs Laravel\n";
    echo "4. Configurer l'URL de webhook dans Airtel Money\n\n";

} else {
    echo "❌ PROBLÈME DE CONFIGURATION\n";
    echo "Les clés ne sont pas correctement chargées.\n";
    echo "Essayez : php artisan config:clear && php artisan config:cache\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "🎉 MISE À JOUR TERMINÉE !\n";
echo str_repeat("=", 50) . "\n";
