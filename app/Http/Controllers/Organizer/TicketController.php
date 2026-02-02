<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Affiche la liste des billets de l'organisateur
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est bien un organisateur
            if (!$user->hasRole('Organizer') && !$user->hasRole('organizer')) {
                Log::warning('Accès non autorisé aux billets', ['user_id' => $user->id]);
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }
            
            $organizer = $user->organizer;
            
            if (!$organizer) {
                return redirect()->route('organizer.profile.edit')
                    ->with('warning', 'Veuillez compléter votre profil organisateur avant de continuer.');
            }

            // Récupérer les événements de l'organisateur
            $events = Event::where('organizer_id', $organizer->id)->pluck('id');
            
            // Récupérer les billets associés à ces événements
            $tickets = Ticket::whereIn('event_id', $events)
                ->with(['event', 'orders' => function($query) {
                    $query->where('statut', 'payé');
                }])
                ->latest()
                ->paginate(15);
            
            // Compter les billets vendus pour chaque type de billet
            foreach ($tickets as $ticket) {
                // Utiliser la quantité vendue déjà enregistrée ou calculer
                $ticket->sold_count = $ticket->quantite_vendue ?? 0;
                $ticket->revenue = $ticket->orders ? $ticket->orders->sum('pivot.total_amount') : 0;
                
                // Convertir les dates si nécessaire
                if ($ticket->promotion_start && !($ticket->promotion_start instanceof \Carbon\Carbon)) {
                    $ticket->promotion_start = Carbon::parse($ticket->promotion_start);
                }
                
                if ($ticket->promotion_end && !($ticket->promotion_end instanceof \Carbon\Carbon)) {
                    $ticket->promotion_end = Carbon::parse($ticket->promotion_end);
                }
            }
            
            return view('organizer.tickets.index', compact('tickets'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des billets', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du chargement des billets. Veuillez réessayer plus tard.');
        }
    }

    /**
     * Affiche le formulaire de création d'un billet
     */
    public function create()
    {
        $events = Event::where('organizer_id', Auth::user()->organizer->id)
            ->where('start_date', '>', now())
            ->get();
            
        return view('organizer.tickets.create', compact('events'));
    }

    /**
     * Enregistre un nouveau billet
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'start_sale_date' => 'required|date',
            'end_sale_date' => 'required|date|after:start_sale_date',
        ]);

        try {
            // Vérifier si l'événement appartient à l'organisateur
            $event = Event::findOrFail($request->event_id);
            
            if ($event->organizer_id != Auth::user()->organizer->id) {
                return back()->with('error', 'Vous n\'êtes pas autorisé à créer des billets pour cet événement.');
            }
            
            // Vérifier si un billet avec le même nom existe déjà pour cet événement
            $existingTicket = Ticket::where('event_id', $request->event_id)
                ->where('nom', $request->name)
                ->first();
            
            if ($existingTicket) {
                return back()->with('error', 'Un billet avec ce nom existe déjà pour cet événement. Veuillez choisir un autre nom.')
                    ->withInput();
            }
            
            $ticket = new Ticket($request->all());
            $ticket->save();
            
            return redirect()->route('organizer.tickets.index')
                ->with('success', 'Le billet a été créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du billet', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la création du billet. Veuillez réessayer.');
        }
    }

    /**
     * Affiche les détails d'un billet
     */
    public function show(Ticket $ticket)
    {
        // Vérifier si le billet appartient à l'organisateur
        if ($ticket->event->organizer_id != Auth::user()->organizer->id) {
            return redirect()->route('organizer.tickets.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir ce billet.');
        }
        
        $sales = Order::whereHas('tickets', function($query) use ($ticket) {
                $query->where('ticket_id', $ticket->id);
            })
            ->where('statut', 'payé')
            ->with('user')
            ->latest()
            ->get();
            
        return view('organizer.tickets.show', compact('ticket', 'sales'));
    }

    /**
     * Affiche le formulaire de modification d'un billet
     */
    public function edit(Ticket $ticket)
    {
        // Vérifier si le billet appartient à l'organisateur
        if ($ticket->event->organizer_id != Auth::user()->organizer->id) {
            return redirect()->route('organizer.tickets.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce billet.');
        }
        
        return view('organizer.tickets.edit', compact('ticket'));
    }

    /**
     * Met à jour un billet
     */
    public function update(Request $request, Ticket $ticket)
    {
        // Vérifier si le billet appartient à l'organisateur
        if ($ticket->event->organizer_id != Auth::user()->organizer->id) {
            return redirect()->route('organizer.tickets.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce billet.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'start_sale_date' => 'required|date',
            'end_sale_date' => 'required|date|after:start_sale_date',
        ]);
        
        try {
            // Vérifier si un autre billet avec le même nom existe déjà pour cet événement
            $existingTicket = Ticket::where('event_id', $ticket->event_id)
                ->where('nom', $request->name)
                ->where('id', '!=', $ticket->id)
                ->first();
            
            if ($existingTicket) {
                return back()->with('error', 'Un autre billet avec ce nom existe déjà pour cet événement. Veuillez choisir un autre nom.')
                    ->withInput();
            }
            
            $ticket->update($request->all());
            
            return redirect()->route('organizer.tickets.index')
                ->with('success', 'Le billet a été mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du billet', [
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du billet. Veuillez réessayer.');
        }
    }

    /**
     * Supprime un billet
     */
    public function destroy(Ticket $ticket)
    {
        // Vérifier si le billet appartient à l'organisateur
        if ($ticket->event->organizer_id != Auth::user()->organizer->id) {
            return redirect()->route('organizer.tickets.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce billet.');
        }
        
        // Vérifier si des billets ont déjà été vendus
        $soldCount = $ticket->orders()->where('statut', 'payé')->count();
        
        if ($soldCount > 0) {
            return back()->with('error', 'Impossible de supprimer ce billet car des ventes ont déjà été effectuées.');
        }
        
        try {
            $ticket->delete();
            
            return redirect()->route('organizer.tickets.index')
                ->with('success', 'Le billet a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du billet', [
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Une erreur est survenue lors de la suppression du billet. Veuillez réessayer.');
        }
    }
}
