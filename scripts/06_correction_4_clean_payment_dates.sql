-- ============================================================================
-- CORRECTION 4 : Nettoyer date_paiement si statut ≠ payé
-- ============================================================================
-- Supprimer les dates de paiement invalides (date_paiement renseignée alors que statut ≠ payé)
-- ============================================================================

UPDATE `paiements`
SET `date_paiement` = NULL
WHERE 
    `date_paiement` IS NOT NULL
    AND `statut` != 'payé';

-- Vérification : Afficher le nombre de paiements corrigés
SELECT 
    COUNT(*) as paiements_corriges,
    'CORRECTION 4: date_paiement nettoyée' as description
FROM `paiements`
WHERE 
    `date_paiement` IS NULL
    AND `statut` != 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 5 MINUTE);

