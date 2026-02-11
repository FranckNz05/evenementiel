-- ============================================================================
-- CORRECTION 6 : Synchroniser avec details.status si airtel_transaction_status absent
-- ============================================================================
-- Utiliser details.status comme source secondaire si airtel_transaction_status n'existe pas
-- ============================================================================

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

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 6: Synchronisation avec details.status' as description
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IS NULL
    AND JSON_EXTRACT(`details`, '$.status') IS NOT NULL
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

