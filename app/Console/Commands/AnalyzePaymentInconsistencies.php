<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentStatusValidator;

/**
 * Commande pour analyser les incohérences de statuts dans les paiements
 * 
 * Détecte les incohérences entre :
 * - statut (état métier)
 * - details.status (état API)
 * - airtel_transaction_status (état réel opérateur)
 */
class AnalyzePaymentInconsistencies extends Command
{
    protected $signature = 'payments:analyze-inconsistencies 
                            {--export-csv : Exporter les résultats en CSV}
                            {--fix : Corriger automatiquement les incohérences détectées}';

    protected $description = 'Analyse les incohérences de statuts dans les paiements (fintech)';

    // Mapping déplacé dans PaymentStatusValidator (source unique de vérité)

    public function handle()
    {
        $this->info('🔍 Analyse des incohérences de statuts de paiements...');
        $this->newLine();

        $inconsistencies = $this->detectInconsistencies();
        
        if (empty($inconsistencies)) {
            $this->info('✅ Aucune incohérence détectée !');
            return 0;
        }

        $this->displayInconsistencies($inconsistencies);
        
        if ($this->option('export-csv')) {
            $this->exportToCsv($inconsistencies);
        }

        if ($this->option('fix')) {
            if ($this->confirm('⚠️  Voulez-vous corriger automatiquement ces incohérences ?', false)) {
                $this->fixInconsistencies($inconsistencies);
            }
        }

        return 0;
    }

    /**
     * Détecte toutes les incohérences de statuts
     */
    private function detectInconsistencies(): array
    {
        $inconsistencies = [];
        
        $payments = Payment::whereNotNull('details')
            ->get();

        foreach ($payments as $payment) {
            $details = json_decode($payment->details ?? '{}', true) ?: [];
            
            $airtelStatus = $details['airtel_transaction_status'] ?? 
                          $details['callback_status'] ?? 
                          $details['transaction_status'] ?? 
                          null;
            
            $apiStatus = $details['status'] ?? null;
            $businessStatus = $payment->statut;

            // Statut réel calculé depuis `details` (nouvelle logique)
            $expectedStatus = PaymentStatusValidator::determiner_statut_reel($details);
            
            $issues = $this->checkInconsistencies(
                $payment,
                $businessStatus,
                $apiStatus,
                $airtelStatus,
                $expectedStatus,
                $details
            );

            if (!empty($issues)) {
                $inconsistencies[] = [
                    'payment_id' => $payment->id,
                    'matricule' => $payment->matricule,
                    'montant' => $payment->montant,
                    'current_statut' => $businessStatus,
                    'airtel_status' => $airtelStatus,
                    'api_status' => $apiStatus,
                    'expected_statut' => $expectedStatus,
                    'date_paiement' => $payment->date_paiement,
                    'has_qr_code' => !empty($payment->qr_code),
                    'issues' => $issues,
                    'details' => $details,
                ];
            }
        }

        return $inconsistencies;
    }

