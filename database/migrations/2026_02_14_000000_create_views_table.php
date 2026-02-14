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
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};

