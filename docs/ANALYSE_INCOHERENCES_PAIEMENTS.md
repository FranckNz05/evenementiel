# Analyse des Incohérences de Statuts de Paiements - Fintech

## 📋 Vue d'ensemble

Ce document analyse les incohérences logiques entre les différents statuts de paiements dans un système fintech intégrant Airtel Money, M-Pesa et Orange Money.

## 🔍 Sources de Vérité

Le système utilise **3 sources de statuts** différentes :

1. **`statut`** (champ métier) : État métier du paiement dans la base de données
   - Valeurs possibles : `'en attente'`, `'payé'`, `'échoué'`, `'annulé'`

2. **`details.status`** (JSON) : État retourné par l'API lors de l'initiation
   - Valeurs possibles : `'success'`, `'pending'`, `'failed'`, `'error'`, `'ambiguous'`, etc.

3. **`details.airtel_transaction_status`** (JSON) : État réel de l'opérateur Airtel
   - Valeurs possibles : `'TS'`, `'TF'`, `'TA'`, `'TIP'`, `'TE'`

## ⚠️ Types d'Incohérences Détectées

### 1. **STATUS_MISMATCH** (CRITIQUE)
**Description** : Le statut métier ne correspond pas au statut Airtel.

**Exemple** :
- `statut` = `'payé'`
- `airtel_transaction_status` = `'TF'` (Transaction Failed)

**Pourquoi c'est faux** :
- L'opérateur confirme l'échec mais le système indique payé
- Aucune garantie que l'argent a été reçu

**Risques** :
- 🚨 **Livraison sans paiement réel** : Billets/tickets délivrés alors que le paiement a échoué
- 🚨 **Perte financière directe** : Montants comptabilisés comme reçus alors qu'ils ne le sont pas
- 🚨 **Litiges clients** : Clients peuvent contester des paiements non effectués
- 🚨 **Reporting erroné** : Revenus fictifs dans les rapports financiers
- 🚨 **Problèmes comptables** : Écarts entre comptabilité et réalité

**Impact financier** : **CRITIQUE** - Perte directe de revenus

---

### 2. **INVALID_PAYMENT_DATE** (HIGH)
**Description** : `date_paiement` renseignée alors que `statut` ≠ `'payé'`.

**Exemple** :
- `statut` = `'en attente'`
- `date_paiement` = `'2026-01-30 08:41:00'`

**Pourquoi c'est faux** :
- Un paiement ne peut avoir de date de paiement que s'il est réellement payé
- La date indique une finalisation qui n'a pas eu lieu

**Risques** :
- 📊 **Reporting erroné** : Montants comptabilisés comme payés à une date donnée
- 📊 **Analyses financières faussées** : Revenus par période incorrects
- 📊 **Audit comptable** : Incohérences détectables lors d'un audit
- 📊 **Conformité réglementaire** : Non-conformité avec les standards comptables

**Impact financier** : **HIGH** - Fausse comptabilité

---

### 3. **MISSING_PAYMENT_DATE** (MEDIUM)
**Description** : `date_paiement` NULL alors que `statut` = `'payé'`.

**Exemple** :
- `statut` = `'payé'`
- `date_paiement` = `NULL`

**Pourquoi c'est faux** :
- Un paiement réussi doit avoir une date de paiement pour la traçabilité
- Nécessaire pour la comptabilité et l'audit

**Risques** :
- 📝 **Traçabilité incomplète** : Impossible de déterminer quand le paiement a été effectué
- 📝 **Audit difficile** : Manque d'informations pour la vérification
- 📝 **Conformité** : Non-conformité avec les exigences de traçabilité

**Impact financier** : **MEDIUM** - Problèmes de traçabilité

---

### 4. **QR_CODE_PENDING** (CRITIQUE)
**Description** : QR code généré alors que `statut` = `'en attente'`.

**Exemple** :
- `statut` = `'en attente'`
- `qr_code` = `'qrcodes/PAY-IPY7P4KG.svg'`

**Pourquoi c'est faux** :
- Un QR code permet l'accès à un événement
- Si généré avant confirmation du paiement, il peut être utilisé frauduleusement

