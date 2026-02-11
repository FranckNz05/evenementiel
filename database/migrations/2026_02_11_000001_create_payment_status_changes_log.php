<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_status_changes_log')) {
            return;
        }

        Schema::create('payment_status_changes_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');

            $table->string('from_statut', 50)->nullable();
            $table->string('to_statut', 50)->nullable();

            $table->timestamp('from_date_paiement')->nullable();
            $table->timestamp('to_date_paiement')->nullable();

            $table->text('from_qr_code')->nullable();
            $table->text('to_qr_code')->nullable();

            $table->string('source', 50)->nullable(); // airtel_callback|cron_verification|migration|manual|...
            $table->string('reason', 255)->nullable();
            $table->json('meta')->nullable();

            $table->timestamp('changed_at')->useCurrent();

            $table->index('payment_id');
            $table->index('changed_at');
            $table->index('to_statut');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_status_changes_log');
    }
};





