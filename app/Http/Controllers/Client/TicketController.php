<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ticket;
use App\Http\Controllers\PaymentController; 

class TicketController extends Controller
{
    public function index()
    {
        // Récupérer les tickets via les reservations de l'utilisateur
        $tickets = Ticket::whereHas('orders', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['event', 'orders' => function($query) {
                $query->where('user_id', auth()->id());
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        // Vérifier si l'utilisateur a accès à ce ticket
        $hasAccess = $ticket->orders()
            ->where('user_id', auth()->id())
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        return view('client.tickets.show', compact('ticket'));
    }

    public function download(Ticket $ticket)
{
    // Vérifier si l'utilisateur a accès à ce ticket
    $hasAccess = $ticket->orders()
        ->where('user_id', auth()->id())
        ->exists();

    if (!$hasAccess) {
        abort(403);
    }

    // Récupérer la commande et le paiement associés
    $order = $ticket->orders()
        ->where('user_id', auth()->id())
        ->first();

    if (!$order) {
        abort(404, 'Commande non trouvée');
    }

    $payment = $order->payment;
    $event = $ticket->event;

    if (!$payment) {
        abort(404, 'Paiement non trouvé');
    }

    // Utiliser le contrôleur principal pour générer le PDF
    $pdfContent = app(\App\Http\Controllers\TicketController::class)
        ->generateSingleTicket($payment, $ticket, $event);

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="billet-'.$ticket->id.'.pdf"');
}

    public function export()
    {
        // Logique d'export
        return Excel::download(new TicketsExport(auth()->user()), 'mes-tickets.xlsx');
    }
}

