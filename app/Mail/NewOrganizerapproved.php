<?php

namespace App\Mail;

use App\Models\User;
use App\Models\OrganizerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrganizerApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $approver;

    public function __construct(OrganizerRequest $request, User $approver)
    {
        $this->request = $request;
        $this->approver = $approver;
    }

    public function build()
    {
        return $this->subject('Nouvel organisateur approuvé - ' . $this->request->company_name)
                    ->markdown('emails.new-organizer-approved');
    }
}