<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array|min:1',
            'tickets.*.id' => 'required|exists:tickets,id',
            'tickets.*.quantity' => 'required|integer|min:1'
        ]);

        // Bloquer l'achat si l'événement est passé
        $event = Event::find($validated['event_id']);
        if ($event && $event->end_date && \Carbon\Carbon::parse($event->end_date)->isPast()) {
            return back()->with('error', "Cet événement est terminé. L'achat de billets n'est plus possible.");
        }

        DB::beginTransaction();

        try {
            // Créer la commande
            $order = Order::create([
                'matricule' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'evenement_id' => $validated['event_id'],
                'statut' => 'en_attente'
            ]);

            $totalAmount = 0;

            // Ajouter les tickets
            foreach ($validated['tickets'] as $ticketData) {
                $ticket = Ticket::find($ticketData['id']);
                $quantity = $ticketData['quantity'];

                // Vérifier la disponibilité
                if ($ticket->quantite_vendue + $quantity > $ticket->quantite) {
                    throw new \Exception("Quantité non disponible pour le billet: {$ticket->nom}");
                }

                $order->tickets()->attach($ticket->id, [
                    'quantity' => $quantity,
                    'unit_price' => $ticket->prix,
                    'total_amount' => $ticket->prix * $quantity
                ]);

                $totalAmount += $ticket->prix * $quantity;
            }

            // Le montant total est stocké dans la table pivot orders_tickets
            // Pas besoin de le stocker dans orders

            DB::commit();

            // Logging explicite pour debug
            \Log::info('Commande créée avec succès', [
                'order_id' => $order->id,
                'total' => $totalAmount,
                'redirect_to' => route('payments.process', $order)
            ]);

            return redirect()->route('payments.process', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de la commande', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette commande.');
        }

        // Charger les relations nécessaires (vérifier que ticket_id existe avant de charger)
        $relationsToLoad = ['evenement', 'paiement'];
        if ($order->ticket_id) {
            $relationsToLoad[] = 'ticket';
        }
        // Toujours charger tickets (many-to-many) si disponible
        $relationsToLoad[] = 'tickets';
        $order->load($relationsToLoad);
        
        return view('orders.show', compact('order'));
    }
}
