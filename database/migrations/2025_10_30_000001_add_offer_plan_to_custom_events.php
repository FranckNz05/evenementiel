<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('custom_events', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_events', 'offer_plan')) {
                $table->string('offer_plan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('custom_events', function (Blueprint $table) {
            $table->dropColumn('offer_plan');
        });
    }
};


