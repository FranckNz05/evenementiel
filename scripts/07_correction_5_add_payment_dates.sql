-- ============================================================================
-- CORRECTION 5 : Ajouter date_paiement si statut = payé mais date_paiement NULL
-- ============================================================================
-- Récupérer la date depuis les détails JSON ou utiliser updated_at/created_at
-- ============================================================================

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

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 5: date_paiement ajoutée' as description
FROM `paiements`
WHERE 
    `statut` = 'payé'
    AND `date_paiement` IS NOT NULL
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

