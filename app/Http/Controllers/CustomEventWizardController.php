<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomEvent;

class CustomEventWizardController extends Controller
{
    public function step1(Request $request)
    {
        $offer = session('custom_offer');
        if ($request->has('purchase')) {
            $purchase = \App\Models\CustomOfferPurchase::where('id', $request->query('purchase'))
                ->where('user_id', auth()->id())
                ->whereNull('used_at')
                ->firstOrFail();
            session(['custom_offer' => [
                'plan' => $purchase->plan,
                'label' => ucfirst($purchase->plan),
                'price' => $purchase->price,
                'purchase_id' => $purchase->id,
            ]]);
            $offer = session('custom_offer');
        }
        
        // Si pas d'offre en session et pas d'achat récent, rediriger vers la sélection des offres
        if (!$offer) {
            $hasRecentPurchase = \App\Models\CustomOfferPurchase::where('user_id', auth()->id())
                ->whereNull('used_at')
                ->exists();
            
            if (!$hasRecentPurchase) {
                return redirect()->route('custom-offers.index')
                    ->with('info', 'Veuillez choisir une formule avant de créer votre événement personnalisé.');
            }
        }
        
        return view('events.custom_wizard.step1', compact('offer'));
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:mariage,anniversaire,soiree,conference,autre',
        ]);
        session(['custom_event.step1' => $validated]);
        return redirect()->route('custom-events.wizard.step2');
    }

    public function step2(Request $request)
    {
        return view('events.custom_wizard.step2');
    }

    public function storeStep2(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        session(['custom_event.step2' => $validated]);
        return redirect()->route('custom-events.wizard.step3');
    }

    public function step3(Request $request)
    {
        $offer = session('custom_offer');
        return view('events.custom_wizard.step3', compact('offer'));
    }

    public function storeStep3(Request $request)
    {
        $validated = $request->validate([
            'guest_limit' => 'nullable|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('event_images', 'public');
        }

        session(['custom_event.step3' => $validated]);
        return redirect()->route('custom-events.wizard.complete');
    }

    public function complete(Request $request)
    {
        $offer = session('custom_offer');
        $step1 = session('custom_event.step1', []);
        $step2 = session('custom_event.step2', []);
        $step3 = session('custom_event.step3', []);

        // Vérifier si l'événement a déjà été créé (pour éviter les doublons)
        if (session('custom_event.created')) {
            $event = CustomEvent::find(session('custom_event.created'));
            if ($event) {
                return view('events.custom_wizard.complete', compact('event'));
            }
        }

        $planLimits = [ 'start'=>100, 'standard'=>300, 'premium'=>800, 'ultimate'=>1500 ];
        $maxGuests = $offer && isset($planLimits[$offer['plan']]) ? $planLimits[$offer['plan']] : 100;

        $data = array_merge($step1, $step2, $step3);
        if (!isset($data['guest_limit'])) {
            $data['guest_limit'] = $maxGuests;
        } else {
            $data['guest_limit'] = min($data['guest_limit'], $maxGuests);
        }

        // Convertir les dates au bon format si nécessaire
        if (isset($data['start_date']) && strpos($data['start_date'], 'T') !== false) {
            $data['start_date'] = str_replace('T', ' ', $data['start_date']) . ':00';
        }
        if (isset($data['end_date']) && strpos($data['end_date'], 'T') !== false) {
            $data['end_date'] = str_replace('T', ' ', $data['end_date']) . ':00';
        }

        // Remplir category avec la valeur de type pour compatibilité
        if (isset($data['type']) && !isset($data['category'])) {
            $data['category'] = $data['type'];
        }

        // Générer l'URL si elle n'existe pas
        if (!isset($data['url'])) {
            $data['url'] = \Str::random(16);
        }

        $data['offer_plan'] = $offer['plan'] ?? 'start';

        $event = auth()->user()->customEvents()->create($data);

        // Générer le lien d'invitation si il n'existe pas
        if (!$event->invitation_link) {
            $event->generateInvitationLink();
            $event->refresh();
        }

        // Générer l'URL de check-in temps réel si l'événement le permet
        if ($event->canUseRealtimeCheckin() && !$event->checkin_url) {
            $event->generateCheckinUrl();
            $event->refresh();
        }

        // Marquer l'achat comme utilisé
        if ($offer && isset($offer['purchase_id'])) {
            \App\Models\CustomOfferPurchase::where('id', $offer['purchase_id'])
                ->where('user_id', auth()->id())
                ->update(['used_at' => now()]);
        }

        // Enregistrer l'ID de l'événement créé dans la session
        session(['custom_event.created' => $event->id]);

        // Nettoyer la session du wizard (mais garder l'ID de l'événement)
        session()->forget(['custom_event.step1', 'custom_event.step2', 'custom_event.step3']);

        return view('events.custom_wizard.complete', compact('event'));
    }
}


