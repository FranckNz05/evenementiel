-- ============================================================================
-- Script SQL d'analyse des incohérences de statuts de paiements
-- Fintech - Système de paiement mobile money (Airtel Money, M-Pesa, Orange)
-- ============================================================================

-- ============================================================================
-- 1. INCOHÉRENCE: Statut métier ≠ Statut attendu depuis airtel_transaction_status
-- ============================================================================

-- Cas 1.1: airtel_transaction_status = TS mais statut ≠ payé
SELECT 
    id,
    matricule,
    montant,
    statut as current_statut,
    'payé' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    'AIRTEL_SUCCESS_NOT_PAID' as inconsistency_type,
    'CRITICAL' as severity,
    CONCAT('Airtel confirme le paiement (TS) mais statut = ''', statut, '''') as description
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
    AND statut != 'payé'
ORDER BY created_at DESC;

-- Cas 1.2: airtel_transaction_status = TF/TE mais statut = payé
SELECT 
    id,
    matricule,
    montant,
    statut as current_statut,
    'échoué' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    'AIRTEL_FAILED_BUT_PAID' as inconsistency_type,
    'CRITICAL' as severity,
    CONCAT('Airtel confirme l''échec (', JSON_EXTRACT(details, '$.airtel_transaction_status'), ') mais statut = ''payé''') as description
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND statut = 'payé'
ORDER BY created_at DESC;

-- Cas 1.3: airtel_transaction_status = TIP/TA mais statut = payé
SELECT 
    id,
    matricule,
    montant,
    statut as current_statut,
    'en attente' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    'AIRTEL_PENDING_BUT_PAID' as inconsistency_type,
    'HIGH' as severity,
    CONCAT('Airtel indique en attente (', JSON_EXTRACT(details, '$.airtel_transaction_status'), ') mais statut = ''payé''') as description
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND statut = 'payé'
ORDER BY created_at DESC;

-- ============================================================================
-- 2. INCOHÉRENCE: date_paiement renseignée alors que statut ≠ payé
-- ============================================================================

