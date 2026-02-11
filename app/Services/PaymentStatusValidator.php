<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

/**
 * Service de validation et synchronisation des statuts de paiement
 * 
 * Garantit la cohérence entre :
 * - statut (métier)
 * - details.status (API)
 * - airtel_transaction_status (opérateur)
 */
class PaymentStatusValidator
{
    /**
     * Mapping strict des statuts Airtel vers statuts métier
     */
    private const AIRTEL_STATUS_MAPPING = [
        'TS' => 'payé',           // Transaction Success → payé
        'TF' => 'échoué',          // Transaction Failed → échoué
        'TE' => 'échoué',          // Transaction Expired → échoué
        'TA' => 'en attente',      // Transaction Ambiguous → en attente
        'TIP' => 'en attente',     // Transaction in Progress → en attente
    ];

    /**
     * Mapping des statuts API vers statuts métier
     */
    private const API_STATUS_MAPPING = [
        'success' => 'payé',
        'failed' => 'échoué',
        'error' => 'échoué',
        'pending' => 'en attente',
        'ambiguous' => 'en attente',
        'expired' => 'échoué',
        'timeout' => 'échoué',
        'refused' => 'échoué',
    ];

    /**
     * Détermine le statut réel (métier) à partir du JSON `details`.
     *
     * Spécification:
     * - "payé" uniquement si:
     *   - airtel_transaction_status = "TS" (source absolue) OU
     *   - api_response.status = "success" ET verification_result.transaction_status = "TS"
     * - "en attente" si:
     *   - airtel_transaction_status = "TIP" OU
     *   - api_response.status = "pending" OU
     *   - requires_user_action = true
     * - "échoué" si:
     *   - airtel_transaction_status = "TF" (Transaction Failed) OU
     *   - success=false sans possibilité de retry
     *
     * @param array|string|null $details
     * @return string|null 'payé'|'en attente'|'échoué' ou null si indéterminé
     */
    public static function determiner_statut_reel(array|string|null $details): ?string
    {
        if (is_string($details)) {
            $decoded = json_decode($details, true);
            $details = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($details)) {
            $details = [];
        }

        // 1) Source de vérité opérateur (Airtel)
        $airtelStatus = $details['airtel_transaction_status']
            ?? $details['callback_status']
            ?? data_get($details, 'verification_result.transaction_status')
            ?? $details['transaction_status']
            ?? null;

        if ($airtelStatus === 'TS') {
            return 'payé';
        }

        if ($airtelStatus === 'TIP' || $airtelStatus === 'TA') {
            return 'en attente';
        }

        if ($airtelStatus === 'TF' || $airtelStatus === 'TE') {
            return 'échoué';
        }

        // 2) Signaux applicatifs (API + vérification)
        $requiresUserAction = (bool) (data_get($details, 'requires_user_action')
            ?? data_get($details, 'api_response.requires_user_action')
            ?? data_get($details, 'verification_result.requires_user_action')
            ?? false);

        if ($requiresUserAction) {
            return 'en attente';
        }

        $apiStatus = data_get($details, 'api_response.status') ?? ($details['status'] ?? null);
        $verificationStatus = data_get($details, 'verification_result.transaction_status');

        // En attente si l'API indique pending
        if ($apiStatus === 'pending' || $apiStatus === 'ambiguous') {
            return 'en attente';
        }

        // Payé uniquement si API success + verification_result TS (si pas de statut Airtel direct)
        if ($apiStatus === 'success' && $verificationStatus === 'TS') {
            return 'payé';
        }

        // Si la vérification indique TIP/TA, on considère non-final → en attente
        if ($apiStatus === 'success' && in_array($verificationStatus, ['TIP', 'TA'], true)) {
            return 'en attente';
        }

        // Échoué si success=false ET retry impossible (sans possibilité de retry)
        $anySuccessFlag = data_get($details, 'api_response.success');
        if (!is_bool($anySuccessFlag)) {
            $anySuccessFlag = data_get($details, 'verification_result.success');
        }

        $retry = data_get($details, 'api_response.retry');
        if (!is_bool($retry)) {
            $retry = data_get($details, 'verification_result.retry');
        }

        if ($anySuccessFlag === false) {
            // Si retry explicitement false → échec final
            if ($retry === false) {
                return 'échoué';
            }

            // Si retry explicite true → garder "en attente" (non-final, action/retry possible)
            if ($retry === true) {
                return 'en attente';
            }
        }

        // Fallback: si l'API indique explicitement un échec et qu'il n'y a pas de retry
        if (in_array($apiStatus, ['failed', 'error', 'expired', 'timeout', 'refused'], true)) {
            if ($retry === false) {
                return 'échoué';
            }
            if ($retry === true) {
                return 'en attente';
            }
        }

        return null;
    }

