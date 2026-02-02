<?php

namespace App\Policies;

use App\Models\OrganizerRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizerRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(3) || $user->hasPermissionTo('manage_organizer_requests');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizerRequest $organizerRequest)
    {
        return $user->hasRole(3) || 
               $user->hasPermissionTo('manage_organizer_requests') || 
               $user->id === $organizerRequest->user_id;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, OrganizerRequest $organizerRequest)
    {
        // Autoriser explicitement les administrateurs à approuver les demandes
        return $user->hasRole(3) || $user->hasPermissionTo('manage_organizer_requests');
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, OrganizerRequest $organizerRequest)
    {
        // Autoriser explicitement les administrateurs à rejeter les demandes
        return $user->hasRole(3) || $user->hasPermissionTo('manage_organizer_requests');
    }
}