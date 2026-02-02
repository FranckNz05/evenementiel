<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Payment;
use App\Models\CustomOfferPurchase;
use App\Models\Reservation;
use App\Mail\PaymentConfirmation;
use Illuminate\Support\Str;

class AirtelCallbackController extends Controller
{
    /**
     * Gère le callback d'Airtel Money avec authentification HMAC
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallback(Request $request)
    {
        try {
            // Logger la requête brute pour le débogage
            Log::info('Callback Airtel Money reçu', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'raw_body' => $request->getContent(),
            ]);

            // Valider la structure de la requête
            $request->validate([
                'transaction.id' => 'required|string',
                'transaction.message' => 'required|string',
                'transaction.status_code' => 'required|string|in:TS,TF',
                'transaction.airtel_money_id' => 'required|string',
                'hash' => 'required|string',
            ]);

            $transaction = $request->input('transaction');
            $receivedHash = $request->input('hash');

            // Vérifier la signature HMAC si l'authentification est activée
            $callbackAuthEnabled = config('services.airtel.callback_auth_enabled', true);
            
            if ($callbackAuthEnabled) {
                $isValid = $this->verifyHash($request, $receivedHash);
                
                if (!$isValid) {
                    Log::warning('Callback Airtel Money - Signature invalide', [
                        'transaction_id' => $transaction['id'],
                        'received_hash' => $receivedHash,
                    ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Signature invalide'
                    ], 403);
                }

                Log::info('Callback Airtel Money - Signature vérifiée avec succès', [
                    'transaction_id' => $transaction['id'],
                ]);
            }

            // Traiter le callback
            $result = $this->processCallback($transaction);

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Callback traité avec succès'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation callback Airtel Money', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Exception lors du traitement du callback Airtel Money', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du traitement du callback'
            ], 500);
        }
    }

    /**
     * Vérifie la signature HMAC du callback
     * 
     * Selon la documentation Airtel :
     * - Algorithme: HMAC SHA256
     * - Format de sortie: Base64
     * - Clé: Private key depuis les paramètres de l'application Airtel
     * - Payload: JSON de la requête sans le champ 'hash'
     * 
     * @param Request $request
     * @param string $receivedHash
     * @return bool
     */
    protected function verifyHash(Request $request, string $receivedHash): bool
    {
        try {
            $privateKey = config('services.airtel.callback_private_key');
            
            if (empty($privateKey)) {
                Log::warning('Clé privée Airtel non configurée pour la vérification du callback');
                return false;
            }

            // Récupérer toutes les données de la requête
            $allData = $request->all();
            
            // Retirer le champ 'hash' pour calculer le hash
            $dataToHash = $allData;
            unset($dataToHash['hash']);
            
            // Convertir en JSON avec formatage cohérent
            // Important: utiliser JSON_UNESCAPED_SLASHES pour correspondre au format Airtel
            $payloadToHash = json_encode($dataToHash, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Générer le hash HMAC SHA256 en Base64
            // Le 4ème paramètre 'true' indique de retourner les données brutes (binary)
            $calculatedHash = base64_encode(hash_hmac('sha256', $payloadToHash, $privateKey, true));

            Log::info('Vérification hash callback Airtel (HMAC SHA256)', [
                'payload_length' => strlen($payloadToHash),
                'payload_preview' => substr($payloadToHash, 0, 200) . '...',
                'calculated_hash' => $calculatedHash,
                'received_hash' => $receivedHash,
                'match' => hash_equals($calculatedHash, $receivedHash)
            ]);

            // Utiliser hash_equals pour éviter les attaques par timing
            $isValid = hash_equals($calculatedHash, $receivedHash);
            
            if (!$isValid) {
                Log::warning('Hash callback Airtel ne correspond pas', [
                    'calculated' => $calculatedHash,
                    'received' => $receivedHash,
                    'payload_preview' => substr($payloadToHash, 0, 200)
                ]);
            }

            return $isValid;

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du hash', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Traite le callback et met à jour les transactions
     * 
     * @param array $transaction
     * @return array
     */
    protected function processCallback(array $transaction): array
    {
        DB::beginTransaction();

        try {
            $transactionId = $transaction['id'];
            $statusCode = $transaction['status_code']; // TS ou TF
            $airtelMoneyId = $transaction['airtel_money_id'];
            $message = $transaction['message'];

            Log::info('Traitement callback Airtel Money', [
                'transaction_id' => $transactionId,
                'status_code' => $statusCode,
                'airtel_money_id' => $airtelMoneyId,
                'message' => $message,
            ]);

            // Chercher la transaction dans les paiements
            $payment = Payment::where('reference_transaction', $transactionId)
                ->orWhere('reference_transaction', 'like', '%' . $transactionId . '%')
                ->first();

            // Chercher aussi dans les réservations (pour les nouveaux paiements Airtel Money)
            $reservation = null;
            if (!$payment && Str::startsWith($transactionId, 'RSV-')) {
                // Extraire l'ID de réservation du format RSV-{id}-{timestamp}
                $parts = explode('-', $transactionId);
                if (count($parts) >= 3) {
                    $reservationId = $parts[1];
                    $reservation = Reservation::with(['payments'])->find($reservationId);

                    // Chercher le paiement associé à cette réservation
                    if ($reservation && $reservation->payments) {
                        $payment = $reservation->payments->where('reference_transaction', $transactionId)->first();
                    }
                }
            }

            // Chercher aussi dans les CustomOfferPurchase
            $purchase = CustomOfferPurchase::where('transaction_id', $transactionId)
                ->orWhere('transaction_id', 'like', '%' . $transactionId . '%')
                ->first();

            $isSuccess = ($statusCode === 'TS');

            // Mettre à jour le paiement si trouvé
            if ($payment) {
                $existingDetails = json_decode($payment->details ?? '{}', true) ?: [];
                
                // Vérifier si le paiement a déjà été traité (éviter les doublons)
                $alreadyProcessed = ($payment->statut === 'payé' && $isSuccess) || 
                                    ($payment->statut === 'échoué' && !$isSuccess);
                
                // Traduire le message Airtel en français
                $translatedMessage = $this->translateAirtelMessage($message);
                
                $updateData = [
                    'statut' => $isSuccess ? 'payé' : 'échoué',
                    'date_paiement' => $isSuccess ? now() : null,
                    'details' => json_encode(array_merge(
                        $existingDetails,
                        [
                            'airtel_callback' => true,
                            'airtel_money_id' => $airtelMoneyId,
                            'callback_status' => $statusCode, // TS ou TF
                            'callback_message' => $translatedMessage,
                            'callback_received_at' => now()->toISOString(),
                            'airtel_transaction_status' => $statusCode, // TS ou TF
                            // Stocker le message pour les succès aussi
                            'airtel_message' => $translatedMessage,
                        ],
                        // Si échec, stocker aussi les informations d'erreur pour affichage
                        !$isSuccess ? [
                            'airtel_error_code' => $statusCode,
                            'error_message' => $translatedMessage,
                        ] : []
                    )),
                ];
                
                $payment->update($updateData);

                // Si le paiement est réussi (TS), mettre à jour la commande
                if ($isSuccess && $payment->order) {
                    $payment->order->update([
                        'statut' => 'payé',
                        'payment_status' => 'payé',
                        'paid_at' => now(),
                    ]);

                    // Mettre à jour les quantités de tickets
                    foreach ($payment->order->tickets as $ticket) {
                        $quantity = $ticket->pivot->quantity ?? 1;
                        $ticket->increment('quantite_vendue', $quantity);
                    }

                    // Envoyer l'email UNIQUEMENT si pas déjà envoyé (éviter les doublons)
                    // Vérifier explicitement que l'email n'a pas été envoyé
                    $emailAlreadySent = ($existingDetails['confirmation_email_sent'] ?? false) === true;
                    
                    if ($isSuccess && !$alreadyProcessed && !$emailAlreadySent) {
                        try {
                            $payment->load(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user']);
                            
                            $paymentController = app(\App\Http\Controllers\PaymentController::class);
                            $reflection = new \ReflectionClass($paymentController);
                            $method = $reflection->getMethod('generateTicketsPdf');
                            $method->setAccessible(true);
                            
                            $pdfContent = $method->invoke($paymentController, $payment);
                            
                            if ($pdfContent) {
                                Mail::to($payment->user->email)
                                    ->send(new PaymentConfirmation($payment, $pdfContent));
                            } else {
                                Mail::to($payment->user->email)
                                    ->send(new PaymentConfirmation($payment));
                            }

                            // Marquer IMMÉDIATEMENT que l'email a été envoyé pour éviter les doublons
                            $updatedDetails = json_decode($payment->fresh()->details ?? '{}', true) ?: [];
                            $payment->update([
                                'details' => json_encode(array_merge(
                                    $updatedDetails,
                                    [
                                        'confirmation_email_sent' => true,
                                        'email_sent_at' => now()->toISOString(),
                                        'email_sent_via' => 'callback'
                                    ]
                                ))
                            ]);
                            
                            Log::info('Email de confirmation envoyé via callback Airtel', [
                                'payment_id' => $payment->id,
                                'user_email' => $payment->user->email
                            ]);

                            Log::info('Email de confirmation envoyé (callback Airtel)', [
                                'payment_id' => $payment->id,
                                'user_email' => $payment->user->email
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Erreur lors de l\'envoi de l\'email (callback Airtel)', [
                                'payment_id' => $payment->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        Log::info('Email de confirmation déjà envoyé ou paiement déjà traité (callback Airtel)', [
                            'payment_id' => $payment->id,
                            'already_processed' => $alreadyProcessed,
                            'email_sent' => isset($existingDetails['confirmation_email_sent'])
                        ]);
                    }
                }

                // Si le paiement est réussi et qu'il y a une réservation associée
                if ($isSuccess && $payment->reservation) {
                    $payment->reservation->update(['status' => 'Payé']);

                    // Générer et envoyer les billets par email
                    try {
                        $reservationController = app(\App\Http\Controllers\ReservationController::class);
                        $reflection = new \ReflectionClass($reservationController);
                        $method = $reflection->getMethod('finalizeSuccessfulPayment');
                        $method->setAccessible(true);
                        $method->invoke($reservationController, $payment, $payment->reservation);
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de la finalisation de la réservation après callback', [
                            'payment_id' => $payment->id,
                            'reservation_id' => $payment->reservation->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Paiement mis à jour via callback Airtel', [
                    'payment_id' => $payment->id,
                    'status' => $isSuccess ? 'payé' : 'échoué',
                    'has_reservation' => $payment->reservation ? true : false
                ]);
            }

            // Mettre à jour l'achat d'offre personnalisée si trouvé
            if ($purchase) {
                $purchase->update([
                    'status' => $isSuccess ? 'completed' : 'failed',
                    'transaction_id' => $airtelMoneyId,
                ]);

                Log::info('Achat personnalisé mis à jour via callback Airtel', [
                    'purchase_id' => $purchase->id,
                    'status' => $isSuccess ? 'completed' : 'failed',
                ]);
            }

            // Si aucune transaction trouvée, logger un avertissement
            if (!$payment && !$purchase) {
                Log::warning('Callback Airtel Money - Transaction introuvable', [
                    'transaction_id' => $transactionId,
                    'airtel_money_id' => $airtelMoneyId,
                ]);

                // Ne pas faire échouer le callback, Airtel doit recevoir une réponse 200
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Transaction introuvable mais callback accepté',
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Callback traité avec succès',
                'transaction_updated' => ($payment || $purchase),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors du traitement du callback Airtel', [
                'transaction' => $transaction,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors du traitement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Traduit les messages Airtel Money en français
     * 
     * @param string $message Message original d'Airtel
     * @return string Message traduit en français
     */
    private function translateAirtelMessage($message)
    {
        if (empty($message)) {
            return 'Transaction réussie';
        }

        // Messages de succès
        $translations = [
            // Messages de succès
            'Your transaction has been successfully processed' => 'Votre transaction a été traitée avec succès',
            'Transaction is successful' => 'Transaction réussie',
            'Transaction is successful.' => 'Transaction réussie',
            'Transaction successfully processed' => 'Transaction traitée avec succès',
            
            // Messages d'échec
            'Transaction failed' => 'Transaction échouée',
            'Transaction Failed' => 'Transaction échouée',
            'Transaction Timed Out' => 'Transaction expirée',
            'Transaction Expired' => 'Transaction expirée',
            'Transaction not permitted to Payee' => 'Transaction non autorisée pour le bénéficiaire',
            'Refused' => 'Transaction refusée',
            'The transaction was refused' => 'La transaction a été refusée',
            'Exceeds withdrawal amount limit' => 'Limite de montant de retrait dépassée',
            'Withdrawal amount limit exceeded' => 'Limite de montant de retrait dépassée',
            'Transaction ID is invalid' => 'Identifiant de transaction invalide',
            'User didn\'t enter the pin' => 'L\'utilisateur n\'a pas entré le code PIN',
            'Transaction Not Found' => 'Transaction introuvable',
            'The transaction was not found' => 'La transaction n\'a pas été trouvée',
            
            // Messages en attente
            'Transaction in Progress' => 'Transaction en cours',
            'Transaction ambiguous' => 'Transaction ambiguë',
            'Transaction Ambiguous' => 'Transaction ambiguë',
            'Please confirm the payment on your phone' => 'Veuillez confirmer le paiement sur votre téléphone',
        ];

        // Vérifier si le message exact existe dans les traductions
        if (isset($translations[$message])) {
            return $translations[$message];
        }

        // Vérifier les correspondances partielles (insensible à la casse)
        $messageLower = strtolower($message);
        foreach ($translations as $english => $french) {
            if (stripos($messageLower, strtolower($english)) !== false) {
                return $french;
            }
        }

        // Si aucune traduction trouvée, retourner le message original
        return $message;
    }
}

