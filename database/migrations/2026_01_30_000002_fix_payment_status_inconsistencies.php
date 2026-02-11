<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrige les incohérences de statuts existantes dans les paiements
     */
    public function up()
    {
        Log::info('Début de la correction des incohérences de statuts de paiements');
        
        // CORRECTION 1: Synchroniser statut avec airtel_transaction_status = TS
        $updated1 = DB::update("
            UPDATE paiements
            SET 
                statut = 'payé',
                date_paiement = COALESCE(
                    date_paiement,
                    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                    STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                    updated_at,
                    created_at
                )
            WHERE 
                JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS'
                AND statut != 'payé'
        ");
        
        Log::info("Correction 1: {$updated1} paiements mis à jour (TS → payé)");
        
        // CORRECTION 2: Synchroniser statut avec airtel_transaction_status = TF ou TE
        $updated2 = DB::update("
            UPDATE paiements
            SET 
                statut = 'échoué',
                date_paiement = NULL
            WHERE 
                JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE')
                AND statut = 'payé'
        ");
        
        Log::info("Correction 2: {$updated2} paiements mis à jour (TF/TE → échoué)");
        
        // CORRECTION 3: Synchroniser statut avec airtel_transaction_status = TIP ou TA
        $updated3 = DB::update("
            UPDATE paiements
            SET 
                statut = 'en attente',
                date_paiement = NULL
            WHERE 
                JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA')
                AND statut = 'payé'
        ");
        
        Log::info("Correction 3: {$updated3} paiements mis à jour (TIP/TA → en attente)");
        
        // CORRECTION 4: Nettoyer date_paiement si statut ≠ payé
        $updated4 = DB::update("
            UPDATE paiements
            SET date_paiement = NULL
            WHERE 
                date_paiement IS NOT NULL
                AND statut != 'payé'
        ");
        
        Log::info("Correction 4: {$updated4} paiements nettoyés (date_paiement supprimée)");
        
        // CORRECTION 5: Ajouter date_paiement si statut = payé mais date_paiement NULL
        $updated5 = DB::update("
            UPDATE paiements
            SET date_paiement = COALESCE(
                STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.initiated_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                updated_at,
                created_at
            )
            WHERE 
                statut = 'payé'
                AND date_paiement IS NULL
        ");
        
        Log::info("Correction 5: {$updated5} paiements mis à jour (date_paiement ajoutée)");
        
        // CORRECTION 6: Synchroniser avec details.status si airtel_transaction_status absent
        $updated6 = DB::update("
            UPDATE paiements
            SET 
                statut = CASE 
                    WHEN JSON_EXTRACT(details, '$.status') = 'success' THEN 'payé'
                    WHEN JSON_EXTRACT(details, '$.status') IN ('failed', 'error', 'expired', 'timeout', 'refused') THEN 'échoué'
                    WHEN JSON_EXTRACT(details, '$.status') IN ('pending', 'ambiguous') THEN 'en attente'
                    ELSE statut
                END,
                date_paiement = CASE 
                    WHEN JSON_EXTRACT(details, '$.status') = 'success' AND date_paiement IS NULL THEN 
                        COALESCE(
                            STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.callback_received_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                            STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(details, '$.verified_at')), '%Y-%m-%dT%H:%i:%s.%fZ'),
                            updated_at
                        )
                    WHEN JSON_EXTRACT(details, '$.status') != 'success' THEN NULL
                    ELSE date_paiement
                END
            WHERE 
                JSON_EXTRACT(details, '$.airtel_transaction_status') IS NULL
                AND JSON_EXTRACT(details, '$.status') IS NOT NULL
                AND (
                    (JSON_EXTRACT(details, '$.status') = 'success' AND statut != 'payé')
                    OR (JSON_EXTRACT(details, '$.status') IN ('failed', 'error', 'expired', 'timeout', 'refused') AND statut = 'payé')
                    OR (JSON_EXTRACT(details, '$.status') IN ('pending', 'ambiguous') AND statut = 'payé')
                )
        ");
        
        Log::info("Correction 6: {$updated6} paiements mis à jour (basés sur details.status)");
        
        $total = $updated1 + $updated2 + $updated3 + $updated4 + $updated5 + $updated6;
        Log::info("Correction terminée: {$total} paiements corrigés au total");
    }

    /**
     * Reverse the migrations.
     * 
     * Note: On ne peut pas vraiment "annuler" ces corrections car on ne connaît pas
     * les valeurs originales. Cette méthode est laissée vide intentionnellement.
     */
    public function down()
    {
        // Ne rien faire - on ne peut pas annuler ces corrections
        Log::warning('Impossible d\'annuler les corrections de statuts (valeurs originales inconnues)');
    }
};


