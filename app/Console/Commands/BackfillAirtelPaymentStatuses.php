<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentStatusValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillAirtelPaymentStatuses extends Command
{
    protected $signature = 'payments:backfill-statuses-airtel
                            {--limit=1000 : Nombre maximum de paiements à traiter}
                            {--dry-run : Ne rien écrire, afficher seulement}
                            {--source=backfill_command : Valeur de details.status_change_source}';

    protected $description = 'Corrige les paiements incohérents selon la nouvelle logique Airtel (avec backup + audit Eloquent).';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');
        $source = (string) $this->option('source');

        $payments = Payment::query()
            ->whereNotNull('details')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        $changed = 0;

        foreach ($payments as $payment) {
            $details = json_decode($payment->details ?? '{}', true) ?: [];
            $real = PaymentStatusValidator::determiner_statut_reel($details);

            // Si indéterminé: on ne force rien
            if (!$real) {
                continue;
            }

            $needsUpdate = false;
            if ($payment->statut !== $real) {
                $needsUpdate = true;
            }
            if ($real !== Payment::STATUS_PAID && ($payment->date_paiement || $payment->qr_code)) {
                $needsUpdate = true;
            }
            if ($real === Payment::STATUS_PAID && !$payment->date_paiement) {
                $needsUpdate = true;
            }

            if (!$needsUpdate) {
                continue;
            }

            if ($dryRun) {
                $this->line("DRY-RUN payment_id={$payment->id}: {$payment->statut} -> {$real}");
                continue;
            }

            DB::transaction(function () use ($payment, $details, $real, $source, &$changed) {
                // Backup (une seule fois)
                DB::table('payment_status_fix_backup_20260211')->updateOrInsert(
                    ['payment_id' => $payment->id],
                    [
                        'old_statut' => $payment->getOriginal('statut'),
                        'old_date_paiement' => $payment->getOriginal('date_paiement'),
                        'old_qr_code' => $payment->getOriginal('qr_code'),
                        'old_details' => $payment->getOriginal('details'),
                        'backed_up_at' => now(),
                    ]
                );

                $details['status_change_source'] = $source;
                $details['status_change_reason'] = 'Backfill statuts (nouvelle logique Airtel)';

                $payment->details = json_encode($details);
                $payment->statut = $real;
                $payment->date_paiement = ($real === Payment::STATUS_PAID) ? ($payment->date_paiement ?? now()) : null;
                if ($real !== Payment::STATUS_PAID) {
                    $payment->qr_code = null;
                }

                $payment->save();
                $changed++;
            });
        }

        Log::info('Backfill statuts Airtel terminé', [
            'changed' => $changed,
            'dry_run' => $dryRun,
        ]);

        $this->info("Terminé. Paiements corrigés: {$changed}");

        return 0;
    }
}





