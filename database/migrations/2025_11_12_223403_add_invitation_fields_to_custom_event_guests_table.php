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
            $table->string('invitation_code', 64)->nullable()->after('status');
            $table->timestamp('scheduled_at')->nullable()->after('invitation_code');
            $table->timestamp('sent_at')->nullable()->after('scheduled_at');
            $table->string('sent_via')->nullable()->after('sent_at'); // email, sms, whatsapp
            $table->text('invitation_message')->nullable()->after('sent_via');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_event_guests', function (Blueprint $table) {
            $table->dropColumn([
                'invitation_code',
                'scheduled_at',
                'sent_at',
                'sent_via',
                'invitation_message',
            ]);
        });
    }
};
