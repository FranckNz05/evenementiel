<?php
namespace App\Http\Controllers;

use App\Models\CustomPersonalEvent;
use App\Models\CustomPersonalEventGuest;
use App\Models\CustomOfferPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Events\CustomPersonalEventGuestUpdated;
use App\Mail\CustomEventInvitation;
use Illuminate\Support\Facades\Mail;

class CustomPersonalEventController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a une offre en session ou un achat récent
        $hasOfferInSession = session()->has('custom_offer');
        $hasRecentPurchase = CustomOfferPurchase::where('user_id', $user->id)
            ->whereNull('used_at')
            ->exists();
        
        // Si pas d'offre, rediriger vers la sélection des offres
        if (!$hasOfferInSession && !$hasRecentPurchase) {
            return redirect()->route('custom-offers.index')
                ->with('info', 'Veuillez choisir une formule avant de créer votre événement personnalisé.');
        }
        
        return view('events.custom_personal.create');
    }

    public function store(Request $request)
    {
        $addGuestsNow = $request->input('add_guests_now', '1');
        $rules = [
            'category' => 'required|string',
            'title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'required|string',
            'invitation_message' => 'nullable|string',
            'send_at' => 'required|date',
        ];
        if ($addGuestsNow == '1') {
            $rules['guests'] = 'required|array|min:1';
            $rules['guests.*.full_name'] = 'required|string';
            $rules['guests.*.email'] = 'required|email';
            $rules['guests.*.phone'] = 'nullable|string';
            $rules['guests.*.is_couple'] = 'boolean';
        }
        $data = $request->validate($rules);

        $url = Str::random(16);
        $event = CustomPersonalEvent::create([
            'organizer_id' => Auth::id(),
            'category' => $data['category'],
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'location' => $data['location'],
            'invitation_message' => $data['invitation_message'] ?? null,
            'url' => $url,
            'is_public' => false,
        ]);

        if ($addGuestsNow == '1' && !empty($data['guests'])) {
            // Filtrer pour ne garder qu'un seul invité par email (le premier trouvé)
            $uniqueGuests = [];
            $seenEmails = [];
            foreach ($data['guests'] as $guest) {
                $email = strtolower(trim($guest['email']));
                if (!in_array($email, $seenEmails)) {
                    $uniqueGuests[] = $guest;
                    $seenEmails[] = $email;
                }
            }
            $emailsEnvoyes = [];
            foreach ($uniqueGuests as $guest) {
                $createdGuest = $event->guests()->create($guest);
                // Envoi du mail d'invitation
                try {
                    Mail::to($createdGuest->email)->send(new CustomEventInvitation($event, $guest));
                    $emailsEnvoyes[] = $createdGuest->email;
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi mail invitation: ' . $e->getMessage());
                }
            }
            \Log::info('Invitations envoyées (uniques) : ' . count($emailsEnvoyes) . ' | Emails : ' . implode(', ', $emailsEnvoyes));
        }

    // Broadcast la liste à jour
    event(new CustomPersonalEventGuestUpdated($event));

        // TODO: Planifier l'envoi des invitations à $data['send_at']

        return redirect()->route('custom-personal-events.dashboard')->with('success', 'Événement personnalisé créé !');
    }

    public function dashboard()
    {
        $events = CustomPersonalEvent::where('organizer_id', Auth::id())->get();
        return view('events.custom_personal.dashboard', compact('events'));
    }

    public function show($url)
    {
        $event = CustomPersonalEvent::where('url', $url)->with('guests')->firstOrFail();
        // Vue organisateur/admin
        return view('events.custom_personal.show', compact('event'));
    }

    // Vue publique temps réel
    public function showPublic($url)
    {
        $event = CustomPersonalEvent::where('url', $url)->with('guests')->firstOrFail();
        return view('events.custom_personal.public', compact('event'));
    }

    public function addGuest(Request $request, CustomPersonalEvent $event)
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'is_couple' => 'boolean',
        ]);
        $event->guests()->create($data);
        // TODO: Envoyer l'invitation si l'événement a déjà commencé
    // Broadcast la liste à jour
    event(new CustomPersonalEventGuestUpdated($event));
    return back()->with('success', 'Invité ajouté !');
    }

    public function cancelGuest(CustomPersonalEvent $event, CustomPersonalEventGuest $guest)
    {
        $guest->update(['status' => 'cancelled']);
    // Broadcast la liste à jour
    event(new CustomPersonalEventGuestUpdated($event));
    return back()->with('success', 'Invitation annulée.');
    }

    public function markArrived(CustomPersonalEvent $event, CustomPersonalEventGuest $guest)
    {
        $guest->update(['status' => 'arrived']);
    // Broadcast la liste à jour
    event(new CustomPersonalEventGuestUpdated($event));
    return back()->with('success', 'Invité marqué comme arrivé.');
    }
    /**
     * Met à jour le code d'accès public (url) d'un événement personnalisé
     */
    public function updateCode(Request $request, CustomPersonalEvent $event)
    {
        $this->authorize('update', $event); // facultatif si tu utilises des policies
        $data = $request->validate([
            'url' => 'required|string|min:6|max:32|alpha_dash|unique:custom_events,url,' . $event->id,
        ]);
        $event->url = $data['url'];
        $event->save();
        // Broadcast pour synchro temps réel sur la page publique
        event(new \App\Events\CustomPersonalEventUrlUpdated($event));
        return response()->json(['success' => true, 'url' => $event->url]);
    }
}
