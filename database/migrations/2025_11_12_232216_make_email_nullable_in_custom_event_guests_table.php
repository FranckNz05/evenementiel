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
        Schema::table('custom_event_guests', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_event_guests', function (Blueprint $table) {
            // Remettre email comme NOT NULL (mais cela peut échouer si des valeurs NULL existent)
            $table->string('email')->nullable(false)->change();
        });
    }
};
