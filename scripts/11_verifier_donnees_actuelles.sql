-- ============================================================================
-- Vérification des Données Actuelles
-- ============================================================================
-- Script pour vérifier les incohérences dans les paiements récents
-- ============================================================================

-- Vérifier les incohérences de statut avec airtel_transaction_status
SELECT 
    id,
    matricule,
    statut,
    date_paiement,
    qr_code,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    JSON_EXTRACT(details, '$.status') as api_status,
    CASE 
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut != 'payé' THEN '❌ TS mais statut ≠ payé'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'payé' THEN '❌ TF/TE mais statut = payé'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA') AND statut = 'payé' THEN '❌ TIP/TA mais statut = payé'
        WHEN date_paiement IS NOT NULL AND statut != 'payé' THEN '❌ date_paiement renseignée mais statut ≠ payé'
        WHEN statut = 'payé' AND date_paiement IS NULL THEN '⚠️ statut = payé mais date_paiement NULL'
        WHEN qr_code IS NOT NULL AND statut = 'en attente' THEN '🚨 QR code généré mais statut = en attente'
        ELSE '✅ OK'
    END as verification
FROM paiements
WHERE id >= 249
ORDER BY id DESC;

-- Statistiques par statut
SELECT 
    statut,
    COUNT(*) as total,
    SUM(CASE WHEN date_paiement IS NOT NULL THEN 1 ELSE 0 END) as with_date,
    SUM(CASE WHEN qr_code IS NOT NULL THEN 1 ELSE 0 END) as with_qr,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' THEN 1 ELSE 0 END) as airtel_ts,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') THEN 1 ELSE 0 END) as airtel_failed,
    SUM(CASE WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA') THEN 1 ELSE 0 END) as airtel_pending
FROM paiements
WHERE id >= 249
GROUP BY statut;

-- Paiements avec QR code mais statut ≠ payé (CRITIQUE)
SELECT 
    id,
    matricule,
    statut,
    qr_code,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status
FROM paiements
WHERE 
    id >= 249
    AND qr_code IS NOT NULL
    AND statut != 'payé'
ORDER BY id DESC;

-- Paiements payés sans date_paiement
SELECT 
    id,
    matricule,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status
FROM paiements
WHERE 
    id >= 249
    AND statut = 'payé'
    AND date_paiement IS NULL
ORDER BY id DESC;

-- Paiements avec date_paiement mais statut ≠ payé
SELECT 
    id,
    matricule,
    statut,
    date_paiement,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status
FROM paiements
WHERE 
    id >= 249
    AND date_paiement IS NOT NULL
    AND statut != 'payé'
ORDER BY id DESC;