    /**
     * Vérifie les incohérences pour un paiement
     */
    private function checkInconsistencies(
        Payment $payment,
        string $businessStatus,
        ?string $apiStatus,
        ?string $airtelStatus,
        ?string $expectedStatus,
        array $details
    ): array {
        $issues = [];

        // INCOHÉRENCE 1: Statut métier ≠ Statut attendu depuis airtel_transaction_status
        if ($expectedStatus && $businessStatus !== $expectedStatus) {
            $issues[] = [
                'type' => 'STATUS_MISMATCH',
                'severity' => 'CRITICAL',
                'description' => "Le statut métier '{$businessStatus}' ne correspond pas au statut Airtel '{$airtelStatus}' (attendu: '{$expectedStatus}')",
                'risk' => $this->getRiskDescription('STATUS_MISMATCH', $businessStatus, $expectedStatus),
            ];
        }

        // INCOHÉRENCE 2: date_paiement renseignée alors que statut ≠ payé
        if ($payment->date_paiement && $businessStatus !== 'payé') {
            $issues[] = [
                'type' => 'INVALID_PAYMENT_DATE',
                'severity' => 'HIGH',
                'description' => "date_paiement renseignée ({$payment->date_paiement}) alors que statut = '{$businessStatus}'",
                'risk' => 'Risque de reporting erroné: montants comptabilisés comme payés alors qu\'ils ne le sont pas.',
            ];
        }

        // INCOHÉRENCE 3: date_paiement NULL alors que statut = payé
        if (!$payment->date_paiement && $businessStatus === 'payé') {
            $issues[] = [
                'type' => 'MISSING_PAYMENT_DATE',
                'severity' => 'MEDIUM',
                'description' => "date_paiement NULL alors que statut = 'payé'",
                'risk' => 'Impossible de tracer la date exacte du paiement pour la comptabilité.',
            ];
        }

        // INCOHÉRENCE 4: QR code généré alors que statut = en attente (risque de livraison sans paiement)
        if (!empty($payment->qr_code) && $businessStatus === 'en attente') {
            $issues[] = [
                'type' => 'QR_CODE_PENDING',
                'severity' => 'CRITICAL',
                'description' => "QR code généré alors que statut = 'en attente'",
                'risk' => 'RISQUE CRITIQUE: Un billet QR peut être utilisé alors que le paiement n\'est pas confirmé. Risque de fraude et de perte financière.',
            ];
        }

        // INCOHÉRENCE 5: airtel_transaction_status = TS mais statut ≠ payé
        if ($airtelStatus === 'TS' && $businessStatus !== 'payé') {
            $issues[] = [
                'type' => 'AIRTEL_SUCCESS_NOT_PAID',
                'severity' => 'CRITICAL',
                'description' => "Airtel confirme le paiement (TS) mais statut métier = '{$businessStatus}'",
                'risk' => 'RISQUE CRITIQUE: L\'opérateur confirme le paiement mais le système ne l\'a pas enregistré. Risque de litige et de perte financière.',
            ];
        }

        // INCOHÉRENCE 6: airtel_transaction_status = TF/TE mais statut = payé
        if (in_array($airtelStatus, ['TF', 'TE']) && $businessStatus === 'payé') {
            $issues[] = [
                'type' => 'AIRTEL_FAILED_BUT_PAID',
                'severity' => 'CRITICAL',
                'description' => "Airtel confirme l'échec ({$airtelStatus}) mais statut métier = 'payé'",
                'risk' => 'RISQUE CRITIQUE: L\'opérateur confirme l\'échec mais le système indique payé. Risque de livraison sans paiement réel.',
            ];
        }

        // INCOHÉRENCE 7: airtel_transaction_status = TIP/TA mais statut = payé
        if (in_array($airtelStatus, ['TIP', 'TA']) && $businessStatus === 'payé') {
            $issues[] = [
                'type' => 'AIRTEL_PENDING_BUT_PAID',
                'severity' => 'HIGH',
                'description' => "Airtel indique en attente ({$airtelStatus}) mais statut métier = 'payé'",
                'risk' => 'Le paiement est marqué comme payé alors qu\'il est encore en attente. Risque de livraison prématurée.',
            ];
        }

        // INCOHÉRENCE 8: Incohérence entre details.status et airtel_transaction_status
        if ($apiStatus && $airtelStatus) {
            $apiExpected = null; // volontairement: l'API n'est plus source finale de "payé"
            $airtelExpected = null;
            
            if ($apiExpected && $airtelExpected && $apiExpected !== $airtelExpected) {
                $issues[] = [
                    'type' => 'API_AIRTEL_MISMATCH',
                    'severity' => 'MEDIUM',
                    'description' => "Incohérence entre details.status ({$apiStatus} → {$apiExpected}) et airtel_transaction_status ({$airtelStatus} → {$airtelExpected})",
                    'risk' => 'Conflit entre deux sources de vérité. La source Airtel (airtel_transaction_status) doit être prioritaire.',
                ];
            }
        }

        return $issues;
    }

    /**
     * Retourne la description du risque selon le type d'incohérence
     */
    private function getRiskDescription(string $type, string $currentStatus, string $expectedStatus): string
    {
        $risks = [
            'STATUS_MISMATCH' => [
                'payé' => 'RISQUE CRITIQUE: Paiement marqué comme payé alors qu\'il ne l\'est pas. Risque de livraison sans paiement réel.',
                'échoué' => 'Paiement marqué comme échoué alors qu\'il pourrait être en attente ou réussi. Risque de perte de revenus.',
                'en attente' => 'Paiement marqué comme en attente alors qu\'il est peut-être réussi. Risque de non-livraison malgré paiement.',
            ],
        ];

        return $risks[$type][$currentStatus] ?? 'Incohérence de statut détectée.';
    }

    /**
     * Affiche les incohérences détectées
     */
    private function displayInconsistencies(array $inconsistencies): void
    {
        $this->warn("⚠️  {count($inconsistencies)} paiements avec incohérences détectées");
        $this->newLine();

        // Grouper par type d'incohérence
        $byType = [];
        foreach ($inconsistencies as $inc) {
            foreach ($inc['issues'] as $issue) {
                $byType[$issue['type']][] = $inc;
            }
        }

        foreach ($byType as $type => $payments) {
            $this->error("📊 Type: {$type} ({count($payments)} paiements)");
            
            foreach (array_slice($payments, 0, 5) as $payment) {
                $issue = $payment['issues'][0];
                $this->line("  • ID {$payment['payment_id']} ({$payment['matricule']}): {$issue['description']}");
                $this->line("    Risque: {$issue['risk']}");
            }
            
            if (count($payments) > 5) {
                $this->line("  ... et " . (count($payments) - 5) . " autres");
            }
            $this->newLine();
        }

        // Statistiques
        $this->info('📈 Statistiques:');
        $this->table(
            ['Type', 'Sévérité', 'Nombre'],
            $this->getStatistics($inconsistencies)
        );
    }

