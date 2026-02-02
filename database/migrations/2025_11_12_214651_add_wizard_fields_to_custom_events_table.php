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
        Schema::table('custom_events', function (Blueprint $table) {
            // Ajouter la colonne type si elle n'existe pas
            if (!Schema::hasColumn('custom_events', 'type')) {
                $table->string('type')->nullable()->after('category');
            }
            
            // Ajouter la colonne description si elle n'existe pas
            if (!Schema::hasColumn('custom_events', 'description')) {
                $table->text('description')->nullable()->after('location');
            }
            
            // Ajouter la colonne image si elle n'existe pas
            if (!Schema::hasColumn('custom_events', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            
            // Ajouter la colonne guest_limit si elle n'existe pas
            if (!Schema::hasColumn('custom_events', 'guest_limit')) {
                $table->integer('guest_limit')->nullable()->after('image');
            }
            
            // Ajouter la colonne invitation_link si elle n'existe pas
            if (!Schema::hasColumn('custom_events', 'invitation_link')) {
                $table->string('invitation_link')->nullable()->after('url');
            }
            
            // Rendre end_date nullable si ce n'est pas déjà le cas
            if (Schema::hasColumn('custom_events', 'end_date')) {
                $table->dateTime('end_date')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_events', function (Blueprint $table) {
            if (Schema::hasColumn('custom_events', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('custom_events', 'description')) {
                $table->dropColumn('description');
            }
            
            if (Schema::hasColumn('custom_events', 'image')) {
                $table->dropColumn('image');
            }
            
            if (Schema::hasColumn('custom_events', 'guest_limit')) {
                $table->dropColumn('guest_limit');
            }
            
            if (Schema::hasColumn('custom_events', 'invitation_link')) {
                $table->dropColumn('invitation_link');
            }
        });
    }
};
