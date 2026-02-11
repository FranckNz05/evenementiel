-- ============================================================================
-- Vérification Complète des Incohérences dans les Données Actuelles
-- ============================================================================
-- Script pour vérifier toutes les incohérences selon les règles établies
-- ============================================================================

-- ============================================================================
-- 1. INCOHÉRENCE CRITIQUE : airtel_transaction_status = TS mais statut ≠ payé
-- ============================================================================
SELECT 
    id,
    matricule,
    statut as current_statut,
    'payé' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    '🚨 CRITIQUE: Airtel confirme le paiement (TS) mais statut ≠ payé' as probleme
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
    AND statut != 'payé'
ORDER BY id DESC;

-- ============================================================================
-- 2. INCOHÉRENCE CRITIQUE : airtel_transaction_status = TF/TE mais statut = payé
-- ============================================================================
SELECT 
    id,
    matricule,
    statut as current_statut,
    'échoué' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    '🚨 CRITIQUE: Airtel confirme l''échec (TF/TE) mais statut = payé' as probleme
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND statut = 'payé'
ORDER BY id DESC;

-- ============================================================================
-- 3. INCOHÉRENCE HIGH : airtel_transaction_status = TIP/TA mais statut = payé
-- ============================================================================
SELECT 
    id,
    matricule,
    statut as current_statut,
    'en attente' as expected_statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    date_paiement,
    qr_code,
    created_at,
    '⚠️ HIGH: Airtel indique en attente (TIP/TA) mais statut = payé' as probleme
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND statut = 'payé'
ORDER BY id DESC;

-- ============================================================================
-- 4. INCOHÉRENCE HIGH : date_paiement renseignée alors que statut ≠ payé
-- ============================================================================
SELECT 
    id,
    matricule,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at,
    '⚠️ HIGH: date_paiement renseignée alors que statut ≠ payé' as probleme
FROM paiements
WHERE 
    date_paiement IS NOT NULL
    AND statut != 'payé'
ORDER BY id DESC;

-- ============================================================================
-- 5. INCOHÉRENCE MEDIUM : date_paiement NULL alors que statut = payé
-- ============================================================================
SELECT 
    id,
    matricule,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at,
    '📊 MEDIUM: date_paiement NULL alors que statut = payé' as probleme
FROM paiements
WHERE 
    statut = 'payé'
    AND date_paiement IS NULL
ORDER BY id DESC;

-- ============================================================================
-- 6. INCOHÉRENCE CRITIQUE : QR code généré alors que statut = en attente
-- ============================================================================
SELECT 
    id,
    matricule,
    statut,
    qr_code,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    created_at,
    '🚨 CRITIQUE: QR code généré alors que statut = en attente' as probleme
FROM paiements
WHERE 
    qr_code IS NOT NULL
    AND statut = 'en attente'
ORDER BY id DESC;

-- ============================================================================
-- 7. RÉSUMÉ : Toutes les incohérences détectées
-- ============================================================================
SELECT 
    'AIRTEL_SUCCESS_NOT_PAID' as type_incoherence,
    COUNT(*) as nombre,
    'CRITICAL' as severite
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
    AND statut != 'payé'

UNION ALL

SELECT 
    'AIRTEL_FAILED_BUT_PAID' as type_incoherence,
    COUNT(*) as nombre,
    'CRITICAL' as severite
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND statut = 'payé'

UNION ALL

SELECT 
    'AIRTEL_PENDING_BUT_PAID' as type_incoherence,
    COUNT(*) as nombre,
    'HIGH' as severite
FROM paiements
WHERE 
    JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND statut = 'payé'

UNION ALL

SELECT 
    'INVALID_PAYMENT_DATE' as type_incoherence,
    COUNT(*) as nombre,
    'HIGH' as severite
FROM paiements
WHERE 
    date_paiement IS NOT NULL
    AND statut != 'payé'

UNION ALL

SELECT 
    'MISSING_PAYMENT_DATE' as type_incoherence,
    COUNT(*) as nombre,
    'MEDIUM' as severite
FROM paiements
WHERE 
    statut = 'payé'
    AND date_paiement IS NULL

UNION ALL

SELECT 
    'QR_CODE_PENDING' as type_incoherence,
    COUNT(*) as nombre,
    'CRITICAL' as severite
FROM paiements
WHERE 
    qr_code IS NOT NULL
    AND statut = 'en attente'

ORDER BY 
    CASE severite
        WHEN 'CRITICAL' THEN 1
        WHEN 'HIGH' THEN 2
        WHEN 'MEDIUM' THEN 3
        ELSE 4
    END,
    nombre DESC;

-- ============================================================================
-- 8. VÉRIFICATION : Paiements avec airtel_transaction_status dans details
-- ============================================================================
SELECT 
    id,
    matricule,
    statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    JSON_EXTRACT(details, '$.status') as api_status,
    date_paiement,
    qr_code,
    CASE 
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut = 'payé' THEN '✅ OK'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'échoué' THEN '✅ OK'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA') AND statut = 'en attente' THEN '✅ OK'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IS NULL THEN '⚠️ Pas de statut Airtel'
        ELSE '❌ INCOHÉRENCE'
    END as verification
FROM paiements
WHERE id >= 230
ORDER BY id DESC
LIMIT 50;

