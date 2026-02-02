<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent;
use App\Models\CustomEventGuest;
use App\Services\WirepickService;
use App\Services\WhatchimpService;
use App\Services\InvitationQrCodeService;
use App\Jobs\SendCustomEventInvitationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Mail\CustomEventInvitation;

class CustomEventInvitationController extends Controller
{
    /**
     * Envoyer des invitations à tous les invités
     */
    public function sendAll(
        Request $request,
        CustomEvent $event,
        WirepickService $wirepickService,
        WhatchimpService $whatchimpService,
        InvitationQrCodeService $qrCodeService
    ) {
        $this->authorize('update', $event);

        if (!$event->canScheduleSms()) {
            abort(403, 'Votre formule ne permet pas l\'envoi/programme d\'invitations.');
        }

        $validated = $request->validate([
            'invitation_methods' => 'required|array',
            'invitation_methods.*' => 'in:email,sms,whatsapp',
            'status_filter' => 'nullable|in:all,pending,confirmed,cancelled,not_invited',
            'message' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $methods = $validated['invitation_methods'];
        $statusFilter = $validated['status_filter'] ?? 'all';
        $customMessage = $validated['message'] ?? null;
        $scheduledAt = $validated['scheduled_at'] ?? null;

        // Filtrer les invités selon le statut
        $guests = $event->guests();
        if ($statusFilter === 'not_invited') {
            $guests->whereNull('sent_at');
        } elseif ($statusFilter !== 'all') {
            $guests->where('status', $statusFilter);
        }
        $guests = $guests->get();

        $sent = 0;
        $scheduled = 0;
        $errors = [];

        foreach ($guests as $guest) {
            try {
                foreach ($methods as $method) {
                    if ($scheduledAt) {
                        // Planifier l'envoi
                        $guest->update(['scheduled_at' => $scheduledAt]);
                        SendCustomEventInvitationJob::dispatch($event, $guest, $method, $customMessage)
                            ->delay($scheduledAt);
                        $scheduled++;
                    } else {
                        // Envoi immédiat
                        if ($method === 'email' && $guest->email) {
                            Mail::to($guest->email)->send(new CustomEventInvitation($event, [
                                'full_name' => $guest->full_name,
                                'email' => $guest->email,
                            ]));
                            
                            $guest->update([
                                'sent_at' => now(),
                                'sent_via' => 'email',
                                'invitation_message' => $customMessage,
                            ]);
                            $sent++;
                        } elseif ($method === 'sms' && $guest->phone) {
                            $smsMessage = $qrCodeService->generateSmsMessage($event, $guest, $customMessage);
                            $smsMessage = str_replace(['{{nom}}', '{{Nom prénom}}'], $guest->full_name, $smsMessage);
                            
                            $formattedPhones = $wirepickService->validateAndFormatPhones([$guest->phone]);
                            if (!empty($formattedPhones)) {
                                $wirepickService->sendBulkSms($formattedPhones, $smsMessage);
                                
                                $guest->update([
                                    'sent_at' => now(),
                                    'sent_via' => 'sms',
                                    'invitation_message' => $smsMessage,
                                ]);
                                $sent++;
                            }
                        } elseif ($method === 'whatsapp' && $guest->phone) {
                            $whatsappMessage = $qrCodeService->generateWhatsAppMessage($event, $guest, $customMessage);
                            $whatsappMessage = str_replace(['{{nom}}', '{{Nom prénom}}'], $guest->full_name, $whatsappMessage);
                            
                            // Générer le code d'invitation et QR code
                            $code = $qrCodeService->generateInvitationCode($event, $guest);
                            
                            // Envoyer via Whatchimp
                            $formattedPhone = $whatchimpService->formatPhoneForWhatsApp($guest->phone);
                            $whatchimpService->sendMessage($formattedPhone, $event->title, $whatsappMessage);
                            
                            $guest->update([
                                'sent_at' => now(),
                                'sent_via' => 'whatsapp',
                                'invitation_message' => $whatsappMessage,
                            ]);
                            $sent++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Erreur lors de l'envoi à {$guest->full_name}: " . $e->getMessage();
                Log::error('Erreur envoi invitation: ' . $e->getMessage());
            }
        }

        if ($scheduled > 0) {
            return back()->with('success', "{$scheduled} invitation(s) planifiée(s) avec succès!")->with('errors', $errors);
        }

        if ($sent > 0) {
            return back()->with('success', "{$sent} invitation(s) envoyée(s) avec succès!")->with('errors', $errors);
        }

        return back()->with('error', 'Aucune invitation n\'a pu être envoyée.')->with('errors', $errors);
    }

    /**
     * Envoyer une invitation à un invité spécifique
     */
    public function send(
        Request $request,
        $eventId,
        $guestId,
        WirepickService $wirepickService,
        WhatchimpService $whatchimpService,
        InvitationQrCodeService $qrCodeService
    ) {
        $event = CustomEvent::findOrFail($eventId);
        $this->authorize('update', $event);

        $guest = CustomEventGuest::findOrFail($guestId);
        
        // Vérifier que l'invité appartient à l'événement
        if ($guest->custom_event_id != $event->id) {
            abort(403, 'Cet invité n\'appartient pas à cet événement.');
        }

        $validated = $request->validate([
            'invitation_method' => 'required|in:email,sms,whatsapp',
            'message' => 'nullable|string',
            'email_temp' => 'nullable|email',
            'phone_temp' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $method = $validated['invitation_method'];
        $customMessage = $validated['message'] ?? null;
        $scheduledAt = $validated['scheduled_at'] ?? null;

        try {
            if ($scheduledAt) {
                // Planifier l'envoi
                $guest->update(['scheduled_at' => $scheduledAt]);
                SendCustomEventInvitationJob::dispatch($event, $guest, $method, $customMessage)
                    ->delay($scheduledAt);
                
                return back()->with('success', 'Invitation planifiée avec succès!');
            }

            if ($method === 'email') {
                $email = $validated['email_temp'] ?? $guest->email;
                if (!$email) {
                    return back()->with('error', 'Aucune adresse email fournie pour cet invité.');
                }

                Mail::to($email)->send(new CustomEventInvitation($event, [
                    'full_name' => $guest->full_name,
                    'email' => $email,
                ]));

                $guest->update([
                    'sent_at' => now(),
                    'sent_via' => 'email',
                    'invitation_message' => $customMessage,
                ]);

                return back()->with('success', 'Invitation envoyée par email avec succès!');
            } elseif ($method === 'sms') {
                $phone = $validated['phone_temp'] ?? $guest->phone;
                if (!$phone) {
                    return back()->with('error', 'Aucun numéro de téléphone fourni pour cet invité.');
                }

                $smsMessage = $qrCodeService->generateSmsMessage($event, $guest, $customMessage);
                $smsMessage = str_replace(['{{nom}}', '{{Nom prénom}}'], $guest->full_name, $smsMessage);
                
                $formattedPhones = $wirepickService->validateAndFormatPhones([$phone]);
                if (empty($formattedPhones)) {
                    return back()->with('error', 'Numéro de téléphone invalide.');
                }

                $wirepickService->sendBulkSms($formattedPhones, $smsMessage);
                
                $guest->update([
                    'sent_at' => now(),
                    'sent_via' => 'sms',
                    'invitation_message' => $smsMessage,
                ]);

                return back()->with('success', 'Invitation envoyée par SMS avec succès!');
            } elseif ($method === 'whatsapp') {
                $phone = $validated['phone_temp'] ?? $guest->phone;
                if (!$phone) {
                    return back()->with('error', 'Aucun numéro de téléphone fourni pour cet invité.');
                }

                $whatsappMessage = $qrCodeService->generateWhatsAppMessage($event, $guest, $customMessage);
                $whatsappMessage = str_replace(['{{nom}}', '{{Nom prénom}}'], $guest->full_name, $whatsappMessage);
                
                // Générer le code d'invitation et QR code
                $code = $qrCodeService->generateInvitationCode($event, $guest);
                
                // Envoyer via Whatchimp
                $formattedPhone = $whatchimpService->formatPhoneForWhatsApp($phone);
                $whatchimpService->sendMessage($formattedPhone, $event->title, $whatsappMessage);
                
                $guest->update([
                    'sent_at' => now(),
                    'sent_via' => 'whatsapp',
                    'invitation_message' => $whatsappMessage,
                ]);

                return back()->with('success', 'Invitation envoyée par WhatsApp avec succès!');
            }
        } catch (\Exception $e) {
            Log::error('Erreur envoi invitation: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de l\'invitation: ' . $e->getMessage());
        }

        return back()->with('error', 'Méthode d\'envoi non reconnue.');
    }

    /**
     * Télécharger un template CSV pour l'import en masse
     */
    public function downloadCsvTemplate(): StreamedResponse
    {
        $rows = [
            ['Nom complet', 'Email', 'Téléphone'],
            ['NGANGA Marie', 'marie.nganga@example.com', '051234567'],
            ['MBEMBA Louis', 'louis.mbemba@example.com', '062345678'],
            ['ONDO Aline', 'aline.ondo@example.com', '043456789'],
            ['BASSA Jonas', 'jonas.bassa@example.com', '054567890'],
            ['MBOU Sylvie', 'sylvie.mbou@example.com', '065678901'],
        ];

        $callback = static function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            // Ajouter BOM pour UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, 'modele_import_invites.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}



