<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEventInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $guest;

    /**
     * Create a new message instance.
     */
    public function __construct($event, $guest)
    {
        $this->event = $event;
        $this->guest = $guest;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Invitation à l\'événement : ' . $this->event->title)
            ->markdown('emails.custom_event_invitation');
    }
}