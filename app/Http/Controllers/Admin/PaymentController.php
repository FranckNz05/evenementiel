<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Affiche la liste des paiements
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'event']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('methode_paiement', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('event', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par mode de paiement
        if ($request->filled('method')) {
            $query->where('methode_paiement', $request->method);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        // Tri
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        $payments = $query->paginate(15);

        // Statistiques générales
        $totalPayments = Payment::count();
        
        // Récupérer tous les statuts distincts pour debug
        $distinctStatuses = Payment::select('statut')->distinct()->pluck('statut')->toArray();
        
        // Utiliser whereIn pour gérer les variantes de casse possibles
        $paidStatuses = [Payment::STATUS_PAID, 'Payé', 'PAYÉ'];
        $pendingStatuses = [Payment::STATUS_PENDING, 'En attente', 'EN ATTENTE'];
        
        $paidPayments = Payment::whereIn('statut', $paidStatuses)->count();
        $pendingPayments = Payment::whereIn('statut', $pendingStatuses)->count();
        $totalRevenue = Payment::whereIn('statut', $paidStatuses)->sum('montant') ?? 0;
        
        // Conversion en float pour éviter les problèmes de type
        $totalRevenue = (float) $totalRevenue;
        
        // Debug si aucune donnée trouvée
        if ($totalPayments > 0 && $totalRevenue == 0) {
            Log::info('Statistiques paiements - Debug', [
                'total_payments' => $totalPayments,
                'paid_count' => $paidPayments,
                'pending_count' => $pendingPayments,
                'total_revenue' => $totalRevenue,
                'distinct_statuses' => $distinctStatuses,
                'status_paid_const' => Payment::STATUS_PAID,
                'status_pending_const' => Payment::STATUS_PENDING,
                'sample_payments' => Payment::select('id', 'statut', 'montant')->take(5)->get()->toArray()
            ]);
        }

        // Statistiques pour les graphiques
        $paymentMethodsData = $this->getPaymentMethodsData();
        $paymentTrendsData = $this->getPaymentTrendsData();

        // Top événements par revenus
        $topEventsData = Payment::whereIn('statut', $paidStatuses)
            ->whereNotNull('evenement_id')
            ->select('evenement_id', \DB::raw('SUM(montant) as total_revenue'), \DB::raw('COUNT(*) as payment_count'))
            ->groupBy('evenement_id')
            ->orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();
        
        // Charger les événements en une seule requête
        $eventIds = $topEventsData->pluck('evenement_id')->toArray();
        $events = \App\Models\Event::whereIn('id', $eventIds)->get()->keyBy('id');
        
        $topEvents = $topEventsData->map(function($item) use ($events) {
            $item->event = $events->get($item->evenement_id);
            $item->total_revenue = (float) $item->total_revenue; // Conversion en float
            return $item;
        })->filter(function($item) {
            return $item->event !== null; // Filtrer les événements supprimés
        });

        // Top utilisateurs par revenus
        $topUsersData = Payment::whereIn('statut', $paidStatuses)
            ->whereNotNull('user_id')
            ->select('user_id', \DB::raw('SUM(montant) as total_spent'), \DB::raw('COUNT(*) as payment_count'))
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();
        
        // Charger les utilisateurs en une seule requête
        $userIds = $topUsersData->pluck('user_id')->toArray();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $topUsers = $topUsersData->map(function($item) use ($users) {
            $item->user = $users->get($item->user_id);
            $item->total_spent = (float) $item->total_spent; // Conversion en float
            return $item;
        })->filter(function($item) {
            return $item->user !== null; // Filtrer les utilisateurs supprimés
        });

        return view('dashboard.admin.payments.index', compact(
            'payments', 
            'totalPayments',
            'paidPayments',
            'pendingPayments',
            'totalRevenue',
            'paymentMethodsData', 
            'paymentTrendsData',
            'topEvents',
            'topUsers'
        ));
    }

    /**
     * Récupère les paiements pour l'API du tableau de bord
     */
    public function getPayments(Request $request)
    {
        $query = Payment::with(['order.user', 'order.evenement']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('mode_paiement', 'like', "%{$search}%")
                  ->orWhereHas('order.user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Tri
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Filtrer par mode de paiement
        if ($request->filled('mode')) {
            $query->where('methode_paiement', $request->mode);
        }

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $payments = $query->paginate(10);

        if ($request->expectsJson()) {
            // Charger explicitement les relations pour chaque paiement
            $paymentsWithRelations = $payments->items();
            foreach ($paymentsWithRelations as $payment) {
                $payment->load(['order.user', 'order.evenement']);
            }
            return response()->json($paymentsWithRelations);
        }

        return response()->json([
            'data' => $payments->items(),
            'current_page' => $payments->currentPage(),
            'last_page' => $payments->lastPage(),
            'total' => $payments->total()
        ]);
    }

    /**
     * Affiche les détails d'un paiement
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.evenement']);

        return view('dashboard.admin.payments.show', compact('payment'));
    }

    /**
     * Télécharge la facture d'un paiement
     */
    public function downloadInvoice(Payment $payment)
    {
        // Logique pour générer et télécharger la facture
        // ...

        return back()->with('success', 'Facture téléchargée avec succès');
    }

    /**
     * Récupère les données des méthodes de paiement pour le graphique
     */
    public function getPaymentMethodsData()
    {
        // Récupérer les paiements groupés par mode de paiement
        $paidStatuses = [Payment::STATUS_PAID, 'Payé', 'PAYÉ'];
        $paymentMethods = Payment::whereIn('statut', $paidStatuses)
            ->select('methode_paiement', \DB::raw('COUNT(*) as total'))
            ->groupBy('methode_paiement')
            ->get();

        // Préparer les données pour le graphique
        $labels = [];
        $data = [];

        foreach ($paymentMethods as $method) {
            $methodName = $method->methode_paiement ?: 'Autre';
            
            // Ignorer les méthodes de paiement contenant "simulation"
            if (stripos($methodName, 'simulation') !== false) {
                continue;
            }
            
            $labels[] = $methodName;
            $data[] = $method->total;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Récupère les données des méthodes de paiement pour l'API
     */
    public function getPaymentMethods()
    {
        $data = $this->getPaymentMethodsData();
        
        // Si aucune donnée n'est trouvée, retourner des données par défaut
        if (empty($data['labels'])) {
            return response()->json([
                'labels' => ['MTN Mobile Money', 'Airtel Money', 'Mobile Money'],
                'data' => [0, 0, 0]
            ]);
        }

        return response()->json($data);
    }

    /**
     * Récupère les données des tendances de paiement pour le graphique
     */
    public function getPaymentTrendsData()
    {
        // Récupérer les paiements des 7 derniers jours
        $paidStatuses = [Payment::STATUS_PAID, 'Payé', 'PAYÉ'];
        $paymentTrends = Payment::whereIn('statut', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('SUM(montant) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Préparer les données pour le graphique
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $dayTotal = $paymentTrends->where('date', $date->format('Y-m-d'))->first();
            $data[] = $dayTotal ? (float) $dayTotal->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Récupère les données des tendances de paiement pour l'API
     */
    public function getPaymentTrends()
    {
        $data = $this->getPaymentTrendsData();
        return response()->json($data);
    }

    /**
     * Exporte les paiements
     */
    public function export()
    {
        // Logique d'exportation des paiements
        // Pour l'instant, rediriger vers la page des paiements
        return redirect()->route('admin.payments.index')
            ->with('success', 'Exportation des paiements en cours de développement');
    }
}
