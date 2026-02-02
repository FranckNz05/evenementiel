<?php
namespace App\Http\Controllers;

use App\Models\CustomEvent;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function store(Request $request, CustomEvent $event)
    {
        $this->authorize('update', $event);

        // Limites & règles par formule
        $plan = $event->offer_plan ?: 'start';
        $planLimits = [
            'start' => 100,
            'standard' => 300,
            'premium' => 800,
            'ultimate' => 1500,
        ];

        // Start: pas d'ajout d'invités après la création
        if ($plan === 'start') {
            return back()->with('error', "Votre formule Start n'autorise pas l'ajout d'invités après la création.");
        }

        // Standard/Premium/Ultimate: contrôler la limite max d'invités
        $currentGuestCount = $event->guests()->count();
        $maxGuests = $planLimits[$plan] ?? null;

        if ($maxGuests !== null && $currentGuestCount >= $maxGuests) {
            return back()->with('error', "Limite d'invités atteinte pour la formule " . ucfirst($plan) . " (max {$maxGuests}).");
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'is_couple' => 'boolean',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        // Si la limite serait dépassée avec cet ajout, bloquer
        if ($maxGuests !== null && ($currentGuestCount + 1) > $maxGuests) {
            return back()->with('error', "Ajout refusé: dépassement de la limite d'invités pour la formule " . ucfirst($plan) . ".");
        }

        $event->guests()->create($validated);

        return back()->with('success', 'Invité ajouté avec succès!');
    }

    public function updateStatus(Request $request, Guest $guest)
    {
        $this->authorize('update', $guest->customEvent);

        $validated = $request->validate([
            'status' => 'required|in:en_attente,invitation_envoyee,confirme,annule,arrive',
            'has_arrived' => 'boolean',
        ]);

        $guest->update($validated);

        if ($validated['has_arrived'] && !$guest->arrived_at) {
            $guest->update(['arrived_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest->customEvent);
        
        $guest->delete();
        
        return redirect()->route('events.show', $guest->customEvent)
            ->with('success', 'Invité supprimé avec succès!');
    }
}