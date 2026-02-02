<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Organizer;
use App\Models\OrganizerAccessCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            Log::info('User ID: ' . $user->id . ' - Starting dashboard load');
            
            // Vérifier et obtenir l'organisateur
            $organizer = $user->organizer()->first();
            Log::info('Organizer found: ' . ($organizer ? 'Yes' : 'No'));
            
            if (!$organizer) {
                Log::info('No organizer found, creating new one');
                // Créer un nouvel organisateur si aucun n'existe
                try {
                    $organizer = new Organizer([
                        'user_id' => $user->id,
                        'company_name' => $user->prenom ? $user->prenom . ' ' . $user->nom : 'Mon Entreprise',
                        'email' => $user->email,
                        'is_verified' => false
                    ]);
                    $organizer->save();
                    Log::info('New organizer created with ID: ' . $organizer->id);
                    
                    // Rafraîchir la relation
                    $user->load('organizer');
                    $organizer = $user->organizer;
                    
                    // Attribuer le rôle organisateur si pas déjà fait
                    if (!$user->hasRole('Organizer') && !$user->hasRole('organizer')) {
                        $user->assignRole('Organizer');
                        Log::info('Assigned Organizer role to user');
                    }
                } catch (\Exception $e) {
                    Log::error('Error creating organizer: ' . $e->getMessage());
                    throw $e;
                }
            }
            
            // Récupérer les événements avec eager loading
            $events = Event::where('organizer_id', $organizer->id)
                ->with(['tickets', 'category', 'payments'])
                ->orderBy('start_date', 'asc')
                ->get();
            
            Log::info('Events found: ' . $events->count());
            
            // Événements à venir
            $upcomingEvents = $events->filter(function($event) {
                return $event->start_date >= now();
            })->take(5);
            
            Log::info('Upcoming events: ' . $upcomingEvents->count());
            
            // Calculer les statistiques
            $stats = [
                'total_events' => $events->count(),
                'active_events' => $events->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->count(),
                'published_events' => $events->where('is_published', true)->count(),
                'draft_events' => $events->where('is_published', false)->count(),
                'completed_events' => $events->where('end_date', '<', now())->count(),
                'cancelled_events' => $events->where('etat', 'Annulé')->count(),
                'tickets_sold' => $events->sum(function($event) {
                    return $event->tickets->sum('quantite_vendue') ?? 0;
                }),
                'total_revenue' => Payment::whereIn('evenement_id', $events->pluck('id'))
    ->where('statut', 'payé')
    ->sum('montant')
            ];
            
            // Paiements récents
            $recentPayments = Payment::whereIn('evenement_id', $events->pluck('id'))
                ->with(['user', 'event'])
                ->latest()
                ->take(10)
                ->get();
                
            // Données pour les graphiques
            $revenueData = $this->getRevenueData($organizer->id);
            $revenueLabels = $this->getRevenueLabels();

            // Debug: Log les données pour voir ce qui est passé à la vue
            Log::info('Dashboard Data:', [
                'organizer_id' => $organizer->id,
                'events_count' => $events->count(),
                'stats' => $stats,
                'upcomingEvents_count' => $upcomingEvents->count(),
                'recentPayments_count' => $recentPayments->count(),
                'revenueData' => $revenueData,
                'revenueLabels' => $revenueLabels
            ]);

            return view('dashboard.organizer.index', compact(
                'stats', 
                'upcomingEvents', 
                'recentPayments', 
                'revenueData',
                'revenueLabels'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in OrganizerDashboardController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Return a default view with error message
            return view('dashboard.organizer.index', [
                'stats' => [
                    'total_events' => 0,
                    'active_events' => 0,
                    'published_events' => 0,
                    'draft_events' => 0,
                    'completed_events' => 0,
                    'cancelled_events' => 0,
                    'tickets_sold' => 0,
                    'total_revenue' => 0
                ],
                'upcomingEvents' => collect(),
                'recentPayments' => collect(), // CORRECTION: avec 's'
                'revenueData' => [],
                'revenueLabels' => []
            ])->with('error', 'Une erreur est survenue lors du chargement du tableau de bord.');
        }
    }
    
    private function getRevenueData($organizerId)
    {
        // Récupérer les revenus des 30 derniers jours
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Payment::whereIn('evenement_id', function($query) use ($organizerId) {
                    $query->select('id')
                        ->from('events')
                        ->where('organizer_id', $organizerId);
                })
                ->where('statut', 'payé')
                ->whereDate('created_at', $date)
                ->sum('montant');
            $revenueData[] = $revenue ?? 0;
        }
        return $revenueData;
    }
    
    private function getRevenueLabels()
    {
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
        }
        return $labels;
    }

    /**
     * Affiche la page des codes d'accès
     */
    public function accessCodes()
    {
        $user = Auth::user();
        $organizer = $user->organizer;
        
        if (!$organizer) {
            return redirect()->route('organizer.profile.edit')
                ->with('error', 'Veuillez compléter votre profil organisateur.');
        }

        $accessCodes = OrganizerAccessCode::where('organizer_id', $organizer->id)
            ->with(['event'])
            ->latest()
            ->paginate(10);

        $events = Event::where('organizer_id', $organizer->id)
            ->orderBy('title')
            ->get();

        return view('organizer.access-codes.index', compact('accessCodes', 'events'));
    }

    /**
     * Génère un nouveau code d'accès
     */
    public function generateAccessCode(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'valid_until' => 'nullable|date|after:now',
            'description' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $organizer = $user->organizer;

        if (!$organizer) {
            return redirect()->back()->with('error', 'Organisateur non trouvé.');
        }

        // Vérifier que l'événement appartient à l'organisateur
        $event = Event::where('id', $request->event_id)
            ->where('organizer_id', $organizer->id)
            ->first();

        if (!$event) {
            return redirect()->back()->with('error', 'Événement non trouvé ou non autorisé.');
        }

        // Créer le code d'accès
        $accessCode = OrganizerAccessCode::create([
            'organizer_id' => $organizer->id,
            'event_id' => $request->event_id,
            'valid_from' => now(),
            'valid_until' => $request->valid_until ?? $event->end_date,
            'is_active' => true,
            'created_by' => $user->id
        ]);

        return redirect()->back()
            ->with('success', 'Code d\'accès généré avec succès: ' . $accessCode->access_code);
    }

    /**
     * Supprime un code d'accès
     */
    public function deleteAccessCode(OrganizerAccessCode $code)
    {
        $user = Auth::user();
        $organizer = $user->organizer;

        if (!$organizer || $code->organizer_id !== $organizer->id) {
            return redirect()->back()->with('error', 'Code d\'accès non trouvé ou non autorisé.');
        }

        $code->delete();

        return redirect()->back()->with('success', 'Code d\'accès supprimé avec succès.');
    }
}