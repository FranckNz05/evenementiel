-- ============================================================================
-- CORRECTION 1 : Synchroniser statut avec airtel_transaction_status = TS
-- ============================================================================
-- Mettre à jour les paiements confirmés par Airtel (TS) mais non marqués comme payés
-- ============================================================================

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

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 1: TS → payé' as description
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS'
    AND `statut` = 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

