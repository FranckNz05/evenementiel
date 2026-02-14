<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe déjà
        if (Schema::hasTable('views')) {
            return; // La table existe déjà, ne rien faire
        }
        
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key vers users (sans contrainte pour éviter les erreurs)
            $table->unsignedBigInteger('viewable_id')->nullable(); // ID de l'objet visualisé (polymorphique)
            $table->string('viewable_type')->nullable(); // Type de l'objet (Event, Blog, Organizer, etc.)
            $table->string('viewed_type')->nullable(); // Type alternatif utilisé par certaines relations (Event, Blog)
            $table->timestamp('update_at')->nullable(); // Note: le modèle utilise 'update_at' au lieu de 'updated_at'
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['viewable_id', 'viewable_type']);
            $table->index(['viewable_id', 'viewed_type']);
            $table->index('user_id');
        });
        
        // Ajouter la contrainte de clé étrangère seulement si la table users existe
        // Utiliser une requête SQL directe pour éviter les erreurs de schéma
        try {
            if (Schema::hasTable('users')) {
                DB::statement('ALTER TABLE views ADD CONSTRAINT views_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {
            // Ignorer l'erreur si la contrainte ne peut pas être ajoutée
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};

