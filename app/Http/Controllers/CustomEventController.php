<?php
namespace App\Http\Controllers;

use App\Models\CustomEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class CustomEventController extends Controller
{
    public function index()
    {
        $events = auth()->user()->customEvents()->latest()->get();
        $unusedPurchases = \App\Models\CustomOfferPurchase::where('user_id', auth()->id())
            ->whereNull('used_at')
            ->latest()
            ->get();
        return view('events.custom_wizard.dashboard', compact('events', 'unusedPurchases'));
    }

    public function create()
    {
        $offer = session('custom_offer');
        // Passer l'offre au formulaire pour afficher les limites/avantages
        return view('events.create', [
            'offer' => $offer,
        ]);
    }

    public function store(Request $request)
    {
        $offer = session('custom_offer');
        $planLimits = [
            'start' => 100,
            'standard' => 300,
            'premium' => 800,
            'ultimate' => 1500,
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:mariage,anniversaire,soiree,conference,autre',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'guest_limit' => 'nullable|integer|min:1',
        ]);

        // Appliquer la limite d'invités selon l'offre choisie (si présente)
        if ($offer && isset($planLimits[$offer['plan']])) {
            $maxGuests = $planLimits[$offer['plan']];
            $validated['guest_limit'] = isset($validated['guest_limit'])
                ? min($validated['guest_limit'], $maxGuests)
                : $maxGuests;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('event_images', 'public');
        }

        // Enregistrer la formule dans l'événement pour contrôle ultérieur
        if ($offer && isset($offer['plan'])) {
            $validated['offer_plan'] = $offer['plan'];
        }

        $event = auth()->user()->customEvents()->create($validated);
        
        return redirect()->route('custom-events.show', $event)
            ->with('success', 'Événement créé avec succès!');
    }

    public function show($id)
    {
        $event = CustomEvent::findOrFail($id);
        $this->authorize('view', $event);
        
        $event->load('guests', 'organizer');
        return view('custom-events.show', compact('event'));
    }

    // autres méthodes (edit, update, destroy)
    public function edit($id)
    {
        $event = CustomEvent::findOrFail($id);
        $this->authorize('update', $event);
        
        return view('custom-events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = CustomEvent::findOrFail($id);
        $this->authorize('update', $event);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:mariage,anniversaire,soiree,conference,autre',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'guest_limit' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('event_images', 'public');
        }

        $event->update($validated);
        
        return redirect()->route('custom-events.show', $event)
            ->with('success', 'Événement mis à jour avec succès!');
    }

    public function destroy($id)
    {
        $event = CustomEvent::findOrFail($id);
        $this->authorize('delete', $event);
        
        $event->delete();
        
        return redirect()->route('custom-events.index')
            ->with('success', 'Événement supprimé avec succès!');
    }

    /**
     * Afficher la page d'invitation publique (accessible sans authentification)
     */
    public function showInvitation($invitationLink)
    {
        $event = CustomEvent::where('invitation_link', $invitationLink)
            ->with('organizer')
            ->firstOrFail();
        
        return view('custom-events.invitation', compact('event'));
    }

    /**
     * Générer le QR code pour un événement personnalisé
     */
    public function qrcode(CustomEvent $event)
    {
        $this->authorize('view', $event);

        // Générer l'URL de l'invitation
        $invitationUrl = $event->invitation_link 
            ? route('custom-events.invitation', $event->invitation_link)
            : route('custom-events.show', $event);

        // Générer le QR code
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($invitationUrl);

            return response($qrCodeSvg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'inline; filename="qr-code-' . $event->id . '.svg"');
        } catch (\Exception $e) {
            // Fallback: générer un QR code simple
            \Log::error('Erreur génération QR Code: ' . $e->getMessage());
            return $this->generateQrCodeFallback($invitationUrl);
        }
    }

    /**
     * Générer un QR code de fallback si la génération principale échoue
     */
    private function generateQrCodeFallback(string $data)
    {
        // Créer une image SVG simple comme fallback
        $svg = '<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
            <rect width="400" height="400" fill="white"/>
            <text x="200" y="200" font-family="Arial" font-size="16" text-anchor="middle" fill="black">QR Code</text>
            <text x="200" y="220" font-family="Arial" font-size="12" text-anchor="middle" fill="gray">' . htmlspecialchars(substr($data, 0, 50)) . '</text>
        </svg>';

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline; filename="qr-code-fallback.svg"');
    }

    /**
     * Générer l'URL de check-in temps réel pour un événement
     */
    public function generateCheckinUrl($id)
    {
        $event = CustomEvent::findOrFail($id);
        $this->authorize('update', $event);

        if (!$event->canUseRealtimeCheckin()) {
            return back()->with('error', 'Cette fonctionnalité n\'est pas disponible pour votre formule.');
        }

        if (!$event->checkin_url) {
            $event->generateCheckinUrl();
        }

        return back()->with('success', 'URL de check-in générée avec succès!');
    }
}