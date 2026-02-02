<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Préférences de catégories (JSON: {category_id: view_count, ...})
            $table->json('preferred_categories')->nullable()->comment('Catégories préférées avec nombre de vues');
            
            // Préférences de localisation (JSON: {"ville": view_count, ...})
            $table->json('preferred_locations')->nullable()->comment('Villes préférées avec nombre de vues');
            
            // Utilisateurs similaires (JSON: {user_id: similarity_score, ...})
            $table->json('similar_users')->nullable()->comment('IDs des utilisateurs similaires avec scores de similarité');
            
            // Statistiques
            $table->unsignedInteger('total_views_count')->default(0)->comment('Nombre total de vues d\'événements');
            $table->unsignedInteger('unique_events_viewed')->default(0)->comment('Nombre d\'événements uniques consultés');
            
            // Métadonnées de mise à jour
            $table->timestamp('last_preferences_update_at')->nullable()->comment('Dernière mise à jour des préférences');
            $table->timestamp('last_similarity_update_at')->nullable()->comment('Dernière mise à jour de la similarité');
            $table->timestamp('last_recommendation_generated_at')->nullable()->comment('Dernière génération de recommandations');
            
            // Données brutes pour ML (optionnel)
            $table->json('view_history_summary')->nullable()->comment('Résumé de l\'historique de vues pour ML');
            $table->json('ml_features')->nullable()->comment('Features extraites pour machine learning');
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('last_preferences_update_at');
            $table->index('last_similarity_update_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
