<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Payment;

class TestTicketData extends Command
{
    protected $signature = 'ticket:test-data {payment_id?}';
    protected $description = 'Vérifier les données disponibles pour un billet';

    public function handle()
    {
        $paymentId = $this->argument('payment_id') ?? Payment::latest()->first()?->id;
        
        if (!$paymentId) {
            $this->error('Aucun paiement trouvé dans la base de données');
            return 1;
        }
        
        $payment = Payment::with(['order.event.organizer', 'order.event.sponsors', 'order.tickets', 'user'])
            ->find($paymentId);
            
        if (!$payment) {
            $this->error("Paiement #{$paymentId} introuvable");
            return 1;
        }
        
        $event = $payment->order->event;
        $ticket = $payment->order->tickets->first();
        
        $this->info('=== DONNÉES DU BILLET ===');
        $this->info('');
        
        $this->line("🎫 Paiement: #{$payment->id} - {$payment->matricule}");
        $this->line("👤 Utilisateur: {$payment->user->prenom} {$payment->user->nom}");
        $this->line('');
        
        $this->line("📅 Événement: {$event->title}");
        $this->line("   - Date: " . ($event->start_date ? $event->start_date->format('d M Y H:i') : 'N/A'));
        $this->line("   - Lieu: " . ($event->lieu ?? 'N/A'));
        $this->line("   - Ville: " . ($event->ville ?? 'N/A'));
        $this->line("   - Image: " . ($event->image ?? 'AUCUNE'));
        
        if ($event->image) {
            $imagePath = $event->image;
            $this->line("   - Type image: " . (filter_var($imagePath, FILTER_VALIDATE_URL) ? 'URL' : 'Chemin local'));
            
            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $paths = [
                    'storage/app/public/' . $imagePath => file_exists(storage_path('app/public/' . $imagePath)),
                    'public/storage/' . $imagePath => file_exists(public_path('storage/' . $imagePath)),
                    'public/' . $imagePath => file_exists(public_path($imagePath)),
                ];
                
                $found = false;
                foreach ($paths as $path => $exists) {
                    if ($exists) {
                        $this->line("   ✅ Trouvée dans: {$path}");
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $this->error("   ❌ Image introuvable dans tous les emplacements");
                }
            }
        }
        
        $this->line('');
        $this->line("🎟️  Billet: {$ticket->nom}");
        $this->line("   - Prix: " . number_format($ticket->prix ?? 0, 0, ',', ' ') . ' FCFA');
        $this->line("   - Quantité: " . ($ticket->pivot->quantity ?? 1));
        
        $this->line('');
        
        if ($event->organizer) {
            $this->line("🏢 Organisateur: {$event->organizer->prenom} {$event->organizer->nom}");
            $this->line("   - Logo: " . ($event->organizer->logo ?? 'AUCUN'));
            
            if ($event->organizer->logo && !filter_var($event->organizer->logo, FILTER_VALIDATE_URL)) {
                $logoExists = file_exists(storage_path('app/public/' . $event->organizer->logo)) 
                           || file_exists(public_path('storage/' . $event->organizer->logo));
                $this->line("   - Existe: " . ($logoExists ? '✅' : '❌'));
            }
        } else {
            $this->line("🏢 Organisateur: AUCUN");
        }
        
        $this->line('');
        
        if ($event->sponsors && $event->sponsors->count() > 0) {
            $this->line("🤝 Sponsors: {$event->sponsors->count()}");
            foreach ($event->sponsors->take(4) as $index => $sponsor) {
                $this->line("   " . ($index + 1) . ". {$sponsor->nom}");
                $this->line("      Logo: " . ($sponsor->logo_path ?? 'AUCUN'));
            }
        } else {
            $this->line("🤝 Sponsors: AUCUN");
        }
        
        $this->info('');
        $this->info("🔗 Prévisualisation: http://127.0.0.1:8000/ticket/design/preview?payment_id={$payment->id}");
        
        return 0;
    }
}

