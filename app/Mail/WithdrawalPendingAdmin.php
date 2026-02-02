<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Withdrawal;

class WithdrawalPendingAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;

    /**
     * Crée une nouvelle instance du mail
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    /**
     * Construit le message
     */
    public function build()
    {
        return $this->subject('Nouvelle demande de retrait en attente - MokiliEvent')
                    ->markdown('emails.withdrawal-pending-admin')
                    ->with([
                        'withdrawal' => $this->withdrawal,
                        'organizer' => $this->withdrawal->organizer,
                    ]);
    }
}

