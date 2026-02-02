<?php

namespace App\Http\Controllers;

use App\Models\OrganizerAccessCode;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrganizerAccessCodeController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:Organizer']);
    }

    /**
     * Affiche la liste des codes d'accès
     */
    public function index()
    {

        $user = Auth::user();
        $organizerId = $user->organizer->id;
        $codes = OrganizerAccessCode::where('organizer_id', $organizerId)
            ->with('event')
            ->latest()
            ->paginate(5);

        return view('dashboard.organizer.access-codes.index', compact('codes'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
{
    $user = Auth::user();
    $organizerId = $user->organizer->id;
    \Log::info("Tentative de récupération des événements pour l'organisateur ID: " . $organizerId);

    $events = Event::where('organizer_id', $organizerId)
        ->where('etat', 'En cours')
        ->get();

    \Log::info("Événements trouvés: " . $events->count());
    \Log::info("Détails des événements: " . $events->toJson());

    return view('dashboard.organizer.access-codes.create', compact('events'));
}

    /**
     * Enregistre un nouveau code d'accès
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $organizerId = $user->organizer->id;
    
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'valid_from' => 'required|date',
            'valid_until' => [
                'required', 
                'date',
                'after:valid_from',
                'after:now'],
            'notes' => 'nullable|string|max:255'
        ]);

        // Vérifier que l'événement appartient à l'organisateur
        $event = Event::findOrFail($request->event_id);
        if ($event->organizer_id != $organizerId) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à créer un code d\'accès pour cet événement.');
        }

        $code = OrganizerAccessCode::create([
            'organizer_id' => $organizerId,
            'event_id' => $request->event_id,
            'access_code' => Str::upper(Str::random(8)),
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'is_active' => true,
            'created_by' => Auth::id(),
            'notes' => $request->notes
        ]);

        return redirect()->route('access-codes.index')
            ->with('success', 'Code d\'accès créé avec succès: ' . $code->access_code);
    }

    /**
     * Supprime un code d'accès
     */
    public function destroy(OrganizerAccessCode $accessCode)
{
    $user = Auth::user();
    
    // Solution 1: Vérification via la relation organizer
    if (!$user->organizer || $accessCode->organizer_id !== $user->organizer->id) {
        \Log::error('Tentative de suppression non autorisée', [
            'user_id' => $user->id,
            'user_has_organizer' => isset($user->organizer),
            'code_organizer_id' => $accessCode->organizer_id,
            'user_organizer_id' => $user->organizer->id ?? null
        ]);
        abort(403, 'Action non autorisée');
    }
    
    $accessCode->delete();
    
    return redirect()->route('organizer.access-codes.index')
        ->with('success', 'Code d\'accès supprimé avec succès');
}
}
