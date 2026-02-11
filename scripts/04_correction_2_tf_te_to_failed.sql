-- ============================================================================
-- CORRECTION 2 : Synchroniser statut avec airtel_transaction_status = TF ou TE
-- ============================================================================
-- Mettre à jour les paiements échoués selon Airtel (TF/TE) mais marqués comme payés
-- ============================================================================

UPDATE `paiements`
SET 
    `statut` = 'échoué',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'payé';

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 2: TF/TE → échoué' as description
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'échoué'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

