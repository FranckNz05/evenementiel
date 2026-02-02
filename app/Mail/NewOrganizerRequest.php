<?php

namespace App\Mail;

use App\Models\OrganizerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrganizerRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $organizerRequest;

    public function __construct(OrganizerRequest $organizerRequest)
    {
        $this->organizerRequest = $organizerRequest;
    }

    public function build()
    {
        return $this->subject('Nouvelle demande d\'organisateur')
                    ->markdown('emails.organizer-requests.new')
                    ->with([
                        'user' => $this->organizerRequest->user,
                        'motivation' => $this->organizerRequest->motivation,
                        'experience' => $this->organizerRequest->experience,
                        'url' => route('admin.organizer-requests.index')
                    ]);
    }
} 