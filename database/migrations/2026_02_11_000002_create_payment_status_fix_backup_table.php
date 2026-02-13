<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_status_fix_backup_20260211')) {
            return;
        }

        Schema::create('payment_status_fix_backup_20260211', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id')->primary();
            $table->string('old_statut', 50)->nullable();
            $table->timestamp('old_date_paiement')->nullable();
            $table->text('old_qr_code')->nullable();
            $table->longText('old_details')->nullable();
            $table->timestamp('backed_up_at')->useCurrent();

            $table->index('backed_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_status_fix_backup_20260211');
    }
};








