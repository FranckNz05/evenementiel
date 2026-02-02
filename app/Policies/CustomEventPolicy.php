<?php

namespace App\Policies;

use App\Models\CustomEvent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CustomEvent $customEvent)
    {
        // Si l'événement n'a pas de organizer_id, vérifier si l'utilisateur est authentifié
        // (pour les événements créés avant l'ajout de organizer_id)
        if (!$customEvent->organizer_id) {
            // Permettre l'accès si l'utilisateur est authentifié
            // (solution temporaire pour les événements existants)
            return true;
        }

        // Le propriétaire peut voir son événement
        // Vérifier si organizer_id correspond à l'ID de l'utilisateur
        if ((int)$user->id === (int)$customEvent->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent voir tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        // Tous les utilisateurs authentifiés peuvent créer des événements personnalisés
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CustomEvent $customEvent)
    {
        // Si l'événement n'a pas de organizer_id, permettre la modification
        // (pour les événements créés avant l'ajout de organizer_id)
        if (!$customEvent->organizer_id) {
            // Permettre la modification si l'utilisateur est authentifié
            // (solution temporaire pour les événements existants)
            return true;
        }

        // Le propriétaire peut modifier son événement
        // Vérifier si organizer_id correspond à l'ID de l'utilisateur
        if ((int)$user->id === (int)$customEvent->organizer_id) {
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
    public function delete(User $user, CustomEvent $customEvent)
    {
        // Si l'événement n'a pas de organizer_id, permettre la suppression
        // (pour les événements créés avant l'ajout de organizer_id)
        if (!$customEvent->organizer_id) {
            // Permettre la suppression si l'utilisateur est authentifié
            // (solution temporaire pour les événements existants)
            return true;
        }

        // Le propriétaire peut supprimer son événement
        // Vérifier si organizer_id correspond à l'ID de l'utilisateur
        if ((int)$user->id === (int)$customEvent->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent supprimer tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CustomEvent $customEvent)
    {
        // Si l'événement n'a pas de organizer_id, permettre la restauration
        if (!$customEvent->organizer_id) {
            return true;
        }

        // Le propriétaire peut restaurer son événement
        if ((int)$user->id === (int)$customEvent->organizer_id) {
            return true;
        }

        // Les administrateurs peuvent restaurer tous les événements
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CustomEvent $customEvent)
    {
        // Seuls les administrateurs peuvent supprimer définitivement
        if ($user->hasRole(3)) {
            return true;
        }

        return false;
    }
}
