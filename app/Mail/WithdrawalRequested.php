<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Withdrawal;

class WithdrawalRequested extends Mailable
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
        return $this->subject('Demande de retrait soumise - MokiliEvent')
                    ->markdown('emails.withdrawal-requested')
                    ->with([
                        'withdrawal' => $this->withdrawal,
                        'organizer' => $this->withdrawal->organizer,
                    ]);
    }
}

