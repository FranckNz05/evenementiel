<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailVerificationNotificationSafe
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            try {
                $event->user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                // Logger l'erreur mais ne pas faire échouer l'inscription
                Log::error('Erreur lors de l\'envoi de l\'email de vérification', [
                    'user_id' => $event->user->id,
                    'email' => $event->user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Si l'email est invalide, on ne fait rien de plus
                // L'utilisateur pourra demander un renvoi plus tard
            }
        }
    }
}

