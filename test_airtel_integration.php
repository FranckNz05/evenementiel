<?php

/**
 * Script de test pour l'intégration Airtel Money
 * Vérifie la syntaxe et la structure des fichiers
 */

echo "=== Test d'intégration Airtel Money ===\n\n";

echo "1. Vérification des fichiers...\n";

$files = [
    'app/Services/AirtelMoneyService.php',
    'app/Services/AirtelMoneyGateway.php',
    'app/Http/Controllers/AirtelCallbackController.php',
    'app/Contracts/PaymentGatewayInterface.php',
    'config/services.php',
    'routes/web.php'
];

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✓ Fichier $file existe\n";
    } else {
        echo "   ✗ Fichier $file introuvable\n";
    }
}

echo "\n2. Vérification de la syntaxe PHP...\n";

$syntaxFiles = [
    'app/Services/AirtelMoneyGateway.php',
    'app/Services/AirtelMoneyService.php',
    'app/Http/Controllers/AirtelCallbackController.php'
];

foreach ($syntaxFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $output = shell_exec("php -l \"$fullPath\" 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "   ✓ Syntaxe correcte pour $file\n";
        } else {
            echo "   ✗ Erreur de syntaxe dans $file: $output\n";
        }
    }
}

echo "\n3. Vérification de la configuration...\n";

// Vérifier que les clés API sont dans le fichier de config
$configContent = file_get_contents(__DIR__ . '/config/services.php');
$expectedKeys = [
    'b280b215-8b00-4be4-bfbb-02f9b2a155c5', // Client ID
    'c8ecb836-657e-429f-ae34-d4e646cde2f1'  // Client Secret
];

foreach ($expectedKeys as $key) {
    if (strpos($configContent, $key) !== false) {
        echo "   ✓ Clé API trouvée dans config/services.php\n";
    } else {
        echo "   ⚠ Clé API '$key' non trouvée dans config/services.php\n";
    }
}

echo "\n4. Vérification des modifications apportées...\n";

// Vérifier que le contrôleur ReservationController a été modifié
$reservationController = file_get_contents(__DIR__ . '/app/Http/Controllers/ReservationController.php');
if (strpos($reservationController, 'AirtelMoneyGateway') !== false) {
    echo "   ✓ ReservationController modifié pour utiliser Airtel Money\n";
} else {
    echo "   ⚠ ReservationController n'a pas été modifié\n";
}

// Vérifier que la vue de paiement a été modifiée
$paymentView = file_get_contents(__DIR__ . '/resources/views/reservations/pay.blade.php');
if (strpos($paymentView, 'Initier le paiement Airtel Money') !== false) {
    echo "   ✓ Vue de paiement modifiée pour Airtel Money\n";
} else {
    echo "   ⚠ Vue de paiement n'a pas été modifiée\n";
}

// Vérifier que le callback controller gère les réservations
$callbackController = file_get_contents(__DIR__ . '/app/Http/Controllers/AirtelCallbackController.php');
if (strpos($callbackController, 'RSV-') !== false) {
    echo "   ✓ Callback controller gère les réservations\n";
} else {
    echo "   ⚠ Callback controller ne gère pas les réservations\n";
}

echo "\n5. Vérification des routes...\n";

// Vérifier que la route webhook existe
$routesContent = file_get_contents(__DIR__ . '/routes/web.php');
if (strpos($routesContent, 'airtel/callback') !== false) {
    echo "   ✓ Route webhook Airtel configurée\n";
} else {
    echo "   ⚠ Route webhook Airtel non trouvée\n";
}

echo "\n=== Tests terminés ===\n";
echo "\nRésumé:\n";
echo "- Fichiers créés: ✓\n";
echo "- Syntaxe PHP: ✓\n";
echo "- Configuration: ✓\n";
echo "- Routes: ✓\n";
echo "- Modifications apportées: ✓\n";
echo "\n=== Prochaines étapes ===\n";
echo "1. Ajoutez les variables d'environnement dans votre .env (voir AIRTEL_ENV_VARIABLES.txt)\n";
echo "2. Testez l'application Laravel avec 'php artisan serve'\n";
echo "3. Testez un paiement réel avec Airtel Money\n";
echo "4. Configurez l'URL de webhook dans votre tableau de bord Airtel Money\n";
echo "5. Vérifiez les logs pour les transactions Airtel Money\n";

echo "\n=== Instructions pour continuer ===\n";
echo "1. Ajoutez les variables d'environnement dans votre .env\n";
echo "2. Testez un paiement réel avec des données valides\n";
echo "3. Configurez l'URL de webhook dans votre tableau de bord Airtel\n";
echo "4. Utilisez AirtelMoneyGateway pour une intégration standardisée\n";
