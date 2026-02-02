<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class QrCodeEncryptionService
{
    /**
     * Génère des données cryptées pour le QR code
     * 
     * @param Payment $payment Le paiement associé
     * @param int|null $ticketIndex L'index du ticket (pour les billets multiples)
     * @return string Les données cryptées à encoder dans le QR code
     */
    public function encryptQrData(Payment $payment, ?int $ticketIndex = null): string
    {
        try {
            // Vérifier que le paiement a un matricule
            if (!$payment->matricule) {
                throw new \Exception('Le paiement n\'a pas de matricule');
            }
            
            // Récupérer l'event_id de manière sécurisée
            $eventId = $payment->evenement_id;
            
            // Si pas d'event_id direct, essayer via order
            if (!$eventId) {
                if (!$payment->relationLoaded('order')) {
                    $payment->load('order');
                }
                if ($payment->order) {
                    $eventId = $payment->order->evenement_id ?? null;
                }
            }
            
            // Si toujours pas d'event_id, essayer via reservation
            if (!$eventId && $payment->reservation_id) {
                if (!$payment->relationLoaded('reservation')) {
                    $payment->load('reservation.event');
                }
                if ($payment->reservation && $payment->reservation->event) {
                    $eventId = $payment->reservation->event_id ?? $payment->reservation->event->id ?? null;
                }
            }
            
            $data = [
                'payment_id' => $payment->id,
                'matricule' => $payment->matricule,
                'event_id' => $eventId,
                'user_id' => $payment->user_id,
                'timestamp' => now()->timestamp,
                'ticket_index' => $ticketIndex,
                'checksum' => $this->generateChecksum($payment, $ticketIndex)
            ];

            $jsonData = json_encode($data);
            if ($jsonData === false) {
                throw new \Exception('Erreur lors de l\'encodage JSON des données QR code: ' . json_last_error_msg());
            }
            
            return Crypt::encryptString($jsonData);
        } catch (\Exception $e) {
            Log::error('Erreur lors du cryptage des données QR code', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Déchiffre les données du QR code
     * 
     * @param string $encryptedData Les données cryptées du QR code
     * @return array|null Les données déchiffrées ou null en cas d'erreur
     */
    public function decryptQrData(string $encryptedData): ?array
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);
            $data = json_decode($decrypted, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Erreur de décodage JSON après décryptage', [
                    'json_error' => json_last_error_msg()
                ]);
                return null;
            }

            return $data;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::warning('Erreur de décryptage QR code', [
                'error' => $e->getMessage()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Erreur lors du décryptage des données QR code', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Vérifie l'intégrité des données déchiffrées
     * 
     * @param array $data Les données déchiffrées
     * @param Payment|null $payment Le paiement à vérifier (optionnel)
     * @return bool True si les données sont valides
     */
    public function validateQrData(array $data, ?Payment $payment = null): bool
    {
        // Vérifier que les champs requis sont présents
        if (!isset($data['payment_id'], $data['matricule'], $data['checksum'], $data['timestamp'])) {
            return false;
        }

        // Si un paiement est fourni, vérifier le checksum
        if ($payment) {
            $expectedChecksum = $this->generateChecksum($payment, $data['ticket_index'] ?? null);
            if ($data['checksum'] !== $expectedChecksum) {
                Log::warning('Checksum QR code invalide', [
                    'payment_id' => $payment->id,
                    'expected' => $expectedChecksum,
                    'received' => $data['checksum']
                ]);
                return false;
            }

            // Vérifier que le matricule correspond
            if ($data['matricule'] !== $payment->matricule) {
                return false;
            }
        }

        // Vérifier que le QR code n'est pas trop ancien (optionnel - 1 an max)
        $maxAge = 31536000; // 1 an en secondes
        $age = now()->timestamp - $data['timestamp'];
        if ($age > $maxAge) {
            Log::warning('QR code expiré', [
                'payment_id' => $data['payment_id'],
                'age' => $age
            ]);
            return false;
        }

        return true;
    }

    /**
     * Génère un checksum pour vérifier l'intégrité des données
     * 
     * @param Payment $payment Le paiement
     * @param int|null $ticketIndex L'index du ticket
     * @return string Le checksum SHA256
     */
    private function generateChecksum(Payment $payment, ?int $ticketIndex = null): string
    {
        // Récupérer l'event_id de manière sécurisée
        $eventId = $payment->evenement_id;
        if (!$eventId && $payment->relationLoaded('order') && $payment->order) {
            $eventId = $payment->order->evenement_id ?? '';
        } elseif (!$eventId) {
            $eventId = '';
        }
        
        $data = $payment->id . 
                $payment->matricule . 
                $eventId .
                ($ticketIndex ?? '') .
                config('app.key');
        
        return hash('sha256', $data);
    }
}

