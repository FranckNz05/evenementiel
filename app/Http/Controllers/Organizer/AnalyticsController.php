<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        if (!$organizer) {
            return redirect()->route('organizer.profile.create')
                ->with('error', 'Vous devez d\'abord créer votre profil organisateur.');
        }
        
        // Déterminer la période
        $period = $request->get('period', 30);
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } else {
            $startDate = Carbon::now()->subDays($period);
            $endDate = Carbon::now();
        }
        
        // Récupérer les événements de l'organisateur
        $events = Event::where('organizer_id', $organizer->id)
            ->when($request->filled('event_id'), function($query) use ($request) {
                $query->where('id', $request->event_id);
            })
            ->orderBy('title')
            ->get();
        
        // Calculer les métriques
        $metrics = $this->calculateMetrics($organizer->id, $startDate, $endDate);
        
        // Données pour les graphiques
        $revenueData = $this->getRevenueData($organizer->id, $startDate, $endDate);
        $revenueLabels = $this->getRevenueLabels($startDate, $endDate);
        
        $eventsData = $this->getEventsData($organizer->id);
        $eventsLabels = $this->getEventsLabels($organizer->id);
        
        $hourlyData = $this->getHourlyData($organizer->id, $startDate, $endDate);
        $hourlyLabels = $this->getHourlyLabels();
        
        $channelsData = $this->getChannelsData($organizer->id, $startDate, $endDate);
        $channelsLabels = $this->getChannelsLabels($organizer->id, $startDate, $endDate);
        
        // Événements les plus performants
        $topEvents = $this->getTopEvents($organizer->id, $startDate, $endDate);

        return view('dashboard.organizer.analytics.index', compact(
            'metrics',
            'events',
            'revenueData',
            'revenueLabels',
            'eventsData',
            'eventsLabels',
            'hourlyData',
            'hourlyLabels',
            'channelsData',
            'channelsLabels',
            'topEvents'
        ));
    }
    
    private function calculateMetrics($organizerId, $startDate, $endDate)
    {
        // Période précédente pour la comparaison
        $previousStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $previousEndDate = $startDate->copy();
        
        // Revenus actuels
        $currentRevenue = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('montant');
        
        // Revenus période précédente
        $previousRevenue = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('montant');
        
        // Tickets vendus
        $currentTickets = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $previousTickets = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();
        
        // Nouveaux clients
        $currentCustomers = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');
        
        $previousCustomers = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->distinct('user_id')
            ->count('user_id');
        
        // Taux de conversion
        $totalPayments = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $conversionRate = $totalPayments > 0 ? ($currentTickets / $totalPayments) * 100 : 0;
        
        $previousTotalPayments = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();
        
        $previousConversionRate = $previousTotalPayments > 0 ? ($previousTickets / $previousTotalPayments) * 100 : 0;
        
        return [
            'total_revenue' => $currentRevenue,
            'revenue_change' => $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0,
            'tickets_sold' => $currentTickets,
            'tickets_change' => $previousTickets > 0 ? (($currentTickets - $previousTickets) / $previousTickets) * 100 : 0,
            'new_customers' => $currentCustomers,
            'customers_change' => $previousCustomers > 0 ? (($currentCustomers - $previousCustomers) / $previousCustomers) * 100 : 0,
            'conversion_rate' => $conversionRate,
            'conversion_change' => $previousConversionRate > 0 ? (($conversionRate - $previousConversionRate) / $previousConversionRate) * 100 : 0,
        ];
    }
    
    private function getRevenueData($organizerId, $startDate, $endDate)
    {
        $revenueData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $revenue = Payment::whereHas('event', function($query) use ($organizerId) {
                    $query->where('organizer_id', $organizerId);
                })
                ->where('statut', 'payé')
                ->whereDate('created_at', $currentDate)
                ->sum('montant');
            
            $revenueData[] = $revenue ?? 0;
            $currentDate->addDay();
        }
        
        return $revenueData;
    }
    
    private function getRevenueLabels($startDate, $endDate)
    {
        $labels = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $labels[] = $currentDate->format('d/m');
            $currentDate->addDay();
        }
        
        return $labels;
    }
    
    private function getEventsData($organizerId)
    {
        $events = Event::where('organizer_id', $organizerId)->get();
        
        return [
            $events->where('is_published', true)->count(),
            $events->where('is_published', false)->count(),
            $events->where('end_date', '<', Carbon::now())->count(),
            $events->where('etat', 'Annulé')->count(),
        ];
    }
    
    private function getEventsLabels($organizerId)
    {
        return ['Publiés', 'Brouillons', 'Terminés', 'Annulés'];
    }
    
    private function getHourlyData($organizerId, $startDate, $endDate)
    {
        $hourlyData = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            $count = Payment::whereHas('event', function($query) use ($organizerId) {
                    $query->where('organizer_id', $organizerId);
                })
                ->where('statut', 'payé')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
            
            $hourlyData[] = $count;
        }
        
        return $hourlyData;
    }
    
    private function getHourlyLabels()
    {
        $labels = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $labels[] = sprintf('%02d:00', $hour);
        }
        return $labels;
    }
    
    private function getChannelsData($organizerId, $startDate, $endDate)
    {
        $channels = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('methode_paiement', DB::raw('count(*) as count'))
            ->groupBy('methode_paiement')
            ->get();
        
        return $channels->pluck('count')->toArray();
    }
    
    private function getChannelsLabels($organizerId, $startDate, $endDate)
    {
        $channels = Payment::whereHas('event', function($query) use ($organizerId) {
                $query->where('organizer_id', $organizerId);
            })
            ->where('statut', 'payé')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('methode_paiement')
            ->distinct()
            ->get();
        
        return $channels->pluck('methode_paiement')->toArray();
    }
    
    private function getTopEvents($organizerId, $startDate, $endDate)
    {
        return Event::where('organizer_id', $organizerId)
            ->with(['tickets'])
            ->get()
            ->map(function($event) use ($startDate, $endDate) {
                $revenue = Payment::where('evenement_id', $event->id)
                    ->where('statut', 'payé')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('montant');
                
                $ticketsSold = Payment::where('evenement_id', $event->id)
                    ->where('statut', 'payé')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                $totalPayments = Payment::where('evenement_id', $event->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                $conversionRate = $totalPayments > 0 ? ($ticketsSold / $totalPayments) * 100 : 0;
                $performance = min(100, ($revenue / 100000) * 100); // Performance basée sur 100k FCFA
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'image' => $event->image,
                    'start_date' => $event->start_date,
                    'revenue' => $revenue,
                    'tickets_sold' => $ticketsSold,
                    'conversion_rate' => $conversionRate,
                    'performance' => $performance
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
    }
    
    public function export(Request $request)
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        // Même logique que index() pour récupérer les données
        $period = $request->get('period', 30);
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        if ($period === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } else {
            $startDate = Carbon::now()->subDays($period);
            $endDate = Carbon::now();
        }
        
        $metrics = $this->calculateMetrics($organizer->id, $startDate, $endDate);
        $topEvents = $this->getTopEvents($organizer->id, $startDate, $endDate);
        
        // Générer le rapport CSV
        $filename = 'rapport_analytics_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($metrics, $topEvents) {
            $file = fopen('php://output', 'w');
            
            // Métriques générales
            fputcsv($file, ['MÉTRIQUES GÉNÉRALES']);
            fputcsv($file, ['Revenus totaux (FCFA)', $metrics['total_revenue']]);
            fputcsv($file, ['Tickets vendus', $metrics['tickets_sold']]);
            fputcsv($file, ['Nouveaux clients', $metrics['new_customers']]);
            fputcsv($file, ['Taux de conversion (%)', $metrics['conversion_rate']]);
            fputcsv($file, []);
            
            // Événements les plus performants
            fputcsv($file, ['ÉVÉNEMENTS LES PLUS PERFORMANTS']);
            fputcsv($file, ['Événement', 'Revenus (FCFA)', 'Tickets vendus', 'Taux de conversion (%)', 'Performance (%)']);
            
            foreach ($topEvents as $event) {
                fputcsv($file, [
                    $event->title,
                    $event->revenue,
                    $event->tickets_sold,
                    $event->conversion_rate,
                    $event->performance
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