    /**
     * Génère les statistiques
     */
    private function getStatistics(array $inconsistencies): array
    {
        $stats = [];
        
        foreach ($inconsistencies as $inc) {
            foreach ($inc['issues'] as $issue) {
                $key = $issue['type'] . '|' . $issue['severity'];
                $stats[$key] = ($stats[$key] ?? 0) + 1;
            }
        }

        $result = [];
        foreach ($stats as $key => $count) {
            [$type, $severity] = explode('|', $key);
            $result[] = [$type, $severity, $count];
        }

        return $result;
    }

    /**
     * Exporte les résultats en CSV
     */
    private function exportToCsv(array $inconsistencies): void
    {
        $filename = storage_path('app/payment_inconsistencies_' . date('Y-m-d_His') . '.csv');
        $file = fopen($filename, 'w');

        // En-têtes
        fputcsv($file, [
            'ID', 'Matricule', 'Montant', 'Statut Actuel', 'Airtel Status', 
            'API Status', 'Statut Attendu', 'Date Paiement', 'QR Code', 
            'Type Incohérence', 'Sévérité', 'Description', 'Risque'
        ]);

        foreach ($inconsistencies as $inc) {
            foreach ($inc['issues'] as $issue) {
                fputcsv($file, [
                    $inc['payment_id'],
                    $inc['matricule'],
                    $inc['montant'],
                    $inc['current_statut'],
                    $inc['airtel_status'] ?? 'N/A',
                    $inc['api_status'] ?? 'N/A',
                    $inc['expected_statut'] ?? 'N/A',
                    $inc['date_paiement'] ?? 'NULL',
                    $inc['has_qr_code'] ? 'Oui' : 'Non',
                    $issue['type'],
                    $issue['severity'],
                    $issue['description'],
                    $issue['risk'],
                ]);
            }
        }

        fclose($file);
        $this->info("✅ Résultats exportés dans: {$filename}");
    }

    /**
     * Corrige automatiquement les incohérences
     */
    private function fixInconsistencies(array $inconsistencies): void
    {
        $this->info('🔧 Correction des incohérences...');
        
        $fixed = 0;
        $errors = 0;

        DB::beginTransaction();
        
        try {
            foreach ($inconsistencies as $inc) {
                $payment = Payment::find($inc['payment_id']);
                
                if (!$payment) {
                    $errors++;
                    continue;
                }

                $details = json_decode($payment->details ?? '{}', true) ?: [];
                $updated = false;

                // Correction 1: Mettre à jour le statut selon airtel_transaction_status
                if ($inc['expected_statut'] && $payment->statut !== $inc['expected_statut']) {
                    $payment->statut = $inc['expected_statut'];
                    $updated = true;
                    
                    // Mettre à jour date_paiement si nécessaire
                    if ($inc['expected_statut'] === 'payé' && !$payment->date_paiement) {
                        // Essayer de récupérer la date depuis les détails
                        $payment->date_paiement = $details['callback_received_at'] ?? 
                                                 $details['verified_at'] ?? 
                                                 $details['initiated_at'] ?? 
                                                 now();
                    } elseif ($inc['expected_statut'] !== 'payé' && $payment->date_paiement) {
                        // Supprimer date_paiement si le statut n'est pas payé
                        $payment->date_paiement = null;
                    }
                }

                // Correction 2: Supprimer date_paiement si statut ≠ payé
                if ($payment->date_paiement && $payment->statut !== 'payé') {
                    $payment->date_paiement = null;
                    $updated = true;
                }

                // Correction 3: Ajouter date_paiement si statut = payé mais date_paiement NULL
                if (!$payment->date_paiement && $payment->statut === 'payé') {
                    $payment->date_paiement = $details['callback_received_at'] ?? 
                                            $details['verified_at'] ?? 
                                            $details['initiated_at'] ?? 
                                            now();
                    $updated = true;
                }

                // Mettre à jour les détails avec le statut corrigé
                $details['status_corrected_at'] = now()->toISOString();
                $details['previous_statut'] = $inc['current_statut'];
                $payment->details = json_encode($details);

                if ($updated) {
                    $payment->save();
                    $fixed++;
                }
            }

            DB::commit();
            $this->info("✅ {$fixed} paiements corrigés avec succès");
            
            if ($errors > 0) {
                $this->warn("⚠️  {$errors} erreurs lors de la correction");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Erreur lors de la correction: " . $e->getMessage());
            Log::error('Erreur correction incohérences paiements', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