    /**
     * Détermine le statut attendu basé sur les sources de vérité
     * 
     * @param Payment $payment
     * @return string|null Statut attendu ou null si aucune source disponible
     */
    public function getExpectedStatus(Payment $payment): ?string
    {
        $details = json_decode($payment->details ?? '{}', true) ?: [];

        // Statut calculé selon la nouvelle logique
        $real = self::determiner_statut_reel($details);
        if ($real) {
            return $real;
        }

        // Fallback ultra-prudent: si on n'arrive pas à déterminer, ne rien changer
        return null;
    }

    /**
     * Valide et synchronise le statut d'un paiement
     * 
     * @param Payment $payment
     * @return bool True si des corrections ont été apportées
     */
    public function validateAndSync(Payment $payment): bool
    {
        $expectedStatus = $this->getExpectedStatus($payment);
        $updated = false;
        
        // Synchroniser le statut si nécessaire
        if ($expectedStatus && $payment->statut !== $expectedStatus) {
            Log::warning('Synchronisation automatique du statut', [
                'payment_id' => $payment->id,
                'matricule' => $payment->matricule,
                'current' => $payment->statut,
                'expected' => $expectedStatus,
            ]);
            
            $payment->statut = $expectedStatus;
            $updated = true;
        }
        
        // Valider et corriger date_paiement
        if ($this->validatePaymentDate($payment)) {
            $updated = true;
        }

        // Nettoyer le QR code si statut != payé (sécurité: éviter livraison sans paiement confirmé)
        if (!empty($payment->qr_code) && $payment->statut !== 'payé') {
            Log::warning('Nettoyage qr_code: statut ≠ payé', [
                'payment_id' => $payment->id,
                'statut' => $payment->statut,
                'qr_code' => $payment->qr_code,
            ]);

            $payment->qr_code = null;
            $updated = true;
        }
        
        if ($updated) {
            $payment->save();
        }
        
        return $updated;
    }

    /**
     * Valide et corrige date_paiement selon le statut
     * 
     * @param Payment $payment
     * @return bool True si des corrections ont été apportées
     */
    private function validatePaymentDate(Payment $payment): bool
    {
        $updated = false;
        $details = json_decode($payment->details ?? '{}', true) ?: [];
        
        // Règle 1: date_paiement ne peut être renseignée que si statut = 'payé'
        if ($payment->date_paiement && $payment->statut !== 'payé') {
            Log::warning('Nettoyage date_paiement: statut ≠ payé', [
                'payment_id' => $payment->id,
                'statut' => $payment->statut,
            ]);
            
            $payment->date_paiement = null;
            $updated = true;
        }
        
        // Règle 2: date_paiement doit être renseignée si statut = 'payé'
        if ($payment->statut === 'payé' && !$payment->date_paiement) {
            // Essayer de récupérer la date depuis les détails
            $paymentDate = $details['callback_received_at'] ?? 
                         $details['verified_at'] ?? 
                         $details['initiated_at'] ?? 
                         null;
            
            if ($paymentDate) {
                try {
                    $payment->date_paiement = \Carbon\Carbon::parse($paymentDate);
                } catch (\Exception $e) {
                    $payment->date_paiement = now();
                }
            } else {
                $payment->date_paiement = now();
            }
            
            Log::info('Ajout date_paiement: statut = payé', [
                'payment_id' => $payment->id,
                'date' => $payment->date_paiement,
            ]);
            
            $updated = true;
        }
        
        return $updated;
    }

