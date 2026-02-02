<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Withdrawal;

class WithdrawalRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $withdrawal;
    public $admin;

    /**
     * Crée une nouvelle instance du mail
     */
    public function __construct(Withdrawal $withdrawal, $admin = null)
    {
        $this->withdrawal = $withdrawal;
        $this->admin = $admin;
    }

    /**
     * Construit le message
     */
    public function build()
    {
        return $this->subject('Retrait rejeté - MokiliEvent')
                    ->markdown('emails.withdrawal-rejected')
                    ->with([
                        'withdrawal' => $this->withdrawal,
                        'organizer' => $this->withdrawal->organizer,
                        'admin' => $this->admin,
                    ]);
    }
}

