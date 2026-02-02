<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AirtelMoneyGateway implements PaymentGatewayInterface
{
    protected $airtelService;

    public function __construct()
    {
        $this->airtelService = new AirtelMoneyService();
    }

    /**
     * Créer une session de paiement Airtel Money
     *
     * @param array $data Données du paiement
     * @return array
     */
    public function createPaymentSession(array $data)
    {
        try {
            Log::info('Création de session de paiement Airtel Money', [
                'amount' => $data['amount'] ?? null,
                'phone' => substr($data['phone'] ?? '', -4), // Log seulement les 4 derniers chiffres
                'reference' => $data['reference'] ?? null
            ]);

            // Préparer les données pour l'API Airtel
            $paymentData = [
                'phone' => $data['phone'],
                'amount' => $data['amount'],
                'reference' => $data['reference'] ?? Str::random(16),
                'transaction_id' => $data['transaction_id'] ?? Str::uuid()->toString(),
            ];

            // Initiér le paiement
            $result = $this->airtelService->initiatePayment($paymentData);

            Log::info('Session de paiement Airtel Money créée', [
                'success' => $result['success'] ?? false,
                'transaction_id' => $result['transaction_id'] ?? null,
                'status' => $result['status'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de session de paiement Airtel Money', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le paiement
     *
     * @param string $reference Référence de la transaction
     * @return array
     */
    public function verifyPayment(string $reference)
    {
        try {
            Log::info('Vérification de paiement Airtel Money', [
                'reference' => $reference
            ]);

            // Vérifier le statut de la transaction
            $result = $this->airtelService->checkTransactionStatus($reference);

            Log::info('Paiement Airtel Money vérifié', [
                'reference' => $reference,
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? null,
                'transaction_id' => $result['transaction_id'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification de paiement Airtel Money', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur lors de la vérification du paiement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Effectuer un remboursement
     *
     * @param array $data Données du remboursement
     * @return array
     */
    public function refund(array $data)
    {
        try {
            Log::info('Demande de remboursement Airtel Money', [
                'airtel_money_id' => $data['airtel_money_id'] ?? 'N/A'
            ]);

            // Effectuer le remboursement
            $result = $this->airtelService->refund($data);

            Log::info('Remboursement Airtel Money traité', [
                'airtel_money_id' => $data['airtel_money_id'] ?? 'N/A',
                'success' => $result['success'] ?? false,
                'status' => $result['status'] ?? null
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur lors du remboursement Airtel Money', [
                'airtel_money_id' => $data['airtel_money_id'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur lors du remboursement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Gérer le webhook Airtel Money
     *
     * @param array $payload Données du webhook
     * @return array
     */
    public function handleWebhook(array $payload)
    {
        try {
            Log::info('Webhook Airtel Money reçu', [
                'transaction_id' => $payload['transaction_id'] ?? null,
                'status' => $payload['status'] ?? null,
                'result_code' => $payload['result_code'] ?? null
            ]);

            // Traiter le webhook selon le type d'événement
            $transactionId = $payload['transaction_id'] ?? null;
            $resultCode = $payload['result_code'] ?? null;
            $status = $payload['status'] ?? null;

            // Mapper les codes de résultat Airtel aux statuts standard
            $statusMapping = [
                'DP00800001001' => 'success',     // Transaction réussie
                'DP00800001000' => 'pending',     // Transaction en cours
                'DP00800001006' => 'pending',     // Transaction en cours
                'DP00800001002' => 'failed',      // PIN incorrect
                'DP00800001003' => 'failed',      // Limite dépassée
                'DP00800001004' => 'failed',      // Montant invalide
                'DP00800001005' => 'failed',      // ID transaction invalide
                'DP00800001007' => 'failed',      // Solde insuffisant
                'DP00800001008' => 'failed',      // Transaction refusée
                'DP00800001010' => 'failed',      // Non autorisé
                'DP00800001024' => 'failed',      // Timeout
                'DP00800001025' => 'failed',      // Transaction introuvable
                'DP00800001026' => 'failed',      // Signature invalide
                'DP00800001029' => 'failed',      // Transaction expirée
            ];

            $webhookStatus = isset($statusMapping[$resultCode]) ? $statusMapping[$resultCode] : 'unknown';

            // Retourner les données traitées du webhook
            $result = [
                'success' => $resultCode === 'DP00800001001',
                'status' => $webhookStatus,
                'transaction_id' => $transactionId,
                'reference' => $payload['reference'] ?? null,
                'result_code' => $resultCode,
                'message' => $payload['message'] ?? null,
                'raw_payload' => $payload,
                'gateway' => 'airtel_money',
                'processed_at' => now(),
            ];

            Log::info('Webhook Airtel Money traité', [
                'transaction_id' => $transactionId,
                'result_code' => $resultCode,
                'status' => $webhookStatus,
                'success' => $result['success']
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du webhook Airtel Money', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Erreur lors du traitement du webhook: ' . $e->getMessage(),
                'raw_payload' => $payload,
                'gateway' => 'airtel_money',
                'processed_at' => now(),
            ];
        }
    }
}