    /**
     * Valide qu'un QR code peut être généré pour ce paiement
     * 
     * @param Payment $payment
     * @return bool
     * @throws \Exception Si le QR code ne peut pas être généré
     */
    public function validateQrCodeGeneration(Payment $payment): bool
    {
        // Guard 1: Le statut doit être 'payé'
        if ($payment->statut !== 'payé') {
            throw new \Exception(
                "CRITIQUE: Impossible de générer un QR code - statut = '{$payment->statut}' (attendu: 'payé')"
            );
        }
        
        // Guard 2: Vérifier airtel_transaction_status si disponible
        $details = json_decode($payment->details ?? '{}', true) ?: [];
        $airtelStatus = $details['airtel_transaction_status'] ?? null;
        
        if ($airtelStatus && $airtelStatus !== 'TS') {
            throw new \Exception(
                "CRITIQUE: Impossible de générer un QR code - Airtel status = '{$airtelStatus}' (attendu: 'TS')"
            );
        }
        
        return true;
    }

    /**
     * Vérifie les incohérences d'un paiement
     * 
     * @param Payment $payment
     * @return array Liste des incohérences détectées
     */
    public function checkInconsistencies(Payment $payment): array
    {
        $inconsistencies = [];
        $details = json_decode($payment->details ?? '{}', true) ?: [];
        
        $airtelStatus = $details['airtel_transaction_status'] ?? 
                       $details['callback_status'] ?? 
                       null;
        
        $apiStatus = $details['status'] ?? null;
        $expectedStatus = $this->getExpectedStatus($payment);
        
        // Incohérence 1: Statut métier ≠ Statut attendu
        if ($expectedStatus && $payment->statut !== $expectedStatus) {
            $inconsistencies[] = [
                'type' => 'STATUS_MISMATCH',
                'severity' => 'CRITICAL',
                'description' => "Statut métier '{$payment->statut}' ≠ statut attendu '{$expectedStatus}'",
            ];
        }
        
        // Incohérence 2: date_paiement renseignée alors que statut ≠ payé
        if ($payment->date_paiement && $payment->statut !== 'payé') {
            $inconsistencies[] = [
                'type' => 'INVALID_PAYMENT_DATE',
                'severity' => 'HIGH',
                'description' => "date_paiement renseignée alors que statut = '{$payment->statut}'",
            ];
        }
        
        // Incohérence 3: date_paiement NULL alors que statut = payé
        if (!$payment->date_paiement && $payment->statut === 'payé') {
            $inconsistencies[] = [
                'type' => 'MISSING_PAYMENT_DATE',
                'severity' => 'MEDIUM',
                'description' => "date_paiement NULL alors que statut = 'payé'",
            ];
        }
        
        // Incohérence 4: QR code généré alors que statut = en attente
        if (!empty($payment->qr_code) && $payment->statut === 'en attente') {
            $inconsistencies[] = [
                'type' => 'QR_CODE_PENDING',
                'severity' => 'CRITICAL',
                'description' => "QR code généré alors que statut = 'en attente'",
            ];
        }
        
        // Incohérence 5: airtel_transaction_status = TS mais statut ≠ payé
        if ($airtelStatus === 'TS' && $payment->statut !== 'payé') {
            $inconsistencies[] = [
                'type' => 'AIRTEL_SUCCESS_NOT_PAID',
                'severity' => 'CRITICAL',
                'description' => "Airtel confirme le paiement (TS) mais statut = '{$payment->statut}'",
            ];
        }
        
        // Incohérence 6: airtel_transaction_status = TF/TE mais statut = payé
        if (in_array($airtelStatus, ['TF', 'TE']) && $payment->statut === 'payé') {
            $inconsistencies[] = [
                'type' => 'AIRTEL_FAILED_BUT_PAID',
                'severity' => 'CRITICAL',
                'description' => "Airtel confirme l'échec ({$airtelStatus}) mais statut = 'payé'",
            ];
        }
        
        return $inconsistencies;
    }

    /**
     * Mappe un statut Airtel vers un statut métier
     * 
     * @param string|null $airtelStatus
     * @return string|null
     */
    public static function mapAirtelStatus(?string $airtelStatus): ?string
    {
        return self::AIRTEL_STATUS_MAPPING[$airtelStatus] ?? null;
    }

    /**
     * Mappe un statut API vers un statut métier
     * 
     * @param string|null $apiStatus
     * @return string|null
     */
    public static function mapApiStatus(?string $apiStatus): ?string
    {
        return self::API_STATUS_MAPPING[$apiStatus] ?? null;
    }
}

