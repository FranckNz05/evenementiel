<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class InfluencerRequestRejected extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $reason;

    public function __construct(User $user, string $reason = '')
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Votre demande Influenceur a été refusée')
            ->view('emails.influencers.rejected')
            ->with(['user' => $this->user, 'reason' => $this->reason]);
    }
}


