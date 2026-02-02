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
        // Pour modifier une colonne existante, on utilise DB::statement
        // car change() nécessite doctrine/dbal
        DB::statement('ALTER TABLE `custom_events` MODIFY COLUMN `category` VARCHAR(255) NULL');
        
        // Rendre url nullable aussi pour plus de flexibilité
        DB::statement('ALTER TABLE `custom_events` MODIFY COLUMN `url` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre category en NOT NULL
        DB::statement('ALTER TABLE `custom_events` MODIFY COLUMN `category` VARCHAR(255) NOT NULL');
        
        // Remettre url en NOT NULL
        DB::statement('ALTER TABLE `custom_events` MODIFY COLUMN `url` VARCHAR(255) NOT NULL');
    }
};
