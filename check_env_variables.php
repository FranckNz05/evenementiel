<?php

/**
 * Script de vérification des variables d'environnement Airtel Money
 */

echo "🔍 VÉRIFICATION DES VARIABLES D'ENVIRONNEMENT AIRTEL MONEY\n";
echo str_repeat("=", 60) . "\n\n";

// Vérifier si le fichier .env existe
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "❌ ERREUR : Le fichier .env n'existe pas dans " . __DIR__ . "\n";
    echo "   Veuillez vérifier que vous êtes dans le bon répertoire.\n\n";
    exit(1);
}

echo "✅ Fichier .env trouvé\n\n";

// Lire le contenu du fichier .env
$envContent = file_get_contents($envFile);
$envLines = file($envFile, FILE_IGNORE_NEW_LINES);

// Variables requises
$requiredVars = [
    'AIRTEL_CLIENT_ID' => 'b280b215-8b00-4be4-bfbb-02f9b2a155c5',
    'AIRTEL_CLIENT_SECRET' => 'c8ecb836-657e-429f-ae34-d4e646cde2f1',
    'AIRTEL_PRODUCTION' => 'false',
    'AIRTEL_COUNTRY' => 'CG',
    'AIRTEL_CURRENCY' => 'XAF'
];

// Variables optionnelles
$optionalVars = [
    'AIRTEL_CALLBACK_AUTH_ENABLED' => 'true',
    'AIRTEL_SIGNATURE_ENABLED' => 'false'
];

$missingRequired = [];
$missingOptional = [];
$incorrectValues = [];

echo "🔧 VÉRIFICATION DES VARIABLES REQUISES :\n";
echo str_repeat("-", 40) . "\n";

foreach ($requiredVars as $varName => $expectedValue) {
    $found = false;
    $correct = false;

    foreach ($envLines as $line) {
        $line = trim($line);

        // Ignorer les commentaires et lignes vides
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Chercher la variable
        if (preg_match('/^' . preg_quote($varName, '/') . '\s*=\s*(.+)$/', $line, $matches)) {
            $found = true;
            $actualValue = trim($matches[1]);

            // Enlever les guillemets si présents
            $actualValue = trim($actualValue, '"\'');

            if ($actualValue === $expectedValue) {
                $correct = true;
                echo "✅ $varName = $actualValue\n";
            } else {
                $incorrectValues[] = "$varName (attendu: $expectedValue, trouvé: $actualValue)";
                echo "❌ $varName = $actualValue (ATTENDU: $expectedValue)\n";
            }
            break;
        }
    }

    if (!$found) {
        $missingRequired[] = $varName;
        echo "❌ $varName = MANQUANT\n";
    }
}

echo "\n🔧 VÉRIFICATION DES VARIABLES OPTIONNELLES :\n";
echo str_repeat("-", 40) . "\n";

foreach ($optionalVars as $varName => $expectedValue) {
    $found = false;

    foreach ($envLines as $line) {
        $line = trim($line);

        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (preg_match('/^' . preg_quote($varName, '/') . '\s*=\s*(.+)$/', $line, $matches)) {
            $found = true;
            $actualValue = trim($matches[1]);
            $actualValue = trim($actualValue, '"\'');

            if ($actualValue === $expectedValue) {
                echo "✅ $varName = $actualValue\n";
            } else {
                echo "⚠️  $varName = $actualValue (recommandé: $expectedValue)\n";
            }
            break;
        }
    }

    if (!$found) {
        $missingOptional[] = $varName;
        echo "⚠️  $varName = MANQUANT (optionnel)\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RÉSULTATS :\n";

$allRequiredPresent = empty($missingRequired) && empty($incorrectValues);
$allOptionalPresent = empty($missingOptional);

if ($allRequiredPresent) {
    echo "✅ TOUTES LES VARIABLES REQUISES SONT CONFIGURÉES CORRECTEMENT\n";
} else {
    echo "❌ VARIABLES REQUISES MANQUANTES OU INCORRECTES :\n";
    foreach ($missingRequired as $var) {
        echo "   - $var\n";
    }
    foreach ($incorrectValues as $var) {
        echo "   - $var\n";
    }
}

if ($allOptionalPresent) {
    echo "✅ TOUTES LES VARIABLES OPTIONNELLES SONT CONFIGURÉES\n";
} else {
    echo "ℹ️  VARIABLES OPTIONNELLES MANQUANTES :\n";
    foreach ($missingOptional as $var) {
        echo "   - $var (optionnel)\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

if ($allRequiredPresent) {
    echo "🎉 FÉLICITATIONS !\n";
    echo "Votre configuration Airtel Money est complète et prête à être utilisée.\n\n";

    echo "🚀 PROCHAINES ÉTAPES :\n";
    echo "1. Redémarrez votre serveur Laravel : php artisan config:clear && php artisan config:cache\n";
    echo "2. Testez l'application : php artisan serve\n";
    echo "3. Essayez un paiement avec Airtel Money\n";
    echo "4. Surveillez les logs pour les transactions\n\n";

    echo "📱 URL DU WEBHOOK À CONFIGURER DANS AIRTEL :\n";
    echo "https://mokilievent.com/webhooks/airtel/callback\n";
    echo "(Remplacez 'mokilievent.com' par votre domaine réel)\n\n";

} else {
    echo "⚠️  ACTION REQUISE :\n";
    echo "Vous devez ajouter ou corriger les variables manquantes dans votre fichier .env\n\n";

    echo "📝 VARIABLES À AJOUTER/MODIFIER :\n";
    echo "# Configuration Airtel Money\n";
    echo "AIRTEL_CLIENT_ID=b280b215-8b00-4be4-bfbb-02f9b2a155c5\n";
    echo "AIRTEL_CLIENT_SECRET=c8ecb836-657e-429f-ae34-d4e646cde2f1\n";
    echo "\n";
    echo "# Configuration environnement\n";
    echo "AIRTEL_PRODUCTION=false\n";
    echo "AIRTEL_COUNTRY=CG\n";
    echo "AIRTEL_CURRENCY=XAF\n";
    echo "\n";
    echo "# Sécurité des callbacks (optionnel)\n";
    echo "AIRTEL_CALLBACK_AUTH_ENABLED=true\n";
    echo "\n";

    echo "📂 INSTRUCTIONS :\n";
    echo "1. Ouvrez le fichier .env dans un éditeur de texte\n";
    echo "2. Ajoutez ces lignes à la fin du fichier\n";
    echo "3. Sauvegardez le fichier\n";
    echo "4. Relancez ce script : php check_env_variables.php\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "📄 FICHIER DE RÉFÉRENCE :\n";
echo "Consultez AIRTEL_ENV_VARIABLES.txt pour plus de détails.\n";
echo str_repeat("=", 60) . "\n";
