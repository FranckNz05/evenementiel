<?php

namespace App\Mail;

use App\Models\User;
use App\Models\OrganizerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrganizerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $request;

    public function __construct(User $user, OrganizerRequest $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function build()
    {
        return $this->subject('Nouvel organisateur approuvé')
                    ->markdown('emails.new-organizer-notification');
    }
}