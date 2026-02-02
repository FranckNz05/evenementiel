<?php

/**
 * Debug script pour vérifier que le code PaymentController est bien mis à jour
 */

echo "🔍 DEBUG PAYMENT CONTROLLER\n";
echo str_repeat("=", 40) . "\n\n";

// Lire directement le fichier PaymentController
$controllerPath = __DIR__ . '/app/Http/Controllers/PaymentController.php';
$content = file_get_contents($controllerPath);

echo "1️⃣  VÉRIFICATION DU CODE ACTUEL :\n\n";

// Chercher les lignes spécifiques
$lines = explode("\n", $content);
$foundPending = false;
$foundPaiementEnCours = false;

foreach ($lines as $lineNumber => $line) {
    if (strpos($line, "'statut' => 'pending'") !== false) {
        echo "✅ LIGNE " . ($lineNumber + 1) . ": " . trim($line) . "\n";
        $foundPending = true;
    }
    if (strpos($line, "Paiement en cours") !== false) {
        echo "❌ LIGNE " . ($lineNumber + 1) . ": " . trim($line) . "\n";
        $foundPaiementEnCours = true;
    }
}

echo "\n📊 RÉSULTATS :\n";
echo "• 'pending' trouvé: " . ($foundPending ? "✅ OUI" : "❌ NON") . "\n";
echo "• 'Paiement en cours' trouvé: " . ($foundPaiementEnCours ? "❌ OUI (PROBLÈME)" : "✅ NON") . "\n\n";

echo "2️⃣  EXTRACTION DU CODE AUTOUR DE LA LIGNE 147 :\n\n";

if (isset($lines[146])) { // ligne 147 en index 146
    for ($i = max(0, 146 - 3); $i <= min(count($lines) - 1, 146 + 3); $i++) {
        $marker = ($i + 1 == 147) ? "▶️ " : "   ";
        echo $marker . ($i + 1) . ": " . $lines[$i] . "\n";
    }
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "💡 DIAGNOSTIC :\n\n";

if ($foundPending && !$foundPaiementEnCours) {
    echo "✅ LE CODE EST CORRECTEMENT MIS À JOUR\n\n";
    echo "🔍 CAUSES POSSIBLES DE L'ERREUR :\n";
    echo "• Cache Laravel non vidé\n";
    echo "• Serveur de développement qui utilise l'ancienne version\n";
    echo "• Cache OPcode PHP\n";
    echo "• Fichier compilé quelque part\n\n";

    echo "🛠️  SOLUTIONS À ESSAYER :\n";
    echo "1. php artisan config:clear\n";
    echo "2. php artisan cache:clear\n";
    echo "3. Redémarrer le serveur de développement\n";
    echo "4. Vider le cache OPcode si vous utilisez OPcache\n";
    echo "5. php artisan optimize:clear (pour tout nettoyer)\n\n";

} else {
    echo "❌ LE CODE N'EST PAS CORRECTEMENT MIS À JOUR\n\n";
    echo "Il faut corriger les lignes dans PaymentController.php\n";
}

echo str_repeat("=", 40) . "\n";
