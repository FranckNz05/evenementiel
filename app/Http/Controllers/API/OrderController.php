<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\OrderTicket;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            // Créer la reservation
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
            // Pas besoin de le stocker dans orders (calculé dynamiquement via accessor)

            DB::commit();

            // Logging explicite pour debug
            \Log::info('reservation créée avec succès', [
                'order_id' => $order->id,
                'total' => $totalAmount,
                'redirect_to' => route('payments.process', $order)
            ]);

            return redirect()->route('payments.process', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['event', 'tickets', 'payment'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Vérifier si l'utilisateur est autorisé à voir cette reservation
        if (auth()->user()->id !== $order->user_id && !auth()->user()->hasRole(3)) {
            abort(403, 'Cette action n\'est pas autorisée.');
        }

        $order->load(['event', 'tickets.ticket']);
        return view('orders.show', compact('order'));
    }
}
