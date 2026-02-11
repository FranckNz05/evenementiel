-- ============================================================================
-- Script SQL de Correction des Incohérences de Statuts de Paiements
-- Fintech - Système de paiement mobile money (Airtel Money, M-Pesa, Orange)
-- ============================================================================
-- 
-- Ce script :
-- 1. Crée les triggers de validation pour garantir la cohérence
-- 2. Corrige les données existantes
-- 3. Crée la table de log pour les incohérences
--
-- ⚠️ ATTENTION : Faites une sauvegarde de votre base de données avant d'exécuter ce script
-- ============================================================================

-- ============================================================================
-- PARTIE 1: Création de la table de log des incohérences
-- ============================================================================

DROP TABLE IF EXISTS `payment_status_inconsistencies_log`;

CREATE TABLE `payment_status_inconsistencies_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payment_id` INT NOT NULL,
    `inconsistency_type` VARCHAR(50) NOT NULL,
    `severity` VARCHAR(20) NOT NULL COMMENT 'CRITICAL, HIGH, MEDIUM, LOW',
    `current_statut` VARCHAR(50) NOT NULL,
    `airtel_status` VARCHAR(10) NULL,
    `api_status` VARCHAR(50) NULL,
    `expected_statut` VARCHAR(50) NULL,
    `description` TEXT NOT NULL,
    `risk_description` TEXT NULL,
    `auto_fixed` TINYINT(1) DEFAULT 0,
    `detected_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fixed_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_payment_id` (`payment_id`),
    INDEX `idx_inconsistency_type` (`inconsistency_type`),
    INDEX `idx_severity` (`severity`),
    INDEX `idx_detected_at` (`detected_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PARTIE 2: Suppression des triggers existants (si présents)
-- ============================================================================

DROP TRIGGER IF EXISTS `validate_payment_status_before_update`;
DROP TRIGGER IF EXISTS `validate_payment_status_before_insert`;

-- ============================================================================
-- PARTIE 3: Création des triggers de validation
-- ============================================================================

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

-- ============================================================================
-- PARTIE 4: Correction des données existantes
-- ============================================================================

-- CORRECTION 1: Synchroniser statut avec airtel_transaction_status = TS (Transaction Success)
-- Mettre à jour les paiements confirmés par Airtel mais non marqués comme payés
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

-- CORRECTION 2: Synchroniser statut avec airtel_transaction_status = TF ou TE (Transaction Failed/Expired)
-- Mettre à jour les paiements échoués selon Airtel mais marqués comme payés
UPDATE `paiements`
SET 
    `statut` = 'échoué',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'payé';

-- CORRECTION 3: Synchroniser statut avec airtel_transaction_status = TIP ou TA (Transaction in Progress/Ambiguous)
-- Mettre à jour les paiements en attente selon Airtel mais marqués comme payés
UPDATE `paiements`
SET 
    `statut` = 'en attente',
    `date_paiement` = NULL
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'payé';

-- CORRECTION 4: Nettoyer date_paiement si statut ≠ payé
-- Supprimer les dates de paiement invalides
UPDATE `paiements`
SET `date_paiement` = NULL
WHERE 
    `date_paiement` IS NOT NULL
    AND `statut` != 'payé';

-- CORRECTION 5: Ajouter date_paiement si statut = payé mais date_paiement NULL
-- Récupérer la date depuis les détails JSON ou utiliser updated_at/created_at
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

-- CORRECTION 6: Synchroniser avec details.status si airtel_transaction_status absent
-- Utiliser details.status comme source secondaire
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

-- ============================================================================
-- PARTIE 5: Vérification des corrections
-- ============================================================================

-- Afficher le nombre de paiements corrigés par type
SELECT 
    'CORRECTION 1: TS → payé' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') = 'TS'
    AND `statut` = 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 2: TF/TE → échoué' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TF', 'TE')
    AND `statut` = 'échoué'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 3: TIP/TA → en attente' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    JSON_EXTRACT(`details`, '$.airtel_transaction_status') IN ('TIP', 'TA')
    AND `statut` = 'en attente'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 4: date_paiement nettoyée' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    `date_paiement` IS NULL
    AND `statut` != 'payé'
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)

UNION ALL

SELECT 
    'CORRECTION 5: date_paiement ajoutée' as correction_type,
    COUNT(*) as paiements_affectes
FROM `paiements`
WHERE 
    `statut` = 'payé'
    AND `date_paiement` IS NOT NULL
    AND `updated_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- ============================================================================
-- PARTIE 6: Vérification des triggers
-- ============================================================================

-- Vérifier que les triggers sont bien créés
SHOW TRIGGERS LIKE 'paiements';

-- ============================================================================
-- PARTIE 7: Statistiques finales
-- ============================================================================

-- Statistiques par statut après correction
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

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================
-- 
-- Résumé des actions effectuées :
-- ✅ Table de log créée
-- ✅ Triggers de validation créés
-- ✅ Données existantes corrigées
-- 
-- Prochaines étapes :
-- 1. Vérifier les résultats des corrections
-- 2. Exécuter le script d'analyse pour détecter les incohérences restantes
-- 3. Mettre en place un monitoring régulier
-- ============================================================================

