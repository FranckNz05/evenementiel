<?php

namespace App\Jobs;

use App\Models\CustomEvent;
use App\Models\CustomEventGuest;
use App\Services\WirepickService;
use App\Services\WhatchimpService;
use App\Services\InvitationQrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEventInvitation;

class SendCustomEventInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public CustomEvent $event,
        public CustomEventGuest $guest,
        public string $method, // email, sms, whatsapp
        public ?string $customMessage = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        WirepickService $wirepickService,
        WhatchimpService $whatchimpService,
        InvitationQrCodeService $qrCodeService
    ): void {
        try {
            $message = $this->customMessage;
            
            if ($this->method === 'email') {
                if ($this->guest->email) {
                    Mail::to($this->guest->email)->send(new CustomEventInvitation($this->event, [
                        'full_name' => $this->guest->full_name,
                        'email' => $this->guest->email,
                    ]));
                    
                    $this->guest->update([
                        'sent_at' => now(),
                        'sent_via' => 'email',
                        'invitation_message' => $message,
                    ]);
                }
            } elseif ($this->method === 'sms') {
                if ($this->guest->phone) {
                    $smsMessage = $qrCodeService->generateSmsMessage($this->event, $this->guest, $message);
                    $smsMessage = str_replace('{{nom}}', $this->guest->full_name, $smsMessage);
                    $smsMessage = str_replace('{{Nom prénom}}', $this->guest->full_name, $smsMessage);
                    
                    $formattedPhones = $wirepickService->validateAndFormatPhones([$this->guest->phone]);
                    if (!empty($formattedPhones)) {
                        $wirepickService->sendBulkSms($formattedPhones, $smsMessage);
                        
                        $this->guest->update([
                            'sent_at' => now(),
                            'sent_via' => 'sms',
                            'invitation_message' => $smsMessage,
                        ]);
                    }
                }
            } elseif ($this->method === 'whatsapp') {
                if ($this->guest->phone) {
                    $whatsappMessage = $qrCodeService->generateWhatsAppMessage($this->event, $this->guest, $message);
                    $whatsappMessage = str_replace('{{nom}}', $this->guest->full_name, $whatsappMessage);
                    $whatsappMessage = str_replace('{{Nom prénom}}', $this->guest->full_name, $whatsappMessage);
                    
                    // Générer le code d'invitation et QR code
                    $code = $qrCodeService->generateInvitationCode($this->event, $this->guest);
                    $qrCodeBase64 = $qrCodeService->generateQrCodeBase64($code);
                    
                    // Envoyer via Whatchimp
                    $formattedPhone = $whatchimpService->formatPhoneForWhatsApp($this->guest->phone);
                    $whatchimpService->sendMessage($formattedPhone, $this->event->title, $whatsappMessage);
                    
                    $this->guest->update([
                        'sent_at' => now(),
                        'sent_via' => 'whatsapp',
                        'invitation_message' => $whatsappMessage,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur envoi invitation planifiée', [
                'event_id' => $this->event->id,
                'guest_id' => $this->guest->id,
                'method' => $this->method,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
