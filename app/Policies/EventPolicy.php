<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Event $event)
    {
        // L'organisateur peut voir ses propres événements
        if ($user->organizer && $user->organizer->id === $event->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent voir tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event)
    {
        // L'organisateur peut modifier ses propres événements
        if ($user->organizer && $user->organizer->id === $event->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent modifier tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event)
    {
        // L'organisateur peut supprimer ses propres événements
        if ($user->organizer && $user->organizer->id === $event->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent supprimer tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }
}
