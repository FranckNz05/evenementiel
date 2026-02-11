-- ============================================================================
-- ÉTAPE 1 : Créer la table de log des incohérences
-- ============================================================================
-- Exécutez ce script dans votre base de données (MySQL/phpMyAdmin)
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

-- Vérification
SELECT 'Table payment_status_inconsistencies_log créée avec succès' as resultat;

