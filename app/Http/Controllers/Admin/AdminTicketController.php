<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['event', 'user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                  ->orWhere('ticket_type', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('is_used', $request->status === 'used');
        }

        $tickets = $query->latest()->paginate(10);
        return view('dashboard.admin.tickets.index', compact('tickets'));
    }

    // Dans TicketController.php

/**
 * Affiche le formulaire de création de promotion
 */
public function showPromotionForm(Ticket $ticket)
{
    return view('dashboard.admin.tickets.promotion', compact('ticket'));
}

/**
 * Applique une promotion au ticket
 */
public function applyPromotion(Request $request, Ticket $ticket)
{
    $validated = $request->validate([
        'montant_promotionnel' => 'required|numeric|min:0',
        'promotion_start' => 'nullable|date',
        'promotion_end' => 'nullable|date|after:promotion_start',
        'description' => 'nullable|string|max:1000',
        'notify_user' => 'boolean'
    ]);

    // Vérification que le montant de la remise est inférieur au prix
    if ($validated['montant_promotionnel'] >= $ticket->prix) {
        return back()->with('error', 'Le montant de la remise doit être inférieur au prix du ticket');
    }

    // Calcul du nouveau prix
    $nouveauPrix = $ticket->prix - $validated['montant_promotionnel'];

    $ticket->update([
        'montant_promotionnel' => $validated['montant_promotionnel'],
        'prix' => $nouveauPrix,
        'promotion_start' => $validated['promotion_start'],
        'promotion_end' => $validated['promotion_end'],
        'description' => $validated['description'] ?? $ticket->description
    ]);

    return redirect()->route('admin.tickets.index')
        ->with('success', 'Promotion appliquée avec succès');
}

/**
 * Supprime une promotion
 */
public function removePromotion(Ticket $ticket)
{
    // Restaurer le prix original
    $prixOriginal = $ticket->prix + $ticket->montant_promotionnel;
    
    $ticket->update([
        'prix' => $prixOriginal,
        'montant_promotionnel' => null,
        'promotion_start' => null,
        'promotion_end' => null
    ]);

    return back()->with('success', 'Promotion supprimée avec succès');
}

    public function create()
    {
        try {
            // Récupérer tous les événements actifs
            $events = Event::whereIn('status', ['Payant', 'Gratuit', 'published'])->get();
            $users = User::all();
            
            // Debug: Log le nombre d'événements
            Log::info('Nombre d\'événements trouvés: ' . $events->count());
            
            return view('dashboard.admin.tickets.create', compact('events', 'users'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des événements: ' . $e->getMessage());
            $events = collect();
            $users = collect();
            return view('dashboard.admin.tickets.create', compact('events', 'users'))
                ->with('error', 'Erreur lors du chargement des données: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'ticket_type' => 'required|string|max:255',
            'valid_until' => 'required|date|after:now',
        ]);

        $validated['ticket_code'] = Str::random(10);
        $validated['is_used'] = false;

        $ticket = Ticket::create($validated);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket créé avec succès');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'event.category',
            'event.organizer',
            'event.user',
            'user'
        ]);

        return view('dashboard.admin.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $events = Event::where('status', 'published')->get();
        $users = User::all();
        return view('dashboard.admin.tickets.edit', compact('ticket', 'events', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'ticket_type' => 'required|string|max:255',
            'valid_until' => 'required|date|after:now',
        ]);

        $ticket->update($validated);

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket mis à jour avec succès');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket supprimé avec succès');
    }

    public function verify(Request $request, Ticket $ticket)
    {
        $isValid = !$ticket->is_used && $ticket->valid_until >= now();

        if ($isValid && $request->has('mark_as_used') && $request->mark_as_used) {
            $ticket->is_used = true;
            $ticket->save();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Ticket valide' : 'Ticket invalide ou déjà utilisé'
            ]);
        }

        return view('dashboard.admin.tickets.verify', compact('ticket', 'isValid'));
    }
}
