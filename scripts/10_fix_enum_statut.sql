-- ============================================================================
-- CORRECTION : Ajouter 'annulé' à l'ENUM statut si nécessaire
-- ============================================================================
-- 
-- Le modèle Payment.php définit STATUS_CANCELLED = 'annulé'
-- mais l'ENUM de la base de données ne contient que : 'en attente', 'payé', 'échoué'
-- 
-- Ce script ajoute 'annulé' à l'ENUM pour être en accord avec le code Laravel
-- ============================================================================

-- Vérifier d'abord si 'annulé' existe déjà dans l'ENUM
SELECT 
    COLUMN_TYPE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'paiements'
    AND COLUMN_NAME = 'statut';

-- Modifier l'ENUM pour ajouter 'annulé'
-- Note: MySQL ne permet pas de modifier directement un ENUM, il faut recréer la colonne
ALTER TABLE `paiements` 
MODIFY COLUMN `statut` ENUM('en attente', 'payé', 'échoué', 'annulé') 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci 
NOT NULL 
DEFAULT 'en attente';

-- Vérification
SELECT 
    COLUMN_TYPE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'paiements'
    AND COLUMN_NAME = 'statut';

SELECT 'ENUM statut mis à jour avec succès (ajout de ''annulé'')' as resultat;

