-- ============================================================================
-- Script SQL : Création des Triggers de Validation pour les Paiements
-- ============================================================================
-- 
-- Ce script crée uniquement les triggers de validation.
-- Utilisez ce script si vous voulez seulement les triggers sans corriger les données.
-- ============================================================================

-- Supprimer les triggers existants (si présents)
DROP TRIGGER IF EXISTS `validate_payment_status_before_update`;
DROP TRIGGER IF EXISTS `validate_payment_status_before_insert`;

-- Créer les triggers
DELIMITER $$

-- Trigger pour UPDATE : Valide la cohérence statut/date_paiement avant mise à jour
CREATE TRIGGER `validate_payment_status_before_update`
BEFORE UPDATE ON `paiements`
FOR EACH ROW
BEGIN
    -- Règle 1: date_paiement ne peut être renseignée que si statut = 'payé'
    IF NEW.date_paiement IS NOT NULL AND NEW.statut != 'payé' THEN
        SET NEW.date_paiement = NULL;
    END IF;
    
    -- Règle 2: date_paiement doit être renseignée si statut = 'payé'
    IF NEW.statut = 'payé' AND NEW.date_paiement IS NULL THEN
        SET NEW.date_paiement = COALESCE(
            OLD.date_paiement,
            NEW.updated_at,
            NOW()
        );
    END IF;
END$$

-- Trigger pour INSERT : Valide la cohérence statut/date_paiement avant insertion
CREATE TRIGGER `validate_payment_status_before_insert`
BEFORE INSERT ON `paiements`
FOR EACH ROW
BEGIN
    -- Règle 1: date_paiement ne peut être renseignée que si statut = 'payé'
    IF NEW.date_paiement IS NOT NULL AND NEW.statut != 'payé' THEN
        SET NEW.date_paiement = NULL;
    END IF;
    
    -- Règle 2: date_paiement doit être renseignée si statut = 'payé'
    IF NEW.statut = 'payé' AND NEW.date_paiement IS NULL THEN
        SET NEW.date_paiement = COALESCE(NEW.created_at, NOW());
    END IF;
END$$

DELIMITER ;

-- Vérifier que les triggers sont bien créés
SHOW TRIGGERS LIKE 'paiements';

