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
        Schema::table('shares', function (Blueprint $table) {
            if (!Schema::hasColumn('shares', 'event_id')) {
                $table->foreignId('event_id')->constrained()->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('shares', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('event_id');
            }
            if (!Schema::hasColumn('shares', 'platform')) {
                $table->string('platform', 50)->comment('whatsapp, facebook, twitter, copy_link, etc.')->after('user_id');
            }
            if (!Schema::hasColumn('shares', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('platform');
            }
            if (!Schema::hasColumn('shares', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('shares', 'shared_at')) {
                $table->timestamp('shared_at')->useCurrent()->after('user_agent');
            }
        });
        
        // Ajouter les index (en utilisant DB::statement pour éviter les erreurs)
        $columns = Schema::getColumnListing('shares');
        
        if (in_array('event_id', $columns)) {
            try {
                DB::statement('CREATE INDEX shares_event_id_index ON shares(event_id)');
            } catch (\Exception $e) {
                // Index existe déjà ou erreur
            }
        }
        
        if (in_array('user_id', $columns)) {
            try {
                DB::statement('CREATE INDEX shares_user_id_index ON shares(user_id)');
            } catch (\Exception $e) {
                // Index existe déjà ou erreur
            }
        }
        
        if (in_array('platform', $columns)) {
            try {
                DB::statement('CREATE INDEX shares_platform_index ON shares(platform(20))');
            } catch (\Exception $e) {
                // Index existe déjà ou erreur
            }
        }
        
        if (in_array('shared_at', $columns)) {
            try {
                DB::statement('CREATE INDEX shares_shared_at_index ON shares(shared_at)');
            } catch (\Exception $e) {
                // Index existe déjà ou erreur
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shares', function (Blueprint $table) {
            //
        });
    }
};
