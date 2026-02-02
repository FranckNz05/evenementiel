<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InfluencerController extends Controller
{
    /**
     * Un utilisateur demande à devenir influenceur.
     */
    public function requestInfluencer(Request $request)
    {
        $user = $request->user();
        if ($user->is_influencer) {
            return response()->json(['message' => 'Déjà influenceur'], 200);
        }
        $user->influencer_requested = true;
        $user->save();
        return response()->json(['message' => 'Demande envoyée'], 200);
    }

    /**
     * Admin: approuver un utilisateur comme influenceur.
     */
    public function approveInfluencer(Request $request, User $user)
    {
        // Autorisation simple: rôle admin (id 3) ou policy existante
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $user->is_influencer = true;
        $user->influencer_requested = false;
        $user->save();
        return response()->json(['message' => 'Utilisateur approuvé comme influenceur']);
    }

    /**
     * Suivre / se désabonner d’un influenceur.
     */
    public function toggleFollow(Request $request, User $influencer)
    {
        $user = $request->user();
        if ($user->id === $influencer->id) {
            return response()->json(['message' => 'Impossible de se suivre soi-même'], 422);
        }
        // On permet de suivre uniquement des influenceurs
        if (! $influencer->is_influencer) {
            return response()->json(['message' => "L'utilisateur n'est pas influenceur"], 422);
        }

        $isFollowing = $user->followingUsers()->where('influencer_id', $influencer->id)->exists();
        if ($isFollowing) {
            $user->followingUsers()->detach($influencer->id);
            return response()->json(['following' => false]);
        }
        $user->followingUsers()->attach($influencer->id);
        return response()->json(['following' => true]);
    }

    public function followers(User $influencer)
    {
        $followers = $influencer->followers()->select('users.id', 'users.prenom', 'users.nom', 'users.profil_image')->get();
        return response()->json(['data' => $followers]);
    }

    public function following(User $user)
    {
        $following = $user->followingUsers()->select('users.id', 'users.prenom', 'users.nom', 'users.profil_image')->get();
        return response()->json(['data' => $following]);
    }

    /**
     * Influenceur: toggle "J'y serais" sur un évènement.
     */
    public function toggleAttend(Request $request, Event $event)
    {
        $user = $request->user();
        if (! $user->is_influencer) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Réservé aux influenceurs'], 403);
            }
            return redirect()->back()->with('error', 'Action réservée aux influenceurs');
        }
        $exists = $user->influencerAttendances()->where('event_id', $event->id)->exists();
        if ($exists) {
            $user->influencerAttendances()->detach($event->id);
            if ($request->expectsJson()) {
                return response()->json(['attending' => false]);
            }
            return redirect()->back()->with('success', "Vous n'êtes plus marqué comme présent");
        }
        $user->influencerAttendances()->attach($event->id);
        if ($request->expectsJson()) {
            return response()->json(['attending' => true]);
        }
        return redirect()->back()->with('success', "Vous avez indiqué 'J'y serais'");
    }

    /**
     * Liste des influenceurs qui seront présents à un évènement.
     */
    public function attendees(Event $event)
    {
        $users = $event->attendingInfluencers()
            ->select('users.id', 'users.prenom', 'users.nom', 'users.profil_image')
            ->orderBy('influencer_event_attendances.created_at', 'desc')
            ->get();
        if (request()->expectsJson()) {
            return response()->json(['data' => $users]);
        }
        return view('events.influencers', [
            'event' => $event,
            'attendees' => $users,
        ]);
    }

    public function profile(User $user)
    {
        $user->load('followers');
        return view('influencers.show', [
            'influencer' => $user,
        ]);
    }
}


