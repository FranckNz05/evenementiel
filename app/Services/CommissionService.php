<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calcule le revenu net d'un organisateur pour un paiement donné
     */
    public function calculateOrganizerNetRevenue(Payment $payment): float
    {
        $ticketPrice = $payment->montant;
        
        // Récupérer les taux de commission depuis les paramètres
        $settings = $this->getCommissionSettings();
        
        // Calculer la commission MokiliEvent (10% du montant total)
        $mokilieventCommission = $ticketPrice * ($settings['mokilievent_commission_rate'] / 100);
        
        // Revenu net = Prix du billet - Commission MokiliEvent (90% du prix)
        // Les frais MTN/Airtel sont à la charge de MokiliEvent
        $netRevenue = $ticketPrice - $mokilieventCommission;
        
        return round($netRevenue, 2);
    }
    
    /**
     * Calcule le revenu net total d'un organisateur
     */
    public function calculateOrganizerTotalNetRevenue(int $organizerId): array
    {
        // Récupérer tous les paiements des événements de cet organisateur
        $payments = Payment::whereHas('event', function($query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        })
        ->where('statut', 'payé')
        ->get();
        
        $totalRevenue = $payments->sum('montant');
        
        $settings = $this->getCommissionSettings();
        
        // Commission MokiliEvent = 10% du total des revenus
        $mokilieventCommission = $totalRevenue * ($settings['mokilievent_commission_rate'] / 100);
        
        // Revenu net de l'organisateur = 90% du total
        $netRevenue = $totalRevenue - $mokilieventCommission;
        
        $breakdown = [
            'mokilievent_commission' => $mokilieventCommission,
            'total_payments' => $payments->count()
        ];
        
        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_commission_paid' => round($mokilieventCommission, 2),
            'net_revenue' => round($netRevenue, 2),
            'breakdown' => $breakdown
        ];
    }
    
    /**
     * Récupère les paramètres de commission depuis la base de données
     */
    private function getCommissionSettings(): array
    {
        // Utiliser le cache pour éviter les requêtes répétées
        return cache()->remember('commission_settings', 3600, function() {
            $settings = DB::table('settings')->first();
            
            return [
                'mtn_commission_rate' => $settings->mtn_commission_rate ?? 3.00,
                'airtel_commission_rate' => $settings->airtel_commission_rate ?? 3.00,
                'mokilievent_commission_rate' => $settings->mokilievent_commission_rate ?? 10.00,
            ];
        });
    }
    
    /**
     * Récupère les paramètres de création d'événements
     */
    public function getEventCreationSettings(): array
    {
        return cache()->remember('event_creation_settings', 3600, function() {
            $settings = DB::table('settings')->first();
            
            return [
                'free_event_creation_fee' => $settings->free_event_creation_fee ?? 25000.00,
                'custom_event_creation_fee' => $settings->custom_event_creation_fee ?? 50000.00,
                'require_payment_for_free_events' => $settings->require_payment_for_free_events ?? true,
                'require_payment_for_custom_events' => $settings->require_payment_for_custom_events ?? true,
            ];
        });
    }
    
    /**
     * Vérifie si un utilisateur peut créer un type d'événement sans paiement
     */
    public function canCreateEventWithoutPayment(string $eventType): bool
    {
        $settings = $this->getEventCreationSettings();
        
        if ($eventType === 'gratuit') {
            return !$settings['require_payment_for_free_events'];
        } elseif ($eventType === 'personnalisé') {
            return !$settings['require_payment_for_custom_events'];
        }
        
        return false;
    }
    
    /**
     * Récupère le montant à payer pour créer un événement
     */
    public function getEventCreationFee(string $eventType): float
    {
        $settings = $this->getEventCreationSettings();
        
        if ($eventType === 'gratuit') {
            return $settings['free_event_creation_fee'];
        } elseif ($eventType === 'personnalisé') {
            return $settings['custom_event_creation_fee'];
        }
        
        return 0;
    }
}
