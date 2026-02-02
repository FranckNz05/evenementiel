<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\QrScan;
use App\Models\Withdrawal;
use App\Services\CommissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->middleware(['auth', 'verified']);
        $this->commissionService = $commissionService;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est bien un organisateur
            if (!$user->hasRole('Organizer') && !$user->hasRole(2) && !$user->hasRole('organizer')) {
                Log::warning('Accès non autorisé au tableau de bord organisateur', ['user_id' => $user->id]);
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }
            
            // Récupérer l'organisateur lié à l'utilisateur avec eager loading
            $organizer = $user->organizer;
            
            Log::info('Debug Dashboard - Utilisateur et Organisateur', [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name'),
                'has_organizer' => $organizer ? true : false,
                'organizer_id' => $organizer ? $organizer->id : null
            ]);
            
            if (!$organizer) {
                Log::warning('Profil organisateur non trouvé', ['user_id' => $user->id]);
                // Créer un organisateur par défaut si l'utilisateur n'en a pas
                $organizer = new Organizer([
                    'user_id' => $user->id,
                    'company_name' => $user->prenom . ' ' . $user->nom,
                    'email' => $user->email,
                    'is_verified' => false
                ]);
                $organizer->save();
                Log::info('Organisateur créé automatiquement', ['organizer_id' => $organizer->id]);
            }
            
            // Charger les relations nécessaires
            $organizer->load(['events' => function($query) {
                $query->withCount(['tickets as tickets_sold' => function($q) {
                    $q->whereHas('orders', function($order) {
                        $order->where('statut', 'payé');
                    });
                }]);
            }]);

            // Récupérer les IDs des événements une seule fois
            $eventIds = $organizer->events()->pluck('id');
            
            Log::info('Debug Dashboard - Événements', [
                'organizer_id' => $organizer->id,
                'events_count' => $eventIds->count(),
                'event_ids' => $eventIds->toArray()
            ]);
            
            // Calculer les statistiques de base avec toutes les clés attendues par la vue
            $stats = [
                'events_count' => $eventIds->count(),
                'active_events' => Event::where('organizer_id', $organizer->id)
                    ->where(function($query) {
                        $query->where('end_date', '>=', now())
                              ->orWhereNull('end_date');
                    })
                    ->count(),
                'published_events' => Event::where('organizer_id', $organizer->id)
                                          ->where('is_published', true)->count(),
                'draft_events' => Event::where('organizer_id', $organizer->id)
                                      ->where('is_published', false)->count(),
                'completed_events' => Event::where('organizer_id', $organizer->id)
                                          ->where('end_date', '<', now())
                                          ->count(),
                'cancelled_events' => Event::where('organizer_id', $organizer->id)
                                          ->where('etat', 'Annulé')
                                          ->count(),
            ];
            
            // Calculer les billets vendus et le chiffre d'affaires
            if ($eventIds->isNotEmpty()) {
                $ticketsData = DB::table('orders_tickets')
                    ->join('tickets', 'orders_tickets.ticket_id', '=', 'tickets.id')
                    ->join('orders', 'orders_tickets.order_id', '=', 'orders.id')
                    ->whereIn('tickets.event_id', $eventIds)
                    ->where('orders.statut', 'payé')
                    ->select(
                        DB::raw('SUM(orders_tickets.quantity) as total_sold'), 
                        DB::raw('SUM(orders_tickets.total_amount) as total_revenue')
                    )
                    ->first();
                    
                Log::info('Debug Dashboard - Billets et Revenus', [
                    'tickets_data' => $ticketsData,
                    'total_sold' => $ticketsData->total_sold ?? 0,
                    'total_revenue' => $ticketsData->total_revenue ?? 0
                ]);
                
                $stats['tickets_sold'] = $ticketsData->total_sold ?? 0;
                $stats['total_revenue'] = $ticketsData->total_revenue ?? 0;
                
                // Compter les scans
                $stats['total_scans'] = \App\Models\QrScan::whereHas('ticket', function($query) use ($eventIds) {
                    $query->whereIn('event_id', $eventIds);
                })->count();
                
                // Récupérer les paiements récents
                $recentPayments = Payment::with(['user', 'event'])
                    ->whereIn('evenement_id', $eventIds)
                    ->where('statut', 'payé')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                    
                // Préparer les données pour le graphique des revenus (30 derniers jours)
                $revenueData = [];
                $revenueLabels = [];
                
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $revenueLabels[] = $date->format('d M');
                    
                    $dailyRevenue = Payment::whereIn('evenement_id', $eventIds)
                        ->where('statut', 'payé')
                        ->whereDate('created_at', $date->toDateString())
                        ->sum('montant');
                        
                    $revenueData[] = $dailyRevenue;
                }
            } else {
                Log::info('Debug Dashboard - Aucun événement trouvé', [
                    'organizer_id' => $organizer->id,
                    'event_ids_count' => $eventIds->count()
                ]);
                $stats['tickets_sold'] = 0;
                $stats['total_revenue'] = 0;
                $stats['total_scans'] = 0;
                $recentPayments = collect([]);
                $revenueData = array_fill(0, 30, 0);
                $revenueLabels = [];
                
                for ($i = 29; $i >= 0; $i--) {
                    $revenueLabels[] = Carbon::now()->subDays($i)->format('d M');
                }
            }
            
            // Assurez-vous que toutes les clés nécessaires existent dans $stats
            $requiredKeys = ['events_count', 'active_events', 'total_orders', 'total_revenue', 'tickets_sold', 'tickets_used', 'access_codes'];
            foreach ($requiredKeys as $key) {
                if (!isset($stats[$key])) {
                    $stats[$key] = 0;
                }
            }
            
            // Ajoutez des tableaux vides pour les données de collection si absents
            if (!isset($stats['recent_events'])) $stats['recent_events'] = [];
            if (!isset($stats['recent_orders'])) $stats['recent_orders'] = [];
            if (!isset($stats['recent_blogs'])) $stats['recent_blogs'] = [];
            if (!isset($stats['recent_scans'])) $stats['recent_scans'] = [];
            if (!isset($stats['blogs_count'])) $stats['blogs_count'] = 0;

            // Tous les événements de l'organisateur
            $upcomingEvents = $organizer->events()
                ->with(['category', 'tickets'])
                ->orderBy('start_date', 'desc')
                ->take(5)
                ->get()
                ->map(function($event) {
                    // Calculer le nombre de billets vendus pour chaque événement
                    $event->tickets_sold = $event->tickets->sum('quantite_vendue');
                    
                    // Calculer le revenu total de l'événement
                    $event->total_revenue = DB::table('orders_tickets')
                        ->join('tickets', 'orders_tickets.ticket_id', '=', 'tickets.id')
                        ->join('orders', 'orders_tickets.order_id', '=', 'orders.id')
                        ->where('tickets.event_id', $event->id)
                        ->where('orders.statut', 'payé')
                        ->sum('orders_tickets.total_amount');
                    
                    return $event;
                });
            
            Log::info('Événements à venir chargés', [
                'count' => $upcomingEvents->count(),
                'event_ids' => $upcomingEvents->pluck('id'),
                'organizer_id' => $organizer->id,
                'all_events_count' => $organizer->events()->count(),
                'all_events' => $organizer->events()->select('id', 'title', 'start_date')->get()->toArray()
            ]);

            // Calculer le solde wallet (revenus nets - retraits effectués)
            $revenueCalculation = $this->commissionService->calculateOrganizerTotalNetRevenue($organizer->id);
            
            // Gérer l'erreur de mémoire MySQL potentielle
            // Seuls les retraits complétés sont déduits du solde
            // Les retraits en "processing" ne sont pas déduits tant qu'Airtel n'a pas confirmé
            try {
                $totalWithdrawn = Withdrawal::where('organizer_id', $organizer->id)
                    ->where('status', 'completed')
                    ->sum('amount');
            } catch (\Exception $e) {
                Log::warning('Erreur lors du calcul des retraits', [
                    'organizer_id' => $organizer->id,
                    'error' => $e->getMessage()
                ]);
                $totalWithdrawn = 0;
            }
            
            $walletBalance = $revenueCalculation['net_revenue'] - $totalWithdrawn;
            $stats['wallet_balance'] = $walletBalance;
            $stats['total_withdrawn'] = $totalWithdrawn;
            
            // Préparer les données pour la vue
            $viewData = [
                'stats' => $stats,
                'upcomingEvents' => $upcomingEvents,
                'recentPayments' => $recentPayments,
                'revenueData' => $revenueData,
                'revenueLabels' => $revenueLabels,
                'currentYear' => Carbon::now()->year,
                'organizer' => $organizer
            ];
            
            Log::info('Données du tableau de bord préparées', [
                'stats' => $stats,
                'upcoming_events_count' => $upcomingEvents->count(),
                'recent_payments_count' => $recentPayments->count(),
                'revenue_data_points' => count($revenueData),
                'organizer_id' => $organizer->id,
                'user_id' => $user->id,
                'wallet_balance' => $walletBalance
            ]);
            
            return view('dashboard.organizer.index', $viewData);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du tableau de bord organisateur', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback: afficher un dashboard minimal au lieu de rediriger vers l'accueil
            $stats = [
                'totalEvents' => 0,
                'upcomingEvents' => 0,
                'ticketsSold' => 0,
                'revenue' => 0,
            ];
            $upcomingEvents = collect();
            $recentPayments = collect();
            $revenueLabels = [];
            $revenueData = [];
            $walletBalance = 0;
            $organizer = Auth::user() ? Auth::user()->organizer : null;

            $viewData = compact(
                'stats',
                'upcomingEvents',
                'recentPayments',
                'revenueData',
                'revenueLabels',
                'organizer',
                'walletBalance'
            );

            return view('dashboard.organizer.index', $viewData)
                ->with('error', 'Certaines données n\'ont pas pu être chargées.');
        }
    }
}