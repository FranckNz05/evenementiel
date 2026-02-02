<?php

namespace App\Http\Controllers;

use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerRevenueController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Affiche le tableau de bord des revenus de l'organisateur
     */
    public function dashboard()
    {
        $organizerId = Auth::id();
        $revenueData = $this->commissionService->calculateOrganizerTotalNetRevenue($organizerId);
        
        // Récupérer les événements de l'organisateur avec leurs revenus
        $events = \App\Models\Event::where('user_id', $organizerId)
            ->withCount(['payments' => function($query) {
                $query->where('statut', 'payé');
            }])
            ->withSum(['payments' => function($query) {
                $query->where('statut', 'payé');
            }], 'montant')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Calculer les revenus nets par événement
        foreach ($events as $event) {
            $eventPayments = \App\Models\Payment::whereHas('event', function($query) use ($event) {
                $query->where('id', $event->id);
            })->where('statut', 'payé')->get();
            
            $eventNetRevenue = 0;
            foreach ($eventPayments as $payment) {
                $eventNetRevenue += $this->commissionService->calculateOrganizerNetRevenue($payment);
            }
            $event->net_revenue = $eventNetRevenue;
        }
        
        return view('dashboard.organizer.revenue', compact('revenueData', 'events'));
    }

    /**
     * API pour récupérer les revenus de l'organisateur
     */
    public function getRevenueData()
    {
        $organizerId = Auth::id();
        $revenueData = $this->commissionService->calculateOrganizerTotalNetRevenue($organizerId);
        
        return response()->json($revenueData);
    }

    /**
     * Affiche l'historique détaillé des revenus
     */
    public function revenueHistory(Request $request)
    {
        $organizerId = Auth::id();
        $perPage = $request->get('per_page', 15);
        
        // Récupérer tous les paiements des événements de l'organisateur
        $payments = \App\Models\Payment::whereHas('event', function($query) use ($organizerId) {
            $query->where('user_id', $organizerId);
        })
        ->where('statut', 'payé')
        ->with(['event', 'user'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
        
        // Calculer le revenu net pour chaque paiement
        foreach ($payments as $payment) {
            $payment->net_revenue = $this->commissionService->calculateOrganizerNetRevenue($payment);
        }
        
        return view('dashboard.organizer.revenue-history', compact('payments'));
    }

    /**
     * Exporte les revenus en CSV
     */
    public function exportRevenue(Request $request)
    {
        $organizerId = Auth::id();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = \App\Models\Payment::whereHas('event', function($query) use ($organizerId) {
            $query->where('user_id', $organizerId);
        })->where('statut', 'payé');
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        $payments = $query->with(['event', 'user'])->get();
        
        $csvData = [];
        $csvData[] = ['Date', 'Événement', 'Client', 'Montant', 'Méthode', 'Revenu Net'];
        
        foreach ($payments as $payment) {
            $netRevenue = $this->commissionService->calculateOrganizerNetRevenue($payment);
            $csvData[] = [
                $payment->created_at->format('d/m/Y H:i'),
                $payment->event->title ?? 'N/A',
                $payment->user->prenom . ' ' . $payment->user->nom ?? 'N/A',
                $payment->montant . ' FCFA',
                $payment->methode_paiement,
                $netRevenue . ' FCFA'
            ];
        }
        
        $filename = 'revenus_organisateur_' . date('Y-m-d') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
