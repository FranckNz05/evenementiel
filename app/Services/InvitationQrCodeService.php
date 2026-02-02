<?php

namespace App\Services;

use App\Models\CustomEvent;
use App\Models\CustomEventGuest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class InvitationQrCodeService
{
    /**
     * Génère un code d'invitation crypté pour un invité
     */
    public function generateInvitationCode(CustomEvent $event, CustomEventGuest $guest): string
    {
        $data = [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'timestamp' => now()->timestamp,
        ];

        $encrypted = Crypt::encryptString(json_encode($data));
        
        // Mettre à jour le code d'invitation dans la base de données
        $guest->update(['invitation_code' => $encrypted]);

        return $encrypted;
    }

    /**
     * Génère un QR code SVG avec le code crypté
     */
    public function generateQrCodeSvg(string $encryptedCode): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            
            // Générer le QR code avec le code crypté
            $qrCodeSvg = $writer->writeString($encryptedCode);

            return $qrCodeSvg;
        } catch (\Exception $e) {
            Log::error('Erreur génération QR Code crypté: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Génère un QR code en base64 pour inclusion dans un message
     */
    public function generateQrCodeBase64(string $encryptedCode): string
    {
        $svg = $this->generateQrCodeSvg($encryptedCode);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Décrypte et valide un code d'invitation
     */
    public function decryptInvitationCode(string $encryptedCode): ?array
    {
        try {
            $decrypted = Crypt::decryptString($encryptedCode);
            $data = json_decode($decrypted, true);
            
            if (!isset($data['event_id']) || !isset($data['guest_id'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Erreur décryptage code invitation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Valide un code QR et marque l'invité comme entré
     * 
     * @param string $encryptedCode Le code crypté du QR code
     * @param int|null $expectedEventId L'ID de l'événement attendu (optionnel, pour validation)
     * @return array ['valid' => bool, 'guest' => CustomEventGuest|null, 'message' => string, 'fraudulent' => bool]
     */
    public function validateAndCheckIn(string $encryptedCode, ?int $expectedEventId = null): array
    {
        try {
            // Décrypter le code
            $data = $this->decryptInvitationCode($encryptedCode);
            
            if (!$data) {
                return [
                    'valid' => false,
                    'guest' => null,
                    'message' => 'Code QR invalide ou corrompu',
                    'fraudulent' => true,
                ];
            }

            $eventId = $data['event_id'];
            $guestId = $data['guest_id'];

            // Vérifier que l'événement correspond si spécifié
            if ($expectedEventId && $eventId != $expectedEventId) {
                return [
                    'valid' => false,
                    'guest' => null,
                    'message' => 'Ce code QR ne correspond pas à cet événement',
                    'fraudulent' => true,
                ];
            }

            // Charger l'invité
            $guest = CustomEventGuest::find($guestId);
            
            if (!$guest) {
                return [
                    'valid' => false,
                    'guest' => null,
                    'message' => 'Invité introuvable',
                    'fraudulent' => true,
                ];
            }

            // Vérifier que l'invité appartient à l'événement
            if ($guest->custom_event_id != $eventId) {
                return [
                    'valid' => false,
                    'guest' => $guest,
                    'message' => 'Ce code QR ne correspond pas à cet invité pour cet événement',
                    'fraudulent' => true,
                ];
            }

            // Vérifier que le code correspond bien au code stocké
            if ($guest->invitation_code !== $encryptedCode) {
                return [
                    'valid' => false,
                    'guest' => $guest,
                    'message' => 'Code QR frauduleux ou révoqué',
                    'fraudulent' => true,
                ];
            }

            // Vérifier si déjà entré
            if ($guest->checked_in_at) {
                return [
                    'valid' => true,
                    'guest' => $guest,
                    'message' => 'Invité déjà marqué comme entré le ' . $guest->checked_in_at->format('d/m/Y à H:i'),
                    'fraudulent' => false,
                    'already_checked_in' => true,
                ];
            }

            // Marquer comme entré
            $guest->update([
                'checked_in_at' => now(),
                'checked_in_via' => 'qrcode',
                'status' => 'arrived',
            ]);

            $guest = $guest->fresh();

            return [
                'valid' => true,
                'guest' => $guest,
                'message' => 'Invité marqué comme entré avec succès',
                'fraudulent' => false,
                'already_checked_in' => false,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur validation QR code: ' . $e->getMessage());
            return [
                'valid' => false,
                'guest' => null,
                'message' => 'Erreur lors de la validation du code QR',
                'fraudulent' => false,
            ];
        }
    }

    /**
     * Génère le message WhatsApp avec QR code
     */
    public function generateWhatsAppMessage(CustomEvent $event, CustomEventGuest $guest, ?string $customMessage = null): string
    {
        // Générer le code d'invitation crypté si nécessaire
        $code = $guest->invitation_code ?? $this->generateInvitationCode($event, $guest);
        
        // Si un message personnalisé est fourni, remplacer les placeholders
        if ($customMessage) {
            $message = str_replace(['{{nom}}', '{{Nom prénom}}'], $guest->full_name, $customMessage);
        } else {
            // Message par défaut
            $message = "Bonjour {$guest->full_name},\n\n";
            $message .= "Vous êtes invité à l'événement : {$event->title}\n\n";
            
            if ($event->start_date) {
                $message .= "Date : " . $event->start_date->format('d/m/Y à H:i');
                if ($event->end_date) {
                    $message .= " - " . $event->end_date->format('d/m/Y à H:i');
                }
                $message .= "\n";
            }
            
            if ($event->location) {
                $message .= "Lieu : {$event->location}\n";
            }
            
            $message .= "\nVotre code d'invitation unique est inclus dans le QR code ci-dessous.\n";
            $message .= "Présentez ce QR code à l'entrée de l'événement.\n\n";
            
            if ($event->invitation_link) {
                $message .= "Plus d'informations : " . route('custom-events.invitation', $event->invitation_link);
            }
        }

        // Note: Le QR code avec le code crypté sera généré et peut être inclus dans le message
        // via l'API Whatchimp si elle supporte les images. Pour l'instant, on retourne le message texte.
        return $message;
    }

    /**
     * Génère le message SMS simple
     */
    public function generateSmsMessage(CustomEvent $event, CustomEventGuest $guest, ?string $customMessage = null): string
    {
        $name = $guest->full_name;
        
        // Si un message personnalisé est fourni, remplacer les placeholders
        if ($customMessage) {
            $message = str_replace(['{{nom}}', '{{Nom prénom}}'], $name, $customMessage);
        } else {
            // Message par défaut au format demandé
            $message = "Salut {$name}, vous êtes invité à l'événement : {$event->title}";
            
            if ($event->start_date) {
                $message .= " le " . $event->start_date->format('d/m/Y à H:i');
            }
            
            if ($event->location) {
                $message .= " à {$event->location}";
            }
            
            if ($event->invitation_link) {
                $message .= ". Infos: " . route('custom-events.invitation', $event->invitation_link);
            }
        }

        return $message;
    }
}

