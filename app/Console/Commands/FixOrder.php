<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FixOrder extends Command
{
    protected $signature = 'fix:order {id}';
    protected $description = 'Vérifie et corrige une commande spécifique';

    public function handle()
    {
        $orderId = $this->argument('id');
        $order = Order::find($orderId);

        if (!$order) {
            $this->error("Commande avec ID {$orderId} non trouvée");
            return 1;
        }

        $this->info("Vérification de la commande #{$orderId}");
        
        // Vérifier les relations
        $order->load(['tickets', 'event', 'payment']);
        
        $this->info("Utilisateur: " . ($order->user_id ?? 'Non défini'));
        $this->info("Événement: " . ($order->event_id ?? $order->evenement_id ?? 'Non défini'));
        $this->info("Statut: " . ($order->statut ?? 'Non défini'));
        $this->info("Montant total: " . ($order->montant_total ?? 'Non défini'));
        
        // Vérifier les tickets
        $tickets = $order->tickets;
        if ($tickets->isEmpty()) {
            $this->warn("Aucun ticket trouvé pour cette commande");
            
            // Demander s'il faut ajouter un ticket de test
            if ($this->confirm('Voulez-vous ajouter un ticket de test à cette commande?')) {
                $ticket = Ticket::first();
                if ($ticket) {
                    $order->tickets()->attach($ticket->id, [
                        'quantity' => 1,
                        'unit_price' => $ticket->prix,
                        'total_amount' => $ticket->prix
                    ]);
                    $this->info("Ticket de test ajouté");
                } else {
                    $this->error("Aucun ticket disponible dans la base de données");
                }
            }
        } else {
            $this->info("Tickets trouvés: " . $tickets->count());
            foreach ($tickets as $ticket) {
                $this->info("- {$ticket->nom}: {$ticket->pivot->quantity} x {$ticket->pivot->unit_price} = {$ticket->pivot->total_amount}");
            }
        }
        
        // Vérifier le paiement
        $payment = $order->payment;
        if (!$payment) {
            $this->warn("Aucun paiement trouvé pour cette commande");
            
            // Vérifier s'il existe un paiement avec order_ticket_id
            $alternativePayment = Payment::where('order_ticket_id', $order->id)->first();
            if ($alternativePayment) {
                $this->info("Paiement trouvé avec order_ticket_id");
                
                // Mettre à jour le paiement
                $alternativePayment->order_id = $order->id;
                $alternativePayment->save();
                
                $this->info("Paiement mis à jour avec order_id");
            }
        } else {
            $this->info("Paiement trouvé: #{$payment->id}");
            $this->info("- Statut: {$payment->statut}");
            $this->info("- Montant: {$payment->montant}");
            $this->info("- Méthode: {$payment->methode_paiement}");
        }
        
        // Recalculer le montant total
        $totalAmount = $order->tickets->sum(function($ticket) {
            return $ticket->pivot->total_amount ?? ($ticket->pivot->quantity * $ticket->pivot->unit_price);
        });
        
        if ($totalAmount != $order->montant_total) {
            $this->warn("Montant total incorrect: {$order->montant_total} vs {$totalAmount}");
            
            if ($this->confirm('Voulez-vous mettre à jour le montant total?')) {
                $order->montant_total = $totalAmount;
                $order->save();
                $this->info("Montant total mis à jour");
            }
        }
        
        $this->info("Vérification terminée");
        return 0;
    }
}