**Risques** :
- 🚨 **FRAUDE CRITIQUE** : Accès à l'événement sans paiement réel
- 🚨 **Perte financière directe** : Billets utilisés alors que le paiement n'est pas confirmé
- 🚨 **Sécurité compromise** : Contournement du système de paiement
- 🚨 **Réputation** : Perte de confiance des organisateurs et clients

**Impact financier** : **CRITIQUE** - Perte directe + fraude

---

### 5. **AIRTEL_SUCCESS_NOT_PAID** (CRITIQUE)
**Description** : `airtel_transaction_status` = `'TS'` (Success) mais `statut` ≠ `'payé'`.

**Exemple** :
- `airtel_transaction_status` = `'TS'`
- `statut` = `'en attente'`

**Pourquoi c'est faux** :
- L'opérateur confirme le succès du paiement
- Le système n'a pas enregistré ce succès
- L'argent a été reçu mais non comptabilisé

**Risques** :
- 🚨 **Perte de revenus** : Argent reçu mais non enregistré
- 🚨 **Litiges clients** : Clients ont payé mais n'ont pas reçu leurs billets
- 🚨 **Problèmes de réconciliation** : Écarts entre comptes opérateur et système
- 🚨 **Non-livraison** : Clients légitimes non servis

**Impact financier** : **CRITIQUE** - Perte de revenus + insatisfaction client

---

### 6. **AIRTEL_FAILED_BUT_PAID** (CRITIQUE)
**Description** : `airtel_transaction_status` = `'TF'` ou `'TE'` mais `statut` = `'payé'`.

**Exemple** :
- `airtel_transaction_status` = `'TF'`
- `statut` = `'payé'`

**Pourquoi c'est faux** :
- L'opérateur confirme l'échec
- Le système indique payé
- Aucun argent n'a été reçu

**Risques** :
- 🚨 **Livraison sans paiement** : Billets délivrés alors que le paiement a échoué
- 🚨 **Perte financière directe** : Montants non reçus mais comptabilisés
- 🚨 **Fraude potentielle** : Exploitation de cette faille

**Impact financier** : **CRITIQUE** - Perte directe

---

### 7. **AIRTEL_PENDING_BUT_PAID** (HIGH)
**Description** : `airtel_transaction_status` = `'TIP'` ou `'TA'` mais `statut` = `'payé'`.

**Exemple** :
- `airtel_transaction_status` = `'TIP'`
- `statut` = `'payé'`

**Pourquoi c'est faux** :
- Le paiement est encore en attente de confirmation utilisateur
- Le système l'a marqué comme payé prématurément
- Le paiement peut encore échouer

**Risques** :
- ⚠️ **Livraison prématurée** : Billets délivrés avant confirmation finale
- ⚠️ **Risque d'échec** : Le paiement peut encore échouer après livraison
- ⚠️ **Réconciliation difficile** : Statut incertain

**Impact financier** : **HIGH** - Risque de perte

---

### 8. **API_AIRTEL_MISMATCH** (MEDIUM)
**Description** : Incohérence entre `details.status` et `airtel_transaction_status`.

**Exemple** :
- `details.status` = `'success'` → attendu `'payé'`
- `airtel_transaction_status` = `'TF'` → attendu `'échoué'`

**Pourquoi c'est faux** :
- Conflit entre deux sources de vérité
- La source Airtel (`airtel_transaction_status`) doit être prioritaire car c'est l'état réel de l'opérateur

**Risques** :
- 📊 **Confusion dans le traitement** : Quelle source croire ?
- 📊 **Décisions erronées** : Basées sur la mauvaise source
- 📊 **Incohérence système** : Manque de source de vérité unique

**Impact financier** : **MEDIUM** - Confusion opérationnelle

---

## 🎯 Modèle de Vérité Unique

### Mapping Strict des Statuts

#### Source de Vérité : `airtel_transaction_status` (PRIORITÉ ABSOLUE)

| Code Airtel | Signification | Statut Métier | Règles |
|------------|---------------|---------------|--------|
| `TS` | Transaction Success | `'payé'` | ✅ Paiement confirmé par l'opérateur |
| `TF` | Transaction Failed | `'échoué'` | ❌ Paiement échoué définitivement |
| `TE` | Transaction Expired | `'échoué'` | ❌ Paiement expiré (échec) |
| `TA` | Transaction Ambiguous | `'en attente'` | ⏳ Statut ambigu, nécessite polling |
| `TIP` | Transaction in Progress | `'en attente'` | ⏳ En attente de confirmation utilisateur |

