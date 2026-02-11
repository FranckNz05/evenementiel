-- ============================================================================
-- Script SQL : Correction des Données Existantes (Sans Créer les Triggers)
-- ============================================================================
-- 
-- Ce script corrige uniquement les données existantes.
-- Utilisez ce script si vous voulez seulement corriger les données sans créer les triggers.
-- 
-- ⚠️ ATTENTION : Faites une sauvegarde de votre base de données avant d'exécuter ce script
-- ============================================================================

-- CORRECTION 1: Synchroniser statut avec airtel_transaction_status = TS (Transaction Success)
-- Mettre à jour les paiements confirmés par Airtel mais non marqués comme payés
UPDATE `paiements`
SET 
    `statut` = 'payé',
    `date_paiement` = COALESCE(
        `date_paiement`,
        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
        `updated_at`,
        `created_at`
    )
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS'
    AND `statut` != 'payé';

-- CORRECTION 2: Synchroniser statut avec airtel_transaction_status = TF ou TE (Transaction Failed/Expired)
-- Mettre à jour les paiements échoués selon Airtel mais marqués comme payés
UPDATE `paiements`
SET 
    `statut` = 'échoué',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'payé';

-- CORRECTION 3: Synchroniser statut avec airtel_transaction_status = TIP ou TA (Transaction in Progress/Ambiguous)
-- Mettre à jour les paiements en attente selon Airtel mais marqués comme payés
UPDATE `paiements`
SET 
    `statut` = 'en attente',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'payé';

-- CORRECTION 4: Nettoyer date_paiement si statut ≠ payé
-- Supprimer les dates de paiement invalides
UPDATE `paiements`
SET `date_paiement` = NULL
WHERE 
    `date_paiement` IS NOT NULL
    AND `statut` != 'payé';

-- CORRECTION 5: Ajouter date_paiement si statut = payé mais date_paiement NULL
-- Récupérer la date depuis les détails JSON ou utiliser updated_at/created_at
UPDATE `paiements`
SET `date_paiement` = COALESCE(
    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.initiated_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
    `updated_at`,
    `created_at`
)
WHERE 
    `statut` = 'payé'
    AND `date_paiement` IS NULL;

-- CORRECTION 6: Synchroniser avec details.status si airtel_transaction_status absent
-- Utiliser details.status comme source secondaire
UPDATE `paiements`
SET 
    `statut` = CASE 
        WHEN JSON_EXTRACT(`details`, '$.status') = 'success' THEN 'payé'
        WHEN JSON_EXTRACT(`details`, '$.status') IN ('failed', 'error', 'expired', 'timeout', 'refused') THEN 'échoué'
        WHEN JSON_EXTRACT(`details`, '$.status') IN ('pending', 'ambiguous') THEN 'en attente'
        ELSE `statut`
    END,
    `date_paiement` = CASE 
        WHEN JSON_EXTRACT(`details`, '$.status') = 'success' AND `date_paiement` IS NULL THEN 
            COALESCE(
                STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(`details`, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                `updated_at`
            )
        WHEN JSON_EXTRACT(`details`, '$.status') != 'success' THEN NULL
        ELSE `date_paiement`
    END
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IS NULL
    AND JSON_EXTRACT(`details`, '$.status') IS NOT NULL
    AND (
        (JSON_EXTRACT(`details`, '$.status') = 'success' AND `statut` != 'payé')
        OR (JSON_EXTRACT(`details`, '$.status') IN ('failed', 'error', 'expired', 'timeout', 'refused') AND `statut` = 'payé')
        OR (JSON_EXTRACT(`details`, '$.status') IN ('pending', 'ambiguous') AND `statut` = 'payé')
    );

-- Afficher un résumé des corrections
SELECT 
    'CORRECTION 1: TS → payé' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS'
    AND `statut` = 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 2: TF/TE → échoué' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'échoué'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 3: TIP/TA → en attente' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'en attente'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 4: date_paiement nettoyée' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    `date_paiement` IS NULL
    AND `statut` != 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 5: date_paiement ajoutée' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    `statut` = 'payé'
    AND `date_paiement` IS NOT NULL
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR);

