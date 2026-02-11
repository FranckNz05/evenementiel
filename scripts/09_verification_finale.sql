-- ============================================================================
-- VÉRIFICATION FINALE : Statistiques après corrections
-- ============================================================================
-- Exécutez ce script pour vérifier que toutes les corrections ont été appliquées
-- ============================================================================

-- Statistiques par statut
SELECT 
    `statut`,
    COUNT(*) as total,
    SUM(CASE WHEN `date_paiement` IS NOT NULL THEN 1 ELSE 0 END) as with_payment_date,
    SUM(CASE WHEN `qr_code` IS NOT NULL THEN 1 ELSE 0 END) as with_qr_code,
    SUM(CASE WHEN JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS' THEN 1 ELSE 0 END) as airtel_ts,
    SUM(CASE WHEN JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TF' THEN 1 ELSE 0 END) as airtel_tf,
    SUM(CASE WHEN JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA') THEN 1 ELSE 0 END) as airtel_pending
FROM `paiements`
GROUP BY `statut`
ORDER BY total DESC;

-- Vérifier les incohérences restantes (devrait être 0)
SELECT 
    'AIRTEL_SUCCESS_NOT_PAID' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS'
    AND `statut` != 'payé'

UNION ALL

SELECT 
    'AIRTEL_FAILED_BUT_PAID' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'payé'

UNION ALL

SELECT 
    'AIRTEL_PENDING_BUT_PAID' as type,
    COUNT(*) as count,
    'HIGH' as severity
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'payé'

UNION ALL

SELECT 
    'INVALID_PAYMENT_DATE' as type,
    COUNT(*) as count,
    'HIGH' as severity
FROM `paiements`
WHERE 
    `date_paiement` IS NOT NULL
    AND `statut` != 'payé'

UNION ALL

SELECT 
    'MISSING_PAYMENT_DATE' as type,
    COUNT(*) as count,
    'MEDIUM' as severity
FROM `paiements`
WHERE 
    `statut` = 'payé'
    AND `date_paiement` IS NULL

UNION ALL

SELECT 
    'QR_CODE_PENDING' as type,
    COUNT(*) as count,
    'CRITICAL' as severity
FROM `paiements`
WHERE 
    `qr_code` IS NOT NULL
    AND `statut` = 'en attente'

ORDER BY 
    CASE severity
        WHEN 'CRITICAL' THEN 1
        WHEN 'HIGH' THEN 2
        WHEN 'MEDIUM' THEN 3
        ELSE 4
    END,
    count DESC;

-- Vérifier les triggers
SHOW TRIGGERS LIKE 'paiements';

