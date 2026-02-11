-- ============================================================================
-- CORRECTION 3 : Synchroniser statut avec airtel_transaction_status = TIP ou TA
-- ============================================================================
-- Mettre à jour les paiements en attente selon Airtel (TIP/TA) mais marqués comme payés
-- ============================================================================

UPDATE `paiements`
SET 
    `statut` = 'en attente',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'payé';

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 3: TIP/TA → en attente' as description
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'en attente'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