#### Source Secondaire : `details.status` (si `airtel_transaction_status` absent)

| Status API | Statut Métier | Règles |
|-----------|---------------|--------|
| `success` | `'payé'` | ✅ Paiement réussi |
| `failed` | `'échoué'` | ❌ Paiement échoué |
| `error` | `'échoué'` | ❌ Erreur technique |
| `pending` | `'en attente'` | ⏳ En attente |
| `ambiguous` | `'en attente'` | ⏳ Statut ambigu |
| `expired` | `'échoué'` | ❌ Expiré |
| `timeout` | `'échoué'` | ❌ Timeout |
| `refused` | `'échoué'` | ❌ Refusé |

### Règles de Priorité

1. **PRIORITÉ 1** : `airtel_transaction_status` (si présent)
2. **PRIORITÉ 2** : `details.status` (si `airtel_transaction_status` absent)
3. **PRIORITÉ 3** : Conserver le statut actuel si aucune source disponible

---

## 🛡️ Règles Métier à Implémenter

### 1. Validation du Statut lors de la Mise à Jour

```php
// Règle : Le statut ne peut être mis à jour que selon airtel_transaction_status
if ($airtelStatus === 'TS' && $payment->statut !== 'payé') {
    throw new PaymentStatusException('Incohérence: Airtel confirme le paiement mais statut ≠ payé');
}
```

### 2. Validation de date_paiement

```php
// Règle : date_paiement ne peut être renseignée que si statut = 'payé'
if ($payment->date_paiement && $payment->statut !== 'payé') {
    $payment->date_paiement = null; // Nettoyer automatiquement
}

// Règle : date_paiement doit être renseignée si statut = 'payé'
if ($payment->statut === 'payé' && !$payment->date_paiement) {
    $payment->date_paiement = now(); // Définir automatiquement
}
```

### 3. Validation du QR Code

```php
// Règle : QR code ne peut être généré que si statut = 'payé'
if ($payment->statut !== 'payé' && !empty($payment->qr_code)) {
    throw new SecurityException('CRITIQUE: QR code généré alors que paiement non confirmé');
}
```

### 4. Contrainte de Cohérence

```php
// Règle : Toujours synchroniser statut avec airtel_transaction_status
$expectedStatus = $this->mapAirtelStatusToBusinessStatus($airtelStatus);
if ($payment->statut !== $expectedStatus) {
    $payment->statut = $expectedStatus;
    $payment->save();
}
```

---

## 🔧 Contraintes à Ajouter

### 1. Contrainte de Base de Données

```sql
-- Trigger pour valider la cohérence statut/date_paiement
DELIMITER $$
CREATE TRIGGER validate_payment_status_before_update
BEFORE UPDATE ON paiements
FOR EACH ROW
BEGIN
    -- Règle: date_paiement ne peut être renseignée que si statut = 'payé'
    IF NEW.date_paiement IS NOT NULL AND NEW.statut != 'payé' THEN
        SET NEW.date_paiement = NULL;
    END IF;
    
    -- Règle: date_paiement doit être renseignée si statut = 'payé'
    IF NEW.statut = 'payé' AND NEW.date_paiement IS NULL THEN
        SET NEW.date_paiement = NOW();
    END IF;
END$$
DELIMITER ;
```

### 2. Validation au Niveau Application

```php
// Dans le modèle Payment
protected static function boot()
{
    parent::boot();
    
    static::updating(function ($payment) {
        // Valider la cohérence statut/date_paiement
        if ($payment->date_paiement && $payment->statut !== 'payé') {
            $payment->date_paiement = null;
        }
        
        if ($payment->statut === 'payé' && !$payment->date_paiement) {
            $payment->date_paiement = now();
        }
        
        // Valider la cohérence avec airtel_transaction_status
        $details = json_decode($payment->details ?? '{}', true) ?: [];
        $airtelStatus = $details['airtel_transaction_status'] ?? null;
        
        if ($airtelStatus) {
            $expectedStatus = self::mapAirtelStatus($airtelStatus);
            if ($payment->statut !== $expectedStatus) {
                Log::warning('Incohérence de statut détectée', [
                    'payment_id' => $payment->id,
                    'current' => $payment->statut,
                    'expected' => $expectedStatus,
                    'airtel_status' => $airtelStatus
                ]);
            }
        }
    });
}
```

