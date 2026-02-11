<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ajoute des contraintes de validation pour garantir la cohérence des statuts de paiement
     */
    public function up()
    {
        // Note: MySQL ne supporte pas directement les CHECK constraints dans les anciennes versions
        // Nous utiliserons des triggers à la place
        
        // Supprimer le trigger s'il existe déjà
        DB::unprepared('DROP TRIGGER IF EXISTS validate_payment_status_before_update');
        
        // Créer le trigger pour valider la cohérence statut/date_paiement
        DB::unprepared("
            CREATE TRIGGER validate_payment_status_before_update
            BEFORE UPDATE ON paiements
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
            END
        ");
        
        // Créer le trigger pour INSERT aussi
        DB::unprepared('DROP TRIGGER IF EXISTS validate_payment_status_before_insert');
        
        DB::unprepared("
            CREATE TRIGGER validate_payment_status_before_insert
            BEFORE INSERT ON paiements
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
            END
        ");
        
        // Créer une table de log pour les incohérences détectées
        if (!Schema::hasTable('payment_status_inconsistencies_log')) {
            Schema::create('payment_status_inconsistencies_log', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_id');
                $table->string('inconsistency_type', 50);
                $table->string('severity', 20); // CRITICAL, HIGH, MEDIUM, LOW
                $table->string('current_statut', 50);
                $table->string('airtel_status', 10)->nullable();
                $table->string('api_status', 50)->nullable();
                $table->string('expected_statut', 50)->nullable();
                $table->text('description');
                $table->text('risk_description')->nullable();
                $table->boolean('auto_fixed')->default(false);
                $table->timestamp('detected_at');
                $table->timestamp('fixed_at')->nullable();
                
                $table->index('payment_id');
                $table->index('inconsistency_type');
                $table->index('severity');
                $table->index('detected_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS validate_payment_status_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS validate_payment_status_before_insert');
        
        Schema::dropIfExists('payment_status_inconsistencies_log');
    }
};


