<?php

namespace App\Http\Controllers;

use App\Models\AccessCode;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccessCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Organizer']);
    }
    
    /**
     * Affiche la liste des codes d'accès
     */
    public function index()
    {
        $accessCodes = AccessCode::whereHas('event', function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('organizer_id', auth()->user()->organizer->id ?? 0);
        })
        ->with('event')
        ->latest()
        ->paginate(15);
        
        return view('dashboard.organizer.access-codes.index', compact('accessCodes'));
    }
    
    /**
     * Affiche le formulaire de création d'un code d'accès
     */
    public function create()
    {
        $events = Event::where('user_id', auth()->id())
            ->orWhere('organizer_id', auth()->user()->organizer->id ?? 0)
            ->where('end_date', '>=', now())
            ->get();
            
        return view('dashboard.organizer.access-codes.create', compact('events'));
    }
    
    /**
     * Enregistre un nouveau code d'accès
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'code' => 'nullable|string|max:20|unique:access_codes,code',
            'description' => 'nullable|string|max:255',
            'max_uses' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        // Vérifier que l'événement appartient à l'organisateur
        $event = Event::findOrFail($validated['event_id']);
        if ($event->user_id != auth()->id() && $event->organizer_id != (auth()->user()->organizer->id ?? 0)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à créer un code d\'accès pour cet événement.');
        }
        
        // Générer un code aléatoire si non fourni
        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(Str::random(8));
        }
        
        AccessCode::create($validated);
        
        return redirect()->route('organizer.access-codes.index')
            ->with('success', 'Le code d\'accès a été créé avec succès.');
    }
    
    /**
     * Supprime un code d'accès
     */
    public function destroy(AccessCode $code)
    {
        // Vérifier que le code appartient à l'organisateur
        $event = $code->event;
        if (!$event || ($event->user_id != auth()->id() && $event->organizer_id != (auth()->user()->organizer->id ?? 0))) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à supprimer ce code d\'accès.');
        }
        
        $code->delete();
        
        return back()->with('success', 'Le code d\'accès a été supprimé avec succès.');
    }
}