SELECT 
    id,
    matricule,
    montant,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at,
    'INVALID_PAYMENT_DATE' as inconsistency_type,
    'HIGH' as severity,
    CONCAT('date_paiement renseignée (', date_paiement, ') alors que statut = ''', statut, '''') as description
FROM paiements
WHERE 
    date_paiement IS NOT NULL
    AND statut != 'payé'
ORDER BY created_at DESC;

-- ============================================================================
-- 3. INCOHÉRENCE: date_paiement NULL alors que statut = payé
-- ============================================================================

SELECT 
    id,
    matricule,
    montant,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    JSON_EXTRACT(details, '$.callback_received_at') as callback_date,
    created_at,
    'MISSING_PAYMENT_DATE' as inconsistency_type,
    'MEDIUM' as severity,
    'date_paiement NULL alors que statut = ''payé''' as description
FROM paiements
WHERE 
    statut = 'payé'
    AND date_paiement IS NULL
ORDER BY created_at DESC;

-- ============================================================================
-- 4. INCOHÉRENCE: QR code généré alors que statut = en attente (CRITIQUE)
-- ============================================================================

SELECT 
    id,
    matricule,
    montant,
    statut,
    qr_code,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    created_at,
    'QR_CODE_PENDING' as inconsistency_type,
    'CRITICAL' as severity,
    'QR code généré alors que statut = ''en attente''' as description
FROM paiements
WHERE 
    qr_code IS NOT NULL
    AND statut = 'en attente'
ORDER BY created_at DESC;

-- ============================================================================
-- 5. INCOHÉRENCE: Incohérence entre details.status et airtel_transaction_status
-- ============================================================================

SELECT 
    id,
    matricule,
    montant,
    statut,
    JSON_EXTRACT(details, '$.status') as api_status,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at,
    'API_AIRTEL_MISMATCH' as inconsistency_type,
    'MEDIUM' as severity,
    CONCAT(
        'Incohérence entre details.status (', 
        JSON_EXTRACT(details, '$.status'), 
        ') et airtel_transaction_status (', 
        JSON_EXTRACT(details, '$.airtel_transaction_status'), 
        ')'
    ) as description
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.status') IS NOT NULL
    AND JSON_EXTRACT(details, '$.airtel_transaction_status') IS NOT NULL
    AND (
        (JSON_EXTRACT(details, '$.status') = 'success' AND JSON_EXTRACT(details, '$.airtel_transaction_status') != 'TS')
        OR (JSON_EXTRACT(details, '$.status') = 'failed' AND JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS')
        OR (JSON_EXTRACT(details, '$.status') = 'pending' AND JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS')
    )
ORDER BY created_at DESC;

-- ============================================================================
-- 6. VUE D'ENSEMBLE: Toutes les incohérences
-- ============================================================================

SELECT 
    'AIRTEL_SUCCESS_NOT_PAID' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
    AND statut != 'payé'

UNION ALL

SELECT 
    'AIRTEL_FAILED_BUT_PAID' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND statut = 'payé'

UNION ALL

SELECT 
    'AIRTEL_PENDING_BUT_PAID' as type,
    COUNT(*) as count,
    'HIGH' as severity
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND statut = 'payé'

UNION ALL

SELECT 
    'INVALID_PAYMENT_DATE' as type,
    COUNT(*) as count,
    'HIGH' as severity
FROM paiements
WHERE 
    date_paiement IS NOT NULL
    AND statut != 'payé'

UNION ALL

SELECT 
    'MISSING_PAYMENT_DATE' as type,
    COUNT(*) as count,
    'MEDIUM' as severity
FROM paiements
WHERE 
    statut = 'payé'
    AND date_paiement IS NULL

UNION ALL

SELECT 
    'QR_CODE_PENDING' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM paiements
WHERE 
    qr_code IS NOT NULL
    AND statut = 'en attente'

ORDER BY 
    CASE severity
        WHEN 'CRITICAL' THEN 1
        WHEN 'HIGH' THEN 2
        WHEN 'MEDIUM' THEN 3
        ELSE 4
    END,
    count DESC;

-- ============================================================================
-- 7. STATISTIQUES PAR STATUT
-- ============================================================================

SELECT 
    statut,
    COUNT(*) as total,
    SUM(CASE WHEN date_paiement IS NOT NULL THEN 1 ELSE 0 END) as with_payment_date,
    SUM(CASE WHEN qr_code IS NOT NULL THEN 1 ELSE 0 END) as with_qr_code,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' THEN 1 ELSE 0 END) as airtel_ts,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TF' THEN 1 ELSE 0 END) as airtel_tf,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA') THEN 1 ELSE 0 END) as airtel_pending
FROM paiements
GROUP BY statut
ORDER BY total DESC;

-- ============================================================================
-- 8. PAIEMENTS À RISQUE (CRITIQUES)
-- ============================================================================

SELECT 
    id,
    matricule,
    montant,
    statut,
    date_paiement,
    qr_code,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    JSON_EXTRACT(details, '$.status') as api_status,
    created_at,
    CASE 
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut != 'payé' THEN 'AIRTEL_SUCCESS_NOT_PAID'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'payé' THEN 'AIRTEL_FAILED_BUT_PAID'
        WHEN qr_code IS NOT NULL AND statut = 'en attente' THEN 'QR_CODE_PENDING'
        WHEN date_paiement IS NOT NULL AND statut != 'payé' THEN 'INVALID_PAYMENT_DATE'
        ELSE 'OTHER'
    END as risk_type
FROM paiements
WHERE 
    (
        (JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut != 'payé')
        OR (JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'payé')
        OR (qr_code IS NOT NULL AND statut = 'en attente')
        OR (date_paiement IS NOT NULL AND statut != 'payé')
    )
ORDER BY 
    CASE 
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut != 'payé' THEN 1
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'payé' THEN 2
        WHEN qr_code IS NOT NULL AND statut = 'en attente' THEN 3
        ELSE 4
    END,
    created_at DESC;

