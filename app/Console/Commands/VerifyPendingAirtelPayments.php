<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\AirtelMoneyService;
use App\Services\PaymentStatusValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyPendingAirtelPayments extends Command
{
    protected $signature = 'payments:verify-pending-airtel
                            {--limit=200 : Nombre maximum de paiements à vérifier}
                            {--older-than-minutes=2 : Ignorer les paiements trop récents}
                            {--notify-after=3 : Notifier l’admin après N incohérences persistantes}';

    protected $description = 'Vérifie périodiquement les paiements Airtel Money en attente (polling) et synchronise leur statut réel.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $olderThan = (int) $this->option('older-than-minutes');
        $notifyAfter = (int) $this->option('notify-after');

        $airtel = app(AirtelMoneyService::class);
        $validator = app(PaymentStatusValidator::class);

        $payments = Payment::query()
            ->where('statut', Payment::STATUS_PENDING)
            ->where('methode_paiement', 'Airtel Money')
            ->whereNotNull('details')
            ->where('created_at', '<=', now()->subMinutes(max(0, $olderThan)))
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        $this->info("Vérification Airtel: {$payments->count()} paiements en attente...");

        foreach ($payments as $payment) {
            $details = json_decode($payment->details ?? '{}', true) ?: [];

            $transactionId = $details['airtel_transaction_id']
                ?? $payment->reference_paiement
                ?? $payment->reference_transaction
                ?? null;

            if (!$transactionId) {
                Log::warning('Paiement en attente sans transactionId Airtel', [
                    'payment_id' => $payment->id,
                    'matricule' => $payment->matricule,
                ]);
                continue;
            }

            try {
                $verification = $airtel->checkTransactionStatus($transactionId);

                $merged = array_merge($details, [
                    'verification_result' => $verification,
                    'last_verified_at' => now()->toISOString(),
                    'status_change_source' => 'cron_verification',
                    'status_change_reason' => 'Vérification périodique Airtel',
                ]);

                if (!empty($verification['transaction_status'])) {
                    $merged['airtel_transaction_status'] = $verification['transaction_status'];
                }

                $realStatus = PaymentStatusValidator::determiner_statut_reel($merged);
                if (!$realStatus) {
                    $this->registerInconsistency($payment, $merged, 'Statut réel indéterminé après polling', $notifyAfter);
                    continue;
                }

                DB::transaction(function () use ($payment, $merged, $realStatus, $validator) {
                    $payment->details = json_encode($merged);
                    $payment->statut = $realStatus;
                    $payment->date_paiement = ($realStatus === Payment::STATUS_PAID) ? now() : null;
                    if ($realStatus !== Payment::STATUS_PAID) {
                        $payment->qr_code = null;
                    }

                    $payment->save();

                    // Dernière passe: appliquer les règles de cohérence (date/qr)
                    $validator->validateAndSync($payment);
                });
            } catch (\Throwable $e) {
                Log::error('Erreur cron vérification Airtel', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return 0;
    }

    private function registerInconsistency(Payment $payment, array $details, string $message, int $notifyAfter): void
    {
        $counter = (int) ($details['inconsistency_counter'] ?? 0);
        $counter++;
        $details['inconsistency_counter'] = $counter;
        $details['last_inconsistency_at'] = now()->toISOString();
        $details['last_inconsistency_message'] = $message;

        $adminNotifiedAt = $details['admin_notified_at'] ?? null;

        if ($counter >= $notifyAfter && !$adminNotifiedAt) {
            $this->notifyAdmin($payment, $message, $details);
            $details['admin_notified_at'] = now()->toISOString();
        }

        $payment->details = json_encode($details);
        $payment->save();

        Log::warning('Incohérence persistante paiement Airtel', [
            'payment_id' => $payment->id,
            'counter' => $counter,
            'message' => $message,
        ]);
    }

    private function notifyAdmin(Payment $payment, string $message, array $details): void
    {
        $adminEmail = config('services.admin.email');
        if (!$adminEmail) {
            Log::warning('ADMIN_EMAIL non configuré: notification admin non envoyée', [
                'payment_id' => $payment->id,
            ]);
            return;
        }

        try {
            $subject = "[ALERTE] Incohérence persistante paiement Airtel (payment_id={$payment->id})";
            $body = "Paiement Airtel incohérent de façon persistante.\n\n"
                . "Payment ID: {$payment->id}\n"
                . "Matricule: {$payment->matricule}\n"
                . "Statut actuel: {$payment->statut}\n"
                . "Message: {$message}\n"
                . "Reference transaction: {$payment->reference_transaction}\n"
                . "Reference paiement: {$payment->reference_paiement}\n\n"
                . "Aperçu details:\n"
                . substr(json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 0, 4000);

            Mail::raw($body, function ($m) use ($adminEmail, $subject) {
                $m->to($adminEmail)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error('Erreur notification admin (mail) incohérence paiement', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}










