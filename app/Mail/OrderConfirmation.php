<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $event;
    public $order;
    public $pdfContent;

    public function __construct(User $user, Event $event, Order $order, $pdfContent)
    {
        $this->user = $user;
        $this->event = $event;
        $this->order = $order;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject("Confirmation de votre commande #" . $this->order->id)
                    ->view('emails.order_confirmation')
                    ->attachData($this->pdfContent, 'billets.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}