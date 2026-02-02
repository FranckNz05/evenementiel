<?php

namespace App\Http\Controllers\API;

use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array',
            'tickets.*.id' => 'required|exists:tickets,id',
            'tickets.*.quantity' => 'required|integer|min:1'
        ]);

        // Filtrer les tickets avec une quantité supérieure à 0
        $selectedTickets = collect($request->tickets)->filter(function ($ticket) {
            return $ticket['quantity'] > 0;
        });

        if ($selectedTickets->isEmpty()) {
            return back()->with('error', 'Veuillez sélectionner au moins un billet à réserver.');
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addDays(3) // Date limite de 3 jours pour le paiement
        ]);

        foreach ($selectedTickets as $ticketData) {
            $ticket = Ticket::findOrFail($ticketData['id']);
            $quantity = $ticketData['quantity'];

            // Vérifier si le ticket est disponible
            if ($ticket->quantite - $ticket->quantite_vendue < $quantity) {
                return back()->with('error', "La quantité demandée pour le ticket {$ticket->nom} n'est plus disponible.");
            }

            // Créer la réservation pour ce ticket
            $reservation->tickets()->attach($ticket->id, [
                'quantity' => $quantity,
                'price' => $ticket->getCurrentPriceAttribute()
            ]);

            // Mettre à jour la quantité disponible
            $ticket->quantite_vendue += $quantity;
            $ticket->save();
        }

        // Envoyer un email de confirmation
        Mail::to(auth()->user()->email)->send(new ReservationConfirmation($reservation));

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Votre réservation a été créée avec succès. Vous avez 3 jours pour effectuer le paiement.');
    }

    public function index()
    {
        $reservations = auth()->user()->reservations()
            ->with(['ticket.event', 'payment'])
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        return view('reservations.show', compact('reservation'));
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);
        
        // Libérer les tickets
        foreach ($reservation->tickets as $ticket) {
            $ticket->quantite_vendue -= $ticket->pivot->quantity;
            $ticket->save();
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Envoyer un email de notification
        Mail::to($reservation->user->email)->send(new ReservationCancelled($reservation));

        return back()->with('success', 'Votre réservation a été annulée.');
    }

    public function checkExpiredReservations()
    {
        $expiredReservations = Reservation::where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->get();

        foreach ($expiredReservations as $reservation) {
            // Libérer les tickets
            foreach ($reservation->tickets as $ticket) {
                $ticket->quantite_vendue -= $ticket->pivot->quantity;
                $ticket->save();
            }

            $reservation->status = 'expired';
            $reservation->save();

            // Envoyer un email de notification
            Mail::to($reservation->user->email)->send(new ReservationExpired($reservation));
        }
    }
}
