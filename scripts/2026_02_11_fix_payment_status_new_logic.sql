-- ============================================================================
-- Correction des statuts paiements (nouvelle logique Airtel Money) - 2026-02-11
-- ============================================================================
-- Objectif:
-- - Recalculer `paiements.statut` à partir de `details` (source Airtel + API + verification_result)
-- - Corriger `date_paiement` et `qr_code` pour éviter les "payé" fantômes
-- - Prévoir un rollback via table de backup
--
-- PRÉREQUIS:
-- - Exécuter une sauvegarde de la base avant toute correction.
-- - (Optionnel mais recommandé) Avoir créé les tables via migrations Laravel :
--   - payment_status_changes_log
--   - payment_status_fix_backup_20260211
--
-- NOTE:
-- - Ce script cible MySQL 5.7+ (JSON_EXTRACT/JSON_UNQUOTE).
-- - Les booléens JSON renvoient true/false via JSON_EXTRACT sans quotes.
-- ============================================================================

START TRANSACTION;

-- 1) Créer la table de backup si elle n'existe pas
CREATE TABLE IF NOT EXISTS `payment_status_fix_backup_20260211` (
  `payment_id` BIGINT UNSIGNED NOT NULL,
  `old_statut` VARCHAR(50) NULL,
  `old_date_paiement` TIMESTAMP NULL,
  `old_qr_code` TEXT NULL,
  `old_details` LONGTEXT NULL,
  `backed_up_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `idx_backed_up_at` (`backed_up_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Sauvegarder les lignes susceptibles d'être modifiées (une seule fois)
INSERT IGNORE INTO payment_status_fix_backup_20260211 (payment_id, old_statut, old_date_paiement, old_qr_code, old_details)
SELECT p.id, p.statut, p.date_paiement, p.qr_code, p.details
FROM paiements p
WHERE p.details IS NOT NULL
  AND (
    -- mismatch évident ou champs incohérents
    (p.statut = 'payé' AND (JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) IN ('TF','TE','TIP','TA')))
    OR (p.statut != 'payé' AND JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) = 'TS')
    OR (p.date_paiement IS NOT NULL AND p.statut != 'payé')
    OR (p.qr_code IS NOT NULL AND p.statut != 'payé')
    OR (JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.api_response.status')) = 'success'
        AND JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.verification_result.transaction_status')) = 'TS'
        AND p.statut != 'payé')
  );

-- 3) Recalculer le statut selon la nouvelle logique
-- Règles:
-- - payé: airtel_transaction_status=TS OU (api_response.status=success ET verification_result.transaction_status=TS)
-- - en attente: airtel_transaction_status=TIP/TA OU api_response.status=pending OU requires_user_action=true
-- - échoué: airtel_transaction_status=TF/TE OU api_response.success=false ET retry=false
UPDATE paiements p
SET
  p.statut = (
    CASE
      WHEN JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) = 'TS' THEN 'payé'
      WHEN JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) IN ('TIP','TA') THEN 'en attente'
      WHEN JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) IN ('TF','TE') THEN 'échoué'

      WHEN JSON_EXTRACT(p.details, '$.requires_user_action') = true
        OR JSON_EXTRACT(p.details, '$.api_response.requires_user_action') = true
        OR JSON_EXTRACT(p.details, '$.verification_result.requires_user_action') = true
        THEN 'en attente'

      WHEN JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.api_response.status')) = 'pending' THEN 'en attente'

      WHEN JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.api_response.status')) = 'success'
        AND JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.verification_result.transaction_status')) = 'TS'
        THEN 'payé'

      WHEN JSON_EXTRACT(p.details, '$.api_response.success') = false
        AND (JSON_EXTRACT(p.details, '$.api_response.retry') = false)
        THEN 'échoué'

      ELSE p.statut
    END
  ),
  p.date_paiement = (
    CASE
      WHEN (
        JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) = 'TS'
        OR (
          JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.api_response.status')) = 'success'
          AND JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.verification_result.transaction_status')) = 'TS'
        )
      )
      THEN COALESCE(
        p.date_paiement,
        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.initiated_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
        p.updated_at,
        p.created_at
      )
      ELSE NULL
    END
  ),
  p.qr_code = (
    CASE
      WHEN (
        JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.airtel_transaction_status')) = 'TS'
        OR (
          JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.api_response.status')) = 'success'
          AND JSON_UNQUOTE(JSON_EXTRACT(p.details, '$.verification_result.transaction_status')) = 'TS'
        )
      )
      THEN p.qr_code
      ELSE NULL
    END
  )
WHERE p.details IS NOT NULL;

COMMIT;

-- ============================================================================
-- ROLLBACK (si problème)
-- ============================================================================
-- START TRANSACTION;
-- UPDATE paiements p
-- JOIN payment_status_fix_backup_20260211 b ON b.payment_id = p.id
-- SET
--   p.statut = b.old_statut,
--   p.date_paiement = b.old_date_paiement,
--   p.qr_code = b.old_qr_code,
--   p.details = b.old_details;
-- COMMIT;
--
-- (Optionnel) DROP TABLE payment_status_fix_backup_20260211;
-- ============================================================================









