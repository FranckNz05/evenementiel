<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;

class PaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $pdfContent;

    /**
     * Crée une nouvelle instance du mail
     * 
     * @param Payment $payment
     * @param string|null $pdfContent Le contenu PDF généré (binaire)
     */
    public function __construct(Payment $payment, $pdfContent = null)
    {
        $this->payment = $payment;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        // Déterminer si c'est une réservation ou une commande
        $isReservation = $this->payment->reservation_id !== null;
        
        $mail = $this->subject('Confirmation de votre paiement - Billets #' . $this->payment->matricule)
                     ->markdown('emails.payment-confirmation')
                     ->with([
                         'payment' => $this->payment,
                         'order' => $this->payment->order,
                         'reservation' => $this->payment->reservation,
                         'event' => $isReservation 
                             ? ($this->payment->reservation->event ?? null)
                             : ($this->payment->order->event ?? null),
                     ]);
        
        // Attacher le PDF s'il est fourni
        if ($this->pdfContent) {
            if ($isReservation) {
                $ticketCount = $this->payment->reservation->tickets->sum('pivot.quantity');
            } else {
                $ticketCount = $this->payment->order->tickets->sum('pivot.quantity');
            }
            
            $filename = $ticketCount > 1 
                ? "billets-{$this->payment->matricule}.pdf" 
                : "billet-{$this->payment->matricule}.pdf";
            
            $mail->attachData($this->pdfContent, $filename, [
                'mime' => 'application/pdf',
            ]);
        }
        
        return $mail;
    }
}