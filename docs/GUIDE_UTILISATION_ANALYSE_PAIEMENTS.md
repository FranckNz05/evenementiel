# Guide d'Utilisation - Analyse et Correction des Incohérences de Paiements

## 📋 Vue d'ensemble

Ce guide explique comment utiliser les outils créés pour analyser et corriger les incohérences de statuts dans les paiements.

## 🚀 Démarrage Rapide

### 1. Analyser les incohérences

```bash
php artisan payments:analyze-inconsistencies
```

Cette commande va :
- Détecter toutes les incohérences entre `statut`, `details.status` et `airtel_transaction_status`
- Afficher un résumé des problèmes détectés
- Classer les incohérences par type et sévérité

### 2. Exporter les résultats en CSV

```bash
php artisan payments:analyze-inconsistencies --export-csv
```

Le fichier CSV sera généré dans `storage/app/payment_inconsistencies_YYYY-MM-DD_HHMMSS.csv`

### 3. Corriger automatiquement les incohérences

```bash
php artisan payments:analyze-inconsistencies --fix
```

⚠️ **ATTENTION** : Cette commande modifie directement la base de données. Assurez-vous d'avoir une sauvegarde avant de l'exécuter.

## 📊 Utilisation du Script SQL

Le fichier `scripts/analyze_payment_inconsistencies.sql` contient des requêtes SQL pour analyser les incohérences directement depuis la base de données.

### Exécution

```bash
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql
```

Ou depuis MySQL :

```sql
source scripts/analyze_payment_inconsistencies.sql;
```

### Requêtes Disponibles

1. **AIRTEL_SUCCESS_NOT_PAID** : Paiements confirmés par Airtel (TS) mais non marqués comme payés
2. **AIRTEL_FAILED_BUT_PAID** : Paiements échoués (TF/TE) mais marqués comme payés
3. **AIRTEL_PENDING_BUT_PAID** : Paiements en attente (TIP/TA) mais marqués comme payés
4. **INVALID_PAYMENT_DATE** : Date de paiement renseignée alors que statut ≠ payé
5. **MISSING_PAYMENT_DATE** : Date de paiement manquante alors que statut = payé
6. **QR_CODE_PENDING** : QR code généré alors que statut = en attente
7. **API_AIRTEL_MISMATCH** : Incohérence entre `details.status` et `airtel_transaction_status`

## 🔧 Migrations

### 1. Ajouter les contraintes de validation

```bash
php artisan migrate
```

Cette migration va :
- Créer des triggers pour valider automatiquement la cohérence `statut`/`date_paiement`
- Créer une table de log pour les incohérences détectées

### 2. Corriger les données existantes

```bash
php artisan migrate
```

Cette migration va corriger automatiquement :
- Les statuts incohérents avec `airtel_transaction_status`
- Les dates de paiement invalides
- Les statuts basés sur `details.status` si `airtel_transaction_status` est absent

## 🛡️ Utilisation du Service de Validation

Le service `PaymentStatusValidator` peut être utilisé dans votre code pour valider et synchroniser les statuts.

### Exemple d'utilisation

```php
use App\Services\PaymentStatusValidator;

$validator = app(PaymentStatusValidator::class);

// Valider et synchroniser un paiement
$payment = Payment::find($id);
$validator->validateAndSync($payment);

// Vérifier les incohérences
$inconsistencies = $validator->checkInconsistencies($payment);

// Valider la génération d'un QR code
try {
    $validator->validateQrCodeGeneration($payment);
    // Générer le QR code...
} catch (\Exception $e) {
    // Gérer l'erreur
    Log::error('Impossible de générer le QR code', ['error' => $e->getMessage()]);
}
```

## 📝 Intégration dans le Code

### Validation automatique dans le modèle Payment

Le modèle `Payment` a été modifié pour valider automatiquement les statuts lors des opérations `create` et `update`.

### Guard pour la génération de QR code

Avant de générer un QR code, utilisez le service de validation :

```php
use App\Services\PaymentStatusValidator;

public function generateQrCode(Payment $payment)
{
    $validator = app(PaymentStatusValidator::class);
    
    // Valider que le QR code peut être généré
    $validator->validateQrCodeGeneration($payment);
    
    // Générer le QR code...
}
```

## 📈 Monitoring Continu

### Ajouter au crontab

Pour analyser quotidiennement les incohérences :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (exécution quotidienne à 2h du matin)
0 2 * * * cd /path/to/project && php artisan payments:analyze-inconsistencies --export-csv >> /var/log/payment-analysis.log 2>&1
```

### Alertes par email

Vous pouvez modifier la commande pour envoyer un email en cas d'incohérences critiques :

```php
// Dans app/Console/Commands/AnalyzePaymentInconsistencies.php
if (count($criticalInconsistencies) > 0) {
    Mail::to('admin@example.com')->send(new PaymentInconsistenciesAlert($criticalInconsistencies));
}
```

## 🔍 Types d'Incohérences et Sévérité

| Type | Sévérité | Description |
|------|----------|-------------|
| `AIRTEL_SUCCESS_NOT_PAID` | CRITICAL | Airtel confirme le paiement mais statut ≠ payé |
| `AIRTEL_FAILED_BUT_PAID` | CRITICAL | Airtel confirme l'échec mais statut = payé |
| `QR_CODE_PENDING` | CRITICAL | QR code généré alors que statut = en attente |
| `STATUS_MISMATCH` | CRITICAL | Statut métier ≠ statut attendu |
| `AIRTEL_PENDING_BUT_PAID` | HIGH | Airtel indique en attente mais statut = payé |
| `INVALID_PAYMENT_DATE` | HIGH | Date de paiement renseignée alors que statut ≠ payé |
| `MISSING_PAYMENT_DATE` | MEDIUM | Date de paiement manquante alors que statut = payé |
| `API_AIRTEL_MISMATCH` | MEDIUM | Incohérence entre details.status et airtel_transaction_status |

## ⚠️ Bonnes Pratiques

1. **Toujours analyser avant de corriger** : Exécutez d'abord l'analyse sans `--fix` pour voir ce qui sera modifié
2. **Sauvegarder la base de données** : Avant d'exécuter `--fix`, faites une sauvegarde complète
3. **Examiner les cas critiques** : Les incohérences CRITICAL doivent être examinées manuellement
4. **Monitoring régulier** : Exécutez l'analyse quotidiennement pour détecter rapidement les problèmes
5. **Documenter les corrections** : Notez les corrections manuelles effectuées

## 🐛 Dépannage

### Erreur : "Class PaymentStatusValidator not found"

```bash
composer dump-autoload
```

### Erreur : "Trigger already exists"

```sql
DROP TRIGGER IF EXISTS validate_payment_status_before_update;
DROP TRIGGER IF EXISTS validate_payment_status_before_insert;
```

Puis réexécutez la migration.

### Les corrections ne s'appliquent pas

Vérifiez que les triggers sont bien créés :

```sql
SHOW TRIGGERS LIKE 'paiements';
```

## 📚 Documentation Complémentaire

- [Analyse détaillée des risques](./ANALYSE_INCOHERENCES_PAIEMENTS.md)
- [Modèle de vérité unique](./ANALYSE_INCOHERENCES_PAIEMENTS.md#-modèle-de-vérité-unique)
- [Règles métier](./ANALYSE_INCOHERENCES_PAIEMENTS.md#-règles-métier-à-implémenter)

## 📞 Support

En cas de problème, consultez les logs :

```bash
tail -f storage/logs/laravel.log
```

---

**Version** : 1.0  
**Date** : 2026-01-30