### 3. Guard pour la Génération de QR Code

```php
// Dans le contrôleur de génération de QR
public function generateQrCode(Payment $payment)
{
    // Guard: Vérifier que le paiement est réellement payé
    if ($payment->statut !== 'payé') {
        throw new SecurityException('Impossible de générer un QR code: paiement non confirmé');
    }
    
    // Vérifier aussi airtel_transaction_status si disponible
    $details = json_decode($payment->details ?? '{}', true) ?: [];
    $airtelStatus = $details['airtel_transaction_status'] ?? null;
    
    if ($airtelStatus && $airtelStatus !== 'TS') {
        throw new SecurityException('Impossible de générer un QR code: Airtel ne confirme pas le paiement');
    }
    
    // Générer le QR code...
}
```

---

## 📊 Requêtes SQL pour Corriger les Données Existantes

### Correction 1: Synchroniser statut avec airtel_transaction_status

```sql
-- Mettre à jour les paiements avec airtel_transaction_status = TS mais statut ≠ payé
UPDATE paiements
SET 
    statut = 'payé',
    date_paiement = COALESCE(date_paiement, updated_at, created_at)
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
    AND statut != 'payé';

-- Mettre à jour les paiements avec airtel_transaction_status = TF/TE mais statut = payé
UPDATE paiements
SET 
    statut = 'échoué',
    date_paiement = NULL
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND statut = 'payé';

-- Mettre à jour les paiements avec airtel_transaction_status = TIP/TA mais statut = payé
UPDATE paiements
SET 
    statut = 'en attente',
    date_paiement = NULL
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND statut = 'payé';
```

### Correction 2: Nettoyer date_paiement

```sql
-- Supprimer date_paiement si statut ≠ payé
UPDATE paiements
SET date_paiement = NULL
WHERE 
    date_paiement IS NOT NULL
    AND statut != 'payé';

-- Ajouter date_paiement si statut = payé mais date_paiement NULL
UPDATE paiements
SET date_paiement = COALESCE(
    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
    updated_at,
    created_at
)
WHERE 
    statut = 'payé'
    AND date_paiement IS NULL;
```

### Correction 3: Signaler les QR codes dangereux

```sql
-- Identifier les paiements avec QR code mais statut ≠ payé
SELECT 
    id,
    matricule,
    montant,
    statut,
    qr_code,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at
FROM paiements
WHERE 
    qr_code IS NOT NULL
    AND statut != 'payé'
ORDER BY created_at DESC;
```

---

## 🚀 Script de Correction Automatique

Voir le fichier `app/Console/Commands/AnalyzePaymentInconsistencies.php` pour le script complet.

**Utilisation** :
```bash
# Analyser les incohérences
php artisan payments:analyze-inconsistencies

# Analyser et exporter en CSV
php artisan payments:analyze-inconsistencies --export-csv

# Analyser et corriger automatiquement
php artisan payments:analyze-inconsistencies --fix
```

---

## 📝 Checklist de Mise en Production

- [ ] Exécuter l'analyse des incohérences
- [ ] Examiner les résultats critiques
- [ ] Corriger manuellement les cas complexes
- [ ] Exécuter le script de correction automatique
- [ ] Ajouter les contraintes de validation
- [ ] Implémenter les guards dans le code
- [ ] Tester les validations
- [ ] Mettre en place un monitoring des incohérences
- [ ] Documenter les procédures de réconciliation

---

## 🔄 Monitoring Continu

Recommandation : Exécuter l'analyse quotidiennement pour détecter rapidement les incohérences.

```bash
# Ajouter au crontab
0 2 * * * cd /path/to/project && php artisan payments:analyze-inconsistencies --export-csv
```

---

**Date de création** : 2026-01-30  
**Version** : 1.0  
**Auteur** : Expert Fintech Backend


