<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\Exports\AdminPaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:3']);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['user', 'order']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'like', "%{$search}%")
                  ->orWhere('reference_transaction', 'like', "%{$search}%")
                  ->orWhere('methode_paiement', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('statut', $request->status);
        }

        $payments = $query->latest()->paginate(10);
        
        // Calculer les statistiques - Utiliser les constantes du modèle Payment
        $totalPayments = Payment::count();
        
        // Utiliser whereIn pour gérer les variantes de casse possibles
        $paidStatuses = [Payment::STATUS_PAID, 'Payé', 'PAYÉ'];
        $pendingStatuses = [Payment::STATUS_PENDING, 'En attente', 'EN ATTENTE'];
        
        $paidPayments = Payment::whereIn('statut', $paidStatuses)->count();
        $totalRevenue = Payment::whereIn('statut', $paidStatuses)->sum('montant') ?? 0;
        $pendingPayments = Payment::whereIn('statut', $pendingStatuses)->count();
        $successRate = $totalPayments > 0 ? round(($paidPayments / $totalPayments) * 100, 1) : 0;
        
        // Conversion en float pour éviter les problèmes de type
        $totalRevenue = (float) $totalRevenue;
        
        // Top 5 événements par revenus
        $topEventsData = Payment::whereIn('statut', $paidStatuses)
            ->whereNotNull('evenement_id')
            ->select('evenement_id', DB::raw('SUM(montant) as total_revenue'), DB::raw('COUNT(*) as payment_count'))
            ->groupBy('evenement_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Charger les événements en une seule requête
        $eventIds = $topEventsData->pluck('evenement_id')->toArray();
        $events = \App\Models\Event::whereIn('id', $eventIds)->get()->keyBy('id');
        
        $topEvents = $topEventsData->map(function($item) use ($events) {
            $item->event = $events->get($item->evenement_id);
            $item->total_revenue = (float) $item->total_revenue;
            return $item;
        })->filter(function($item) {
            return $item->event !== null;
        });
        
        // Top 5 utilisateurs par dépenses
        $topUsersData = Payment::whereIn('statut', $paidStatuses)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('SUM(montant) as total_spent'), DB::raw('COUNT(*) as payment_count'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
        
        // Charger les utilisateurs en une seule requête
        $userIds = $topUsersData->pluck('user_id')->toArray();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $topUsers = $topUsersData->map(function($item) use ($users) {
            $item->user = $users->get($item->user_id);
            $item->total_spent = (float) $item->total_spent;
            return $item;
        })->filter(function($item) {
            return $item->user !== null;
        });
        
        // Données pour le graphique des méthodes de paiement
        $paymentMethods = Payment::whereIn('statut', $paidStatuses)
            ->where('methode_paiement', 'NOT LIKE', '%simulation%')
            ->where('methode_paiement', 'NOT LIKE', '%Simulation%')
            ->select('methode_paiement', DB::raw('COUNT(*) as count'))
            ->groupBy('methode_paiement')
            ->get();
        
        $paymentMethodsData = [
            'labels' => $paymentMethods->pluck('methode_paiement')->toArray(),
            'data' => $paymentMethods->pluck('count')->toArray()
        ];
        
        // Données pour le graphique des tendances (7 derniers jours)
        $trends = Payment::whereIn('statut', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(montant) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $paymentTrendsData = [
            'labels' => $trends->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m');
            })->toArray(),
            'data' => $trends->pluck('total')->toArray()
        ];
        
        return view('dashboard.admin.payments.index', compact(
            'payments',
            'totalPayments',
            'paidPayments',
            'totalRevenue',
            'pendingPayments',
            'successRate',
            'topEvents',
            'topUsers',
            'paymentMethodsData',
            'paymentTrendsData'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'order', 'order.tickets']);
        return view('dashboard.admin.payments.show', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'statut' => 'required|in:' . Payment::STATUS_PENDING . ',' . Payment::STATUS_PAID . ',' . Payment::STATUS_FAILED . ',' . Payment::STATUS_CANCELLED,
            'note_admin' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $payment->update($validated);

            if ($validated['statut'] === Payment::STATUS_PAID && $payment->order) {
                $payment->order->update(['statut' => 'payé']);
            }

            DB::commit();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Paiement mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du paiement: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du paiement');
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            if ($payment->statut === Payment::STATUS_PAID) {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer un paiement déjà effectué');
            }

            $payment->delete();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Paiement supprimé avec succès');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du paiement: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du paiement');
        }
    }

    public function export(Request $request)
    {
        $query = Payment::with(['user', 'order', 'event']);

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $payments = $query->orderByDesc('created_at')->get();

        $periodLabel = match (true) {
            $request->filled('date_start') && $request->filled('date_end') =>
                'Du ' . date('d/m/Y', strtotime($request->date_start)) . ' au ' . date('d/m/Y', strtotime($request->date_end)),
            $request->filled('date_start') => 'Depuis le ' . date('d/m/Y', strtotime($request->date_start)),
            $request->filled('date_end') => 'Jusqu\'au ' . date('d/m/Y', strtotime($request->date_end)),
            default => 'Tous les paiements',
        };

        $filename = 'paiements_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new AdminPaymentsExport($payments, $periodLabel), $filename);
    }

    public function statistics()
    {
        $paidStatuses = [Payment::STATUS_PAID, 'Payé', 'PAYÉ'];
        
        $stats = [
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::whereIn('statut', $paidStatuses)->count(),
            'total_amount' => Payment::whereIn('statut', $paidStatuses)->sum('montant') ?? 0,
            'payment_methods' => Payment::whereIn('statut', $paidStatuses)
                ->select('methode_paiement', DB::raw('count(*) as total'))
                ->groupBy('methode_paiement')
                ->get()
        ];

        return view('dashboard.admin.payments.statistics', compact('stats'));
    }

    public function download(Payment $payment)
    {
        try {
            $payment->load(['user', 'order', 'order.tickets']);

            $pdf = PDF::loadView('pdf.invoice', [
                'payment' => $payment,
                'user' => $payment->user,
                'order' => $payment->order
            ]);

            $filename = 'facture_' . $payment->reference_transaction . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement de la facture: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du téléchargement de la facture');
        }
    }
}

