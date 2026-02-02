<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrScan;
use App\Models\Ticket;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Affiche la liste des scans de l'organisateur
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur est bien un organisateur
            if (!$user->hasRole('Organizer') && !$user->hasRole('organizer')) {
                Log::warning('Accès non autorisé aux scans', ['user_id' => $user->id]);
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }
            
            $organizer = $user->organizer;
            
            if (!$organizer) {
                return redirect()->route('organizer.profile.edit')
                    ->with('warning', 'Veuillez compléter votre profil organisateur avant de continuer.');
            }

            // Récupérer les événements de l'organisateur
            $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');
            
            // Récupérer les scans pour ces événements
            $scans = QrScan::whereHas('ticket', function($query) use ($eventIds) {
                    $query->whereIn('event_id', $eventIds);
                })
                ->with(['ticket.event', 'scannedBy'])
                ->latest()
                ->paginate(15);
            
            // Récupérer les statistiques de scan
            $scanStats = [
                'today' => QrScan::whereHas('ticket', function($query) use ($eventIds) {
                        $query->whereIn('event_id', $eventIds);
                    })
                    ->whereDate('created_at', Carbon::today())
                    ->count(),
                    
                'week' => QrScan::whereHas('ticket', function($query) use ($eventIds) {
                        $query->whereIn('event_id', $eventIds);
                    })
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->count(),
                    
                'month' => QrScan::whereHas('ticket', function($query) use ($eventIds) {
                        $query->whereIn('event_id', $eventIds);
                    })
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                    
                'total' => QrScan::whereHas('ticket', function($query) use ($eventIds) {
                        $query->whereIn('event_id', $eventIds);
                    })
                    ->count(),
            ];
            
            // Récupérer les événements pour le filtre
            $events = Event::where('organizer_id', $organizer->id)
                ->orderBy('title')
                ->get();
            
            return view('organizer.scans.index', compact('scans', 'scanStats', 'events'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des scans', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du chargement des scans. Veuillez réessayer plus tard.');
        }
    }

    /**
     * Filtrer les scans par événement
     */
    public function filter(Request $request)
    {
        $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
        ]);
        
        try {
            $user = Auth::user();
            $organizer = $user->organizer;
            
            // Récupérer les événements de l'organisateur
            $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');
            
            // Construire la requête
            $query = QrScan::whereHas('ticket', function($query) use ($eventIds, $request) {
                $query->whereIn('event_id', $eventIds);
                
                if ($request->filled('event_id')) {
                    $query->where('event_id', $request->event_id);
                }
            });
            
            // Filtrer par date
            if ($request->filled('date_start')) {
                $query->whereDate('created_at', '>=', $request->date_start);
            }
            
            if ($request->filled('date_end')) {
                $query->whereDate('created_at', '<=', $request->date_end);
            }
            
            // Récupérer les résultats
            $scans = $query->with(['ticket.event', 'scannedBy'])
                ->latest()
                ->paginate(15)
                ->appends($request->all());
            
            // Récupérer les statistiques de scan
            $scanStats = [
                'filtered_count' => $query->count(),
                'total' => QrScan::whereHas('ticket', function($query) use ($eventIds) {
                    $query->whereIn('event_id', $eventIds);
                })->count(),
            ];
            
            // Récupérer les événements pour le filtre
            $events = Event::where('organizer_id', $organizer->id)
                ->orderBy('title')
                ->get();
            
            return view('organizer.scans.index', compact('scans', 'scanStats', 'events'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du filtrage des scans', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('organizer.scans.index')
                ->with('error', 'Une erreur est survenue lors du filtrage des scans. Veuillez réessayer.');
        }
    }

    /**
     * Télécharger les scans en CSV
     */
    public function export(Request $request)
    {
        try {
            $user = Auth::user();
            $organizer = $user->organizer;
            
            // Récupérer les événements de l'organisateur
            $eventIds = Event::where('organizer_id', $organizer->id)->pluck('id');
            
            // Construire la requête
            $query = QrScan::whereHas('ticket', function($query) use ($eventIds, $request) {
                $query->whereIn('event_id', $eventIds);
                
                if ($request->filled('event_id')) {
                    $query->where('event_id', $request->event_id);
                }
            });
            
            // Filtrer par date
            if ($request->filled('date_start')) {
                $query->whereDate('created_at', '>=', $request->date_start);
            }
            
            if ($request->filled('date_end')) {
                $query->whereDate('created_at', '<=', $request->date_end);
            }
            
            // Récupérer les résultats
            $scans = $query->with(['ticket.event', 'scannedBy'])->get();
            
            // Créer le nom du fichier
            $filename = 'scans_' . Carbon::now()->format('Y-m-d') . '.csv';
            
            // Créer l'en-tête du fichier CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            // Créer le contenu du fichier CSV
            $callback = function() use ($scans) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'ID',
                    'Événement',
                    'Type de billet',
                    'Code QR',
                    'Scanné par',
                    'Date de scan',
                    'Statut'
                ]);
                
                foreach ($scans as $scan) {
                    fputcsv($file, [
                        $scan->id,
                        $scan->ticket->event->title ?? 'Événement inconnu',
                        $scan->ticket->nom ?? 'Billet inconnu',
                        $scan->qr_code,
                        $scan->scannedBy->name ?? 'Utilisateur inconnu',
                        $scan->created_at->format('d/m/Y H:i:s'),
                        $scan->status
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation des scans', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('organizer.scans.index')
                ->with('error', 'Une erreur est survenue lors de l\'exportation des scans. Veuillez réessayer.');
        }
    }
}
